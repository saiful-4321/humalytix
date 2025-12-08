<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Main\Models\Permission;
use App\Modules\Main\Models\Module;
use App\Modules\Main\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            // Clear existing roles and permissions
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Role::truncate();
            Permission::truncate();
            Module::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Create roles
            $roles = ['Super Admin', 'Admin'];
            foreach ($roles as $role) {
                Role::firstOrCreate(['name' => $role]);
            }

            // Insert data into the permission_module table
            Module::insert([
                ['id' => 1, 'name' => 'Access Control', 'status' => 1, 'created_at' => now()],
                ['id' => 2, 'name' => 'User', 'status' => 1, 'created_at' => now()],
                ['id' => 3, 'name' => 'Logs', 'status' => 1, 'created_at' => now()],
                ['id' => 4, 'name' => 'System', 'status' => 1, 'created_at' => now()],
                ['id' => 5, 'name' => 'Settings', 'status' => 1, 'created_at' => now()],
                ['id' => 6, 'name' => 'Documentation', 'status' => 1, 'created_at' => now()],
            ]);

            // Define permissions for different modules
            $permissions = [
                // Permissions for module_id 1
                [
                    'module_id' => 1,
                    'permissions' => [
                        'module-list', 'module-create', 'module-update',
                        'permission-list', 'permission-create', 'permission-update',
                        'role-list', 'role-create', 'role-update'
                    ]
                ],
                // Permissions for module_id 2
                [
                    'module_id' => 2,
                    'permissions' => [
                        'user-profile-edit', 'user-change-password',
                        'user-list', 'user-create', 'user-update', 'user-delete',
                        'user-reset-password',
                        // 'user-whitelisted-ip', 'user-whitelisted-ip-create', 'user-whitelisted-ip-update',
                        'user-pretend-login', 'user-session'
                    ]
                ],
                // Permissions for module_id 3
                [
                    'module_id' => 3,
                    'permissions' => ['log-viewer', 'activity-log']
                ],
                // Permissions for module_id 4
                [
                    'module_id' => 4,
                    'permissions' => ['backup-management', 'cache-management', 'system-health']
                ],
                // Permissions for module_id 5
                [
                    'module_id' => 5,
                    'permissions' => ['company-settings-update', 'theme-settings-update', 'schedule-settings-update']
                ],
                // Permissions for module_id 6
                [
                    'module_id' => 6,
                    'permissions' => ['api-documentation']
                ],
            ];

            // Assign permissions to the admin role
            $adminRole = Role::where('name', 'Admin')->first();
            foreach ($permissions as $permissionModule) {
                foreach ($permissionModule['permissions'] as $permissionName) {
                    $permission = Permission::firstOrCreate([
                        'name' => $permissionName,
                        'module_id' => $permissionModule['module_id']
                    ]);
                    $adminRole->givePermissionTo($permission);
                }
            }

            // Assign superadmin roles to a the first
            $user = User::first();
            $user->assignRole("Super Admin");

            $user = User::skip(1)->first();
            $user->assignRole("Admin");
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
