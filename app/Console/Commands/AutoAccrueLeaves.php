<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\LeaveType;
use App\Modules\HRM\Models\LeaveAllocation;
use App\Notifications\CustomNotification;
use Illuminate\Support\Facades\Log;

class AutoAccrueLeaves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrm:auto-accrue-leaves {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically accrue leaves for all employees based on their leave policies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if auto-accrual is enabled
        if (!\App\Modules\HRM\Models\AutomationSetting::shouldAutoAccrueLeaves()) {
            $this->info('Auto leave accrual is disabled in settings');
            return Command::SUCCESS;
        }

        $dryRun = $this->option('dry-run');
        $year = now()->year;
        
        $this->info("Starting auto leave accrual for year {$year}" . ($dryRun ? ' (DRY RUN)' : ''));

        $employees = Employee::active()->get();
        $accrued = 0;
        $skipped = 0;

        foreach ($employees as $employee) {
            // Get leave types for accrual
            $leaveTypes = LeaveType::where('is_active', true)->get();

            foreach ($leaveTypes as $leaveType) {
                // Check if allocation already exists
                $exists = LeaveAllocation::where('employee_id', $employee->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->where('year', $year)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Use default days from leave type (you may want to customize this)
                $daysToAllocate = $leaveType->default_days ?? 20;

                if (!$dryRun) {
                    // Create allocation
                    LeaveAllocation::create([
                        'employee_id' => $employee->id,
                        'leave_type_id' => $leaveType->id,
                        'year' => $year,
                        'total_days' => $daysToAllocate,
                        'used_days' => 0,
                        'remaining_days' => $daysToAllocate,
                    ]);

                    $accrued++;
                    $this->line("✓ Accrued {$daysToAllocate} days of {$leaveType->name} for {$employee->full_name}");
                } else {
                    $this->line("[DRY RUN] Would accrue {$daysToAllocate} days of {$leaveType->name} for {$employee->full_name}");
                    $accrued++;
                }
            }
        }

        $this->info("\nAccrual Summary:");
        $this->info("- Accrued: {$accrued}");
        $this->info("- Skipped (already exists): {$skipped}");

        if (!$dryRun) {
            // Send summary notification to HR
            $hrUsers = \App\Models\User::role('HR Manager')->get();
            foreach ($hrUsers as $hrUser) {
                $hrUser->notify(new CustomNotification([
                    'type' => 'leave',
                    'title' => 'Monthly Leave Accrual Complete',
                    'message' => "Leave accrual for {$year} completed. {$accrued} allocations created, {$skipped} skipped.",
                    'action_url' => route('hrm.leaves.allocations.index'),
                ]));
            }

            Log::info("AutoAccrueLeaves: Completed. Accrued: {$accrued}, Skipped: {$skipped}");
        }

        return Command::SUCCESS;
    }
}
