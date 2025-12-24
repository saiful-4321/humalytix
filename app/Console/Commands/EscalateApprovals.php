<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\HRM\Models\WorkflowRun;
use App\Notifications\CustomNotification;
use Illuminate\Support\Facades\Log;

class EscalateApprovals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrm:escalate-approvals {--dry-run : Run without sending notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Escalate pending approvals and send reminder notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if approval escalation is enabled
        if (!\App\Modules\HRM\Models\AutomationSetting::shouldEscalateApprovals()) {
            $this->info('Approval escalation is disabled in settings');
            return Command::SUCCESS;
        }

        $dryRun = $this->option('dry-run');
        
        $this->info("Checking for pending approvals to escalate" . ($dryRun ? ' (DRY RUN)' : ''));

        // Find workflow runs pending for more than 3 days
        $reminderThreshold = now()->subDays(3);
        $escalationThreshold = now()->subDays(7);

        $pendingRuns = WorkflowRun::where('status', 'in_progress')
            ->where('started_at', '<', $reminderThreshold)
            ->with(['workflow', 'currentStep', 'workflowable'])
            ->get();

        $reminders = 0;
        $escalations = 0;

        foreach ($pendingRuns as $run) {
            if (!$run->currentStep || !$run->workflowable) {
                continue;
            }

            $daysPending = now()->diffInDays($run->started_at);
            $item = $run->workflowable;

            // Determine if escalation or reminder
            if ($run->started_at < $escalationThreshold) {
                // Escalate to next level
                $this->line("⚠ Escalating approval for {$run->workflow->name} (pending {$daysPending} days)");
                
                if (!$dryRun) {
                    // TODO: Implement escalation logic (move to next approver)
                    $escalations++;
                }
            } else {
                // Send reminder
                $this->line("📧 Sending reminder for {$run->workflow->name} (pending {$daysPending} days)");
                
                if (!$dryRun) {
                    // Resolve current approver
                    $approver = $this->resolveApprover($run->currentStep, $item);
                    
                    if ($approver) {
                        $approver->notify(new CustomNotification([
                            'type' => 'approval',
                            'title' => 'Reminder: Pending Approval',
                            'message' => "You have a pending approval for {$run->workflow->name} (waiting {$daysPending} days).",
                            'action_url' => '#',
                        ]));
                        $reminders++;
                    }
                }
            }
        }

        $this->info("\nSummary:");
        $this->info("- Reminders sent: {$reminders}");
        $this->info("- Escalations: {$escalations}");

        if (!$dryRun && ($reminders > 0 || $escalations > 0)) {
            Log::info("EscalateApprovals: Reminders: {$reminders}, Escalations: {$escalations}");
        }

        return Command::SUCCESS;
    }

    /**
     * Resolve approver (simplified version)
     */
    private function resolveApprover($step, $item)
    {
        if (!$step) return null;

        switch ($step->approver_type) {
            case 'reporting_manager':
                return $item->employee->reportingManager->user ?? null;
            case 'hr_manager':
                return \App\Models\User::role('HR Manager')->first();
            case 'finance_manager':
                return \App\Models\User::role('Finance Manager')->first();
            default:
                return null;
        }
    }
}
