<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Modules\HRM\Models\Competency;
use App\Modules\HRM\Models\Kpi;
use App\Modules\HRM\Models\PerformanceGoal;
use App\Modules\HRM\Models\Okr;
use App\Modules\HRM\Models\OkrKeyResult;
use App\Modules\HRM\Models\Appraisal360;
use App\Modules\HRM\Models\AppraisalReviewer;
use App\Modules\HRM\Models\Pip;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\Department;
use Log;

class PMSSeeder extends Seeder
{
    public function run()
    {
        $this->seedPermissions();
        
        // Ensure we have some employees to attach data to
        $employees = Employee::limit(10)->get();
        $departments = Department::limit(5)->get();
        
        if ($employees->isEmpty()) {
            $this->command->info('No employees found. Seed employees first.');
            return;
        }

        $this->seedCompetencies();
        $this->seedKpis($departments);
        $this->seedOkrs($employees, $departments);
        $this->seedGoals($employees);
        $this->seedAppraisals($employees);
        $this->seedPips($employees);

        $this->command->info('PMS Seeding Completed Successfully!');
    }

    private function seedPermissions()
    {
        // Get HRM Module
        $hrmModule = \App\Modules\Main\Models\Module::firstOrCreate(
            ['name' => 'HRM'],
            ['status' => 1]
        );

        $permissions = [
            'hrm.performance.view',
            'hrm.performance.create',
            'hrm.performance.edit',
            'hrm.performance.delete',
            'hrm.pips.view',
            'hrm.pips.create',
            'hrm.pips.edit',
            'hrm.pips.delete',
        ];

        foreach ($permissions as $permissionName) {
            \App\Modules\Main\Models\Permission::firstOrCreate(
                ['name' => $permissionName],
                [
                    'module_id' => $hrmModule->id,
                    'guard_name' => 'web',
                ]
            );
        }

        // Assign to Super Admin
        $role = Role::where('name', 'Super Admin')->first();
        if ($role) {
            $role->givePermissionTo($permissions);
        }
        
        $this->command->info('Permissions seeded.');
    }

    private function seedCompetencies()
    {
        $competencies = [
            [
                'name' => 'Communication',
                'description' => 'Effectively conveys information and ideas.',
                'type' => 'core'
            ],
            [
                'name' => 'Teamwork',
                'description' => 'Works collaboratively with others to achieve group goals.',
                'type' => 'core'
            ],
            [
                'name' => 'Problem Solving',
                'description' => 'Identifies problems and implements effective solutions.',
                'type' => 'functional'
            ],
            [
                'name' => 'Strategic Thinking',
                'description' => 'Understands the big picture and aligns actions with long-term goals.',
                'type' => 'leadership'
            ],
            [
                'name' => 'Leadership',
                'description' => 'Inspires and motivates others to perform their best.',
                'type' => 'leadership'
            ]
        ];

        foreach ($competencies as $data) {
            Competency::firstOrCreate(['name' => $data['name']], $data);
        }
        $this->command->info('Competencies seeded.');
    }

    private function seedKpis($departments)
    {
        foreach ($departments as $dept) {
            Kpi::create([
                'name' => 'Monthly Revenue Target - ' . $dept->name,
                'description' => 'Achieve monthly revenue target for the department.',
                'type' => 'kpi',
                'measurement_unit' => 'USD',
                'target_value' => rand(10000, 50000),
                'weightage' => 30,
                'frequency' => 'monthly',
                'department_id' => $dept->id,
                'is_active' => true,
            ]);
        }

        Kpi::create([
            'name' => 'Customer Satisfaction Score',
            'description' => 'Maintain high CSAT score.',
            'type' => 'kra',
            'measurement_unit' => 'Points',
            'target_value' => 4.5,
            'weightage' => 20,
            'frequency' => 'quarterly',
            'is_active' => true,
        ]);
        
        $this->command->info('KPIs seeded.');
    }

    private function seedOkrs($employees, $departments)
    {
        // Company Level OKR
        $okr = Okr::create([
            'title' => 'Expand Market Share in Asia',
            'description' => 'Increase our footprint in the Asian market through strategic partnerships.',
            'start_date' => now()->startOfQuarter(),
            'end_date' => now()->endOfQuarter(),
            'quarter' => 'Q' . ceil(now()->month / 3),
            'year' => now()->year,
            'level' => 'company',
            'status' => 'active',
        ]);
        
        OkrKeyResult::create([
            'okr_id' => $okr->id,
            'description' => 'Establish 5 new partnerships in Japan',
            'measurement_unit' => 'Partnerships',
            'target_value' => 5,
            'current_value' => 2,
            'weightage' => 40,
        ]);
        
        OkrKeyResult::create([
            'okr_id' => $okr->id,
            'description' => 'Achieve $1M revenue from Asian region',
            'measurement_unit' => 'USD',
            'target_value' => 1000000,
            'current_value' => 350000,
            'weightage' => 60,
        ]);

        // Individual OKR
        if ($employees->isNotEmpty()) {
            $emp = $employees->first();
            $empOkr = Okr::create([
                'title' => 'Master New Tech Stack',
                'description' => 'Become proficient in the new framework.',
                'start_date' => now()->startOfQuarter(),
                'end_date' => now()->endOfQuarter(),
                'quarter' => 'Q' . ceil(now()->month / 3),
                'year' => now()->year,
                'level' => 'individual',
                'employee_id' => $emp->id,
                'status' => 'active',
            ]);

            OkrKeyResult::create([
                'okr_id' => $empOkr->id,
                'description' => 'Complete Advanced Certification',
                'measurement_unit' => '%',
                'target_value' => 100,
                'current_value' => 50,
                'weightage' => 100,
            ]);
        }
        
        $this->command->info('OKRs seeded.');
    }

    private function seedGoals($employees)
    {
        foreach ($employees->take(5) as $emp) {
            PerformanceGoal::create([
                'employee_id' => $emp->id,
                'title' => 'Improve Code Quality',
                'description' => 'Reduce bugs by 20% and improve test coverage.',
                'start_date' => now(),
                'due_date' => now()->addMonths(3),
                'priority' => 'high',
                'status' => 'in_progress',
                'progress' => rand(10, 80),
            ]);
        }
        $this->command->info('Performance Goals seeded.');
    }

    private function seedAppraisals($employees)
    {
        if ($employees->count() < 2) return;

        $targetEmp = $employees[0];
        $reviewerEmp = $employees[1];

        $appraisal = Appraisal360::create([
            'employee_id' => $targetEmp->id,
            'appraisal_name' => 'Annual Review ' . now()->year,
            'review_period' => 'Annual ' . now()->year,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'status' => 'in_progress',
        ]);

        AppraisalReviewer::create([
            'appraisal_id' => $appraisal->id,
            'reviewer_id' => $reviewerEmp->id,
            'reviewer_type' => 'peer',
            'status' => 'pending',
        ]);
        
        $this->command->info('360 Appraisals seeded.');
    }

    private function seedPips($employees)
    {
        if ($employees->count() < 2) return;

        $emp = $employees->last();
        $manager = $employees->first();

        \App\Modules\HRM\Models\Pip::create([
            'employee_id' => $emp->id,
            'manager_id' => $manager->id,
            'title' => 'Attendance Improvement Plan',
            'reason' => 'Consistent missed deadlines',
            'success_criteria' => 'Submit all weekly reports on time for 4 weeks.',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        $this->command->info('PIPs seeded.');
    }
}
