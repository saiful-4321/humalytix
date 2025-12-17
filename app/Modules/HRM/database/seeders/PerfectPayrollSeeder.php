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
use App\Modules\HRM\Models\LoanType;
use App\Modules\HRM\Models\EmployeeLoan;
use App\Modules\HRM\Models\EmployeeAdvance;
use App\Modules\HRM\Models\Attendance;
use App\Modules\HRM\Models\Department;
use App\Modules\HRM\Models\Branch;
use App\Models\User;
use Carbon\Carbon;

class PerfectPayrollSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting Perfect Payroll Seeder (Real Data Mode)...');

        // 1. Ensure Base Configurations exist
        $this->call(CompletePayrollSeeder::class);
        $this->call(HRMDemoDataSeeder::class);

        // Get admin for 'created_by' / 'approved_by'
        $admin = User::first();
        
        // 2. Setup Employee Salaries & Bank Details
        $this->command->info('💰 Setting up Employee Salaries & Bank Details...');
        
        $employees = Employee::get();
        $structures = SalaryStructure::all();
        
        if ($structures->isEmpty()) {
            $this->command->error('No Salary Structure found! Check CompletePayrollSeeder.');
            return;
        }

        foreach ($employees as $index => $employee) {
            // Assign Grade based on index/random or designation logic
            $structure = $structures->random();
            if (str_contains(strtolower($employee->designation), 'manager')) {
                $structure = $structures->where('name', 'like', '%Grade A%')->first() ?? $structure;
            } elseif (str_contains(strtolower($employee->designation), 'executive')) {
                $structure = $structures->where('name', 'like', '%Grade C%')->first() ?? $structure;
            }

            // Update Bank Details
            $employee->update([
                'bank_name' => $this->getRandomBankName(),
                'bank_account_number' => '2050' . rand(10000000, 99999999),
                'bank_branch' => 'Gulshan Branch',
                'payment_method' => 'bank',
            ]);

            // Assign Salary if not exists
            EmployeeSalary::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                ],
                [
                    'salary_structure_id' => $structure->id,
                    'basic_salary' => $employee->basic_salary,
                    'effective_date' => Carbon::parse($employee->joining_date),
                    'is_active' => true,
                ]
            );
            $this->command->info("  - Assigned salary & bank details to {$employee->first_name}");
        }

        // 3. Create Attendance for CURRENT MONTH (CRITICAL for Payroll)
        $this->command->info('📅 Creating Attendance for Current Month...');
        
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth(); // Or today
        
        if ($endDate->isFuture()) {
            $endDate = Carbon::now();
        }

        foreach ($employees as $employee) {
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                // Skip Fridays (Weekend in BD)
                if ($currentDate->dayOfWeek === Carbon::FRIDAY) {
                    $currentDate->addDay();
                    continue;
                }

                Attendance::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date' => $currentDate->format('Y-m-d'),
                    ],
                    [
                        'check_in' => $currentDate->copy()->setTime(9, rand(0, 30), 0),
                        'check_out' => $currentDate->copy()->setTime(18, rand(0, 30), 0),
                        'working_hours' => 8.5,
                        'status' => 'present',
                        'created_by' => $admin->id,
                    ]
                );
                
                $currentDate->addDay();
            }
            $this->command->info("  - Generated attendance for {$employee->first_name}");
        }

        // 4. Create Approved Overtime
        $this->command->info('⏰ Creating Approved Overtime...');
        
        $policy = OTPolicy::first();
        $currentMonth = Carbon::now();
        
        if ($policy) {
            // Give OT to 3 random employees
            $otEmployees = $employees->random(min(3, $employees->count()));
            
            foreach ($otEmployees as $emp) {
                EmployeeOvertime::firstOrCreate(
                    [
                        'employee_id' => $emp->id,
                        'ot_date' => $currentMonth->copy()->subDays(rand(2, 10))->format('Y-m-d'),
                    ],
                    [
                        'ot_policy_id' => $policy->id,
                        'start_time' => '18:00:00',
                        'end_time' => '20:00:00',
                        'total_minutes' => 120,
                        'total_hours' => 2,
                        'ot_type' => 'regular',
                        'multiplier' => $policy->multiplier,
                        'hourly_rate' => ($emp->basic_salary / 208), // approx
                        'ot_amount' => ($emp->basic_salary / 208) * 2 * $policy->multiplier,
                        'status' => 'approved',
                        'approved_by' => $admin->id,
                        'approved_at' => now(),
                        'created_by' => $admin->id,
                    ]
                );
                $this->command->info("  - Created OT for {$emp->first_name}");
            }
        }

        // 5. Create Approved Bonus
        $this->command->info('🎁 Creating Approved Bonus...');
        $bonusType = BonusType::first();
        
        if ($bonusType) {
            // Give Bonus to 2 random employees
            $bonusEmployees = $employees->random(min(2, $employees->count()));
            
            foreach ($bonusEmployees as $emp) {
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
                        'amount' => ($emp->basic_salary * 0.5), // 50% bonus
                        'status' => 'approved',
                        'approved_by' => $admin->id,
                        'approved_at' => now(),
                        'created_by' => $admin->id,
                    ]
                );
                 $this->command->info("  - Created Bonus for {$emp->first_name}");
            }
        }

        // 6. Create Active Loans
        $this->command->info('💳 Creating Active Loans...');
        $loanType = LoanType::first(); // Personal Loan
        
        if ($loanType) {
            $loanEmp = $employees->first(); // Give a loan to the first employee
             
            $amount = 50000;
            $tenure = 12;
            $monthlyInst = $amount / $tenure; // Simple calc for demo
            
            $loan = EmployeeLoan::firstOrCreate(
                [
                    'employee_id' => $loanEmp->id,
                    'status' => 'active',
                ],
                [
                    'loan_type_id' => $loanType->id,
                    'loan_amount' => $amount,
                    'interest_rate' => 10,
                    'interest_type' => 'flat',
                    'tenure_months' => $tenure,
                    'monthly_installment' => $monthlyInst,
                    'total_payable' => $amount + ($amount * 0.10),
                    'total_paid' => $monthlyInst * 2, // Paid 2 months
                    'outstanding_balance' => ($amount + ($amount * 0.10)) - ($monthlyInst * 2),
                    'disbursement_date' => now()->subMonths(2),
                    'first_installment_date' => now()->subMonths(1),
                    'purpose' => 'Home Renovation',
                    'approved_by' => $admin->id,
                    'approved_at' => now()->subMonths(2),
                    'created_by' => $admin->id,
                ]
            );
            $this->command->info("  - Created Active Loan for {$loanEmp->first_name}");
        }

        // 7. Create Salary Advance & Deduction
        $this->command->info('💸 Creating Salary Advance...');
        if ($employees->count() > 1) {
            $advEmp = $employees->last();
            
            EmployeeAdvance::firstOrCreate(
                [
                    'employee_id' => $advEmp->id,
                    'status' => 'active',
                ],
                [
                    'amount' => 10000,
                    'disbursement_date' => now()->startOfMonth(),
                    'deduction_months' => 2,
                    'monthly_deduction' => 5000,
                    'total_deducted' => 0,
                    'outstanding_balance' => 10000,
                    'reason' => 'Medical Emergency',
                    'approved_by' => $admin->id,
                    'approved_at' => now()->startOfMonth(),
                    'created_by' => $admin->id,
                ]
            );
             $this->command->info("  - Created Advance for {$advEmp->first_name}");
        }

        // 8. Generate Payroll Records for Last Month (For Bank Transfer Testing)
        $this->command->info('💵 Generating Payroll Records for Last Month...');
        $lastMonth = Carbon::now()->subMonth();
        
        foreach ($employees as $emp) {
            // Check if payroll already exists
            $exists = \App\Modules\HRM\Models\Payroll::where('employee_id', $emp->id)
                ->where('month', $lastMonth->month)
                ->where('year', $lastMonth->year)
                ->exists();

            if (!$exists) {
                $gross = $emp->basic_salary + ($emp->basic_salary * 0.4); // +40% allowances
                $deductions = $emp->basic_salary * 0.05; // 5% deduction
                $tax = 0;
                
                \App\Modules\HRM\Models\Payroll::create([
                    'employee_id' => $emp->id,
                    'month' => $lastMonth->month,
                    'year' => $lastMonth->year,
                    'basic_salary' => $emp->basic_salary,
                    'allowances' => $emp->basic_salary * 0.4,
                    'bonuses' => 0,
                    'deductions' => $deductions,
                    'tax' => $tax,
                    'gross_salary' => $gross,
                    'net_salary' => $gross - $deductions - $tax,
                    'status' => rand(0, 1) ? 'paid' : 'pending',
                    'payment_date' => rand(0, 1) ? $lastMonth->copy()->endOfMonth() : null,
                    'created_by' => $admin->id,
                ]);
                 $this->command->info("  - Generated Payroll for {$emp->first_name}");
            }
        }

        $this->command->info('✅ Perfect Payroll Environment (Real Data) Ready! you can now Run Payroll.');
    }

    private function getRandomBankName()
    {
        $banks = ['Dutch-Bangla Bank', 'BRAC Bank', 'City Bank', 'Eastern Bank Ltd', 'Islami Bank BD'];
        return $banks[array_rand($banks)];
    }
}
