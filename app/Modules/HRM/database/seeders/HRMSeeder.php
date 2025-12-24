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
            \App\Modules\HRM\Database\Seeders\HRMDemoDataSeeder::class, // Core data (Employees, Depts)
            
            // Core Modules
            CompleteLeaveSeeder::class,
            PMSSeeder::class,
            ExpenseSeeder::class,
            AssetSeeder::class,
            \App\Modules\HRM\Database\Seeders\TrainingSeeder::class, // L&D
            \App\Modules\HRM\Database\Seeders\ComplianceSeeder::class, // Compliance
            \App\Modules\HRM\Database\Seeders\RecruitmentSeeder::class, // Recruitment
            \App\Modules\HRM\Database\Seeders\WorkflowSeeder::class, // Workflows
            \App\Modules\HRM\Database\Seeders\ShiftRosterSeeder::class, // Shifts & Rosters
            \App\Modules\HRM\Database\Seeders\NotificationSeeder::class, // Notifications
        ]);
    }
}
