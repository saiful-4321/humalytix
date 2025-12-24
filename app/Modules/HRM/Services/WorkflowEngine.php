<?php

namespace App\Modules\HRM\Services;

use App\Modules\HRM\Models\Workflow;
use App\Modules\HRM\Models\WorkflowStep;
use App\Modules\HRM\Models\WorkflowRun;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomNotification;

class WorkflowEngine
{
    /**
     * Process a workflow trigger for a given item.
     * Finds matching workflow and initiates the first step.
     */
    public function process(Model $item, string $event)
    {
        // 1. Determine module type based on model class
        $moduleType = $this->getModuleType($item);

        if (!$moduleType) {
            Log::warning("WorkflowEngine: Unknown module type for " . get_class($item));
            return;
        }

        // 2. Find active workflow for this module and event
        $workflow = Workflow::where('module_type', $moduleType)
            ->where('trigger_event', $event)
            ->where('is_active', true)
            ->first();

        if (!$workflow) {
            Log::info("WorkflowEngine: No workflow found for module: $moduleType, event: $event");
            // No workflow defined, auto-approve
            $item->update(['status' => 'approved']);
            return;
        }

        // 3. Create workflow run to track execution
        $workflowRun = WorkflowRun::create([
            'workflow_id' => $workflow->id,
            'workflowable_type' => get_class($item),
            'workflowable_id' => $item->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        Log::info("WorkflowEngine: Starting workflow '{$workflow->name}' (Run #{$workflowRun->id}) for item {$item->id}");
        
        // 4. Start with the first step
        $firstStep = $workflow->steps()->orderBy('step_order')->first();

        if ($firstStep) {
            $this->executeStep($firstStep, $item, $workflowRun);
        } else {
            // No steps defined, complete immediately
            $this->finalizeWorkflow($item, $workflowRun, 'approved');
        }
    }

    /**
     * Execute logic for a specific step.
     */
    private function executeStep(WorkflowStep $step, Model $item, WorkflowRun $workflowRun)
    {
        // Update current step
        $workflowRun->update(['current_step_id' => $step->id]);

        Log::info("WorkflowEngine: Executing step '{$step->step_name}' (Order: {$step->step_order})");

        // Check conditions
        if ($this->shouldSkipStep($step, $item)) {
            Log::info("WorkflowEngine: Skipping step '{$step->step_name}' due to conditions");
            $workflowRun->addStepToHistory($step->id, 'skipped', auth()->id() ?? 1, 'Conditions not met');
            
            // Move to next step
            $nextStep = $this->getNextStep($step);
            if ($nextStep) {
                $this->executeStep($nextStep, $item, $workflowRun);
            } else {
                // Workflow complete
                $this->finalizeWorkflow($item, $workflowRun, 'approved');
            }
            return;
        }

        // Check auto-action
        if ($step->auto_action === 'approve') {
            Log::info("WorkflowEngine: Auto-approving step '{$step->step_name}'");
            $workflowRun->addStepToHistory($step->id, 'approved', 1, 'Auto-approved');
            
            // Move to next step
            $nextStep = $this->getNextStep($step);
            if ($nextStep) {
                $this->executeStep($nextStep, $item, $workflowRun);
            } else {
                // Workflow complete
                $this->finalizeWorkflow($item, $workflowRun, 'approved');
            }
        } elseif ($step->auto_action === 'reject') {
            Log::info("WorkflowEngine: Auto-rejecting step '{$step->step_name}'");
            $workflowRun->addStepToHistory($step->id, 'rejected', 1, 'Auto-rejected');
            $this->finalizeWorkflow($item, $workflowRun, 'rejected');
        } else {
            // Manual approval required: Send Notification
            Log::info("WorkflowEngine: Manual approval required for step '{$step->step_name}'");
            $this->notifyApprover($step, $item, $workflowRun);
        }
    }

    /**
     * Progress workflow after manual approval/rejection
     */
    public function progressWorkflow(Model $item, string $action)
    {
        // Find active workflow run
        $workflowRun = WorkflowRun::where('workflowable_type', get_class($item))
            ->where('workflowable_id', $item->id)
            ->where('status', 'in_progress')
            ->first();

        if (!$workflowRun || !$workflowRun->current_step_id) {
            Log::warning("WorkflowEngine: No active workflow run found for item {$item->id}");
            return;
        }

        $currentStep = $workflowRun->currentStep;
        
        // Record the action
        $workflowRun->addStepToHistory(
            $currentStep->id,
            $action,
            auth()->id() ?? 1,
            "Manual {$action}"
        );

        if ($action === 'approved') {
            // Move to next step
            $nextStep = $this->getNextStep($currentStep);
            if ($nextStep) {
                $this->executeStep($nextStep, $item, $workflowRun);
            } else {
                // Workflow complete
                $this->finalizeWorkflow($item, $workflowRun, 'approved');
            }
        } else {
            // Rejected - end workflow
            $this->finalizeWorkflow($item, $workflowRun, 'rejected');
        }
    }

    /**
     * Cancel workflow
     */
    public function cancelWorkflow(Model $item)
    {
        $workflowRun = WorkflowRun::where('workflowable_type', get_class($item))
            ->where('workflowable_id', $item->id)
            ->where('status', 'in_progress')
            ->first();

        if ($workflowRun) {
            $workflowRun->update([
                'status' => 'cancelled',
                'completed_at' => now(),
            ]);
            Log::info("WorkflowEngine: Workflow run #{$workflowRun->id} cancelled");
        }
    }

    /**
     * Check if step should be skipped based on conditions
     */
    private function shouldSkipStep(WorkflowStep $step, Model $item)
    {
        if (empty($step->conditions)) {
            return false;
        }

        $conditions = $step->conditions;
        
        // Example condition checks
        if (isset($conditions['min_amount']) && isset($item->amount)) {
            if ($item->amount < $conditions['min_amount']) {
                return true;
            }
        }

        if (isset($conditions['max_amount']) && isset($item->amount)) {
            if ($item->amount > $conditions['max_amount']) {
                return true;
            }
        }

        if (isset($conditions['leave_type']) && isset($item->leave_type_id)) {
            if ($item->leave_type_id != $conditions['leave_type']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get next step in workflow
     */
    private function getNextStep(WorkflowStep $currentStep)
    {
        return $currentStep->workflow->steps()
            ->where('step_order', '>', $currentStep->step_order)
            ->orderBy('step_order')
            ->first();
    }

    /**
     * Finalize workflow
     */
    private function finalizeWorkflow(Model $item, WorkflowRun $workflowRun, string $status)
    {
        Log::info("WorkflowEngine: Finalizing workflow run #{$workflowRun->id} with status: {$status}");
        
        // Update workflow run
        $workflowRun->complete($status);

        // Update item status
        $item->update(['status' => $status]);

        // Send completion notification
        if ($item->employee && $item->employee->user) {
            $item->employee->user->notify(new CustomNotification([
                'type' => $this->getModuleType($item),
                'title' => ucfirst($status) . ' - ' . class_basename($item),
                'message' => "Your " . strtolower(class_basename($item)) . " has been {$status}.",
                'action_url' => '#',
            ]));
        }
    }

    /**
     * Notify approver for manual approval
     */
    private function notifyApprover(WorkflowStep $step, Model $item, WorkflowRun $workflowRun)
    {
        // Resolve approver based on type
        $approver = $this->resolveApprover($step, $item);

        if (!$approver) {
            Log::warning("WorkflowEngine: Could not resolve approver for step '{$step->step_name}'");
            // Auto-approve if no approver found
            $workflowRun->addStepToHistory($step->id, 'approved', 1, 'No approver found - auto-approved');
            $nextStep = $this->getNextStep($step);
            if ($nextStep) {
                $this->executeStep($nextStep, $item, $workflowRun);
            } else {
                $this->finalizeWorkflow($item, $workflowRun, 'approved');
            }
            return;
        }

        // Send notification
        $approver->notify(new CustomNotification([
            'type' => 'approval',
            'title' => 'Approval Required - ' . $step->step_name,
            'message' => "You have a pending " . class_basename($item) . " approval from " . ($item->employee->full_name ?? 'an employee'),
            'action_url' => '#', // TODO: Add proper URL
        ]));

        Log::info("WorkflowEngine: Notification sent to approver (User #{$approver->id}) for step '{$step->step_name}'");
    }

    /**
     * Resolve approver based on approver type
     */
    private function resolveApprover(WorkflowStep $step, Model $item)
    {
        switch ($step->approver_type) {
            case 'reporting_manager':
                return $item->employee->reportingManager->user ?? null;
            
            case 'hr_manager':
                return \App\Models\User::role('HR Manager')->first();
            
            case 'finance_manager':
                return \App\Models\User::role('Finance Manager')->first();
            
            case 'specific_user':
                return \App\Models\User::find($step->approver_id);
            
            case 'specific_role':
                return \App\Models\User::role($step->approver_role)->first();
            
            default:
                return null;
        }
    }

    /**
     * Get module type from model
     */
    private function getModuleType(Model $item)
    {
        $class = get_class($item);
        if (str_contains($class, 'Leave')) return 'leave';
        if (str_contains($class, 'Expense')) return 'expense';
        if (str_contains($class, 'Loan')) return 'loan';
        if (str_contains($class, 'Advance')) return 'advance';
        return null;
    }
}
