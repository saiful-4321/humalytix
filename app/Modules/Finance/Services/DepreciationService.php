<?php

namespace App\Modules\Finance\Services;

use App\Modules\HRM\Models\Asset;
use App\Modules\Finance\Models\Journal;
use App\Modules\Finance\Models\FiscalYear;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepreciationService
{
    /**
     * Run depreciation for all active assets up to a specific date
     * Usually run monthly or annually.
     */
    public function runDepreciation($date = null)
    {
        $date = $date ? Carbon::parse($date) : now();
        $fiscalYear = FiscalYear::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('status', 'open')
            ->first();

        if (!$fiscalYear) {
            throw new \Exception("No open fiscal year found for date: {$date->format('Y-m-d')}");
        }

        $assets = Asset::where('status', '!=', 'retired')
            ->whereNotNull('purchase_date')
            ->whereNotNull('purchase_cost')
            ->with('category')
            ->get();

        $processedCount = 0;
        $journal = null;
        $totalDepr = 0;
        
        // Group entries into one big journal for the month? Or one per asset?
        // Industry standard: One Journal per Run, with multiple lines. Or grouped by Category Account.
        
        DB::beginTransaction();
        try {
            // Create the Journal Header
            $journal = Journal::create([
                'journal_number' => 'DEPR-' . $date->format('Y-m'),
                'date' => $date,
                'reference' => 'DEPR-RUN',
                'description' => "Depreciation Run for " . $date->format('F Y'),
                'fiscal_year_id' => $fiscalYear->id,
                'status' => 'draft', // Draft first for review
                'type' => 'depreciation',
                'created_by' => auth()->id() ?? 1,
            ]);

            $entriesByAccount = []; // Group by debit/credit accounts to minimize lines

            foreach ($assets as $asset) {
                if (!$asset->category || !$asset->category->depreciation_expense_account_id || !$asset->category->accumulated_depreciation_account_id) {
                    continue; // Skip if no accounts mapped
                }

                // Calculate Monthly Depreciation
                $annualDepr = $this->calculateAnnualDepreciation($asset);
                $monthlyDepr = $annualDepr / 12; // Simple straight line monthly

                if ($monthlyDepr <= 0) continue;

                // Group Debits (Expense)
                $drAcc = $asset->category->depreciation_expense_account_id;
                if (!isset($entriesByAccount[$drAcc])) $entriesByAccount[$drAcc] = 0;
                $entriesByAccount[$drAcc] += $monthlyDepr;

                // Group Credits (Accumulated)
                $crAcc = $asset->category->accumulated_depreciation_account_id;
                // We use negative key or separate array? Let's use separate array for credits to avoid collision if same acc used (unlikely)
                // Actually Cr is Acc Depr.
            }
            
            // Re-loop for structured entry creation based on Asset Category grouping
            // Better: Loop assets, calculate, and add to sum array keyed by "Dr_Acc_ID|Cr_Acc_ID"
            
            $groupedEntries = [];
            
            foreach ($assets as $asset) {
                if (!$asset->category || !$asset->category->depreciation_expense_account_id || !$asset->category->accumulated_depreciation_account_id) {
                     continue;
                }
                
                $annualDepr = $this->calculateAnnualDepreciation($asset);
                $monthlyDepr = $annualDepr / 12; 

                if ($monthlyDepr <= 0) continue;
                
                $key = $asset->category->depreciation_expense_account_id . '|' . $asset->category->accumulated_depreciation_account_id;
                
                if (!isset($groupedEntries[$key])) {
                    $groupedEntries[$key] = [
                        'dr_acc' => $asset->category->depreciation_expense_account_id,
                        'cr_acc' => $asset->category->accumulated_depreciation_account_id,
                        'amount' => 0
                    ];
                }
                $groupedEntries[$key]['amount'] += $monthlyDepr;
            }

            foreach ($groupedEntries as $entry) {
                // Debit Expense
                $journal->entries()->create([
                    'account_id' => $entry['dr_acc'],
                    'debit' => $entry['amount'],
                    'credit' => 0,
                    'description' => 'Depreciation Expense'
                ]);

                // Credit Accumulated Depreciation
                $journal->entries()->create([
                    'account_id' => $entry['cr_acc'],
                    'debit' => 0,
                    'credit' => $entry['amount'],
                    'description' => 'Accumulated Depreciation'
                ]);
            }
            
            DB::commit();
            return $journal;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function calculateAnnualDepreciation($asset)
    {
        $cost = $asset->purchase_cost;
        $salvage = $asset->salvage_value ?? 0;
        
        // Simple Straight Line
        if ($asset->category->useful_life_years > 0) {
            return ($cost - $salvage) / $asset->category->useful_life_years;
        }
        
        if ($asset->category->depreciation_rate > 0) {
            return ($cost - $salvage) * ($asset->category->depreciation_rate / 100);
        }
        
        return 0;
    }
}
