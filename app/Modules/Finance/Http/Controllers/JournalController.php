<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Models\Journal;
use App\Modules\Finance\Models\FiscalYear;
use App\Modules\Finance\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $query = Journal::with('fiscalYear')->orderBy('date', 'desc');

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        if ($request->filled('reference')) {
            $query->where('reference', 'like', '%' . $request->reference . '%');
        }
        if ($request->filled('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        $journals = $query->paginate(15);
        $fiscalYear = FiscalYear::current();
        $accounts = ChartOfAccount::where('is_group', false)->orderBy('code')->get();

        return view('Finance::pages.journals.index', compact('journals', 'fiscalYear', 'accounts'));
    }

    public function create()
    {
        $fiscalYear = FiscalYear::current();
        // Allow creation even if no current year, as user might pick a date. 
        // We can pass current if available for default display


        // Get leaf accounts only for entry
        $accounts = ChartOfAccount::where('is_group', false)->orderBy('code')->get();

        return view('Finance::pages.journals.create', compact('fiscalYear', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'reference' => 'nullable|string',
            'description' => 'nullable|string',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:finance_chart_of_accounts,id',
            'entries.*.debit' => 'required|numeric|min:0',
            'entries.*.credit' => 'required|numeric|min:0',
            'entries.*.description' => 'nullable|string',
        ]);

        // Find Fiscal Year for the Date
        $fiscalYear = FiscalYear::where('start_date', '<=', $request->date)
            ->where('end_date', '>=', $request->date)
            ->where('status', 'open') // Assuming we only allow posting to OPEN years.
            ->first();

        if (!$fiscalYear) {
             // If no open year, check for ANY year just to associate it (maybe closed?)
             // For now, let's strict it to OPEN years or return specific error
             $fiscalYear = FiscalYear::where('start_date', '<=', $request->date)
                ->where('end_date', '>=', $request->date)->first();
                
             if (!$fiscalYear) {
                 return back()->withInput()->with('error', "No Fiscal Year defined for date: {$request->date}");
             }
             
             if ($fiscalYear->status != 'open') {
                  return back()->withInput()->with('error', "Fiscal Year ({$fiscalYear->name}) is {$fiscalYear->status}. Cannot post.");
             }
        }
        
        // Validate Total Debit = Total Credit
        $totalDebit = collect($request->entries)->sum('debit');
        $totalCredit = collect($request->entries)->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
             return back()->withInput()->with('error', "Journal must balance. Debit: $totalDebit, Credit: $totalCredit");
        }
        
        // Validate Zero amounts in rows
        foreach($request->entries as $entry) {
            if ($entry['debit'] == 0 && $entry['credit'] == 0) {
                 return back()->withInput()->with('error', 'Entries cannot have both debit and credit as zero.');
            }
        }

        DB::beginTransaction();
        try {
            $journal = Journal::create([
                'date' => $request->date,
                'reference' => $request->reference,
                'description' => $request->description,
                'fiscal_year_id' => $fiscalYear->id,
                'status' => 'draft', // Default to draft
                'created_by' => Auth::id(),
            ]);

            foreach ($request->entries as $entry) {
                $journal->entries()->create([
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'],
                    'credit' => $entry['credit'],
                    'description' => $entry['description'] ?? null,
                ]);
            }

            DB::commit();
            
            // if 'post' action was clicked
            if ($request->has('post_journal')) {
                // Post logic could call a method here
                $this->postJournal($journal);
                return redirect()->route('finance.journals.index')->with('success', 'Journal created and posted successfully');
            }

            return redirect()->route('finance.journals.index')->with('success', 'Journal created as draft');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating journal: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $journal = Journal::with('entries')->findOrFail($id);
        
        if ($journal->status != 'draft') {
            return back()->with('error', 'Only draft journals can be edited.');
        }

        $fiscalYear = $journal->fiscalYear;
        $accounts = ChartOfAccount::where('is_group', false)->orderBy('code')->get();
        // Allow changing voucher type? Yes.
        $type = $journal->type;

        return view('Finance::pages.journals.edit', compact('journal', 'fiscalYear', 'accounts', 'type'));
    }

    public function update(Request $request, $id)
    {
        $journal = Journal::findOrFail($id);
        
        if ($journal->status != 'draft') {
            return back()->with('error', 'Only draft journals can be edited.');
        }

        $request->validate([
            'date' => 'required|date',
            'reference' => 'nullable|string|max:50',
            'description' => 'required|string|max:255',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:finance_chart_of_accounts,id',
            'entries.*.debit' => 'required|numeric|min:0',
            'entries.*.credit' => 'required|numeric|min:0',
            'entries.*.description' => 'nullable|string',
            'type' => 'required|in:journal,payment,receipt,contra,depreciation,payroll',
        ]);

        // Validate Balance
        $totalDebit = collect($request->entries)->sum('debit');
        $totalCredit = collect($request->entries)->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
             return back()->withInput()->with('error', "Journal must balance. Debit: $totalDebit, Credit: $totalCredit");
        }

        DB::transaction(function() use ($request, $journal) {
            $journal->update([
                'date' => $request->date,
                'reference' => $request->reference,
                'description' => $request->description,
                'type' => $request->type,
                'updated_by' => Auth::id(),
            ]);

            // Replace Entries
            $journal->entries()->delete(); // Soft delete old
            // Or force delete? Soft delete is safer if using SoftDeletes trait.
            
            // Actually, force delete entries might be cleaner to avoid clutter if editing a draft many times.
            // But let's stick to standard relations.
            
            foreach ($request->entries as $entry) {
                if ($entry['debit'] == 0 && $entry['credit'] == 0) continue;
                
                $journal->entries()->create([
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'],
                    'credit' => $entry['credit'],
                    'description' => $entry['description'] ?? null,
                ]);
            }
        });

        return redirect()->route('finance.journals.index')->with('success', 'Journal updated successfully.');
    }

    public function show($id)
    {
        $journal = Journal::with('entries.account')->findOrFail($id);
        return view('Finance::pages.journals.show', compact('journal'));
    }

    public function post($id)
    {
        $journal = Journal::findOrFail($id);
        if ($journal->status == 'posted') {
            return back()->with('error', 'Journal is already posted');
        }

        $this->postJournal($journal);
        return back()->with('success', 'Journal posted successfully');
    }

    private function postJournal($journal)
    {
        DB::transaction(function() use ($journal) {
            $journal->update([
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => Auth::id(),
            ]);

            // Update account balances
            foreach ($journal->entries as $entry) {
                $account = $entry->account;
                
                // Logic based on normal balance
                // If Debit Normal (Asset, Expense): Increase with Debit, Decrease with Credit
                // If Credit Normal (Liability, Equity, Income): Increase with Credit, Decrease with Debit
                
                $balanceChange = 0;
                $normalBalance = $account->type->normal_balance; // 'debit' or 'credit'

                if ($normalBalance == 'debit') {
                    $balanceChange = $entry->debit - $entry->credit;
                } else {
                    $balanceChange = $entry->credit - $entry->debit;
                }

                $account->current_balance += $balanceChange;
                $account->save();
                
                // Recursive update parent balances? 
                // Usually reporting calculates parents on the fly, but for dashboard speed we might store it.
                // For this implementation, we will stick to calculating leaf nodes and summing parents in reports.
            }
        });
    }
    public function destroy($id)
    {
        $journal = Journal::findOrFail($id);
        if ($journal->status != 'draft') {
            return back()->with('error', 'Only draft journals can be deleted.');
        }

        $journal->entries()->delete();
        $journal->delete();

        return back()->with('success', 'Journal deleted successfully.');
    }

    public function export(Request $request)
    {
        $query = Journal::with(['entries.account', 'fiscalYear'])->orderBy('date', 'desc');

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        if ($request->filled('reference')) {
            $query->where('reference', 'like', '%' . $request->reference . '%');
        }
        if ($request->filled('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        $journals = $query->get();
        $type = $request->get('export_type', 'excel');

        if ($type == 'pdf') {
            $company = \App\Modules\Settings\Models\CompanySetting::first();
            $pdf = \PDF::loadView('Finance::pages.journals.pdf_export', compact('journals', 'company'));
            return $pdf->download('journals.pdf');
        }

        return (new \Rap2hpoutre\FastExcel\FastExcel($journals))->download("journals.{$type}", function ($journal) {
            return [
                'Date' => $journal->date->format('Y-m-d'),
                'Number' => $journal->journal_number,
                'Reference' => $journal->reference,
                'Type' => ucfirst($journal->type),
                'Description' => $journal->description,
                'Status' => ucfirst($journal->status),
                'Amount' => $journal->total_amount, // Accessor assuming exists or sum entries
            ];
        });
    }

    public function print($id)
    {
        $journal = Journal::with('entries.account')->findOrFail($id);
        $company = \App\Modules\Settings\Models\CompanySetting::first();
        $pdf = \PDF::loadView('Finance::pages.journals.pdf_single', compact('journal', 'company'));
        return $pdf->stream("journal-{$journal->journal_number}.pdf");
    }
}
