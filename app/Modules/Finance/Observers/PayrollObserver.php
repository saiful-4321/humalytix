<?php

namespace App\Modules\Finance\Observers;

use App\Modules\HRM\Models\Payroll;
use App\Modules\Finance\Models\Journal;
use App\Modules\Finance\Models\JournalEntry; // Assuming this model exists
use App\Modules\Finance\Models\PayrollMapping;
use App\Modules\Finance\Models\FiscalYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PayrollObserver
{
    public function created(Payroll $payroll)
    {
        // Accrual Logic: When Payroll is generated (Pending)
        // Dr Salary Expense (Component wise), Cr Salary Payable (Global or Specific)
        
        $this->createJournalEntry($payroll, 'accrual');
    }

    public function updated(Payroll $payroll)
    {
        // Payment Logic: When Payroll is Paid
        // Dr Salary Payable, Cr Cash/Bank
        
        if ($payroll->isDirty('status') && $payroll->status == 'paid') {
            $this->createJournalEntry($payroll, 'payment');
        }
    }

    protected function createJournalEntry(Payroll $payroll, $type)
    {
        // Check for active Fiscal Year
        $fiscalYear = FiscalYear::where('is_current', true)->first();
        if (!$fiscalYear) {
            Log::warning("No active fiscal year found. Cannot post payroll journal for Payroll ID: {$payroll->id}");
            return;
        }

        $payroll->load('items');
        $journalEntries = [];

        if ($type === 'accrual') {
            // 1. Basic Salary
            $basicMapping = PayrollMapping::where('component_slug', 'basic_salary')->first();
            if ($basicMapping && $payroll->basic_salary > 0) {
                 $journalEntries[] = [
                     'account_id' => $basicMapping->debit_account_id,
                     'debit' => $payroll->basic_salary,
                     'credit' => 0
                 ];
                 $journalEntries[] = [
                     'account_id' => $basicMapping->credit_account_id,
                     'debit' => 0,
                     'credit' => $payroll->basic_salary
                 ];
            }

            // 2. Items (Allowances/Deductions)
            foreach ($payroll->items as $item) {
                // Try specific component mapping first
                $mapping = null;
                if ($item->salary_component_id) {
                    $mapping = PayrollMapping::where('salary_component_id', $item->salary_component_id)->first();
                }
                // Fallback to name/slug match
                if (!$mapping) {
                    $slug = \Illuminate\Support\Str::slug($item->component_name);
                    $mapping = PayrollMapping::where('component_slug', $slug)->orWhere('component_name', $item->component_name)->first();
                }
                
                if (!$mapping) continue;

                if ($item->type == 'earning') {
                    if ($mapping->debit_account_id) {
                        $journalEntries[] = ['account_id' => $mapping->debit_account_id, 'debit' => $item->amount, 'credit' => 0];
                    }
                    if ($mapping->credit_account_id) {
                         $journalEntries[] = ['account_id' => $mapping->credit_account_id, 'debit' => 0, 'credit' => $item->amount];
                    }
                } 
                elseif ($item->type == 'deduction') {
                    if ($mapping->debit_account_id) {
                         $journalEntries[] = ['account_id' => $mapping->debit_account_id, 'debit' => $item->amount, 'credit' => 0];
                    }
                    if ($mapping->credit_account_id) {
                        $journalEntries[] = ['account_id' => $mapping->credit_account_id, 'debit' => 0, 'credit' => $item->amount];
                    }
                }
            }
            
            // Tax
            if ($payroll->tax > 0) {
                 $taxMapping = PayrollMapping::where('component_slug', 'income_tax')->first();
                 if ($taxMapping) {
                      $journalEntries[] = ['account_id' => $taxMapping->debit_account_id, 'debit' => $payroll->tax, 'credit' => 0];
                      $journalEntries[] = ['account_id' => $taxMapping->credit_account_id, 'debit' => 0, 'credit' => $payroll->tax];
                 }
            }

        } elseif ($type === 'payment') {
            $netSalaryMapping = PayrollMapping::where('component_slug', 'net_salary_payable')->first();
            if ($netSalaryMapping && $payroll->net_salary > 0) {
                $journalEntries[] = ['account_id' => $netSalaryMapping->debit_account_id, 'debit' => $payroll->net_salary, 'credit' => 0];
                $journalEntries[] = ['account_id' => $netSalaryMapping->credit_account_id, 'debit' => 0, 'credit' => $payroll->net_salary];
            }
        }

        if (empty($journalEntries)) return;

        // Create Journal
        $journal = Journal::create([
            'date' => now(), 
            'reference' => 'PAYROLL-' . $payroll->month . '-' . $payroll->year . '-' . $payroll->employee->employee_code,
            'description' => ($type == 'accrual' ? 'Payroll Accrual' : 'Payroll Payment') . " for {$payroll->employee->full_name} ({$payroll->month}/{$payroll->year})",
            'fiscal_year_id' => $fiscalYear->id,
            'status' => 'posted', 
            'type' => 'payroll', // Set Type
            'created_by' => auth()->id() ?? 1,
            'posted_by' => auth()->id() ?? 1,
            'posted_at' => now(),
        ]);

        foreach ($journalEntries as $entry) {
            $journal->entries()->create([
                'account_id' => $entry['account_id'],
                'debit' => $entry['debit'],
                'credit' => $entry['credit'],
            ]);
            
            // Update Account Balance (Logic usually in JournalController or Service, duplicating here for simplicity or call controller method?)
            // Best to use the model's update logic if available.
            // But JournalController::post does it.
            // Since we set status posted, we should update current_balances.
            
            $account = \App\Modules\Finance\Models\ChartOfAccount::find($entry['account_id']);
            if ($account) {
                 if ($account->type->normal_balance == 'debit') {
                     $account->current_balance += ($entry['debit'] - $entry['credit']);
                 } else {
                     $account->current_balance += ($entry['credit'] - $entry['debit']);
                 }
                 $account->save();
            }
        }
    }
}
