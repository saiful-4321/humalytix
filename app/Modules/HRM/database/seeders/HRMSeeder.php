<?php

namespace App\Modules\HRM\database\seeders;

use Illuminate\Database\Seeder;
use App\Modules\HRM\Database\Seeders\PerfectPayrollSeeder;
use App\Modules\HRM\Database\Seeders\PMSSeeder;

class HRMSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            HRMPermissionSeeder::class,
            PerfectPayrollSeeder::class,
            SkillSeeder::class,
            PMSSeeder::class,
        ]);
    }
}
