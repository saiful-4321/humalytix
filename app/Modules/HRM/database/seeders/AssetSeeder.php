<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Modules\Main\Models\Module;
use App\Modules\HRM\Models\AssetCategory;
use Illuminate\Support\Facades\DB;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create permissions
        $module = Module::firstOrCreate(['name' => 'Asset Management'], ['status' => 1]);

        $permissions = [
            'hrm.assets.view',
            'hrm.assets.create',
            'hrm.assets.edit',
            'hrm.assets.delete',
            'hrm.assets.assign',
            'hrm.assets.return',
            'hrm.assets.report',
            'hrm.assets.settings',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                [
                    'guard_name' => 'web',
                    'module_id' => $module->id,
                ]
            );
        }

        // Assign to Super Admin
        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $role->givePermissionTo($permissions);

        // 2. Default Asset Categories
        $categories = [
            ['name' => 'Laptops', 'description' => 'Business laptops and accessories', 'depreciation_rate' => 20, 'useful_life_years' => 5],
            ['name' => 'Desktops', 'description' => 'Workstations and monitors', 'depreciation_rate' => 15, 'useful_life_years' => 7],
            ['name' => 'Mobiles', 'description' => 'Company issued phones', 'depreciation_rate' => 25, 'useful_life_years' => 4],
            ['name' => 'Furniture', 'description' => 'Chairs, desks, cabinets', 'depreciation_rate' => 10, 'useful_life_years' => 10],
            ['name' => 'Vehicles', 'description' => 'Cars, bikes', 'depreciation_rate' => 10, 'useful_life_years' => 10],
        ];

        foreach ($categories as $cat) {
            AssetCategory::firstOrCreate(
                ['name' => $cat['name']],
                $cat
            );
        }

        // 3. Create Real Assets
        $laptopCat = AssetCategory::where('name', 'Laptops')->first();
        $furnitureCat = AssetCategory::where('name', 'Furniture')->first();
        $vehicleCat = AssetCategory::where('name', 'Vehicles')->first();

        $assetsData = [
            ['name' => 'MacBook Pro M2', 'code' => 'AST-LAP-001', 'asset_category_id' => $laptopCat->id, 'purchase_cost' => 120000, 'purchase_date' => now()->subMonths(6), 'status' => 'assigned', 'condition' => 'good'],
            ['name' => 'MacBook Air M1', 'code' => 'AST-LAP-002', 'asset_category_id' => $laptopCat->id, 'purchase_cost' => 95000, 'purchase_date' => now()->subMonths(12), 'status' => 'available', 'condition' => 'good'],
            ['name' => 'Dell XPS 15', 'code' => 'AST-LAP-003', 'asset_category_id' => $laptopCat->id, 'purchase_cost' => 140000, 'purchase_date' => now()->subMonths(3), 'status' => 'assigned', 'condition' => 'new'],
            ['name' => 'Herman Miller Sayl', 'code' => 'AST-FUR-001', 'asset_category_id' => $furnitureCat->id, 'purchase_cost' => 45000, 'purchase_date' => now()->subMonths(24), 'status' => 'assigned', 'condition' => 'fair'],
            ['name' => 'Herman Miller Aeron', 'code' => 'AST-FUR-002', 'asset_category_id' => $furnitureCat->id, 'purchase_cost' => 85000, 'purchase_date' => now()->subMonths(18), 'status' => 'available', 'condition' => 'good'],
            ['name' => 'Toyota Corolla 2022', 'code' => 'AST-VEH-001', 'asset_category_id' => $vehicleCat->id, 'purchase_cost' => 2500000, 'purchase_date' => now()->subMonths(12), 'status' => 'assigned', 'condition' => 'good'],
        ];

        foreach ($assetsData as $data) {
            \App\Modules\HRM\Models\Asset::firstOrCreate(
                ['code' => $data['code']],
                $data
            );
        }

        // 4. Assign Assets
        $assets = \App\Modules\HRM\Models\Asset::where('status', 'assigned')->get();
        $employees = \App\Modules\HRM\Models\Employee::inRandomOrder()->limit($assets->count())->get();

        if ($employees->count() > 0) {
            foreach ($assets as $index => $asset) {
                if (isset($employees[$index])) {
                    \App\Modules\HRM\Models\AssetAssignment::create([
                        'asset_id' => $asset->id,
                        'employee_id' => $employees[$index]->id,
                        'assigned_by' => 1, // Admin
                        'assigned_date' => now()->subMonths(rand(1, 5)),
                        'assigned_condition' => $asset->condition,
                        'notes' => 'Initial assignment',
                    ]);
                }
            }
        }
    }
}
