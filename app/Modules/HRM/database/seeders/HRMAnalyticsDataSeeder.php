<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\HRM\Models\{
    Employee,
    Department,
    Job,
    Candidate,
    Attendance,
    Leave,
    LeaveType,
    Payroll,
    PerformanceGoal,
    Kpi
};
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HRMAnalyticsDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting Comprehensive HRM Analytics Seeding...');

        $admin = User::first();
        if (!$admin) {
            $this->command->error('No admin user found. Please run UserSeeder first.');
            return;
        }

        $employees = Employee::all();
        if ($employees->isEmpty()) {
            $this->command->info('No employees found. Running HRMDemoDataSeeder first...');
            $this->call(HRMDemoDataSeeder::class);
            $employees = Employee::all();
        }

        $departments = Department::all();
        $leaveTypes = LeaveType::all();
        if ($leaveTypes->isEmpty()) {
            $this->call(CompleteLeaveSeeder::class);
            $leaveTypes = LeaveType::all();
        }

        // 1. Update Employee Joining Dates & Birth Dates for Diversity
        $this->command->info('Diversifying Employee dates...');
        foreach ($employees as $index => $employee) {
            $employee->update([
                'joining_date' => now()->subYears(rand(0, 12))->subMonths(rand(0, 11))->subDays(rand(0, 28)),
                'date_of_birth' => now()->subYears(rand(22, 55))->subMonths(rand(0, 11)),
                'gender' => $index % 2 == 0 ? 'male' : 'female',
            ]);
        }

        // 2. Recruitment Data (Jobs & Candidates)
        $this->command->info('Seeding Recruitment data...');
        $jobTitles = ['Senior Dev', 'HR Manager', 'Sales Lead', 'QA Engineer', 'UI/UX Designer'];
        foreach ($jobTitles as $title) {
            $job = Job::firstOrCreate(
                ['title' => $title],
                [
                    'department_id' => $departments->random()->id,
                    'status' => 'active',
                    'is_published' => true,
                    'vacancies' => rand(1, 5),
                    'posted_date' => now()->subDays(rand(5, 30)),
                    'created_by' => $admin->id
                ]
            );

            $stages = ['applied', 'screening', 'interview', 'offer', 'hired', 'rejected'];
            for ($i = 0; $i < 15; $i++) {
                Candidate::create([
                    'job_id' => $job->id,
                    'first_name' => 'Candidate',
                    'last_name' => '#' . rand(1000, 9999),
                    'email' => 'cand' . rand(1, 100000) . '@example.com',
                    'phone' => '017' . rand(10000000, 99999999),
                    'stage' => $stages[array_rand($stages)],
                    'status' => 'active',
                    'applied_date' => now()->subDays(rand(1, 45)),
                    'created_by' => $admin->id
                ]);
            }
        }

        // 3. Attendance Data (Last 30 Days)
        $this->command->info('Seeding Attendance records...');
        $statuses = ['present', 'present', 'present', 'late_in', 'absent'];
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i);
            if ($date->isWeekend()) continue;

            foreach ($employees as $employee) {
                $status = $statuses[array_rand($statuses)];
                $checkIn = null;
                $checkOut = null;
                $workingHours = 0;

                if ($status !== 'absent') {
                    $checkIn = (clone $date)->setTime(rand(8, 11), rand(0, 59));
                    $checkOut = (clone $checkIn)->addHours(rand(7, 10));
                    $workingHours = $checkIn->diffInMinutes($checkOut) / 60;
                }

                Attendance::updateOrCreate(
                    ['employee_id' => $employee->id, 'date' => $date->toDateString()],
                    [
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'status' => $status,
                        'working_hours' => $workingHours,
                        'created_by' => $admin->id
                    ]
                );
            }
        }

        // 4. Payroll Data (Last 4 Months)
        $this->command->info('Seeding Payroll records...');
        for ($i = 4; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            foreach ($employees as $employee) {
                Payroll::updateOrCreate(
                    [
                        'employee_id' => $employee->id, 
                        'month' => $date->month, 
                        'year' => $date->year
                    ],
                    [
                        'basic_salary' => $employee->basic_salary ?? 50000,
                        'net_salary' => ($employee->basic_salary ?? 50000) * 1.1,
                        'status' => 'paid',
                        'payment_date' => (clone $date)->startOfMonth()->addDays(5),
                        'created_by' => $admin->id
                    ]
                );
            }
        }

        // 5. Leave Data (Current Year)
        $this->command->info('Seeding Leave records...');
        foreach ($employees as $employee) {
            for ($i = 0; $i < 3; $i++) {
                $leaveDate = now()->subMonths(rand(0, 11))->subDays(rand(0, 28));
                $days = rand(1, 5);
                Leave::create([
                    'employee_id' => $employee->id,
                    'leave_type_id' => $leaveTypes->random()->id,
                    'start_date' => $leaveDate->toDateString(),
                    'end_date' => (clone $leaveDate)->addDays($days - 1)->toDateString(),
                    'days' => $days,
                    'status' => 'approved',
                    'approved_by' => $admin->id,
                    'created_by' => $admin->id
                ]);
            }
        }

        // 6. Performance Goals
        $this->command->info('Seeding Performance Goals...');
        $goalTitles = ['Improve Efficiency', 'Learn New Stack', 'Client Satisfaction', 'Speed Up Delivery', 'Team Collaboration'];
        foreach ($employees as $employee) {
            foreach (array_rand(array_flip($goalTitles), 3) as $title) {
                PerformanceGoal::create([
                    'employee_id' => $employee->id,
                    'title' => $title,
                    'progress' => rand(10, 100),
                    'status' => rand(0, 1) ? 'in_progress' : 'completed',
                    'start_date' => now()->subMonths(rand(1, 3)),
                    'due_date' => now()->addMonths(rand(1, 6)),
                    'created_by' => $admin->id
                ]);
            }
        }

        $this->command->info('Comprehensive HRM Analytics Seeding Completed!');
    }
}
