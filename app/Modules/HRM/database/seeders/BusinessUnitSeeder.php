<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Modules\HRM\Models\BusinessUnit;
use App\Modules\HRM\Models\Employee;

class BusinessUnitSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure there's at least one employee for the head position if needed, or leave null
        $head = Employee::first();
        $headId = $head ? $head->id : null;

        $units = [
            [
                'name' => 'IT Services',
                'code' => 'BU-IT-001',
                'description' => 'Information Technology Services Division',
                'head_employee_id' => $headId,
                'is_active' => true,
            ],
            [
                'name' => 'Marketing & Sales',
                'code' => 'BU-MKT-002',
                'description' => 'Global Marketing and Sales Division',
                'head_employee_id' => $headId,
                'is_active' => true,
            ],
            [
                'name' => 'Research & Development',
                'code' => 'BU-RD-003',
                'description' => 'Product Innovation and R&D',
                'head_employee_id' => $headId,
                'is_active' => true,
            ],
            [
                'name' => 'Customer Operations',
                'code' => 'BU-OPS-004',
                'description' => 'Customer Support and Operations',
                'head_employee_id' => $headId,
                'is_active' => true,
            ],
            [
                'name' => 'Finance & Legal',
                'code' => 'BU-FIN-005',
                'description' => 'Finance, Accounting and Legal Affairs',
                'head_employee_id' => $headId,
                'is_active' => true,
            ],
        ];

        foreach ($units as $unit) {
            BusinessUnit::updateOrCreate(
                ['code' => $unit['code']],
                $unit
            );
        }
    }
}
