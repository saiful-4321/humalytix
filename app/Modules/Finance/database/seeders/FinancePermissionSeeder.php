<?php

namespace App\Modules\Finance\database\seeders;

use Illuminate\Database\Seeder;
use App\Modules\Main\Models\Permission;
use App\Modules\Main\Models\Module;
use Spatie\Permission\Models\Role;

class FinancePermissionSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Finance Module
        $module = Module::firstOrCreate(
            ['name' => 'Finance Management'],
            ['status' => 1]
        );

        // 2. Finance Permissions
        $permissions = [
            'finance-module', // Access to the module
            'finance-dashboard',
            
            // Chart of Accounts
            'finance-coa-list',
            'finance-coa-create',
            'finance-coa-edit',
            'finance-coa-delete',
            
            // Journals
            'finance-journal-list',
            'finance-journal-create',
            'finance-journal-edit',
            'finance-journal-delete',
            'finance-journal-approve',
            
            // Reports
            'finance-report-trial-balance',
            'finance-report-balance-sheet',
            'finance-report-pl',
            
            // Settings
            'finance-settings-manage',
        ];

        // 3. Create Permissions
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                [
                    'guard_name' => 'web',
                    'module_id' => $module->id
                ]
            );
        }

        // 4. Assign to Super Admin
        $role = Role::where('name', 'Super Admin')->first();
        if ($role) {
            $role->givePermissionTo($permissions);
        }
    }
}
