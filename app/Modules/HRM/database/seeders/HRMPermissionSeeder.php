<?php

namespace App\Modules\HRM\database\seeders;

use Illuminate\Database\Seeder;
use App\Modules\Main\Models\Module;
use App\Modules\Main\Models\Permission;
use Spatie\Permission\Models\Role;

class HRMPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create HRM Module Categories (Sub-modules for better organization)
        $modules = [
            'HRM - Core HR' => ['status' => 1],
            'HRM - Time & Attendance' => ['status' => 1],
            'HRM - Leave Management' => ['status' => 1],
            'HRM - Compensation & Payroll' => ['status' => 1],
            'HRM - Expenses' => ['status' => 1],
            'HRM - Assets' => ['status' => 1],
            'HRM - Performance Management' => ['status' => 1],
            'HRM - Training & Development' => ['status' => 1],
            'HRM - Recruitment' => ['status' => 1],
            'HRM - Compliance & Documents' => ['status' => 1],
            'HRM - Analytics & Reports' => ['status' => 1],
            'HRM - Automation & Workflows' => ['status' => 1],
            'HRM - Settings' => ['status' => 1],
            'HRM - Additional Features' => ['status' => 1],
        ];

        $createdModules = [];
        foreach ($modules as $moduleName => $moduleData) {
            $createdModules[$moduleName] = Module::firstOrCreate(
                ['name' => $moduleName],
                $moduleData
            );
        }

        // Define all HRM permissions - Organized by Module
        $permissionsByModule = [
            // ==================== CORE HR ====================
            'HRM - Core HR' => [
                // Employees
                ['name' => 'hrm.employees.view', 'display_name' => 'View Employees'],
                ['name' => 'hrm.employees.create', 'display_name' => 'Create Employee'],
                ['name' => 'hrm.employees.edit', 'display_name' => 'Edit Employee'],
                ['name' => 'hrm.employees.delete', 'display_name' => 'Delete Employee'],
                ['name' => 'hrm.employees.export', 'display_name' => 'Export Employees'],
                ['name' => 'hrm.employees.import', 'display_name' => 'Import Employees'],
                
                // Organization Structure
                ['name' => 'hrm.departments.view', 'display_name' => 'View Departments'],
                ['name' => 'hrm.departments.manage', 'display_name' => 'Manage Departments'],
                ['name' => 'hrm.branches.view', 'display_name' => 'View Branches'],
                ['name' => 'hrm.branches.manage', 'display_name' => 'Manage Branches'],
                ['name' => 'hrm.business-units.view', 'display_name' => 'View Business Units'],
                ['name' => 'hrm.business-units.manage', 'display_name' => 'Manage Business Units'],
                ['name' => 'hrm.document-types.manage', 'display_name' => 'Manage Document Types'],
            ],
            
            // ==================== TIME & ATTENDANCE ====================
            'HRM - Time & Attendance' => [
                ['name' => 'hrm.attendance.view', 'display_name' => 'View Attendance'],
                ['name' => 'hrm.attendance.manage', 'display_name' => 'Manage Attendance'],
                ['name' => 'hrm.attendance.checkin', 'display_name' => 'Check-in/out'],
                ['name' => 'hrm.shifts.view', 'display_name' => 'View Shifts'],
                ['name' => 'hrm.shifts.manage', 'display_name' => 'Manage Shifts'],
                ['name' => 'hrm.rosters.view', 'display_name' => 'View Rosters'],
                ['name' => 'hrm.rosters.manage', 'display_name' => 'Manage Rosters'],
            ],
            
            // ==================== LEAVE MANAGEMENT ====================
            'HRM - Leave Management' => [
                ['name' => 'hrm.leaves.view', 'display_name' => 'View Leaves'],
                ['name' => 'hrm.leaves.create', 'display_name' => 'Apply Leave'],
                ['name' => 'hrm.leaves.approve', 'display_name' => 'Approve Leaves'],
                ['name' => 'hrm.leaves.cancel', 'display_name' => 'Cancel Leave'],
                ['name' => 'hrm.leave-allocations.view', 'display_name' => 'View Leave Allocations'],
                ['name' => 'hrm.leave-allocations.manage', 'display_name' => 'Manage Leave Allocations'],
                ['name' => 'hrm.leave-types.manage', 'display_name' => 'Manage Leave Types'],
                ['name' => 'hrm.leave-policies.manage', 'display_name' => 'Manage Leave Policies'],
            ],
            
            // ==================== COMPENSATION & PAYROLL ====================
            'HRM - Compensation & Payroll' => [
                ['name' => 'hrm.payroll.view', 'display_name' => 'View Payroll'],
                ['name' => 'hrm.payroll.process', 'display_name' => 'Process Payroll'],
                ['name' => 'hrm.payroll.approve', 'display_name' => 'Approve Payroll'],
                ['name' => 'hrm.payroll.export', 'display_name' => 'Export Payroll'],
                ['name' => 'hrm.bonuses.view', 'display_name' => 'View Bonuses'],
                ['name' => 'hrm.bonuses.manage', 'display_name' => 'Manage Bonuses'],
                ['name' => 'hrm.overtime.view', 'display_name' => 'View Overtime'],
                ['name' => 'hrm.overtime.manage', 'display_name' => 'Manage Overtime'],
                ['name' => 'hrm.loans.view', 'display_name' => 'View Loans'],
                ['name' => 'hrm.loans.manage', 'display_name' => 'Manage Loans'],
                ['name' => 'hrm.gratuity.view', 'display_name' => 'View Gratuity'],
                ['name' => 'hrm.gratuity.manage', 'display_name' => 'Manage Gratuity'],
                ['name' => 'hrm.salary-components.manage', 'display_name' => 'Manage Salary Components'],
            ],
            
            // ==================== EXPENSES ====================
            'HRM - Expenses' => [
                ['name' => 'hrm.expenses.view', 'display_name' => 'View Expenses'],
                ['name' => 'hrm.expenses.create', 'display_name' => 'Create Expense'],
                ['name' => 'hrm.expenses.approve', 'display_name' => 'Approve Expenses'],
                ['name' => 'hrm.expense-categories.manage', 'display_name' => 'Manage Expense Categories'],
            ],
            
            // ==================== ASSETS ====================
            'HRM - Assets' => [
                ['name' => 'hrm.assets.view', 'display_name' => 'View Assets'],
                ['name' => 'hrm.assets.manage', 'display_name' => 'Manage Assets'],
                ['name' => 'hrm.asset-categories.manage', 'display_name' => 'Manage Asset Categories'],
            ],
            
            // ==================== PERFORMANCE MANAGEMENT ====================
            'HRM - Performance Management' => [
                ['name' => 'hrm.performance.view', 'display_name' => 'View Performance'],
                ['name' => 'hrm.kpis.view', 'display_name' => 'View KPIs'],
                ['name' => 'hrm.kpis.manage', 'display_name' => 'Manage KPIs'],
                ['name' => 'hrm.okrs.view', 'display_name' => 'View OKRs'],
                ['name' => 'hrm.okrs.manage', 'display_name' => 'Manage OKRs'],
                ['name' => 'hrm.goals.view', 'display_name' => 'View Goals'],
                ['name' => 'hrm.goals.manage', 'display_name' => 'Manage Goals'],
                ['name' => 'hrm.appraisals.view', 'display_name' => 'View Appraisals'],
                ['name' => 'hrm.appraisals.manage', 'display_name' => 'Manage Appraisals'],
                ['name' => 'hrm.competencies.view', 'display_name' => 'View Competencies'],
                ['name' => 'hrm.competencies.manage', 'display_name' => 'Manage Competencies'],
                ['name' => 'hrm.pips.view', 'display_name' => 'View PIPs'],
                ['name' => 'hrm.pips.manage', 'display_name' => 'Manage PIPs'],
            ],
            
            // ==================== TRAINING & DEVELOPMENT ====================
            'HRM - Training & Development' => [
                ['name' => 'hrm.trainings.view', 'display_name' => 'View Trainings'],
                ['name' => 'hrm.trainings.manage', 'display_name' => 'Manage Trainings'],
                ['name' => 'hrm.trainings.enroll', 'display_name' => 'Enroll in Training'],
                ['name' => 'hrm.skills.view', 'display_name' => 'View Skills'],
                ['name' => 'hrm.skills.manage', 'display_name' => 'Manage Skills'],
                ['name' => 'hrm.certifications.view', 'display_name' => 'View Certifications'],
                ['name' => 'hrm.certifications.manage', 'display_name' => 'Manage Certifications'],
            ],
            
            // ==================== RECRUITMENT ====================
            'HRM - Recruitment' => [
                ['name' => 'hrm.jobs.view', 'display_name' => 'View Jobs'],
                ['name' => 'hrm.jobs.manage', 'display_name' => 'Manage Jobs'],
                ['name' => 'hrm.candidates.view', 'display_name' => 'View Candidates'],
                ['name' => 'hrm.candidates.manage', 'display_name' => 'Manage Candidates'],
                ['name' => 'hrm.interviews.view', 'display_name' => 'View Interviews'],
                ['name' => 'hrm.interviews.manage', 'display_name' => 'Manage Interviews'],
                ['name' => 'hrm.offers.view', 'display_name' => 'View Offers'],
                ['name' => 'hrm.offers.manage', 'display_name' => 'Manage Offers'],
                ['name' => 'hrm.letters.view', 'display_name' => 'View Letters'],
                ['name' => 'hrm.letters.generate', 'display_name' => 'Generate Letters'],
                ['name' => 'hrm.letter-templates.manage', 'display_name' => 'Manage Letter Templates'],
            ],
            
            // ==================== COMPLIANCE & DOCUMENTS ====================
            'HRM - Compliance & Documents' => [
                ['name' => 'hrm.policies.view', 'display_name' => 'View Policies'],
                ['name' => 'hrm.policies.manage', 'display_name' => 'Manage Policies'],
                ['name' => 'hrm.contracts.view', 'display_name' => 'View Contracts'],
                ['name' => 'hrm.contracts.manage', 'display_name' => 'Manage Contracts'],
                ['name' => 'hrm.documents.view', 'display_name' => 'View Documents'],
                ['name' => 'hrm.documents.upload', 'display_name' => 'Upload Documents'],
                ['name' => 'hrm.documents.verify', 'display_name' => 'Verify Documents'],
            ],
            
            // ==================== ANALYTICS & REPORTS ====================
            'HRM - Analytics & Reports' => [
                ['name' => 'hrm.analytics.view', 'display_name' => 'View Analytics'],
                ['name' => 'hrm.analytics.export', 'display_name' => 'Export Analytics'],
                ['name' => 'hrm.reports.view', 'display_name' => 'View Reports'],
                ['name' => 'hrm.reports.export', 'display_name' => 'Export Reports'],
            ],
            
            // ==================== AUTOMATION & WORKFLOWS ====================
            'HRM - Automation & Workflows' => [
                ['name' => 'hrm.workflows.view', 'display_name' => 'View Workflows'],
                ['name' => 'hrm.workflows.manage', 'display_name' => 'Manage Workflows'],
                ['name' => 'hrm.automation.view', 'display_name' => 'View Automation Settings'],
                ['name' => 'hrm.automation.manage', 'display_name' => 'Manage Automation'],
                ['name' => 'hrm.notifications.send', 'display_name' => 'Send Notifications'],
            ],
            
            // ==================== SETTINGS ====================
            'HRM - Settings' => [
                ['name' => 'hrm.settings.view', 'display_name' => 'View HRM Settings'],
                ['name' => 'hrm.settings.manage', 'display_name' => 'Manage HRM Settings'],
            ],
            
            // ==================== ADDITIONAL FEATURES ====================
            'HRM - Additional Features' => [
                // Onboarding & Offboarding
                ['name' => 'hrm.onboarding.view', 'display_name' => 'View Onboarding'],
                ['name' => 'hrm.onboarding.manage', 'display_name' => 'Manage Onboarding'],
                ['name' => 'hrm.resignations.view', 'display_name' => 'View Resignations'],
                ['name' => 'hrm.resignations.manage', 'display_name' => 'Manage Resignations'],
                ['name' => 'hrm.clearances.manage', 'display_name' => 'Manage Clearances'],
                ['name' => 'hrm.settlements.view', 'display_name' => 'View Settlements'],
                ['name' => 'hrm.settlements.manage', 'display_name' => 'Manage Settlements'],
                
                // Time Tracking
                ['name' => 'hrm.projects.view', 'display_name' => 'View Projects'],
                ['name' => 'hrm.projects.manage', 'display_name' => 'Manage Projects'],
                ['name' => 'hrm.time-entries.view', 'display_name' => 'View Time Entries'],
                ['name' => 'hrm.time-entries.manage', 'display_name' => 'Manage Time Entries'],
                ['name' => 'hrm.timesheets.view', 'display_name' => 'View Timesheets'],
                ['name' => 'hrm.timesheets.approve', 'display_name' => 'Approve Timesheets'],
            ],
        ];

        // Create permissions and assign to modules
        $allPermissions = [];
        foreach ($permissionsByModule as $moduleName => $permissions) {
            $module = $createdModules[$moduleName];
            
            foreach ($permissions as $permission) {
                $perm = Permission::firstOrCreate(
                    ['name' => $permission['name']],
                    [
                        'module_id' => $module->id,
                        'guard_name' => 'web',
                    ]
                );
                $allPermissions[] = $permission['name'];
            }
        }

        // Assign all HRM permissions to Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($allPermissions);
        }

        // Create HR Manager role with most permissions
        $hrManagerRole = Role::firstOrCreate(['name' => 'HR Manager'], ['guard_name' => 'web']);
        $hrManagerPermissions = array_diff($allPermissions, [
            'hrm.settings.manage',
            'hrm.employees.delete',
            'hrm.automation.manage',
        ]);
        $hrManagerRole->syncPermissions($hrManagerPermissions);

        // Create HR Executive role with limited permissions
        $hrExecutiveRole = Role::firstOrCreate(['name' => 'HR Executive'], ['guard_name' => 'web']);
        $hrExecutivePermissions = [
            'hrm.employees.view',
            'hrm.employees.create',
            'hrm.employees.edit',
            'hrm.departments.view',
            'hrm.branches.view',
            'hrm.candidates.view',
            'hrm.candidates.manage',
            'hrm.interviews.view',
            'hrm.interviews.manage',
            'hrm.attendance.view',
            'hrm.leaves.view',
            'hrm.documents.view',
            'hrm.documents.upload',
            'hrm.onboarding.view',
            'hrm.onboarding.manage',
            'hrm.trainings.view',
            'hrm.reports.view',
        ];
        $hrExecutiveRole->syncPermissions($hrExecutivePermissions);

        // Create Employee role with self-service permissions
        $employeeRole = Role::firstOrCreate(['name' => 'Employee'], ['guard_name' => 'web']);
        $employeePermissions = [
            'hrm.attendance.checkin',
            'hrm.leaves.create',
            'hrm.leaves.view',
            'hrm.expenses.create',
            'hrm.expenses.view',
            'hrm.documents.view',
            'hrm.trainings.view',
            'hrm.trainings.enroll',
            'hrm.time-entries.view',
            'hrm.time-entries.manage',
            'hrm.timesheets.view',
            'hrm.policies.view',
            'hrm.contracts.view',
        ];
        $employeeRole->syncPermissions($employeePermissions);

        $this->command->info('HRM permissions seeded successfully!');
        $this->command->info('Total permissions: ' . count($allPermissions));
        $this->command->info('Total modules: ' . count($createdModules));
    }
}
