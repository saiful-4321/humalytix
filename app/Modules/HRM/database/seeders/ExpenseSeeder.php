<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Modules\Main\Models\Module; // Corrected namespace
use Illuminate\Support\Facades\DB;

class ExpenseSeeder extends Seeder
{
    public function run()
    {
        // 0. Create Module
        $module = Module::firstOrCreate(['name' => 'Expense Management'], ['name' => 'Expense Management']);

        // 1. Create Permissions
        $permissions = [
            'hrm.expenses.view',
            'hrm.expenses.create',
            'hrm.expenses.edit',
            'hrm.expenses.delete',
            'hrm.expenses.approve', // Manager/Admin approval
            'hrm.expenses.pay',     // Finance/Admin pay/reimburse
            'hrm.expenses.settings', // Manage categories
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                ['module_id' => $module->id]
            );
        }

        // 2. Assign Permissions to Admin Role
        $adminRole = Role::where('name', 'Super Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        // 3. Create Default Expense Categories
        $categories = [
            [
                'name' => 'Travel & Accommodation',
                'description' => 'Flight tickets, hotel stays, train/bus fares.',
                'created_at' => now(), 'updated_at' => now(),
                'is_active' => true
            ],
            [
                'name' => 'Meals & Entertainment',
                'description' => 'Client dinners, team lunches, food during travel.',
                'created_at' => now(), 'updated_at' => now(),
                'is_active' => true
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Stationery, electronics, software subscriptions.',
                'created_at' => now(), 'updated_at' => now(),
                'is_active' => true
            ],
            [
                'name' => 'Fuel & Mileage',
                'description' => 'Fuel reimbursement for business travel.',
                'created_at' => now(), 'updated_at' => now(),
                'is_active' => true
            ],
             [
                'name' => 'Internet & Phone',
                'description' => 'Reimbursement for work-related communication costs.',
                'created_at' => now(), 'updated_at' => now(),
                'is_active' => true
            ],
            [
                'name' => 'Other',
                'description' => 'Miscellaneous expenses.',
                'created_at' => now(), 'updated_at' => now(),
                'is_active' => true
            ],
        ];

        DB::table('hrm_expense_categories')->insertOrIgnore($categories);
    }
}
