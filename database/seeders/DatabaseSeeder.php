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
            UserSeeder::class,
            PermissionsSeeder::class,
            SettingsSeeder::class,
            \App\Modules\HRM\database\seeders\HRMSeeder::class,
        ]);
    }
}
