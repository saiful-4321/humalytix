<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HRMSettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'group' => 'leave',
                'name' => 'weekly_holidays',
                'payload' => json_encode(['Friday']), // Default BD Weekend
                'is_system' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'group' => 'system',
                'name' => 'fiscal_year_start',
                'payload' => json_encode(['month' => 7, 'day' => 1]), // July 1st
                'is_system' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        foreach ($settings as $setting) {
            DB::table('hrm_settings')->updateOrInsert(
                ['name' => $setting['name']],
                $setting
            );
        }
    }
}
