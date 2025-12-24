<?php

namespace App\Observers;

use App\Modules\HRM\Models\Expense;
use App\Modules\HRM\Services\WorkflowEngine;
use Illuminate\Support\Facades\Log;

class ExpenseObserver
{
    protected $workflowEngine;

    public function __construct(WorkflowEngine $workflowEngine)
    {
        $this->workflowEngine = $workflowEngine;
    }

    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        // Check if auto-trigger is enabled
        if (!\App\Modules\HRM\Models\AutomationSetting::shouldAutoTriggerWorkflow('expense')) {
            Log::info("ExpenseObserver: Auto-trigger disabled for expense workflows");
            return;
        }

        // Only trigger workflow if status is pending
        if ($expense->status === 'pending') {
            Log::info("ExpenseObserver: Triggering workflow for expense #{$expense->id}");
            $this->workflowEngine->process($expense, 'created');
        }
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        // Check if status changed to approved or rejected
        if ($expense->wasChanged('status')) {
            $oldStatus = $expense->getOriginal('status');
            $newStatus = $expense->status;

            Log::info("ExpenseObserver: Status changed from {$oldStatus} to {$newStatus} for expense #{$expense->id}");

            // Progress workflow if needed
            if (in_array($newStatus, ['approved', 'rejected'])) {
                $this->workflowEngine->progressWorkflow($expense, $newStatus);
            }
        }
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        // Cancel any active workflows
        $this->workflowEngine->cancelWorkflow($expense);
    }
}
