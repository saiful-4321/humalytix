<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\Department;
use App\Modules\HRM\Models\Kpi;
use App\Modules\HRM\Models\Okr;
use App\Modules\HRM\Models\PerformanceGoal;
use App\Modules\HRM\Models\Appraisal;
use App\Modules\HRM\Models\Appraisal360;
use App\Modules\HRM\Models\AppraisalReviewer;
use App\Modules\HRM\Models\Competency;
use App\Modules\HRM\Models\Pip;
use App\Modules\HRM\Models\PipActionItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PMSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Ensure Dependencies (Employees, Departments) exist
        $this->ensureDependencies();

        $employees = Employee::limit(20)->get();
        if ($employees->isEmpty()) {
            $this->command->info('No employees found to seed performance data.');
            return;
        }

        $departments = Department::all();

        // 2. Seed Competencies
        $this->seedCompetencies();

        // 3. Seed KPIs (Generic & Departmental)
        $this->seedKPIs($departments);

        // 4. Seed OKRs (Company, Dept, Individual)
        $this->seedOKRs($employees, $departments);

        // 5. Seed Goals
        $this->seedGoals($employees);

        // 6. Seed Standard Appraisals
        $this->seedStandardAppraisals($employees);

        // 7. Seed 360 Appraisals
        $this->seed360Appraisals($employees);

        // 8. Seed PIPs
        $this->seedPIPs($employees);
    }

    private function ensureDependencies()
    {
        if (Department::count() == 0) {
            Department::create(['name' => 'IT Department', 'code' => 'IT']);
            Department::create(['name' => 'HR Department', 'code' => 'HR']);
            Department::create(['name' => 'Sales Department', 'code' => 'SALES']);
        }

        if (Employee::count() == 0) {
            // Create a dummy employee if none exist
            $dept = Department::first();
            Employee::create([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '1234567890',
                'employee_code' => 'DEMO001',
                'department_id' => $dept->id,
                'designation_id' => 1, // Assuming 1 exists or is nullable
                'joining_date' => now(),
                'salary' => 50000,
                'status' => 'active', // Using string literal just in case
            ]);
        }
    }

    private function seedCompetencies()
    {
        $competencies = [
            // Core
            ['name' => 'Communication', 'type' => 'core', 'description' => 'Effectively conveys information and ideas.'],
            ['name' => 'Teamwork', 'type' => 'core', 'description' => 'Works cooperatively with others to achieve group goals.'],
            ['name' => 'Integrity', 'type' => 'core', 'description' => 'Upholds high ethical standards and honesty.'],
            
            // Leadership
            ['name' => 'Strategic Thinking', 'type' => 'leadership', 'description' => 'Develops strategies to achieve organizational goals.'],
            ['name' => 'Team Leadership', 'type' => 'leadership', 'description' => 'Motivates and guides team members.'],
            ['name' => 'Decision Making', 'type' => 'leadership', 'description' => 'Makes timely and effective decisions.'],

            // Functional
            ['name' => 'Technical Proficiency', 'type' => 'functional', 'description' => 'Demonstrates necessary technical skills.'],
            ['name' => 'Project Management', 'type' => 'functional', 'description' => 'Plans and executes projects effectively.'],
            ['name' => 'Data Analysis', 'type' => 'functional', 'description' => 'Interprets data to drive business decisions.'],
        ];

        foreach ($competencies as $comp) {
            Competency::firstOrCreate(
                ['name' => $comp['name']],
                $comp
            );
        }
    }

    private function seedKPIs($departments)
    {
        // Generic KPIs
        $kpis = [
            ['name' => 'Attendance Score', 'type' => 'kpi', 'target_value' => 95, 'measurement_unit' => '%', 'weightage' => 10, 'frequency' => 'monthly'],
            ['name' => 'Policy Compliance', 'type' => 'kpi', 'target_value' => 100, 'measurement_unit' => '%', 'weightage' => 10, 'frequency' => 'annually'],
        ];

        foreach ($kpis as $kpi) {
            Kpi::updateOrCreate(['name' => $kpi['name']], $kpi);
        }

        // Department Specific KPIs
        foreach ($departments as $dept) {
            $deptKpiName = "{$dept->name} Efficiency";
            Kpi::updateOrCreate(
                ['name' => $deptKpiName, 'department_id' => $dept->id],
                [
                    'type' => 'kpi',
                    'target_value' => 90,
                    'measurement_unit' => '%',
                    'weightage' => 20,
                    'frequency' => 'monthly',
                    'description' => "Operational efficiency for {$dept->name}"
                ]
            );
        }
    }

    private function seedOKRs($employees, $departments)
    {
        // Company Level
        $companyOkr = Okr::create([
            'title' => 'Expand Market Reach ' . date('Y'),
            'description' => 'Increase global footprint and market share.',
            'level' => 'company',
            'start_date' => Carbon::now()->startOfYear(),
            'end_date' => Carbon::now()->endOfYear(),
            'year' => date('Y'),
            'status' => 'active',
            'progress' => 45,
        ]);

        // Individual Level
        foreach ($employees->take(5) as $emp) {
            Okr::create([
                'title' => 'Personal Development Q' . ceil(date('n')/3),
                'description' => 'Improve key skills and certifications',
                'level' => 'individual',
                'employee_id' => $emp->id,
                'start_date' => Carbon::now()->startOfQuarter(),
                'end_date' => Carbon::now()->endOfQuarter(),
                'year' => date('Y'),
                'quarter' => 'Q' . ceil(date('n')/3),
                'status' => 'active',
                'progress' => rand(10, 80),
            ]);
        }
    }

    private function seedGoals($employees)
    {
        foreach ($employees->take(10) as $emp) {
            PerformanceGoal::create([
                'employee_id' => $emp->id,
                'title' => 'Complete Advanced Training',
                'description' => 'Finish the advanced certification course.',
                'start_date' => Carbon::now()->subDays(15),
                'due_date' => Carbon::now()->addDays(15),
                'priority' => rand(0, 1) ? 'high' : 'medium',
                'status' => rand(0, 1) ? 'in_progress' : 'not_started',
                'progress' => rand(0, 60),
            ]);
        }
    }

    private function seedStandardAppraisals($employees)
    {
        foreach ($employees->take(3) as $emp) {
            Appraisal::create([
                'employee_id' => $emp->id,
                'review_date' => Carbon::now()->subMonth(),
                'review_period' => Carbon::now()->subYear()->format('Y') . ' - ' . Carbon::now()->format('Y'),
                'reviewer_id' => $employees->last()->id,
                'status' => 'completed',
                'performance_score' => rand(3, 5),
                'strengths' => 'Consistent performance, good team player.',
                'weaknesses' => 'Need to improve on public speaking.',
                'goals' => 'Achieve 100% target in next quarter.',
                'comments' => 'Solid performance throughout the year.',
            ]);
        }
    }

    private function seed360Appraisals($employees)
    {
        if ($employees->count() < 3) return;

        foreach ($employees->take(2) as $emp) {
            $appraisal = Appraisal360::create([
                'employee_id' => $emp->id,
                'appraisal_name' => 'Annual 360 Review ' . date('Y'),
                'review_period' => date('Y'),
                'start_date' => Carbon::now()->subWeek(),
                'end_date' => Carbon::now()->addWeek(),
                'status' => 'in_progress',
                'created_by' => 1, // Assuming admin ID 1
            ]);

            // Add reviewers
            // Self
            AppraisalReviewer::create([
                'appraisal_id' => $appraisal->id,
                'reviewer_id' => $emp->id,
                'reviewer_type' => 'self',
                'status' => 'completed',
                'rating' => 4,
                'feedback' => 'I met most of my goals.'
            ]);

            // Peer
            $peer = $employees->where('id', '!=', $emp->id)->first();
            if ($peer) {
                AppraisalReviewer::create([
                    'appraisal_id' => $appraisal->id,
                    'reviewer_id' => $peer->id,
                    'reviewer_type' => 'peer',
                    'status' => 'pending',
                ]);
            }
        }
    }

    private function seedPIPs($employees)
    {
        if ($employees->isEmpty()) return;
        
        $emp = $employees->first();
        $manager = $employees->last();

        $pip = Pip::create([
            'employee_id' => $emp->id,
            'manager_id' => $manager->id,
            'title' => 'Performance Improvement Plan - Q3',
            'reason' => 'Consistently missing deadlines.',
            'start_date' => Carbon::now()->subDays(10),
            'end_date' => Carbon::now()->addDays(20),
            'status' => 'active',
            'success_criteria' => 'Zero late submissions for 4 weeks.',
        ]);

        PipActionItem::create([
            'pip_id' => $pip->id,
            'action_required' => 'Submit all weekly reports on time',
            'expected_outcome' => 'No delays in reporting',
            'due_date' => Carbon::now()->addDays(7),
            'status' => 'pending',
        ]);
    }
}
