<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\HRM\Models\Job;
use App\Modules\HRM\Models\Candidate;
use App\Modules\HRM\Models\Interview;
use App\Modules\HRM\Models\OfferLetter;
use App\Modules\HRM\Models\Department;
use App\Modules\HRM\Models\Employee;
use Illuminate\Support\Facades\DB;

class RecruitmentSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Jobs
        $departments = Department::all();
        if ($departments->isEmpty()) return;

        $jobs = [
            [
                'title' => 'Senior Laravel Developer',
                'department_id' => $departments->first()->id,
                'description' => 'Looking for an experienced developer to lead our backend team.',
                'requirements' => '5+ years PHP, Laravel, MySQL.',
                'vacancies' => 2,
                'status' => 'active', // Changed 'published' to 'active' or keep 'published' if enum allows match migration default? Migration says 'status' default 'draft', enum comments: draft, active, closed
                'is_published' => true,
                'location' => 'Dhaka, Bangladesh',
                'employment_type' => 'Full Time',
                'salary_range_min' => 80000,
                'salary_range_max' => 120000,
                'posted_date' => now()->subDays(10),
                'closing_date' => now()->addDays(20),
                'code' => 'JOB-' . rand(1000, 9999),
            ],
            [
                'title' => 'HR Executive',
                'department_id' => $departments->last()->id,
                'description' => 'Manage daily HR operations and recruitment.',
                'requirements' => 'BBA in HRM, 1-2 years experience.',
                'vacancies' => 1,
                'status' => 'active',
                'is_published' => true,
                'location' => 'Dhaka, Bangladesh',
                'employment_type' => 'Full Time',
                'salary_range_min' => 30000,
                'salary_range_max' => 45000,
                'posted_date' => now()->subDays(5),
                'closing_date' => now()->addDays(25),
                'code' => 'JOB-' . rand(1000, 9999),
            ]
        ];

        foreach ($jobs as $jobData) {
            $job = Job::create($jobData);
            
            // 2. Create Candidates
            $candidates = [
                [
                    'job_id' => $job->id,
                    'first_name' => 'Rahim',
                    'last_name' => 'Uddin',
                    'email' => 'rahim.' . rand(100,999) . '@example.com',
                    'phone' => '01700000' . rand(100,999),
                    'status' => 'interview',
                    'rating' => 4,
                ],
                [
                    'job_id' => $job->id,
                    'first_name' => 'Karim',
                    'last_name' => 'Hasan',
                    'email' => 'karim.' . rand(100,999) . '@example.com',
                    'phone' => '01800000' . rand(100,999),
                    'status' => 'applied',
                    'rating' => 0,
                    'applied_date' => now()->subDays(rand(1,5)),
                ]
            ];

            foreach ($candidates as $cand) {
                // Ensure applied_date is set for all candidates if loop logic is shared
                $cand['applied_date'] = $cand['applied_date'] ?? now()->subDays(2);
                
                $candidate = Candidate::create($cand);

                // 3. Schedule Interview for shortlisted/interview candidates
                if ($candidate->status == 'interview') {
                    $interview = Interview::create([
                        'candidate_id' => $candidate->id,
                        'job_id' => $job->id,
                        'scheduled_at' => now()->addDays(2)->setTime(10, 0),
                        'interview_type' => 'in_person',
                        'location' => 'Meeting Room A',
                        'status' => 'scheduled',
                    ]);

                    // Attach Interviewer
                    $interviewerId = Employee::first()->id ?? 1;
                    $interview->interviewers()->sync([$interviewerId]);
                }
            }
        }
    }
}
