<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Models\ChartOfAccount;
use App\Modules\Finance\Models\JournalEntry;
use App\Modules\Finance\Models\FiscalYear;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = FiscalYear::current();
        
        // --- 1. Filter Logic (Presets & Intervals) ---
        $preset = $request->input('preset', 'this_year'); // Default to This Year
        $interval = $request->input('interval', 'month'); // Default to Monthly view
        
        // Determine Dates based on Preset
        switch ($preset) {
            case 'today':
                $startDate = date('Y-m-d');
                $endDate = date('Y-m-d');
                $interval = 'day'; // Force daily for single day? Or maybe hourly (not supported yet)
                break;
            case 'this_week':
                $startDate = date('Y-m-d', strtotime('monday this week'));
                $endDate = date('Y-m-d', strtotime('sunday this week'));
                if ($request->missing('interval')) $interval = 'day';
                break;
            case 'this_month':
                $startDate = date('Y-m-01');
                $endDate = date('Y-m-t');
                if ($request->missing('interval')) $interval = 'week';
                break;
            case 'last_month':
                $startDate = date('Y-m-01', strtotime('last month'));
                $endDate = date('Y-m-t', strtotime('last month'));
                if ($request->missing('interval')) $interval = 'week';
                break;
            case 'this_quarter':
                $startDate = $this->getQuarterStart();
                $endDate = $this->getQuarterEnd();
                 if ($request->missing('interval')) $interval = 'month';
                break;
            case 'this_year':
                $startDate = date('Y-01-01');
                $endDate = date('Y-12-31');
                 if ($request->missing('interval')) $interval = 'month';
                break;
            case 'custom':
                $startDate = $request->input('start_date', date('Y-01-01'));
                $endDate = $request->input('end_date', date('Y-12-31'));
                break;
            default:
                $startDate = date('Y-01-01');
                $endDate = date('Y-12-31');
                break;
        }

        // --- 2. KPI Totals (Aggregate in Range) ---
        $totalRevenue = $this->getAccountGroupTotal(['4'], $startDate, $endDate);
        $totalCOGS = 0; // Placeholder
        $totalOPEX = $this->getAccountGroupTotal(['5'], $startDate, $endDate);
        
        $grossProfit = $totalRevenue - $totalCOGS;
        $netProfit = $grossProfit - $totalOPEX;
        
        $grossProfitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
        $opexRatio = $totalRevenue > 0 ? ($totalOPEX / $totalRevenue) * 100 : 0;
        $netProfitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // --- 3. Trend Data (Dynamic Interval) ---
        $trendData = $this->getTrendData($startDate, $endDate, $interval);
        
        // Prepare Sparkline Arrays (Just the numbers from the trend data)
        $sparklineRevenue = $trendData['revenue'];
        $sparklineCOGS = array_fill(0, count($sparklineRevenue), 0);
        $sparklineGrossProfit = array_map(function($r, $c) { return $r - $c; }, $sparklineRevenue, $sparklineCOGS);
        $sparklineNetProfit = array_map(function($g, $o) { return $g - $o; }, $sparklineGrossProfit, $trendData['expense']);

        // --- 4. Balance Sheet (As of End Date) ---
        $totalAssets = $this->getAccountGroupTotal(['1']); 
        $totalLiabilities = $this->getAccountGroupTotal(['2']); 
        $totalEquity = $this->getAccountGroupTotal(['3']); 
        
        $assetsBreakdown = $this->getAccountTypeBreakdown(['1']);
        $liabilitiesBreakdown = $this->getAccountTypeBreakdown(['2']);

        $expenseData = $this->getCommonExpenseCategories($startDate, $endDate);
        
        $recentTransactions = JournalEntry::with(['journal', 'account'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('Finance::pages.dashboard', compact(
            'currentYear', 'startDate', 'endDate', 'preset', 'interval',
            'totalRevenue', 'totalCOGS', 'totalOPEX', 'grossProfit', 'netProfit',
            'grossProfitMargin', 'opexRatio', 'netProfitMargin',
            'sparklineRevenue', 'sparklineCOGS', 'sparklineGrossProfit', 'sparklineNetProfit',
            'totalAssets', 'totalLiabilities', 'totalEquity',
            'assetsBreakdown', 'liabilitiesBreakdown',
            'recentTransactions', 'trendData', 'expenseData'
        ));
    }

    // Helper for Quarters
    private function getQuarterStart() {
        $month = date('n');
        $quarter = ceil($month / 3);
        return date('Y-m-d', mktime(0, 0, 0, ($quarter - 1) * 3 + 1, 1, date('Y')));
    }
    
    private function getQuarterEnd() {
        $month = date('n');
        $quarter = ceil($month / 3);
        return date('Y-m-t', mktime(0, 0, 0, $quarter * 3, 1, date('Y')));
    }

    private function getTrendData($start, $end, $interval)
    {
        // 1. Generate Labels & Time Buckets
        $labels = [];
        $buckets = [];
        $current = \Carbon\Carbon::parse($start);
        $endC = \Carbon\Carbon::parse($end);
        
        while ($current <= $endC) {
            if ($interval == 'day') {
                $key = $current->format('Y-m-d');
                $label = $current->format('d M');
                $next = $current->copy()->addDay();
            } elseif ($interval == 'week') {
                $key = $current->format('o-W'); // ISO Year-Week
                $label = 'W' . $current->format('W') . ' ' . $current->format('M');
                $next = $current->copy()->addWeek();
            } elseif ($interval == 'quarter') {
                 $q = ceil($current->month / 3);
                 $key = $current->year . '-Q' . $q;
                 $label = 'Q' . $q . ' ' . $current->year;
                 $next = $current->copy()->addMonths(3)->startOfQuarter(); 
                 // Fix: ensure we don't loop forever if addMonths lands same
                 if($next <= $current) $next = $current->copy()->addMonths(1);
            } else { // month default
                $key = $current->format('Y-m');
                $label = $current->format('M Y');
                $next = $current->copy()->addMonth();
            }
            
            $labels[] = $label;
            $buckets[$key] = [
                'revenue' => 0, 
                'expense' => 0,
                'start' => $current->copy(),
                'end' => $interval == 'day' ? $current->copy()->endOfDay() : ($interval == 'week' ? $current->copy()->endOfWeek() : ($interval == 'quarter' ? $current->copy()->endOfQuarter() : $current->copy()->endOfMonth()))
            ];
            
            $current = $next;
        }

        // 2. Query Data (Optimized: Single query per Type if possible, or iterate buckets is clearer for varying intervals)
        // For accurate interval summation, iteratively querying ranges is safest given complex SQL date grouping across DB types
        
        $revenueData = [];
        $expenseData = [];
        
        foreach ($buckets as $key => $bucket) {
            $revenueData[] = $this->getTotalByRange(['4'], $bucket['start'], $bucket['end']);
            $expenseData[] = $this->getTotalByRange(['5'], $bucket['start'], $bucket['end']);
        }
        
        return ['labels' => $labels, 'revenue' => $revenueData, 'expense' => $expenseData];
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

        $entries = $query->get();

        $total = 0;
        foreach ($entries as $entry) {
            $normal = $entry->account->type->normal_balance;
            // For P&L (Rev/Exp), we want absolute values for usage
            if ($prefixes[0] == '4' || $prefixes[0] == '5') {
                 if ($prefixes[0] == '4') { // Revenue (Credit normal)
                     $total += $entry->credit - $entry->debit;
                 } else { // Expense (Debit normal)
                     $total += $entry->debit - $entry->credit;
                 }
            } else {
                // Balance Sheet
                if ($normal == 'debit') {
                    $total += $entry->debit - $entry->credit;
                } else {
                    $total += $entry->credit - $entry->debit;
                }
            }
        }
        return $total;
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
             $prefix = $prefixes[0];
             if ($prefix == '4') { // Revenue
                 $total += $entry->credit - $entry->debit;
             } else { // Expense
                 $total += $entry->debit - $entry->credit;
             }
        }
        return $total;
    }
    
    private function getCommonExpenseCategories($start = null, $end = null)
    {
        // Use passed dates or default
        if (!$start) $start = date('Y-01-01');
        if (!$end) $end = date('Y-12-31');
        
        $accounts = JournalEntry::whereHas('journal', function($q) use ($start, $end) {
                $q->where('status', 'posted')
                  ->whereBetween('date', [$start, $end]);
            })
            ->whereHas('account.type', function($q) {
                $q->where('code_prefix', '5');
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
    
    private function getAccountTypeBreakdown($prefixes)
    {
         // Simple breakdown by Account Name for now
         $accounts = JournalEntry::whereHas('journal', function($q) {
                $q->where('status', 'posted');
            })
            ->whereHas('account.type', function($q) use ($prefixes) {
                $q->whereIn('code_prefix', $prefixes);
            })
            ->select('account_id', DB::raw('sum(CASE WHEN (select normal_balance from finance_account_types where id = accounts.type_id) = "debit" THEN debit - credit ELSE credit - debit END) as total'))
            ->leftJoin('finance_chart_of_accounts as accounts', 'finance_journal_entries.account_id', '=', 'accounts.id')
            ->groupBy('account_id') // Group by ID is safer standard SQL
            ->with('account')
            ->orderByDesc('total')
            ->take(5)
            ->get();

         return $accounts;
    }
}
