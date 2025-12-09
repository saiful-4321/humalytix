<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\HRM\Models\LeaveType;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\LeaveAllocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccrueLeaves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrm:accrue-leaves {--force : Force run regardless of date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Accrue leave balances for employees based on leave type configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $isFirstDayOfMonth = $today->day === 1;
        $isFirstDayOfQuarter = $isFirstDayOfMonth && in_array($today->month, [1, 4, 7, 10]);
        $isFirstDayOfYear = $today->day === 1 && $today->month === 1;

        $this->info("Running Leave Accrual for date: " . $today->toDateString());

        $leaveTypes = LeaveType::active()->whereNotNull('accrual_frequency')->get();

        foreach ($leaveTypes as $type) {
            $shouldRun = false;
            switch(strtolower($type->accrual_frequency)) {
                case 'monthly':
                    $shouldRun = $isFirstDayOfMonth;
                    break;
                case 'quarterly':
                    $shouldRun = $isFirstDayOfQuarter;
                    break;
                case 'yearly':
                    $shouldRun = $isFirstDayOfYear;
                    break;
            }

            if ($shouldRun || $this->option('force') ?? false) {
                $this->info("Accruing {$type->name} ({$type->accrual_frequency}). Rate: {$type->accrual_rate}");
                
                $employees = Employee::active()->get();
                $count = 0;

                DB::transaction(function() use ($employees, $type, $today, &$count) {
                    foreach($employees as $employee) {
                        $allocation = LeaveAllocation::firstOrCreate(
                            [
                                'employee_id' => $employee->id,
                                'leave_type_id' => $type->id,
                                'year' => $today->year
                            ],
                            [
                                'allocated_days' => 0,
                                'used_days' => 0,
                                'carried_over_days' => 0
                            ]
                        );

                        // Incremental Accrual
                        $allocation->increment('allocated_days', $type->accrual_rate);
                        $count++;
                    }
                });
                
                $this->info("Processed $count employees for {$type->name}.");
            } else {
                $this->comment("Skipping {$type->name} ({$type->accrual_frequency}) - Not due today.");
            }
        }

        $this->info('Leave accrual process completed.');
    }
}
