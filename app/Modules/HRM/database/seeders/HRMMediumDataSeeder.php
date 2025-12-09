<?php

namespace App\Modules\HRM\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Modules\HRM\Models\Department;
use App\Modules\HRM\Models\Branch;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\Job;
use App\Modules\HRM\Models\Candidate;
use App\Modules\HRM\Models\Asset;
use App\Modules\HRM\Models\AssetAssignment;
use App\Modules\HRM\Models\Shift;
use App\Modules\HRM\Models\Roster;
use App\Modules\HRM\Models\LeaveType;
use App\Modules\HRM\Models\Leave;
use App\Modules\HRM\Models\Attendance;
use Carbon\Carbon;

class HRMMediumDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // 1. Branches (Ensure at least 5)
        $dhaka = Branch::firstOrCreate(['name' => 'Dhaka HQ'], ['code' => 'BR-001', 'is_active' => true]);
        $branches = [$dhaka];
        foreach(['Chittagong', 'Sylhet', 'Khulna', 'Rajshahi'] as $city) {
            $branches[] = Branch::firstOrCreate(
                ['name' => "$city Branch"],
                [
                    'code' => 'BR-' . strtoupper(substr($city, 0, 3)),
                    'city' => $city,
                    'country' => 'Bangladesh',
                    'is_active' => true
                ]
            );
        }

        // 2. Departments
        $depts = [];
        $deptNames = ['HR', 'Finance', 'IT', 'Marketing', 'Sales', 'Operations', 'Legal', 'R&D'];
        foreach ($deptNames as $name) {
            $depts[] = Department::firstOrCreate(
                ['name' => $name],
                [
                    'code' => strtoupper($name),
                    'is_active' => true
                ]
            );
        }

        // 3. Employees (Create 50)
        $employees = [];
        // Get existing or create new
        $existingCount = Employee::count();
        $targetCount = 50;
        
        // If we already have 50+, just fetch them
        if ($existingCount >= $targetCount) {
            $employees = Employee::limit($targetCount)->get();
        } else {
            // Fetch existing to fill array
            $employees = Employee::all()->all();
            
            for ($i = $existingCount; $i < $targetCount; $i++) {
                $email = $faker->unique()->userName . '@example.com';
                $employees[] = Employee::create([
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'email' => $email,
                    'phone' => $faker->phoneNumber,
                    'date_of_birth' => $faker->date('Y-m-d', '-20 years'),
                    'gender' => $faker->randomElement(['male', 'female']),
                    'present_address' => $faker->address,
                    'employee_code' => 'EMP-' . str_pad($i + 100, 4, '0', STR_PAD_LEFT), // Start from 100 to avoid conflicts
                    'department_id' => $faker->randomElement($depts)->id,
                    'designation' => $faker->randomElement(['Executive', 'Manager', 'Senior Officer', 'Developer', 'Analyst']),
                    'joining_date' => $faker->date('Y-m-d', '-2 years'),
                    'basic_salary' => $faker->numberBetween(30000, 150000),
                    'status' => 'active',
                    'user_id' => 1,
                ]);
            }
        }

        // 4. Jobs & Candidates
        $jobs = [];
        foreach(['Senior Laravel Developer', 'HR Manager', 'Sales Executive', 'Accountant'] as $title) {
            $jobs[] = Job::firstOrCreate(
                ['title' => $title],
                [
                    'code' => 'JOB-' . rand(1000, 9999),
                    'department_id' => $faker->randomElement($depts)->id,
                    'vacancies' => rand(1, 5),
                    'status' => 'active', // Should align with 'open' or 'active' pending check
                    'is_published' => true,
                    'description' => $faker->paragraph,
                    'employment_type' => 'full_time',
                    'posted_date' => now(),
                ]
            );
        }

        foreach ($jobs as $job) {
            // Check count to prevent infinite loop or over-seeding
            if ($job->candidates()->count() < 5) {
                for ($k = 0; $k < 5; $k++) {
                    // Try to avoid duplicate emails
                    $email = $faker->unique()->email;
                    if (!Candidate::where('email', $email)->exists()) {
                        Candidate::create([
                            'job_id' => $job->id,
                            'first_name' => $faker->firstName,
                            'last_name' => $faker->lastName,
                            'email' => $email,
                            'phone' => $faker->phoneNumber,
                            'status' => $faker->randomElement(['applied', 'screening', 'interview', 'rejected']),
                            'applied_date' => $faker->dateTimeBetween('-1 month', 'now'),
                        ]);
                    }
                }
            }
        }

        // 5. Assets
        for ($a = 0; $a < 30; $a++) {
            $code = 'AST-' . ($a + 1000);
            $asset = Asset::firstOrCreate(
                ['code' => $code],
                [
                    'name' => $faker->randomElement(['MacBook Pro', 'Dell Latitude', 'iPhone 13', 'Office Chair']),
                    'type' => $faker->randomElement(['laptop', 'mobile', 'furniture']),
                    'status' => 'available',
                    'condition' => 'good'
                ]
            );
            
            // Assign some
            if ($a < 20 && isset($employees[$a]) && !$asset->currentAssignment) {
                AssetAssignment::create([
                    'asset_id' => $asset->id,
                    'employee_id' => $employees[$a]->id,
                    'assigned_date' => now()->subMonths(rand(1, 6)),
                    'assigned_condition' => 'good',
                    'assigned_by' => 1
                ]);
                $asset->update(['status' => 'assigned']);
            }
        }

        // 6. Shifts & Rosters
        $morning = Shift::firstOrCreate(
            ['name' => 'Morning'],
            ['code' => 'S-MORN', 'start_time' => '09:00', 'end_time' => '17:00']
        );
        $evening = Shift::firstOrCreate(
            ['name' => 'Evening'],
            ['code' => 'S-EVE', 'start_time' => '16:00', 'end_time' => '00:00']
        );

        $startDate = Carbon::now()->startOfMonth(); 
        $endDate = Carbon::now()->endOfMonth();
        
        foreach ($employees as $emp) {
            // Skip if roster already exists
            if (Roster::where('employee_id', $emp->id)->whereBetween('date', [$startDate, $endDate])->exists()) {
                continue;
            }

            $shift = $faker->randomElement([$morning, $evening]);
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                if ($date->isWeekend()) continue;
                Roster::create([
                    'employee_id' => $emp->id,
                    'shift_id' => $shift->id,
                    'date' => $date->format('Y-m-d')
                ]);
                
                // 7. Attendance (Simulate 90% attendance)
                if ($date->lte(now()) && rand(1, 10) > 1) {
                    Attendance::updateOrCreate([
                        'employee_id' => $emp->id,
                        'date' => $date->format('Y-m-d'),
                    ], [
                        'check_in' => $shift->start_time,
                        'check_out' => $shift->end_time,
                        'status' => 'present',
                        'working_hours' => 8
                    ]);
                }
            }
        }

        // 8. Leaves
        $sick = LeaveType::firstOrCreate(['name' => 'Sick Leave', 'code' => 'SL', 'days_per_year' => 14]);
        
        foreach ($employees as $emp) {
            if (rand(0, 1)) {
                Leave::create([
                    'employee_id' => $emp->id,
                    'leave_type_id' => $sick->id,
                    'start_date' => now()->subDays(rand(10, 20)),
                    'end_date' => now()->subDays(rand(10, 20)),
                    'days' => 1,
                    'reason' => 'Fever',
                    'status' => 'approved'
                ]);
            }
        }
    }
}
