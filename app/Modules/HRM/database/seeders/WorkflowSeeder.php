<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\HRM\Models\Workflow;
use App\Modules\HRM\Models\WorkflowStep;

class WorkflowSeeder extends Seeder
{
    public function run()
    {
        // 1. Leave Approval Workflow
        $leaveWorkflow = Workflow::create([
            'name' => 'Standard Leave Approval',
            'module_type' => 'leave',
            'description' => 'Two-step approval for standard leaves.',
            'is_active' => true,
            'trigger_event' => 'created',
            'created_by' => 1,
        ]);

        WorkflowStep::create([
            'workflow_id' => $leaveWorkflow->id,
            'step_order' => 1,
            'step_name' => 'Reporting Manager Approval',
            'approver_type' => 'reporting_manager',
        ]);

        WorkflowStep::create([
            'workflow_id' => $leaveWorkflow->id,
            'step_order' => 2,
            'step_name' => 'HR Manager Approval',
            'approver_type' => 'hr_manager', // Assuming role based
             // Condition: Only if > 3 days
            'conditions' => ['min_days' => 3],
        ]);

        // 2. Expense Approval Workflow
        $expenseWorkflow = Workflow::create([
            'name' => 'Expense Claim Approval',
            'module_type' => 'expense',
            'description' => 'Approval for expense claims.',
            'is_active' => true,
            'trigger_event' => 'created',
            'created_by' => 1,
        ]);

        WorkflowStep::create([
            'workflow_id' => $expenseWorkflow->id,
            'step_order' => 1,
            'step_name' => 'Manager Verification',
            'approver_type' => 'reporting_manager',
        ]);
        
        WorkflowStep::create([
            'workflow_id' => $expenseWorkflow->id,
            'step_order' => 2,
            'step_name' => 'Finance Approval',
            'approver_type' => 'finance_manager', // TODO: Ensure this role exists or map correctly
        ]);
    }
}
