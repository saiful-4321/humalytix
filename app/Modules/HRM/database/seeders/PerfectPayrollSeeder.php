<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\HRM\Models\SalaryStructure;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\EmployeeSalary;
use App\Modules\HRM\Models\EmployeeOvertime;
use App\Modules\HRM\Models\OTPolicy;
use App\Modules\HRM\Models\EmployeeBonus;
use App\Modules\HRM\Models\BonusType;
use App\Modules\HRM\Models\Department;
use App\Modules\HRM\Models\Branch;
use App\Models\User;
use Carbon\Carbon;

class PerfectPayrollSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting Perfect Payroll Seeder...');

        // 1. Ensure Base Configurations exist
        $this->call(CompletePayrollSeeder::class);
        $this->call(HRMDemoDataSeeder::class);

        // Get admin for 'created_by' / 'approved_by'
        $admin = User::first();
        
        // 2. Setup Employee Salaries
        $this->command->info('💰 Setting up Employee Salaries...');
        
        $employees = Employee::where('status', 'confirmed')->take(5)->get();
        $structure = SalaryStructure::first(); // Grab the first one (Grade A)
        
        if (!$structure) {
            $this->command->error('No Salary Structure found! Check CompletePayrollSeeder.');
            return;
        }

        foreach ($employees as $employee) {
            // Assign Salary if not exists
            EmployeeSalary::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                ],
                [
                    'salary_structure_id' => $structure->id,
                    'basic_salary' => $employee->basic_salary ?? 50000,
                    'effective_date' => Carbon::parse($employee->joining_date),
                    'is_active' => true,
                ]
            );
            $this->command->info("  - Assigned salary to {$employee->first_name}");
        }

        // 3. Create Approved Overtime for CURRENT MONTH (to show up in payroll)
        $this->command->info('⏰ Creating Approved Overtime for current month...');
        
        $policy = OTPolicy::first();
        $currentMonth = Carbon::now();
        
        if ($policy && $employees->count() > 0) {
            $emp = $employees->first();
            
            EmployeeOvertime::firstOrCreate(
                [
                    'employee_id' => $emp->id,
                    'ot_date' => $currentMonth->copy()->subDays(2)->format('Y-m-d'),
                ],
                [
                    'ot_policy_id' => $policy->id,
                    'start_time' => '18:00:00',
                    'end_time' => '20:00:00',
                    'total_minutes' => 120,
                    'total_hours' => 2,
                    'ot_type' => 'regular',
                    'multiplier' => $policy->multiplier, // 1.5 usually
                    'hourly_rate' => 200, // Dummy
                    'ot_amount' => 600, // Dummy
                    'status' => 'approved',
                    'approved_by' => $admin->id,
                    'approved_at' => now(),
                    'created_by' => $admin->id,
                ]
            );
            $this->command->info("  - Created 2hrs OT for {$emp->first_name}");
        }

        // 4. Create Approved Bonus
        $this->command->info('🎁 Creating Approved Bonus for current month...');
        $bonusType = BonusType::first();
        
        if ($bonusType && $employees->count() > 1) {
            $emp = $employees->skip(1)->first(); // Second employee
            
            EmployeeBonus::firstOrCreate(
                [
                    'employee_id' => $emp->id,
                    'bonus_type_id' => $bonusType->id,
                    'bonus_month' => $currentMonth->month,
                    'bonus_year' => $currentMonth->year,
                ],
                [
                    'bonus_name' => $bonusType->name,
                    'bonus_date' => $currentMonth->format('Y-m-d'),
                    'amount' => 5000,
                    'status' => 'approved',
                    'approved_by' => $admin->id,
                    'approved_at' => now(),
                    'created_by' => $admin->id,
                ]
            );
             $this->command->info("  - Created Bonus for {$emp->first_name}");
        }

        $this->command->info('✅ Perfect Payroll Environment Ready! You can now Run Payroll.');
    }
}
