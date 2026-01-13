<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\HRM\database\seeders\HRMMediumDataSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core System Seeders
            UserSeeder::class,
            PermissionsSeeder::class,
            SettingsSeeder::class,
            
            // HRM Module Seeders
            \App\Modules\HRM\database\seeders\HRMPermissionSeeder::class, // HRM Permissions first
            \App\Modules\HRM\database\seeders\HRMRoleSeeder::class, // Then HRM Roles
            \App\Modules\HRM\database\seeders\HRMSeeder::class, // Then all HRM data

            // Finance Module Seeders
            \App\Modules\Finance\database\seeders\FinancePermissionSeeder::class,
            \App\Modules\Finance\database\seeders\FinanceSeeder::class,
        ]);
        
        $this->command->info('✅ All seeders completed successfully!');
    }
}
