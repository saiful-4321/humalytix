<?php

namespace App\Modules\Finance\Observers;

use App\Modules\HRM\Models\Expense;
use App\Modules\Finance\Models\Journal;
use App\Modules\Finance\Models\PayrollMapping;
use App\Modules\Finance\Models\FiscalYear;
use Illuminate\Support\Facades\Log;

class ExpenseObserver
{
    public function updated(Expense $expense)
    {
        // When Expense is Approved -> Create Journal (Accrual or Payment depending on workflow)
        // Usually: Dr Expense, Cr Payable (to Employee)
        // Later when paid (via Payroll or separate): Dr Payable, Cr Cash.
        
        // Check if status changed to 'approved'
        if ($expense->isDirty('status') && $expense->status === 'approved') {
            $this->createJournalEntry($expense);
        }
    }

    protected function createJournalEntry(Expense $expense)
    {
        $fiscalYear = FiscalYear::where('is_current', true)->first();
        if (!$fiscalYear) {
            Log::warning("No active fiscal year found. Cannot post expense journal for Expense ID: {$expense->id}");
            return;
        }

        // Get Category Name for Mapping
        $categoryName = $expense->category->name ?? 'General Expense';
        
        // Find Mapping
        $mapping = PayrollMapping::where('component_name', $categoryName)->first();
        
        // If specific mapping not found, try 'General Expense' or log error
        if (!$mapping) {
            $mapping = PayrollMapping::where('component_name', 'General Expense')->first();
        }

        if (!$mapping) {
            Log::warning("No mapping found for expense category: {$categoryName}");
            return;
        }

        // Journal Entry:
        // Dr Expense Account (from mapping debit)
        // Cr Employee Payable (from mapping credit) - or Cash if direct payment?
        // Let's assume Credit is 'Accounts Payable - Employees'
        
        if (!$mapping->debit_account_id || !$mapping->credit_account_id) {
             Log::warning("Incomplete mapping for expense category: {$categoryName}");
             return;
        }

        $journal = Journal::create([
            'date' => now(),
            'reference' => 'EXP-' . $expense->id,
            'description' => "Expense Claim: {$categoryName} by {$expense->employee->full_name}",
            'fiscal_year_id' => $fiscalYear->id,
            'status' => 'posted',
            'created_by' => auth()->id() ?? 1,
            'posted_by' => auth()->id() ?? 1,
            'posted_at' => now(),
        ]);

        // Debit Expense
        $journal->entries()->create([
            'account_id' => $mapping->debit_account_id,
            'debit' => $expense->amount,
            'credit' => 0,
        ]);

        // Credit Payable
        $journal->entries()->create([
            'account_id' => $mapping->credit_account_id,
            'debit' => 0,
            'credit' => $expense->amount,
        ]);

        // Update Balances
        $debitAccount = \App\Modules\Finance\Models\ChartOfAccount::find($mapping->debit_account_id);
        if ($debitAccount) {
            if ($debitAccount->type->normal_balance == 'debit') {
                 $debitAccount->current_balance += $expense->amount;
            } else {
                 $debitAccount->current_balance -= $expense->amount;
            }
            $debitAccount->save();
        }

        $creditAccount = \App\Modules\Finance\Models\ChartOfAccount::find($mapping->credit_account_id);
        if ($creditAccount) {
             if ($creditAccount->type->normal_balance == 'credit') {
                 $creditAccount->current_balance += $expense->amount;
             } else {
                 $creditAccount->current_balance -= $expense->amount;
             }
             $creditAccount->save();
        }
    }
}
