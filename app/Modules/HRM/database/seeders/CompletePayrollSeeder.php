<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\HRM\Models\SalaryComponent;
use App\Modules\HRM\Models\SalaryStructure;
use App\Modules\HRM\Models\TaxSlab;
use App\Modules\HRM\Models\OTPolicy;
use App\Modules\HRM\Models\BonusType;
use App\Modules\HRM\Models\LoanType;
use App\Modules\HRM\Models\GratuityConfig;
use App\Modules\HRM\Models\CommissionScheme;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CompletePayrollSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting Complete Payroll System Seeder...');
        
        $hrmModule = \App\Modules\Main\Models\Module::firstOrCreate(
            ['name' => 'HRM'],
            ['slug' => 'hrm', 'description' => 'Human Resource Management']
        );
        
        // 1. Seed Salary Components
        $this->seedSalaryComponents();
        
        // 2. Seed Salary Structures (Grades)
        $this->seedSalaryStructures();
        
        // 3. Seed Tax Slabs
        $this->seedTaxSlabs();
        
        // 4. Seed OT Policies
        $this->seedOTPolicies();
        
        // 5. Seed Bonus Types
        $this->seedBonusTypes();
        
        // 6. Seed Loan Types
        $this->seedLoanTypes();
        
        // 7. Seed Gratuity Config
        $this->seedGratuityConfig();
        
        // 8. Seed Commission Schemes
        $this->seedCommissionSchemes();
        
        // 9. Create ALL Permissions
        $this->seedPermissions($hrmModule);
        
        $this->command->info('✅ Complete Payroll System Seeded Successfully!');
    }
    
    protected function seedSalaryComponents()
    {
        $this->command->info('📝 Seeding Salary Components...');
        
        $components = [
            // Earnings
            ['name' => 'House Rent Allowance (HRA)', 'type' => 'earning', 'calculation_type' => 'percentage', 'default_percentage' => 40.00, 'is_taxable' => true],
            ['name' => 'Medical Allowance', 'type' => 'earning', 'calculation_type' => 'percentage', 'default_percentage' => 10.00, 'is_taxable' => true],
            ['name' => 'Transport Allowance', 'type' => 'earning', 'calculation_type' => 'percentage', 'default_percentage' => 10.00, 'is_taxable' => true],
            ['name' => 'Food Allowance', 'type' => 'earning', 'calculation_type' => 'fixed', 'default_amount' => 3000.00, 'is_taxable' => true],
            ['name' => 'Mobile Allowance', 'type' => 'earning', 'calculation_type' => 'fixed', 'default_amount' => 1000.00, 'is_taxable' => false],
            ['name' => 'Special Allowance', 'type' => 'earning', 'calculation_type' => 'percentage', 'default_percentage' => 15.00, 'is_taxable' => true],
            
            // Deductions
            ['name' => 'Provident Fund (PF)', 'type' => 'deduction', 'calculation_type' => 'percentage', 'default_percentage' => 10.00, 'is_taxable' => false],
            ['name' => 'Professional Tax', 'type' => 'deduction', 'calculation_type' => 'fixed', 'default_amount' => 200.00, 'is_taxable' => false],
            ['name' => 'Insurance Premium', 'type' => 'deduction', 'calculation_type' => 'fixed', 'default_amount' => 500.00, 'is_taxable' => false],
            ['name' => 'Welfare Fund', 'type' => 'deduction', 'calculation_type' => 'percentage', 'default_percentage' => 2.00, 'is_taxable' => false],
        ];
        
        foreach ($components as $component) {
            SalaryComponent::firstOrCreate(
                ['name' => $component['name']],
                array_merge($component, ['is_active' => true])
            );
        }
    }
    
    protected function seedSalaryStructures()
    {
        $this->command->info('📊 Seeding Salary Structures (Grades)...');
        
        $structures = [
            [
                'name' => 'Grade A - Executive Level',
                'description' => 'For C-level executives and senior management',
                'components' => ['House Rent Allowance (HRA)', 'Medical Allowance', 'Transport Allowance', 'Food Allowance', 'Mobile Allowance', 'Special Allowance', 'Provident Fund (PF)', 'Insurance Premium']
            ],
            [
                'name' => 'Grade B - Manager Level',
                'description' => 'For department managers and team leads',
                'components' => ['House Rent Allowance (HRA)', 'Medical Allowance', 'Transport Allowance', 'Food Allowance', 'Mobile Allowance', 'Provident Fund (PF)']
            ],
            [
                'name' => 'Grade C - Officer Level',
                'description' => 'For officers and senior staff',
                'components' => ['House Rent Allowance (HRA)', 'Medical Allowance', 'Transport Allowance', '  Provident Fund (PF)', 'Professional Tax']
            ],
            [
                'name' => 'Grade D - Entry Level',
                'description' => 'For entry-level employees and junior staff',
                'components' => ['House Rent Allowance (HRA)', 'Medical Allowance', 'Provident Fund (PF)', 'Professional Tax']
            ],
        ];
        
        foreach ($structures as $structureData) {
            $structure = SalaryStructure::firstOrCreate(
                ['name' => $structureData['name']],
                ['description' => $structureData['description'], 'is_active' => true]
            );
            
            // Attach components
            foreach ($structureData['components'] as $componentName) {
                $component = SalaryComponent::where('name', $componentName)->first();
                if ($component && !$structure->components->contains($component->id)) {
                    $structure->components()->attach($component->id);
                }
            }
        }
    }
    
    protected function seedTaxSlabs()
    {
        $this->command->info('💰 Seeding Tax Slabs (Bangladesh)...');
        
        $slabs = [
            // Male
            ['gender' => 'male', 'min_income' => 0, 'max_income' => 350000, 'tax_rate' => 0, 'fixed_deduction' => 0],
            ['gender' => 'male', 'min_income' => 350000, 'max_income' => 450000, 'tax_rate' => 5, 'fixed_deduction' => 0],
            ['gender' => 'male', 'min_income' => 450000, 'max_income' => 750000, 'tax_rate' => 10, 'fixed_deduction' => 5000],
            ['gender' => 'male', 'min_income' => 750000, 'max_income' => 1150000, 'tax_rate' => 15, 'fixed_deduction' => 35000],
            ['gender' => 'male', 'min_income' => 1150000, 'max_income' => 1650000, 'tax_rate' => 20, 'fixed_deduction' => 95000],
            ['gender' => 'male', 'min_income' => 1650000, 'max_income' => null, 'tax_rate' => 25, 'fixed_deduction' => 195000],
            
            // Female
            ['gender' => 'female', 'min_income' => 0, 'max_income' => 400000, 'tax_rate' => 0, 'fixed_deduction' => 0],
            ['gender' => 'female', 'min_income' => 400000, 'max_income' => 500000, 'tax_rate' => 5, 'fixed_deduction' => 0],
            ['gender' => 'female', 'min_income' => 500000, 'max_income' => 800000, 'tax_rate' => 10, 'fixed_deduction' => 5000],
            ['gender' => 'female', 'min_income' => 800000, 'max_income' => 1200000, 'tax_rate' => 15, 'fixed_deduction' => 35000],
            ['gender' => 'female', 'min_income' => 1200000, 'max_income' => 1700000, 'tax_rate' => 20, 'fixed_deduction' => 95000],
            ['gender' => 'female', 'min_income' => 1700000, 'max_income' => null, 'tax_rate' => 25, 'fixed_deduction' => 195000],
        ];
        
        foreach ($slabs as $slab) {
            TaxSlab::firstOrCreate(
                ['gender' => $slab['gender'], 'min_income' => $slab['min_income']],
                $slab
            );
        }
    }
    
    protected function seedOTPolicies()
    {
        $this->command->info('⏰ Seeding OT Policies...');
        
        $policies = [
            [
                'name' => 'Standard OT Policy',
                'description' => 'Default overtime policy with industry standard multipliers',
                'calculation_basis' => 'hourly_rate',
                'multiplier' => 1.5,
                'weekend_multiplier' => 2.0,
                'holiday_multiplier' => 2.5,
                'night_shift_multiplier' => 1.25,
                'night_shift_start' => '22:00:00',
                'night_shift_end' => '06:00:00',
                'min_ot_minutes' => 30,
                'max_ot_hours_per_day' => 4,
                'max_ot_hours_per_month' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Premium OT Policy',
                'description' => 'Higher multipliers for critical operations',
                'calculation_basis' => 'hourly_rate',
                'multiplier' => 2.0,
                'weekend_multiplier' => 2.5,
                'holiday_multiplier' => 3.0,
                'night_shift_multiplier' => 1.5,
                'night_shift_start' => '22:00:00',
                'night_shift_end' => '06:00:00',
                'min_ot_minutes' => 30,
                'max_ot_hours_per_day' => 6,
                'max_ot_hours_per_month' => 80,
                'is_active' => true,
            ],
        ];
        
        foreach ($policies as $policy) {
            OTPolicy::firstOrCreate(
                ['name' => $policy['name']],
                $policy
            );
        }
    }
    
    protected function seedBonusTypes()
    {
        $this->command->info('🎁 Seeding Bonus Types...');
        
        $bonuses = [
            ['name' => 'Eid Bonus', 'description' => 'Festival bonus for Eid celebration', 'calculation_type' => 'percentage', 'default_percentage' => 50.00, 'frequency' => 'yearly', 'is_taxable' => true, 'is_active' => true],
            ['name' => 'Performance Bonus', 'description' => 'Annual performance-based bonus', 'calculation_type' => 'performance_based', 'frequency' => 'yearly', 'is_taxable' => true, 'is_active' => true],
            ['name' => 'Joining Bonus', 'description' => 'One-time bonus on joining', 'calculation_type' => 'fixed', 'default_amount' => 10000.00, 'frequency' => 'one_time', 'is_taxable' => true, 'is_active' => true],
            ['name' => 'Year-End Bonus', 'description' => 'Annual year-end bonus', 'calculation_type' => 'percentage', 'default_percentage' => 100.00, 'frequency' => 'yearly', 'is_taxable' => true, 'is_active' => true],
            ['name' => 'Project Completion Bonus', 'description' => 'Bonus for successful project delivery', 'calculation_type' => 'fixed', 'default_amount' => 5000.00, 'frequency' => 'one_time', 'is_taxable' => true, 'is_active' => true],
        ];
        
        foreach ($bonuses as $bonus) {
            BonusType::firstOrCreate(
                ['name' => $bonus['name']],
                $bonus
            );
        }
    }
    
    protected function seedLoanTypes()
    {
        $this->command->info('💳 Seeding Loan Types...');
        
        $loans = [
            ['name' => 'Personal Loan', 'description' => 'General purpose personal loan', 'max_amount' => 100000, 'interest_rate' => 12.00, 'interest_type' => 'reducing', 'max_tenure_months' => 36, 'is_active' => true],
            ['name' => 'Emergency Loan', 'description' => 'Quick loan for emergencies', 'max_amount' => 50000, 'interest_rate' => 10.00, 'interest_type' => 'flat', 'max_tenure_months' => 12, 'is_active' => true],
            ['name' => 'Education Loan', 'description' => 'Loan for education expenses', 'max_amount' => 200000, 'interest_rate' => 8.00, 'interest_type' => 'reducing', 'max_tenure_months' => 48, 'is_active' => true],
            ['name' => 'Housing Loan', 'description' => 'Loan for housing/rent advance', 'max_amount' => 500000, 'interest_rate' => 10.00, 'interest_type' => 'reducing', 'max_tenure_months' => 60, 'is_active' => true],
            ['name' => 'Vehicle Loan', 'description' => 'Loan for vehicle purchase', 'max_amount' => 300000, 'interest_rate' => 15.00, 'interest_type' => 'reducing', 'max_tenure_months' => 36, 'is_active' => true],
        ];
        
        foreach ($loans as $loan) {
            LoanType::firstOrCreate(
                ['name' => $loan['name']],
                $loan
            );
        }
    }
    
    protected function seedGratuityConfig()
    {
        $this->command->info('🏆 Seeding Gratuity Configuration...');
        
        GratuityConfig::firstOrCreate(
            ['name' => 'Default Gratuity Policy'],
            [
                'description' => 'Standard gratuity calculation for employees',
                'min_service_years' => 5,
                'multiplier' => 0.5,
                'calculation_formula' => 'last_basic_salary',
                'formula_description' => 'Last Basic Salary × Service Years × 0.5',
                'max_gratuity_amount' => 500000,
                'is_active' => true,
            ]
        );
    }
    
    protected function seedCommissionSchemes()
    {
        $this->command->info('💵 Seeding Commission Schemes...');
        
        $schemes = [
            ['name' => 'Sales Commission - Tier 1', 'description' => 'Basic sales commission', 'commission_type' => 'percentage', 'rate' => 5.00, 'min_target' => 0, 'max_target' => 100000, 'is_active' => true],
            ['name' => 'Sales Commission - Tier 2', 'description' => 'Mid-level sales commission', 'commission_type' => 'percentage', 'rate' => 7.50, 'min_target' => 100000, 'max_target' => 500000, 'is_active' => true],
            ['name' => 'Sales Commission - Tier 3', 'description' => 'Premium sales commission', 'commission_type' => 'percentage', 'rate' => 10.00, 'min_target' => 500000, 'max_target' => null, 'is_active' => true],
        ];
        
        foreach ($schemes as $scheme) {
            CommissionScheme::firstOrCreate(
                ['name' => $scheme['name']],
                $scheme
            );
        }
    }
    
    protected function seedPermissions($hrmModule)
    {
        $this->command->info('🔐 Seeding All Permissions...');
        
        $allPermissions = [
            // Payroll
            'hrm.payroll.view', 'hrm.payroll.create', 'hrm.payroll.update', 'hrm.payroll.delete', 'hrm.payroll.process',
            
            // Salary Management
            'hrm.salaries.view', 'hrm.salaries.assign',
            
            // Loans
            'hrm.loan-types.view', 'hrm.loan-types.create', 'hrm.loan-types.update', 'hrm.loan-types.delete',
            'hrm.loans.view', 'hrm.loans.create', 'hrm.loans.update', 'hrm.loans.delete', 'hrm.loans.approve', 'hrm.loans.disburse',
            
            // Advances
            'hrm.advances.view', 'hrm.advances.create', 'hrm.advances.update', 'hrm.advances.delete', 'hrm.advances.approve',
            
            // Overtime
            'hrm.ot-policies.view', 'hrm.ot-policies.create', 'hrm.ot-policies.update', 'hrm.ot-policies.delete',
            'hrm.overtime.view', 'hrm.overtime.create', 'hrm.overtime.update', 'hrm.overtime.delete', 'hrm.overtime.approve',
            
            // Bonuses
            'hrm.bonus-types.view', 'hrm.bonus-types.create', 'hrm.bonus-types.update', 'hrm.bonus-types.delete',
            'hrm.bonuses.view', 'hrm.bonuses.create', 'hrm.bonuses.update', 'hrm.bonuses.delete', 'hrm.bonuses.approve',
            
            // Gratuity
            'hrm.gratuity.view', 'hrm.gratuity.calculate', 'hrm.gratuity.approve', 'hrm.gratuity.pay',
            
            // Reports
            'hrm.reports.view', 'hrm.reports.salary-register', 'hrm.reports.export',
            
            // Bank Transfers
            'hrm.bank-transfers.view', 'hrm.bank-transfers.generate',
        ];
        
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['module_id' => $hrmModule->id]
            );
        }
        
        // Assign all to Admin role
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo($allPermissions);
        
        $this->command->info('✅ ' . count($allPermissions) . ' permissions created and assigned to Admin role');
    }
}
