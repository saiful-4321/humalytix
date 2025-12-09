<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\HRM\Models\OTPolicy;
use App\Modules\HRM\Models\BonusType;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdvancedPayrollSeeder extends Seeder
{
    public function run(): void
    {
        $hrmModule = \App\Modules\Main\Models\Module::where('name', 'HRM')->first();
        
        // Overtime Permissions
        $otPermissions = [
            'hrm.ot-policies.view',
            'hrm.ot-policies.create',
            'hrm.ot-policies.update',
            'hrm.ot-policies.delete',
            'hrm.overtime.view',
            'hrm.overtime.create',
            'hrm.overtime.update',
            'hrm.overtime.delete',
            'hrm.overtime.approve',
        ];

        // Bonus Permissions
        $bonusPermissions = [
            'hrm.bonus-types.view',
            'hrm.bonus-types.create',
            'hrm.bonus-types.update',
            'hrm.bonus-types.delete',
            'hrm.bonuses.view',
            'hrm.bonuses.create',
            'hrm.bonuses.update',
            'hrm.bonuses.delete',
            'hrm.bonuses.approve',
        ];

        $allPermissions = array_merge($otPermissions, $bonusPermissions);

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['module_id' => $hrmModule?->id]
            );
        }

        // Assign to Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo($allPermissions);

        // Sample OT Policies
        $otPolicies = [
            [
                'name' => 'Standard OT Policy',
                'description' => 'Default overtime policy with standard multipliers',
                'calculation_basis' => 'hourly_rate',
                'multiplier' => 1.5,
                'weekend_multiplier' => 2.0,
                'holiday_multiplier' => 2.5,
                'night_shift_multiplier' => 1.25,
                'night_shift_start' => '22:00:00',
                'night_shift_end' => '06:00:00',
                'min_ot_minutes' => 30,
                'max_ot_hours_per_day' => 4,
                'max_ot_hours_per_month' => 60,
                'is_active' => true,
            ],
        ];

        foreach ($otPolicies as $policy) {
            OTPolicy::firstOrCreate(
                ['name' => $policy['name']],
                $policy
            );
        }

        // Sample Bonus Types
        $bonusTypes = [
            [
                'name' => 'Festival Bonus',
                'description' => 'Bonus given during festivals (Eid, etc.)',
                'calculation_type' => 'percentage',
                'default_percentage' => 50.00, // 50% of basic
                'frequency' => 'yearly',
                'is_taxable' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Performance Bonus',
                'description' => 'Bonus based on performance rating',
                'calculation_type' => 'performance_based',
                'frequency' => 'yearly',
                'is_taxable' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Joining Bonus',
                'description' => 'One-time bonus on joining',
                'calculation_type' => 'fixed',
                'default_amount' => 10000.00,
                'frequency' => 'one_time',
                'is_taxable' => true,
                'is_active' => true,
            ],
        ];

        foreach ($bonusTypes as $type) {
            BonusType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }

        $this->command->info('Advanced Payroll features seeded successfully!');
    }
}
