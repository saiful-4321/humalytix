<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Models\ChartOfAccount;
use App\Modules\Finance\Models\JournalEntry;
use App\Modules\Finance\Models\FiscalYear;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $currentYear = FiscalYear::current();
        
        $totalAssets = $this->getAccountGroupTotal(['1']); // Asset (Lifetime)
        $totalLiabilities = $this->getAccountGroupTotal(['2']); // Liability (Lifetime)
        $totalEquity = $this->getAccountGroupTotal(['3']); // Equity (Lifetime)
        
        // P&L for Current Fiscal Year
        $start = $currentYear ? $currentYear->start_date : date('Y-01-01');
        $end = $currentYear ? $currentYear->end_date : date('Y-12-31');
        
        $totalRevenue = $this->getAccountGroupTotal(['4'], $start, $end); // Revenue (FY)
        $totalExpenses = $this->getAccountGroupTotal(['5'], $start, $end); // Expense (FY)
        
        $netIncome = $totalRevenue - $totalExpenses;
        
        // Recent 5 entries
        $recentTransactions = JournalEntry::with(['journal', 'account'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $monthlyData = $this->getMonthlyData();
        $expenseData = $this->getCommonExpenseCategories();

        return view('Finance::pages.dashboard', compact(
            'currentYear', 
            'totalAssets', 
            'totalLiabilities', 
            'totalEquity', 
            'totalRevenue', 
            'totalExpenses', 
            'netIncome',
            'recentTransactions',
            'monthlyData',
            'expenseData'
        ));
    }
    
    private function getAccountGroupTotal($prefixes, $start = null, $end = null) {
        $query = JournalEntry::whereHas('journal', function($q) use ($start, $end) {
                $q->where('status', 'posted');
                if ($start && $end) {
                    $q->whereBetween('date', [$start, $end]);
                }
            })
            ->whereHas('account.type', function($q) use ($prefixes) {
                $q->whereIn('code_prefix', $prefixes);
            })
            ->with('account.type');

        // Use sum directly on DB for performance
        $sum = 0;
        // Optimization: We can't easily do sums with mixed debit/credit logic in one query without DB::raw case statements.
        // Keeping it simple but iterating chunk if massive? 
        // For dashboard, assume dataset isn't millions yet.
        
        $entries = $query->get();

        $total = 0;
        foreach ($entries as $entry) {
            $normal = $entry->account->type->normal_balance;
             if ($normal == 'debit') {
                $total += $entry->debit - $entry->credit;
            } else {
                $total += $entry->credit - $entry->debit;
            }
        }
        return $total;
    }

    private function getMonthlyData()
    {
        // Get last 12 months revenue and expense
        $months = [];
        $revenue = [];
        $expense = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;
            
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();
            
            $revenue[] = $this->getTotalByRange(['4'], $start, $end);
            $expense[] = $this->getTotalByRange(['5'], $start, $end);
        }
        
        return ['months' => $months, 'revenue' => $revenue, 'expense' => $expense];
    }
    
    private function getCommonExpenseCategories()
    {
        $currentYear = FiscalYear::current();
        $start = $currentYear ? $currentYear->start_date : date('Y-01-01');
        $end = $currentYear ? $currentYear->end_date : date('Y-12-31');
        
        $totalExpenses = $this->getAccountGroupTotal(['5'], $start, $end);
        if ($totalExpenses == 0) return ['labels' => [], 'series' => []];

        $accounts = JournalEntry::whereHas('journal', function($q) use ($start, $end) {
                $q->where('status', 'posted')
                  ->whereBetween('date', [$start, $end]);
            })
            ->whereHas('account.type', function($q) {
                $q->where('code_prefix', '5'); // Expense
            })
            ->select('account_id', DB::raw('sum(debit - credit) as total'))
            ->groupBy('account_id')
            ->with('account')
            ->orderByDesc('total')
            ->take(5)
            ->get();
            
        return [
            'labels' => $accounts->pluck('account.name')->toArray(),
            'series' => $accounts->pluck('total')->toArray()
        ];
    }
    
    private function getTotalByRange($prefixes, $start, $end)
    {
        $entries = JournalEntry::whereHas('journal', function($q) use ($start, $end) {
                $q->whereBetween('date', [$start, $end])->where('status', 'posted');
            })
            ->whereHas('account.type', function($q) use ($prefixes) {
                $q->whereIn('code_prefix', $prefixes);
            })
            ->get();

        $total = 0;
        foreach ($entries as $entry) {
             // Revenue (4) is Credit normal. Expense (5) is Debit normal.
             // But we want absolute values for charts usually? 
             // Logic: Rev = Cr - Dr. Exp = Dr - Cr.
             $prefix = $prefixes[0]; // Assuming single type group for this simple chart
             
             if ($prefix == '4') { // Revenue
                 $total += $entry->credit - $entry->debit;
             } else { // Expense
                 $total += $entry->debit - $entry->credit;
             }
        }
        return $total;
    }
}
