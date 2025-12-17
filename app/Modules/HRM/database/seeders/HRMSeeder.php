<?php

namespace App\Modules\HRM\database\seeders;

use Illuminate\Database\Seeder;
use App\Modules\HRM\Database\Seeders\PerfectPayrollSeeder;
use App\Modules\HRM\Database\Seeders\PMSSeeder;
use App\Modules\HRM\Database\Seeders\CompleteLeaveSeeder;
use App\Modules\HRM\Database\Seeders\HRMSettingSeeder; // Added this line
use App\Modules\HRM\Database\Seeders\ExpenseSeeder; // Added this line

class HRMSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            HRMSettingSeeder::class,
            CompleteLeaveSeeder::class,
            PMSSeeder::class,
            ExpenseSeeder::class,
        ]);
    }
}
