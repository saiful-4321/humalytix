<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\EmployeeDocument;
use App\Notifications\CustomNotification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckExpiringDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrm:check-expiring-documents {--dry-run : Run without sending notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expiring employee documents and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if document expiry check is enabled
        if (!\App\Modules\HRM\Models\AutomationSetting::shouldCheckExpiringDocuments()) {
            $this->info('Document expiry check is disabled in settings');
            return Command::SUCCESS;
        }

        $dryRun = $this->option('dry-run');
        
        $this->info("Checking for expiring documents" . ($dryRun ? ' (DRY RUN)' : ''));

        $thresholds = [30, 15, 7]; // Days before expiry
        $totalNotifications = 0;

        foreach ($thresholds as $days) {
            $targetDate = now()->addDays($days)->toDateString();
            
            // Use EmployeeDocument model with relationships
            $expiringDocuments = EmployeeDocument::with(['employee.user', 'documentType'])
                ->whereDate('expiry_date', $targetDate)
                ->whereHas('employee', function($query) {
                    $query->whereNull('deleted_at');
                })
                ->get();

            foreach ($expiringDocuments as $document) {
                if (!$document->employee || !$document->employee->user) {
                    continue;
                }

                $employee = $document->employee;
                $employeeName = trim($employee->first_name . ' ' . $employee->last_name);
                $docType = $document->documentType ? $document->documentType->name : 'Document';
                $message = "Your {$docType} will expire in {$days} days. Please renew it.";

                if (!$dryRun) {
                    $employee->user->notify(new CustomNotification([
                        'type' => 'document',
                        'title' => 'Document Expiring Soon',
                        'message' => $message,
                        'action_url' => route('hrm.employees.show', $employee->id),
                    ]));
                    $totalNotifications++;
                    $this->line("✓ Notified {$employeeName} about {$docType} expiring in {$days} days");
                } else {
                    $this->line("[DRY RUN] Would notify {$employeeName} about {$docType} expiring in {$days} days");
                    $totalNotifications++;
                }
            }
        }

        $this->info("\nDocument Expiry Check Summary:");
        $this->info("- Notifications sent: {$totalNotifications}");

        if (!$dryRun && $totalNotifications > 0) {
            // Send summary to HR
            $hrUsers = \App\Models\User::role('hr_manager')->get();
            foreach ($hrUsers as $hrUser) {
                $hrUser->notify(new CustomNotification([
                    'type' => 'info',
                    'title' => 'Document Expiry Check Complete',
                    'message' => "{$totalNotifications} employees notified about expiring documents.",
                    'action_url' => route('hrm.employees.index'),
                ]));
            }

            Log::info("CheckExpiringDocuments: {$totalNotifications} notifications sent");
        }

        return Command::SUCCESS;
    }
}
