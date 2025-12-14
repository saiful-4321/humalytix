<?php

namespace App\Modules\HRM\database\seeders;

use Illuminate\Database\Seeder;

class HRMSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            HRMPermissionSeeder::class,
            PerfectPayrollSeeder::class, // Replaces HRMDemoDataSeeder and includes CompletePayrollSeeder
            SkillSeeder::class,
        ]);
    }
}
