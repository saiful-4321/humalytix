<?php

namespace App\Modules\HRM\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Modules\Main\Models\Permission;

class HRMRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🎭 Creating Simplified HRM Roles...');
        $this->command->info('');

        // Get all HRM permissions
        $allHrmPermissions = Permission::where('name', 'like', 'hrm.%')->pluck('name')->toArray();

        // ==================== EXECUTIVE ROLES ====================
        
        // 1. CHAIRMAN
        $this->command->info('Creating Chairman role...');
        $chairmanRole = Role::firstOrCreate(['name' => 'Chairman'], ['guard_name' => 'web']);
        // Chairman gets ALL permissions (HRM + Finance + System)
        $allPermissions = Permission::pluck('name')->toArray();
        $chairmanRole->syncPermissions($allPermissions);
        $this->command->info('✅ Chairman: ALL permissions (' . count($allPermissions) . ')');

        // 2. CEO (Chief Executive Officer)
        $this->command->info('Creating CEO role...');
        $ceoRole = Role::firstOrCreate(['name' => 'CEO'], ['guard_name' => 'web']);
        // CEO gets all except system settings
        $ceoPermissions = Permission::whereNotIn('name', [
            'hrm.settings.manage',
            'backup-management',
            'cache-management',
        ])->pluck('name')->toArray();
        $ceoRole->syncPermissions($ceoPermissions);
        $this->command->info('✅ CEO: ' . count($ceoPermissions) . ' permissions');

        // 3. MD (Managing Director)
        $this->command->info('Creating MD role...');
        $mdRole = Role::firstOrCreate(['name' => 'Managing Director'], ['guard_name' => 'web']);
        // MD gets all HRM + Finance + Reports
        $mdPermissions = Permission::where(function($query) {
            $query->where('name', 'like', 'hrm.%')
                  ->orWhere('name', 'like', 'finance-%')
                  ->orWhere('name', 'like', '%-report-%');
        })->pluck('name')->toArray();
        $mdRole->syncPermissions($mdPermissions);
        $this->command->info('✅ MD: ' . count($mdPermissions) . ' permissions');

        // ==================== DEPARTMENT HEADS ====================

        // 4. FINANCE DEPARTMENT HEAD
        $this->command->info('Creating Finance Department Head role...');
        $financeHeadRole = Role::firstOrCreate(['name' => 'Finance Department Head'], ['guard_name' => 'web']);
        $financePermissions = array_merge(
            // All Finance permissions
            Permission::where('name', 'like', 'finance-%')->pluck('name')->toArray(),
            // HRM Payroll & Expenses (view/approve)
            [
                'hrm.dashboard',
                'hrm.payroll.access',
                'hrm.payroll.approve',
                'hrm.expenses.access',
                'hrm.expenses.approve',
                'hrm.reports.access',
                'hrm.analytics.access',
            ]
        );
        $financeHeadRole->syncPermissions($financePermissions);
        $this->command->info('✅ Finance Department Head: ' . count($financePermissions) . ' permissions');

        // 5. HR DEPARTMENT HEAD (CHRO)
        $this->command->info('Creating HR Department Head role...');
        $hrHeadRole = Role::firstOrCreate(['name' => 'HR Department Head'], ['guard_name' => 'web']);
        // All HRM permissions
        $hrHeadRole->syncPermissions($allHrmPermissions);
        $this->command->info('✅ HR Department Head: ' . count($allHrmPermissions) . ' permissions');

        // ==================== MANAGERS ====================

        // 6. HR MANAGER
        $this->command->info('Creating HR Manager role...');
        $hrManagerRole = Role::firstOrCreate(['name' => 'HR Manager'], ['guard_name' => 'web']);
        $hrManagerPermissions = array_diff($allHrmPermissions, [
            'hrm.settings.manage',
            'hrm.payroll.approve', // Can process but not approve
        ]);
        $hrManagerRole->syncPermissions($hrManagerPermissions);
        $this->command->info('✅ HR Manager: ' . count($hrManagerPermissions) . ' permissions');

        // 7. PAYROLL MANAGER
        $this->command->info('Creating Payroll Manager role...');
        $payrollManagerRole = Role::firstOrCreate(['name' => 'Payroll Manager'], ['guard_name' => 'web']);
        $payrollPermissions = [
            'hrm.dashboard',
            'hrm.employees.access',
            'hrm.employees.view-salary',
            'hrm.payroll.access',
            'hrm.payroll.process',
            'hrm.payroll.approve',
            'hrm.payroll.manage',
            'hrm.expenses.access',
            'hrm.expenses.manage',
            'hrm.expenses.approve',
            'hrm.attendance.access', // For payroll processing
            'hrm.leave.access', // For payroll processing
            'hrm.reports.access',
        ];
        $payrollManagerRole->syncPermissions($payrollPermissions);
        $this->command->info('✅ Payroll Manager: ' . count($payrollPermissions) . ' permissions');

        // 8. RECRUITMENT MANAGER
        $this->command->info('Creating Recruitment Manager role...');
        $recruitmentManagerRole = Role::firstOrCreate(['name' => 'Recruitment Manager'], ['guard_name' => 'web']);
        $recruitmentPermissions = [
            'hrm.dashboard',
            'hrm.employees.access',
            'hrm.employees.manage', // Can create new employees
            'hrm.recruitment.access',
            'hrm.recruitment.manage',
            'hrm.recruitment.approve',
            'hrm.documents.access',
            'hrm.documents.manage',
            'hrm.reports.access',
        ];
        $recruitmentManagerRole->syncPermissions($recruitmentPermissions);
        $this->command->info('✅ Recruitment Manager: ' . count($recruitmentPermissions) . ' permissions');

        // 9. TRAINING MANAGER
        $this->command->info('Creating Training Manager role...');
        $trainingManagerRole = Role::firstOrCreate(['name' => 'Training Manager'], ['guard_name' => 'web']);
        $trainingPermissions = [
            'hrm.dashboard',
            'hrm.employees.access',
            'hrm.training.access',
            'hrm.training.manage',
            'hrm.performance.access', // To see training needs
            'hrm.reports.access',
        ];
        $trainingManagerRole->syncPermissions($trainingPermissions);
        $this->command->info('✅ Training Manager: ' . count($trainingPermissions) . ' permissions');

        // 10. LINE MANAGER / TEAM LEAD
        $this->command->info('Creating Manager role...');
        $managerRole = Role::firstOrCreate(['name' => 'Manager'], ['guard_name' => 'web']);
        $managerPermissions = [
            'hrm.dashboard',
            'hrm.employees.access', // View team only
            'hrm.attendance.access',
            'hrm.leave.access',
            'hrm.leave.approve',
            'hrm.expenses.access',
            'hrm.expenses.approve',
            'hrm.performance.access',
            'hrm.performance.manage', // Manage team performance
            'hrm.performance.review',
            'hrm.training.access',
            'hrm.reports.access',
        ];
        $managerRole->syncPermissions($managerPermissions);
        $this->command->info('✅ Manager: ' . count($managerPermissions) . ' permissions');

        // ==================== STAFF ROLES ====================

        // 11. HR EXECUTIVE
        $this->command->info('Creating HR Executive role...');
        $hrExecutiveRole = Role::firstOrCreate(['name' => 'HR Executive'], ['guard_name' => 'web']);
        $hrExecutivePermissions = [
            'hrm.dashboard',
            'hrm.employees.access',
            'hrm.employees.manage',
            'hrm.organization.access',
            'hrm.attendance.access',
            'hrm.attendance.manage',
            'hrm.leave.access',
            'hrm.leave.manage',
            'hrm.documents.access',
            'hrm.documents.manage',
            'hrm.recruitment.access',
            'hrm.recruitment.manage',
            'hrm.reports.access',
        ];
        $hrExecutiveRole->syncPermissions($hrExecutivePermissions);
        $this->command->info('✅ HR Executive: ' . count($hrExecutivePermissions) . ' permissions');

        // 12. ACCOUNTANT
        $this->command->info('Creating Accountant role...');
        $accountantRole = Role::firstOrCreate(['name' => 'Accountant'], ['guard_name' => 'web']);
        $accountantPermissions = array_merge(
            Permission::where('name', 'like', 'finance-%')
                ->whereNotIn('name', ['finance-settings-manage'])
                ->pluck('name')->toArray(),
            [
                'hrm.payroll.access',
                'hrm.expenses.access',
            ]
        );
        $accountantRole->syncPermissions($accountantPermissions);
        $this->command->info('✅ Accountant: ' . count($accountantPermissions) . ' permissions');

        // 13. EMPLOYEE (STANDARD)
        $this->command->info('Creating Employee role...');
        $employeeRole = Role::firstOrCreate(['name' => 'Employee'], ['guard_name' => 'web']);
        $employeePermissions = [
            'hrm.dashboard',
            'hrm.attendance.checkin', // Self check-in
            'hrm.leave.access', // View own leaves
            'hrm.expenses.access', // Submit expenses
            'hrm.training.access',
            'hrm.training.enroll',
            'hrm.performance.access', // View own performance
            'hrm.documents.access', // View own documents
        ];
        $employeeRole->syncPermissions($employeePermissions);
        $this->command->info('✅ Employee: ' . count($employeePermissions) . ' permissions');

        // Summary
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('✅ Simplified Roles Created Successfully!');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('📋 Roles Summary:');
        $this->command->info('');
        $this->command->info('EXECUTIVE LEVEL:');
        $this->command->info('  1. Chairman - Full System Access');
        $this->command->info('  2. CEO - Strategic Leadership');
        $this->command->info('  3. Managing Director - Operations Leadership');
        $this->command->info('');
        $this->command->info('DEPARTMENT HEADS:');
        $this->command->info('  4. Finance Department Head - Finance + Payroll');
        $this->command->info('  5. HR Department Head - All HRM');
        $this->command->info('');
        $this->command->info('MANAGERS:');
        $this->command->info('  6. HR Manager - HR Operations');
        $this->command->info('  7. Payroll Manager - Payroll & Compensation');
        $this->command->info('  8. Recruitment Manager - Hiring & Onboarding');
        $this->command->info('  9. Training Manager - L&D');
        $this->command->info('  10. Manager - Team Leadership');
        $this->command->info('');
        $this->command->info('STAFF:');
        $this->command->info('  11. HR Executive - HR Support');
        $this->command->info('  12. Accountant - Finance Operations');
        $this->command->info('  13. Employee - Self Service');
        $this->command->info('');
    }
}
