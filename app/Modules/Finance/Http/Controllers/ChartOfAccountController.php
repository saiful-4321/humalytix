<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Models\ChartOfAccount;
use App\Modules\Finance\Models\AccountType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartOfAccountController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'default');
        
        // Fetch Account Types with Root Accounts eager loaded
        $accountTypes = AccountType::with(['accounts' => function($query) use ($sort) {
            $query->whereNull('parent_id')->with(['children' => function($q) use ($sort) {
                // Apply sort to children too if needed, or just roots? usually tree sort is by code.
                // But user asked for "created date, desc wise".
                if ($sort == 'date_desc') {
                    $q->orderBy('created_at', 'desc');
                } else {
                    $q->orderBy('code', 'asc');
                }
            }]);

            if ($sort == 'date_desc') {
                $query->orderBy('created_at', 'desc');
            } else {
                $query->orderBy('code', 'asc');
            }
        }])->get();

        return view('Finance::pages.accounts.index', compact('accountTypes', 'sort'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'excel');
        $accounts = ChartOfAccount::with('type')->orderBy('code')->get();

        // Flatten for Excel/CSV
        if ($type == 'excel' || $type == 'csv') {
            $list = $accounts->map(function ($account) {
                return [
                    'Code' => $account->code,
                    'Name' => $account->name,
                    'Type' => $account->type->name ?? '',
                    'Group' => $account->is_group ? 'Yes' : 'No',
                    'Parent Code' => $account->parent ? $account->parent->code : '-',
                    'Description' => $account->description,
                ];
            });

            if ($type == 'csv') {
                return (new \Rap2hpoutre\FastExcel\FastExcel($list))->download('chart_of_accounts.csv');
            }
            return (new \Rap2hpoutre\FastExcel\FastExcel($list))->download('chart_of_accounts.xlsx');
        }

        // PDF Export
        if ($type == 'pdf') {
            $pdf = \PDF::loadView('Finance::pages.accounts.pdf_export', compact('accounts'));
            return $pdf->download('chart_of_accounts.pdf');
        }

        return redirect()->back();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:finance_chart_of_accounts,code',
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:finance_account_types,id',
            'parent_id' => 'nullable|exists:finance_chart_of_accounts,id',
            'is_group' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $validated['is_group'] = $request->has('is_group');

        DB::beginTransaction();
        try {
            ChartOfAccount::create($validated);
            DB::commit();
            return redirect()->back()->with('success', 'Account created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create account: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $account = ChartOfAccount::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|unique:finance_chart_of_accounts,code,' . $id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Prevent changing type or parent if it has transactions (simplified check)
            if ($account->journalEntries()->exists()) {
                 // Basic update only
                 $account->update($request->only(['name', 'description']));
            } else {
                 $account->update($request->all());
            }
            
            DB::commit();
            return redirect()->back()->with('success', 'Account updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update account: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $account = ChartOfAccount::findOrFail($id);

        if ($account->is_system) {
            return redirect()->back()->with('error', 'Cannot delete system account');
        }
        
        if ($account->children()->exists()) {
             return redirect()->back()->with('error', 'Cannot delete account with sub-accounts');
        }

        if ($account->journalEntries()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete account with transactions');
        }

        $account->delete();
        return redirect()->back()->with('success', 'Account deleted successfully');
    }
}
