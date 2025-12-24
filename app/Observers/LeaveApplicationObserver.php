<?php

namespace App\Observers;

use App\Modules\HRM\Models\Leave;
use App\Modules\HRM\Services\WorkflowEngine;
use Illuminate\Support\Facades\Log;

class LeaveApplicationObserver
{
    protected $workflowEngine;

    public function __construct(WorkflowEngine $workflowEngine)
    {
        $this->workflowEngine = $workflowEngine;
    }

    /**
     * Handle the Leave "created" event.
     */
    public function created(Leave $leave): void
    {
        // Check if auto-trigger is enabled
        if (!\App\Modules\HRM\Models\AutomationSetting::shouldAutoTriggerWorkflow('leave')) {
            Log::info("LeaveApplicationObserver: Auto-trigger disabled for leave workflows");
            return;
        }

        // Only trigger workflow if status is pending
        if ($leave->status === 'pending') {
            Log::info("LeaveApplicationObserver: Triggering workflow for leave #{$leave->id}");
            $this->workflowEngine->process($leave, 'created');
        }
    }

    /**
     * Handle the Leave "updated" event.
     */
    public function updated(Leave $leave): void
    {
        // Check if status changed to approved or rejected
        if ($leave->wasChanged('status')) {
            $oldStatus = $leave->getOriginal('status');
            $newStatus = $leave->status;

            Log::info("LeaveApplicationObserver: Status changed from {$oldStatus} to {$newStatus} for leave #{$leave->id}");

            // Progress workflow if needed
            if (in_array($newStatus, ['approved', 'rejected'])) {
                $this->workflowEngine->progressWorkflow($leave, $newStatus);
            }
        }
    }

    /**
     * Handle the Leave "deleted" event.
     */
    public function deleted(Leave $leave): void
    {
        // Cancel any active workflows
        $this->workflowEngine->cancelWorkflow($leave);
    }
}
