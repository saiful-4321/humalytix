<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\HRM\Models\LeaveType;
use App\Modules\HRM\Models\Holiday;
use App\Modules\HRM\Models\LeaveAllocation;
use App\Modules\HRM\Models\Leave;
use App\Modules\HRM\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CompleteLeaveSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Seeding Industry Level Leave Management Data...');

        // 1. Seed Leave Types (Bangladesh Standard)
        $leaveTypes = [
            [
                'name' => 'Casual Leave',
                'code' => 'CL',
                'days_per_year' => 10,
                'is_paid' => true,
                'is_active' => true,
                'description' => 'For personal matters. Cannot be carried forward.',
                'accrual_frequency' => 'yearly',
                'max_carry_forward_days' => 0,
                'allow_encashment' => false,
                'require_approval' => true,
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SL',
                'days_per_year' => 14,
                'is_paid' => true,
                'is_active' => true,
                'description' => 'For medical reasons. Medical certificate required for > 2 days.',
                'accrual_frequency' => 'yearly',
                'max_carry_forward_days' => 0,
                'allow_encashment' => false,
                'require_approval' => true,
                'requires_attachment' => true,
            ],
            [
                'name' => 'Earned/Annual Leave',
                'code' => 'EL',
                'days_per_year' => 18, // Typically 1 day for every 18 days worked
                'is_paid' => true,
                'is_active' => true,
                'description' => 'Based on days worked. Can be carried forward or encashed.',
                'accrual_frequency' => 'yearly',
                'max_carry_forward_days' => 45, // Labor Law Maximum
                'allow_encashment' => true,
                'encashment_limit' => 15,
                'require_approval' => true,
                'probation_restricted' => true,
            ],
            [
                'name' => 'Maternity Leave',
                'code' => 'ML',
                'days_per_year' => 112, // 16 Weeks
                'is_paid' => true,
                'is_active' => true,
                'description' => 'For female employees. 8 weeks pre-natal, 8 weeks post-natal.',
                'accrual_frequency' => 'yearly',
                'max_carry_forward_days' => 0,
                'allow_encashment' => false,
                'require_approval' => true,
            ],
            [
                'name' => 'Paternity Leave',
                'code' => 'PL',
                'days_per_year' => 5,
                'is_paid' => true,
                'is_active' => true,
                'description' => 'For male employees upon birth of child.',
                'accrual_frequency' => 'yearly',
                'max_carry_forward_days' => 0,
                'allow_encashment' => false,
                'require_approval' => true,
            ],
            [
                'name' => 'Compensatory Off',
                'code' => 'CO',
                'days_per_year' => 0, // Accrued based on work
                'is_paid' => true,
                'is_active' => true,
                'description' => 'Granted for accurate working on weekends or holidays.',
                'accrual_frequency' => 'monthly',
                'max_carry_forward_days' => 10,
                'allow_encashment' => false,
                'require_approval' => true,
            ]
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::updateOrCreate(['code' => $type['code']], $type);
        }

        // 2. Seed Holidays (Bangladesh 2024-2025 Context)
        $years = [2024, 2025];
        $holidays2024 = [
            ['name' => 'International Mother Language Day', 'date' => '02-21', 'type' => 'national'],
            ['name' => 'Shab-e-Barat', 'date' => '02-26', 'type' => 'religious'],
            ['name' => 'Sheikh Mujibur Rahman Birthday', 'date' => '03-17', 'type' => 'national'],
            ['name' => 'Independence Day', 'date' => '03-26', 'type' => 'national'],
            ['name' => 'Jumatul Bidah', 'date' => '04-05', 'type' => 'religious'],
            ['name' => 'Eid-ul-Fitr', 'date' => '04-10', 'end_date' => '04-12', 'type' => 'religious'],
            ['name' => 'Pohela Boishakh', 'date' => '04-14', 'type' => 'cultural'],
            ['name' => 'May Day', 'date' => '05-01', 'type' => 'international'],
            ['name' => 'Buddha Purnima', 'date' => '05-22', 'type' => 'religious'],
            ['name' => 'Eid-ul-Adha', 'date' => '06-17', 'end_date' => '06-19', 'type' => 'religious'],
            ['name' => 'Ashura', 'date' => '07-17', 'type' => 'religious'],
            ['name' => 'National Mourning Day', 'date' => '08-15', 'type' => 'national'],
            ['name' => 'Janmashtami', 'date' => '08-26', 'type' => 'religious'],
            ['name' => 'Eid-e-Miladunnabi', 'date' => '09-16', 'type' => 'religious'],
            ['name' => 'Durga Puja (Dashami)', 'date' => '10-13', 'type' => 'religious'],
            ['name' => 'Victory Day', 'date' => '12-16', 'type' => 'national'],
            ['name' => 'Christmas Day', 'date' => '12-25', 'type' => 'religious'],
        ];

        foreach ($years as $year) {
            foreach ($holidays2024 as $holiday) {
                // Adjust date for year
                $startDate = Carbon::createFromFormat('Y-m-d', $year . '-' . $holiday['date']);
                
                // Simple logic: if religious/lunar, dates shift ~10 days/year back, but for demo simpler to keep approx
                // For industry level, we'd use exact calendars, but keeping it fixed for demo purposes on specific dates
                
                $endDate = isset($holiday['end_date']) 
                    ? Carbon::createFromFormat('Y-m-d', $year . '-' . $holiday['end_date']) 
                    : $startDate;

                Holiday::updateOrCreate(
                    [
                        'name' => $holiday['name'],
                        'start_date' => $startDate->format('Y-m-d'),
                    ],
                    [
                        'end_date' => $endDate->format('Y-m-d'),
                        'type' => $holiday['type'],
                        'is_recurring' => true,
                        'description' => $holiday['name'],
                        'is_active' => true,
                    ]
                );
            }
        }

        // 3. Allocations & History for Employees
        $employees = Employee::where('status', 'confirmed')->get();
        $clType = LeaveType::where('code', 'CL')->first();
        $slType = LeaveType::where('code', 'SL')->first();
        $elType = LeaveType::where('code', 'EL')->first();

        // Seed current year and next year
        $currentYear = date('Y');
        
        foreach ($employees as $employee) {
            // Allocate for 2024 & 2025
            foreach ([$currentYear, $currentYear + 1] as $yr) {
                foreach ([$clType, $slType, $elType] as $lType) {
                    if (!$lType) continue;

                    LeaveAllocation::updateOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'leave_type_id' => $lType->id,
                            'year' => $yr
                        ],
                        [
                            'allocated_days' => $lType->days_per_year,
                            'used_days' => 0, // Will update below
                            'carried_over_days' => 0, 
                        ]
                    );
                }
            }

            // Generate Fake Leave History for Current Year
            // 1. Some CLs taken
            $startOfYear = Carbon::create($currentYear, 1, 1);
            $today = Carbon::now();

            if ($clType) {
                // Approve 3 days of CL earlier in year
                $leaveDate = $startOfYear->copy()->addDays(rand(10, 50));
                
                // Check if leave already exists to avoid dupes
                $exists = Leave::where('employee_id', $employee->id)
                    ->where('start_date', $leaveDate->format('Y-m-d'))
                    ->exists();

                if (!$exists) {
                    $days = rand(1, 2);
                    $leave = Leave::create([
                        'employee_id' => $employee->id,
                        'leave_type_id' => $clType->id,
                        'start_date' => $leaveDate->format('Y-m-d'),
                        'end_date' => $leaveDate->copy()->addDays($days - 1)->format('Y-m-d'),
                        'days' => $days,
                        'reason' => 'Personal matter',
                        'status' => 'approved',
                        'approved_by' => 1, // Admin
                        'approved_at' => $leaveDate->copy()->subDays(1),
                    ]);

                    // Update alloc
                    $alloc = LeaveAllocation::where('employee_id', $employee->id)
                        ->where('leave_type_id', $clType->id)
                        ->where('year', $currentYear)
                        ->first();
                    $alloc->increment('used_days', $days);
                }
            }

            if ($slType && rand(0, 1)) {
                 // Random SL
                $leaveDate = $startOfYear->copy()->addDays(rand(60, 150));
                 $exists = Leave::where('employee_id', $employee->id)
                    ->where('start_date', $leaveDate->format('Y-m-d'))
                    ->exists();
                
                 if (!$exists) {
                    $days = rand(1, 3);
                    Leave::create([
                        'employee_id' => $employee->id,
                        'leave_type_id' => $slType->id,
                        'start_date' => $leaveDate->format('Y-m-d'),
                        'end_date' => $leaveDate->copy()->addDays($days - 1)->format('Y-m-d'),
                        'days' => $days,
                        'reason' => 'Viral fever',
                        'status' => 'approved',
                        'approved_by' => 1,
                        'approved_at' => $leaveDate->copy()->subDays(1),
                    ]);
                    
                    $alloc = LeaveAllocation::where('employee_id', $employee->id)
                        ->where('leave_type_id', $slType->id)
                        ->where('year', $currentYear)
                        ->first();
                    $alloc->increment('used_days', $days);
                 }
            }

            // Create some PENDING leave requests for near future
            if (rand(0, 5) == 0) { // 1 in 6 chance
                $futureDate = $today->copy()->addDays(rand(5, 30));
                 $exists = Leave::where('employee_id', $employee->id)
                    ->where('start_date', $futureDate->format('Y-m-d'))
                    ->exists();
                 
                  if (!$exists && $elType) {
                     Leave::create([
                        'employee_id' => $employee->id,
                        'leave_type_id' => $elType->id,
                        'start_date' => $futureDate->format('Y-m-d'),
                        'end_date' => $futureDate->copy()->addDays(2)->format('Y-m-d'),
                        'days' => 3,
                        'reason' => 'Family vacation',
                        'status' => 'pending',
                    ]);
                  }
            }

            // Half Day Leave Example
            if ($employee->id % 2 == 0 && $clType) { // For even ID employees
                $halfDayDate = $today->copy()->subDays(rand(5, 20));
                 $exists = Leave::where('employee_id', $employee->id)
                    ->where('start_date', $halfDayDate->format('Y-m-d'))
                    ->exists();
                 
                 if (!$exists) {
                    Leave::create([
                        'employee_id' => $employee->id,
                        'leave_type_id' => $clType->id,
                        'start_date' => $halfDayDate->format('Y-m-d'),
                        'end_date' => $halfDayDate->format('Y-m-d'),
                        'days' => 0.5,
                        'is_half_day' => true,
                        'half_day_session' => 'first_half',
                        'reason' => 'Doctor appointment',
                        'status' => 'approved',
                        'approved_by' => 1,
                        'approved_at' => $halfDayDate,
                    ]);
                    
                    // Increment usage
                     $alloc = LeaveAllocation::where('employee_id', $employee->id)
                        ->where('leave_type_id', $clType->id)
                        ->where('year', $currentYear)
                        ->first();
                    if ($alloc) $alloc->increment('used_days', 0.5);
                 }
            }
        }
    }
}
