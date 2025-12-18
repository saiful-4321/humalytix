<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\HRM\Models\Training;
use App\Modules\HRM\Models\TrainingSession;
use App\Modules\HRM\Models\TrainingParticipant;
use App\Modules\HRM\Models\Certification;
use App\Modules\HRM\Models\Employee;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Modules\Main\Models\Module;

class TrainingSeeder extends Seeder
{
    public function run()
    {
        // 0. Permission Setup
        $module = Module::firstOrCreate(['name' => 'Training & Development'], ['status' => 1]);
        $permissions = [
            'hrm.trainings.view',
            'hrm.trainings.create',
            'hrm.trainings.edit',
            'hrm.trainings.delete',
            'hrm.certifications.view',
            'hrm.certifications.create',
            'hrm.certifications.edit',
            'hrm.certifications.delete',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                ['module_id' => $module->id]
            );
        }

        $adminRole = Role::where('name', 'Super Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        // 1. Create Trainings
        $trainings = [
            [
                'code' => 'TRN-LEA-101',
                'title' => 'Leadership 101: Leading with Empathy',
                'description' => 'Core leadership principles for new managers.',
                'trainer' => 'John Maxwell (External)',
                'type' => 'external',
                'duration_hours' => 8,
            ],
            [
                'code' => 'TRN-DEV-202',
                'title' => 'Advanced Laravel Architecture',
                'description' => 'Deep dive into service providers, patterns, and optimization.',
                'trainer' => 'Tanvir Ahmed (Internal)',
                'type' => 'internal',
                'duration_hours' => 12,
            ],
            [
                'code' => 'TRN-SAF-001',
                'title' => 'Workplace Safety & Compliance',
                'description' => 'Mandatory safety training for all operations staff.',
                'trainer' => 'Safety Officer',
                'type' => 'internal',
                'duration_hours' => 4,
            ],
            [
                'code' => 'TRN-COR-105',
                'title' => 'Effective Communication Skills',
                'description' => 'Improving verbal and written communication within teams.',
                'trainer' => 'HR Department',
                'type' => 'internal',
                'duration_hours' => 6,
            ],
        ];

        foreach ($trainings as $t) {
            Training::firstOrCreate(['code' => $t['code']], $t);
        }

        // 2. Create Sessions & Enrollments
        $laravelTraining = Training::where('code', 'TRN-DEV-202')->first();
        $leadershipTraining = Training::where('code', 'TRN-LEA-101')->first();

        // Session 1: Completed Laravel Training
        $session1 = TrainingSession::create([
            'training_id' => $laravelTraining->id,
            'start_date' => now()->subMonths(1)->setTime(10, 0),
            'end_date' => now()->subMonths(1)->addDays(2)->setTime(16, 0),
            'location' => 'Conference Room B',
            'status' => 'completed',
            'max_participants' => 10,
        ]);

        // Enroll Developers
        $devs = Employee::whereHas('department', function($q) {
            $q->where('code', 'like', '%IT%');
        })->take(5)->get();

        foreach ($devs as $dev) {
            TrainingParticipant::create([
                'training_session_id' => $session1->id,
                'employee_id' => $dev->id,
                'status' => 'completed',
                'completion_date' => $session1->end_date,
                'score' => rand(85, 98),
                'feedback' => 'Excellent deep dive!',
            ]);
        }

        // Session 2: Upcoming Leadership Training
        $session2 = TrainingSession::create([
            'training_id' => $leadershipTraining->id,
            'start_date' => now()->addWeeks(2)->setTime(9, 0),
            'end_date' => now()->addWeeks(2)->setTime(17, 0),
            'location' => 'Grand Ballroom, Hotel Sarina',
            'status' => 'scheduled',
            'max_participants' => 15,
        ]);

        // Enroll Managers
        $managers = Employee::inRandomOrder()->take(4)->get();
        foreach ($managers as $mgr) {
            TrainingParticipant::create([
                'training_session_id' => $session2->id,
                'employee_id' => $mgr->id,
                'status' => 'enrolled',
            ]);
        }

        // 3. Create Certifications
        $employees = Employee::inRandomOrder()->take(10)->get();
        foreach ($employees as $emp) {
            // Chance to have a certification
            if (rand(0, 1)) {
                Certification::create([
                    'employee_id' => $emp->id,
                    'name' => 'Project Management Professional (PMP)',
                    'issuing_organization' => 'PMI',
                    'issue_date' => now()->subYears(rand(1, 3)),
                    'expiry_date' => now()->addYears(rand(1, 2)),
                    'credential_id' => 'PMP-' . rand(10000, 99999),
                ]);
            }
            if (rand(0, 1)) {
                 Certification::create([
                    'employee_id' => $emp->id,
                    'name' => 'AWS Certified Solutions Architect',
                    'issuing_organization' => 'Amazon Web Services',
                    'issue_date' => now()->subMonths(rand(6, 18)),
                    'expiry_date' => now()->addYears(2),
                    'credential_url' => 'https://aws.amazon.com/verify?id=' . rand(100000, 999999),
                ]);
            }
        }
    }
}
