<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Models\ChartOfAccount;
use App\Modules\Finance\Models\JournalEntry;
use App\Modules\Finance\Models\FiscalYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function trialBalance(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-01-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        // Group by account
        $balances = JournalEntry::select(
                'account_id',
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit')
            )
            ->whereHas('journal', function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->where('status', 'posted');
            })
            ->groupBy('account_id')
            ->with('account')
            ->get();
            
        return view('Finance::pages.reports.trial_balance', compact('balances', 'startDate', 'endDate'));
    }

    public function balanceSheet(Request $request)
    {
        $asOfDate = $request->get('as_of_date', date('Y-m-d'));

        // Get Assets (1), Liabilities (2), Equity (3) - cumulative from start
        $data = $this->getAccountBalances(['1', '2', '3'], $asOfDate);
        
        // Calculate Retained Earnings (cumulative Revenue - Expense from all time)
        $retainedEarnings = $this->getAccountGroupTotal(['4'], $asOfDate, null) - $this->getAccountGroupTotal(['5'], $asOfDate, null);
        
        return view('Finance::pages.reports.balance_sheet', compact('data', 'asOfDate', 'retainedEarnings'));
    }

    public function profitLoss(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-01-01'));
        $endDate = $request->get('end_date', date('Y-m-d')); // Current Period

        // Get Revenue (4), Expense (5)
        $data = $this->getAccountBalancesRange(['4', '5'], $startDate, $endDate);

        return view('Finance::pages.reports.profit_loss', compact('data', 'startDate', 'endDate'));
    }

    public function ledger(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-01-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        $accountId = $request->get('account_id');

        $accounts = ChartOfAccount::orderBy('code')->get();
        $entries = collect([]);
        $openingBalance = 0;
        $selectedAccount = null;

        if ($accountId) {
            $selectedAccount = ChartOfAccount::find($accountId);
            
            // Calculate Opening Balance (Sum of all posted entries before start_date)
            // Or if Asset/Liability, it's lifetime. If P&L, it resets?
            // "Industry Level" Ledger typically shows Balance Brought Forward.
            // For P&L accounts, opening balance for a period is usually 0 if it's a new fiscal year.
            // But mechanically:
            
            $openingEntries = JournalEntry::where('account_id', $accountId)
                ->whereHas('journal', function($q) use ($startDate) {
                    $q->where('date', '<', $startDate)->where('status', 'posted');
                })->get();

            foreach ($openingEntries as $entry) {
                if ($selectedAccount->type->normal_balance == 'debit') {
                    $openingBalance += $entry->debit - $entry->credit;
                } else {
                    $openingBalance += $entry->credit - $entry->debit;
                }
            }
            
            // Get entries for the period
            $entries = JournalEntry::where('account_id', $accountId)
                ->whereHas('journal', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate])->where('status', 'posted');
                })
                ->with(['journal'])
                ->join('finance_journals', 'finance_journals.id', '=', 'finance_journal_entries.journal_id')
                ->orderBy('finance_journals.date')
                ->select('finance_journal_entries.*') // Avoid column collision
                ->get();
        }

        return view('Finance::pages.reports.ledger', compact('accounts', 'entries', 'openingBalance', 'selectedAccount', 'startDate', 'endDate'));
    }

    private function getAccountBalances($typePrefixes, $asOfDate) {
        // Complex logic to sum all entries up to date
        // Simplified: Fetch entries grouped by account type
        
        $entries = JournalEntry::whereHas('journal', function($q) use ($asOfDate) {
                $q->where('date', '<=', $asOfDate)
                  ->where('status', 'posted');
            })
            ->whereHas('account.type', function($q) use ($typePrefixes) {
                $q->whereIn('code_prefix', $typePrefixes);
            })
            ->with(['account.type'])
            ->get();
            
        // Group by Type -> Account
        $grouped = [];
        foreach($typePrefixes as $prefix) {
            $grouped[$prefix] = [];
        }

        foreach ($entries as $entry) {
            $prefix = $entry->account->type->code_prefix;
            $accountId = $entry->account_id;
            
            if (!isset($grouped[$prefix][$accountId])) {
                $grouped[$prefix][$accountId] = [
                    'account' => $entry->account,
                    'balance' => 0
                ];
            }
            
            // Calculate Balance based on Normal Balance
            $normal = $entry->account->type->normal_balance;
            if ($normal == 'debit') {
                $grouped[$prefix][$accountId]['balance'] += $entry->debit - $entry->credit;
            } else {
                $grouped[$prefix][$accountId]['balance'] += $entry->credit - $entry->debit;
            }
        }
        
        return $grouped;
    }

    private function getAccountBalancesRange($typePrefixes, $startDate, $endDate) {
        $entries = JournalEntry::whereHas('journal', function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->where('status', 'posted');
            })
            ->whereHas('account.type', function($q) use ($typePrefixes) {
                $q->whereIn('code_prefix', $typePrefixes);
            })
            ->with(['account.type'])
            ->get();
            
        // Group by Type -> Account
        $grouped = [];
        foreach($typePrefixes as $prefix) {
            $grouped[$prefix] = [];
        }

        foreach ($entries as $entry) {
            $prefix = $entry->account->type->code_prefix;
            $accountId = $entry->account_id;
            
            if (!isset($grouped[$prefix][$accountId])) {
                $grouped[$prefix][$accountId] = [
                    'account' => $entry->account,
                    'balance' => 0
                ];
            }
            
            $normal = $entry->account->type->normal_balance;
            if ($normal == 'debit') {
                $grouped[$prefix][$accountId]['balance'] += $entry->debit - $entry->credit;
            } else {
                $grouped[$prefix][$accountId]['balance'] += $entry->credit - $entry->debit;
            }
        }
        
        return $grouped;
    }
    
    private function calculateNetIncome($asOfDate) {
        // Revenue (4) - Expense (5)
        $revenue = $this->getAccountGroupTotal(['4'], $asOfDate, null);
        $expense = $this->getAccountGroupTotal(['5'], $asOfDate, null);
        return $revenue - $expense;
    }


    public function cashbook(Request $request) {
        return $this->book($request, 'Cash');
    }

    public function bankbook(Request $request) {
        return $this->book($request, 'Bank');
    }

    private function book(Request $request, $typeInfo)
    {
        $startDate = $request->get('start_date', date('Y-01-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        
        // Find accounts by code pattern (Cash: 1010*, Bank: 1020*)
        $codePrefix = $typeInfo == 'Cash' ? '1010' : '1020';
        
        $accountIds = ChartOfAccount::where('code', 'like', $codePrefix . '%')
            ->where('is_group', false)
            ->pluck('id');
        
        if ($accountIds->isEmpty()) {
            // Fallback: try by account type name
            $entries = JournalEntry::whereHas('account.type', function($q) use ($typeInfo) {
                    $q->where('name', 'like', "%$typeInfo%"); 
                })
                ->whereHas('journal', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate])->where('status', 'posted');
                })
                ->with(['journal', 'account'])
                ->get()
                ->groupBy('account_id');
        } else {
            $entries = JournalEntry::whereIn('account_id', $accountIds)
                ->whereHas('journal', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate])->where('status', 'posted');
                })
                ->with(['journal', 'account'])
                ->get()
                ->groupBy('account_id');
        }
            
        $accounts = ChartOfAccount::whereIn('id', $entries->keys())->get();

        return view('Finance::pages.reports.cashbook', compact('entries', 'startDate', 'endDate', 'typeInfo', 'accounts'));
    }

    public function summary(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-01-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        
        $query = \App\Modules\Finance\Models\Journal::with('entries.account')
            ->whereIn('type', ['payment', 'receipt'])
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'posted');
            
        $journals = $query->orderBy('date', 'asc')->get();
        
        $receipts = $journals->where('type', 'receipt');
        $payments = $journals->where('type', 'payment');
        
        $summary = $journals->groupBy('type')->map(function ($group, $key) {
            return (object)[
                'type' => $key,
                'total_amount' => $group->sum(function($j) { return $j->entries->sum('debit'); }),
                'count' => $group->count()
            ];
        })->values();

        return view('Finance::pages.reports.summary', [
            'journals' => $journals,
            'receipts' => $receipts,
            'payments' => $payments,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'type' => $request->get('type', 'all')
        ]);
    }
    
    public function dishonoured(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-01-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        
        $journals = \App\Modules\Finance\Models\Journal::where('status', 'dishonoured')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
            
        return view('Finance::pages.reports.dishonoured', compact('journals', 'startDate', 'endDate'));
    }
    
    public function retainedEarnings(Request $request)
    {
        $asOfDate = $request->get('as_of_date', date('Y-m-d'));
        
        // Get all revenue and expense data grouped by fiscal year
        $fiscalYears = \App\Modules\Finance\Models\FiscalYear::where('start_date', '<=', $asOfDate)
            ->orderBy('start_date')
            ->get();
        
        $details = [];
        $cumulativeRE = 0;
        
        foreach ($fiscalYears as $fy) {
            $calculationEndDate = min($fy->end_date, $asOfDate);
            $revenue = $this->getAccountGroupTotal(['4'], $calculationEndDate, $fy->start_date);
            $expense = $this->getAccountGroupTotal(['5'], $calculationEndDate, $fy->start_date);
            $netIncome = $revenue - $expense;
            $cumulativeRE += $netIncome;
            
            $details[] = [
                'fiscal_year' => $fy->name,
                'revenue' => $revenue,
                'expense' => $expense,
                'net_income' => $netIncome,
                'cumulative_re' => $cumulativeRE
            ];
        }
        
        return view('Finance::pages.reports.retained_earnings', compact('details', 'asOfDate', 'cumulativeRE'));
    }
    
    public function bankTransfer(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-01-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        
        // Find journals where both debit and credit are in cash/bank accounts (1010*, 1020*)
        $journals = \App\Modules\Finance\Models\Journal::with('entries.account')
            ->where('status', 'posted')
            ->whereBetween('date', [$startDate, $endDate])
            ->whereHas('entries', function($q) {
                $q->whereHas('account', function($acc) {
                    $acc->where('code', 'like', '1010%')->orWhere('code', 'like', '1020%');
                })->where('debit', '>', 0);
            })
            ->whereHas('entries', function($q) {
                $q->whereHas('account', function($acc) {
                    $acc->where('code', 'like', '1010%')->orWhere('code', 'like', '1020%');
                })->where('credit', '>', 0);
            })
            ->orderBy('date', 'desc')
            ->get()
            ->filter(function($journal) {
                // Only include if ALL entries are cash/bank accounts
                $allCashBank = $journal->entries->every(function($entry) {
                    return str_starts_with($entry->account->code, '1010') || str_starts_with($entry->account->code, '1020');
                });
                return $allCashBank;
            });
            
        return view('Finance::pages.reports.bank_transfer', compact('journals', 'startDate', 'endDate'));
    }
    
    public function voucherWise(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-01-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        $voucherType = $request->get('voucher_type', 'all');
        
        $query = \App\Modules\Finance\Models\Journal::with('entries.account')
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'posted');
        
        if ($voucherType != 'all') {
            $query->where('type', $voucherType);
        }
        
        $journals = $query->orderBy('date', 'desc')->get();
            
        return view('Finance::pages.reports.voucher_wise', compact('journals', 'startDate', 'endDate', 'voucherType'));
    }
    
    private function getAccountGroupTotal($prefixes, $endDate, $startDate = null) {
        $query = JournalEntry::whereHas('journal', function($q) use ($endDate, $startDate) {
                if ($startDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                } else {
                    $q->where('date', '<=', $endDate);
                }
                $q->where('status', 'posted');
            })
            ->whereHas('account.type', function($q) use ($prefixes) {
                $q->whereIn('code_prefix', $prefixes);
            })
            ->with('account.type')
            ->get();

        $total = 0;
        foreach ($query as $entry) {
            $normal = $entry->account->type->normal_balance;
             if ($normal == 'debit') {
                $total += $entry->debit - $entry->credit;
            } else {
                $total += $entry->credit - $entry->debit;
            }
        }
        return $total;
    }

    public function export(Request $request)
    {
        $type = $request->get('type');
        $startDate = $request->get('start_date', date('Y-01-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));
        $asOfDate = $request->get('as_of_date', date('Y-m-d'));
        
        $filename = 'report_' . $type . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($request, $type, $startDate, $endDate, $asOfDate) {
            $file = fopen('php://output', 'w');
            
            switch($type) {
                case 'trial-balance':
                    $balances = $this->getAccountBalances(['1', '2', '3', '4', '5'], $endDate);
                    fputcsv($file, ['TRIAL BALANCE', 'From: ' . $startDate, 'To: ' . $endDate]);
                    fputcsv($file, ['Account Name', 'Code', 'Debit', 'Credit']);
                    foreach($balances as $prefix => $accs) {
                        foreach($accs as $item) {
                            $bal = $item['balance'];
                            fputcsv($file, [$item['account']->name, $item['account']->code, $bal > 0 ? $bal : 0, $bal < 0 ? abs($bal) : 0]);
                        }
                    }
                    break;
                
                case 'balance-sheet':
                    $data = $this->getAccountBalances(['1', '2', '3'], $asOfDate);
                    fputcsv($file, ['BALANCE SHEET', 'As of: ' . $asOfDate]);
                    fputcsv($file, ['Category', 'Account', 'Amount']);
                    foreach(['1' => 'Assets', '2' => 'Liabilities', '3' => 'Equity'] as $prefix => $label) {
                        if (isset($data[$prefix])) {
                            foreach($data[$prefix] as $item) {
                                fputcsv($file, [$label, $item['account']->name, $item['balance']]);
                            }
                        }
                    }
                    break;

                case 'profit-loss':
                    $data = $this->getAccountBalances(['4', '5'], $endDate);
                    fputcsv($file, ['PROFIT & LOSS', 'From: ' . $startDate, 'To: ' . $endDate]);
                    fputcsv($file, ['Type', 'Account', 'Amount']);
                    foreach(['4' => 'Revenue', '5' => 'Expense'] as $prefix => $label) {
                        if (isset($data[$prefix])) {
                            foreach($data[$prefix] as $item) {
                                fputcsv($file, [$label, $item['account']->name, $item['balance']]);
                            }
                        }
                    }
                    break;

                case 'ledger':
                    $accountId = $request->get('account_id');
                    $account = ChartOfAccount::find($accountId);
                    $entries = JournalEntry::where('account_id', $accountId)
                        ->whereHas('journal', function($q) use ($startDate, $endDate) {
                            $q->whereBetween('date', [$startDate, $endDate])->where('status', 'posted');
                        })->with('journal')->get();
                    fputcsv($file, ['LEDGER REPORT', $account->name ?? 'All Accounts', 'From: ' . $startDate, 'To: ' . $endDate]);
                    fputcsv($file, ['Date', 'Voucher NO', 'Description', 'Debit', 'Credit']);
                    foreach($entries as $e) {
                        fputcsv($file, [$e->journal->date->format('Y-m-d'), $e->journal->journal_number, $e->description ?: $e->journal->description, $e->debit, $e->credit]);
                    }
                    break;

                case 'cashbook':
                case 'bankbook':
                    $typeInfo = $type == 'cashbook' ? 'Cash' : 'Bank';
                    $accountIds = ChartOfAccount::whereHas('type', function($q) use ($typeInfo) {
                        $q->where('name', $typeInfo);
                    })->pluck('id');
                    $entries = JournalEntry::whereIn('account_id', $accountIds)
                        ->whereHas('journal', function($q) use ($startDate, $endDate) {
                            $q->whereBetween('date', [$startDate, $endDate])->where('status', 'posted');
                        })->with(['journal', 'account'])->get();
                    fputcsv($file, [strtoupper($typeInfo) . ' BOOK', 'From: ' . $startDate, 'To: ' . $endDate]);
                    fputcsv($file, ['Date', 'Account', 'Voucher NO', 'Description', 'Debit', 'Credit']);
                    foreach($entries as $e) {
                        fputcsv($file, [$e->journal->date->format('Y-m-d'), $e->account->name, $e->journal->journal_number, $e->description ?: $e->journal->description, $e->debit, $e->credit]);
                    }
                    break;

                case 'bank-transfer':
                    $journals = \App\Modules\Finance\Models\Journal::with('entries.account')
                        ->where('status', 'posted')
                        ->whereBetween('date', [$startDate, $endDate])
                        ->get()
                        ->filter(function($j) {
                            return $j->entries->every(fn($e) => str_starts_with($e->account->code, '1010') || str_starts_with($e->account->code, '1020'));
                        });
                    fputcsv($file, ['BANK TRANSFER REPORT', 'From: ' . $startDate, 'To: ' . $endDate]);
                    fputcsv($file, ['Date', 'Voucher NO', 'Amount', 'Description']);
                    foreach($journals as $j) {
                        fputcsv($file, [$j->date->format('Y-m-d'), $j->journal_number, $j->entries->sum('debit'), $j->description]);
                    }
                    break;

                case 'voucher-wise':
                    $journals = \App\Modules\Finance\Models\Journal::with('entries.account')
                        ->whereBetween('date', [$startDate, $endDate])
                        ->where('status', 'posted')
                        ->get();
                    fputcsv($file, ['VOUCHER-WISE REPORT', 'From: ' . $startDate, 'To: ' . $endDate]);
                    fputcsv($file, ['Date', 'Voucher NO', 'Account', 'Debit', 'Credit', 'Description']);
                    foreach($journals as $j) {
                        foreach($j->entries as $e) {
                            fputcsv($file, [$j->date->format('Y-m-d'), $j->journal_number, $e->account->name, $e->debit, $e->credit, $e->description ?: $j->description]);
                        }
                    }
                    break;

                case 'retained-earnings':
                    fputcsv($file, ['RETAINED EARNINGS STATEMENT', 'As of: ' . $asOfDate]);
                    fputcsv($file, ['Year', 'Net Income', 'Cumulative']);
                    // Simplified: Re-fetch or pass data
                    fputcsv($file, ['Summary export only for retained earnings']);
                    break;

                case 'dishonoured':
                    $journals = \App\Modules\Finance\Models\Journal::with('entries.account')
                        ->where('status', 'dishonoured')
                        ->whereBetween('date', [$startDate, $endDate])
                        ->get();
                    fputcsv($file, ['CHEQUE DISHONOUR REPORT', 'From: ' . $startDate, 'To: ' . $endDate]);
                    fputcsv($file, ['Date', 'Voucher NO', 'Account', 'Amount', 'Description']);
                    foreach($journals as $j) {
                        fputcsv($file, [$j->date->format('Y-m-d'), $j->journal_number, $j->entries->first()->account->name ?? 'N/A', $j->entries->sum('debit'), $j->description]);
                    }
                    break;

                case 'summary':
                    $query = \App\Modules\Finance\Models\Journal::with('entries')
                        ->whereIn('type', ['payment', 'receipt'])
                        ->whereBetween('date', [$startDate, $endDate])
                        ->where('status', 'posted');
                    $journals = $query->orderBy('date', 'asc')->get();
                    $receipts = $journals->where('type', 'receipt');
                    $payments = $journals->where('type', 'payment');
                    
                    fputcsv($file, ['PAYMENT & RECEIPT SUMMARY', 'From: ' . $startDate, 'To: ' . $endDate]);
                    fputcsv($file, ['', 'RECEIPTS', '', '', '', 'PAYMENTS']);
                    fputcsv($file, ['Date', 'Voucher', 'Particulars', 'Amount', '|', 'Date', 'Voucher', 'Particulars', 'Amount']);
                    
                    $maxRows = max($receipts->count(), $payments->count());
                    $rArr = $receipts->values();
                    $pArr = $payments->values();
                    
                    for($i = 0; $i < $maxRows; $i++) {
                        $row = [];
                        // Receipt
                        if(isset($rArr[$i])) {
                            $row[] = $rArr[$i]->date->format('Y-m-d');
                            $row[] = $rArr[$i]->journal_number;
                            $row[] = $rArr[$i]->description;
                            $row[] = $rArr[$i]->entries->sum('debit');
                        } else {
                            $row = array_merge($row, ['', '', '', '']);
                        }
                        $row[] = '|';
                        // Payment
                        if(isset($pArr[$i])) {
                            $row[] = $pArr[$i]->date->format('Y-m-d');
                            $row[] = $pArr[$i]->journal_number;
                            $row[] = $pArr[$i]->description;
                            $row[] = $pArr[$i]->entries->sum('debit');
                        } else {
                            $row = array_merge($row, ['', '', '', '']);
                        }
                        fputcsv($file, $row);
                    }
                    fputcsv($file, ['TOTAL', '', '', $receipts->sum(fn($j)=>$j->entries->sum('debit')), '|', 'TOTAL', '', '', $payments->sum(fn($j)=>$j->entries->sum('debit'))]);
                    break;

                default:
                    fputcsv($file, ['Export details not implemented for: ' . $type]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

