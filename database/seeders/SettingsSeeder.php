<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Main\Models\Permission;
use App\Modules\Main\Models\Role;
use App\Modules\Main\Models\Module;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Settings Module
        $module = Module::firstOrCreate(['name' => 'Settings'], ['status' => 1]);

        $permissions = [
            'settings-list',
            'company-settings-update',
            'theme-settings-update',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'module_id' => $module->id
            ]);
        }

        // Assign permissions to Super Admin (implicitly done via Gate in AppServiceProvider, but let's assign to Admin too if needed, or just ensure they exist)
        // The AppServiceProvider grants Super Admin all permissions.
        // Let's assign to Admin role as well for now, or just leave it as created.
        // The previous seeder assigned to Admin. Let's do that.
        
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }
    }
}
