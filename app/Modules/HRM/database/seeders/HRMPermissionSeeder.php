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
        // Create HRM Module
        $hrmModule = Module::firstOrCreate(
            ['name' => 'HRM'],
            ['status' => 1]
        );

        // Define all HRM permissions
        $permissions = [
            // Employee Management
            ['name' => 'hrm.employees.view', 'display_name' => 'View Employees', 'description' => 'Can view employee list and details'],
            ['name' => 'hrm.employees.create', 'display_name' => 'Create Employee', 'description' => 'Can create new employees'],
            ['name' => 'hrm.employees.edit', 'display_name' => 'Edit Employee', 'description' => 'Can edit employee information'],
            ['name' => 'hrm.employees.delete', 'display_name' => 'Delete Employee', 'description' => 'Can delete employees'],
            ['name' => 'hrm.employees.export', 'display_name' => 'Export Employees', 'description' => 'Can export employee data'],
            ['name' => 'hrm.employees.import', 'display_name' => 'Import Employees', 'description' => 'Can import employee data'],
            
            // Organization Structure
            ['name' => 'hrm.departments.view', 'display_name' => 'View Departments', 'description' => 'Can view departments'],
            ['name' => 'hrm.departments.manage', 'display_name' => 'Manage Departments', 'description' => 'Can create, edit, delete departments'],
            ['name' => 'hrm.branches.view', 'display_name' => 'View Branches', 'description' => 'Can view branches'],
            ['name' => 'hrm.branches.manage', 'display_name' => 'Manage Branches', 'description' => 'Can create, edit, delete branches'],
            ['name' => 'hrm.cost-centers.view', 'display_name' => 'View Cost Centers', 'description' => 'Can view cost centers'],
            ['name' => 'hrm.cost-centers.manage', 'display_name' => 'Manage Cost Centers', 'description' => 'Can create, edit, delete cost centers'],
            
            // Recruitment & ATS
            ['name' => 'hrm.jobs.view', 'display_name' => 'View Jobs', 'description' => 'Can view job postings'],
            ['name' => 'hrm.jobs.manage', 'display_name' => 'Manage Jobs', 'description' => 'Can create, edit, delete job postings'],
            ['name' => 'hrm.candidates.view', 'display_name' => 'View Candidates', 'description' => 'Can view candidates'],
            ['name' => 'hrm.candidates.manage', 'display_name' => 'Manage Candidates', 'description' => 'Can manage candidate pipeline'],
            ['name' => 'hrm.interviews.view', 'display_name' => 'View Interviews', 'description' => 'Can view interviews'],
            ['name' => 'hrm.interviews.manage', 'display_name' => 'Manage Interviews', 'description' => 'Can schedule and manage interviews'],
            ['name' => 'hrm.offers.view', 'display_name' => 'View Offers', 'description' => 'Can view offer letters'],
            ['name' => 'hrm.offers.manage', 'display_name' => 'Manage Offers', 'description' => 'Can create and manage offer letters'],
            
            // Attendance
            ['name' => 'hrm.attendance.view', 'display_name' => 'View Attendance', 'description' => 'Can view attendance records'],
            ['name' => 'hrm.attendance.manage', 'display_name' => 'Manage Attendance', 'description' => 'Can manage attendance records'],
            ['name' => 'hrm.attendance.checkin', 'display_name' => 'Check-in/out', 'description' => 'Can check-in and check-out'],
            ['name' => 'hrm.shifts.view', 'display_name' => 'View Shifts', 'description' => 'Can view shifts'],
            ['name' => 'hrm.shifts.manage', 'display_name' => 'Manage Shifts', 'description' => 'Can create, edit, delete shifts'],
            ['name' => 'hrm.rosters.view', 'display_name' => 'View Rosters', 'description' => 'Can view rosters'],
            ['name' => 'hrm.rosters.manage', 'display_name' => 'Manage Rosters', 'description' => 'Can create and manage rosters'],
            
            // Documents
            ['name' => 'hrm.documents.view', 'display_name' => 'View Documents', 'description' => 'Can view employee documents'],
            ['name' => 'hrm.documents.upload', 'display_name' => 'Upload Documents', 'description' => 'Can upload documents'],
            ['name' => 'hrm.documents.verify', 'display_name' => 'Verify Documents', 'description' => 'Can verify documents'],
            ['name' => 'hrm.document-types.manage', 'display_name' => 'Manage Document Types', 'description' => 'Can manage document types'],
            
            // Onboarding
            ['name' => 'hrm.onboarding.view', 'display_name' => 'View Onboarding', 'description' => 'Can view onboarding processes'],
            ['name' => 'hrm.onboarding.manage', 'display_name' => 'Manage Onboarding', 'description' => 'Can manage onboarding processes'],
            
            // Offboarding
            ['name' => 'hrm.resignations.view', 'display_name' => 'View Resignations', 'description' => 'Can view resignations'],
            ['name' => 'hrm.resignations.manage', 'display_name' => 'Manage Resignations', 'description' => 'Can manage resignation workflow'],
            ['name' => 'hrm.clearances.manage', 'display_name' => 'Manage Clearances', 'description' => 'Can manage clearance checklist'],
            ['name' => 'hrm.settlements.view', 'display_name' => 'View Settlements', 'description' => 'Can view final settlements'],
            ['name' => 'hrm.settlements.manage', 'display_name' => 'Manage Settlements', 'description' => 'Can manage final settlements'],
            
            // Time Tracking
            ['name' => 'hrm.projects.view', 'display_name' => 'View Projects', 'description' => 'Can view projects'],
            ['name' => 'hrm.projects.manage', 'display_name' => 'Manage Projects', 'description' => 'Can create, edit, delete projects'],
            ['name' => 'hrm.time-entries.view', 'display_name' => 'View Time Entries', 'description' => 'Can view time entries'],
            ['name' => 'hrm.time-entries.manage', 'display_name' => 'Manage Time Entries', 'description' => 'Can create and manage time entries'],
            ['name' => 'hrm.timesheets.view', 'display_name' => 'View Timesheets', 'description' => 'Can view timesheets'],
            ['name' => 'hrm.timesheets.approve', 'display_name' => 'Approve Timesheets', 'description' => 'Can approve timesheets'],
            
            // Assets
            ['name' => 'hrm.assets.view', 'display_name' => 'View Assets', 'description' => 'Can view assets'],
            ['name' => 'hrm.assets.manage', 'display_name' => 'Manage Assets', 'description' => 'Can manage assets and assignments'],
            
            // Skills
            ['name' => 'hrm.skills.view', 'display_name' => 'View Skills', 'description' => 'Can view skills'],
            ['name' => 'hrm.skills.manage', 'display_name' => 'Manage Skills', 'description' => 'Can manage skill database'],
            
            // Letters
            ['name' => 'hrm.letters.view', 'display_name' => 'View Letters', 'description' => 'Can view employee letters'],
            ['name' => 'hrm.letters.generate', 'display_name' => 'Generate Letters', 'description' => 'Can generate employee letters'],
            ['name' => 'hrm.letter-templates.manage', 'display_name' => 'Manage Letter Templates', 'description' => 'Can manage letter templates'],
            
            // Reports
            ['name' => 'hrm.reports.view', 'display_name' => 'View Reports', 'description' => 'Can view HRM reports'],
            ['name' => 'hrm.reports.export', 'display_name' => 'Export Reports', 'description' => 'Can export HRM reports'],
            
            // Settings
            ['name' => 'hrm.settings.manage', 'display_name' => 'Manage HRM Settings', 'description' => 'Can manage HRM system settings'],
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'module_id' => $hrmModule->id,
                    'guard_name' => 'web',
                ]
            );
        }

        // Assign all HRM permissions to Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $hrmPermissions = Permission::where('module_id', $hrmModule->id)->pluck('name')->toArray();
            $superAdminRole->givePermissionTo($hrmPermissions);
        }

        // Create HR Manager role with most permissions
        $hrManagerRole = Role::firstOrCreate(['name' => 'HR Manager'], ['guard_name' => 'web']);
        $hrManagerPermissions = Permission::where('module_id', $hrmModule->id)
            ->whereNotIn('name', [
                'hrm.settings.manage',
                'hrm.employees.delete',
            ])
            ->pluck('name')
            ->toArray();
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
            'hrm.documents.view',
            'hrm.documents.upload',
            'hrm.onboarding.view',
            'hrm.onboarding.manage',
        ];
        $hrExecutiveRole->syncPermissions($hrExecutivePermissions);

        // Create Employee role with self-service permissions
        $employeeRole = Role::firstOrCreate(['name' => 'Employee'], ['guard_name' => 'web']);
        $employeePermissions = [
            'hrm.attendance.checkin',
            'hrm.documents.view',
            'hrm.time-entries.view',
            'hrm.time-entries.manage',
            'hrm.timesheets.view',
        ];
        $employeeRole->syncPermissions($employeePermissions);

        $this->command->info('HRM permissions seeded successfully!');
    }
}
