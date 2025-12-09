<?php

namespace App\Modules\HRM\database\seeders;

use Illuminate\Database\Seeder;
use App\Modules\HRM\Models\{
    Department,
    Branch,
    CostCenter,
    BusinessUnit,
    Employee,
    DocumentType,
    Skill,
    Job,
    Shift
};
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HRMDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding HRM demo data...');

        // Get or create admin user
        $admin = User::first();

        // Create Cost Centers
        $costCenters = [
            ['name' => 'Administration', 'code' => 'CC-ADM', 'budget' => 500000],
            ['name' => 'Operations', 'code' => 'CC-OPS', 'budget' => 1000000],
            ['name' => 'Sales & Marketing', 'code' => 'CC-SAL', 'budget' => 750000],
            ['name' => 'Technology', 'code' => 'CC-TECH', 'budget' => 1200000],
        ];

        foreach ($costCenters as $cc) {
            CostCenter::firstOrCreate(
                ['code' => $cc['code']],
                array_merge($cc, ['created_by' => $admin->id])
            );
        }

        // Create Branches
        $branches = [
            [
                'name' => 'Head Office',
                'code' => 'BR-HO',
                'address' => '123 Main Street',
                'city' => 'Dhaka',
                'state' => 'Dhaka',
                'country' => 'Bangladesh',
                'postal_code' => '1000',
                'phone' => '+880-2-1234567',
                'email' => 'headoffice@company.com',
                'is_head_office' => true,
                'latitude' => 23.8103,
                'longitude' => 90.4125,
            ],
            [
                'name' => 'Chittagong Branch',
                'code' => 'BR-CTG',
                'address' => '456 Port Road',
                'city' => 'Chittagong',
                'state' => 'Chittagong',
                'country' => 'Bangladesh',
                'postal_code' => '4000',
                'phone' => '+880-31-1234567',
                'email' => 'chittagong@company.com',
                'is_head_office' => false,
                'latitude' => 22.3569,
                'longitude' => 91.7832,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::firstOrCreate(
                ['code' => $branch['code']],
                array_merge($branch, ['created_by' => $admin->id])
            );
        }

        // Create Departments
        $adminCC = CostCenter::where('code', 'CC-ADM')->first();
        $opsCC = CostCenter::where('code', 'CC-OPS')->first();
        $salesCC = CostCenter::where('code', 'CC-SAL')->first();
        $techCC = CostCenter::where('code', 'CC-TECH')->first();

        $departments = [
            ['name' => 'Human Resources', 'code' => 'DEPT-HR', 'cost_center_id' => $adminCC->id, 'parent_id' => null],
            ['name' => 'Finance & Accounts', 'code' => 'DEPT-FIN', 'cost_center_id' => $adminCC->id, 'parent_id' => null],
            ['name' => 'Information Technology', 'code' => 'DEPT-IT', 'cost_center_id' => $techCC->id, 'parent_id' => null],
            ['name' => 'Sales', 'code' => 'DEPT-SAL', 'cost_center_id' => $salesCC->id, 'parent_id' => null],
            ['name' => 'Marketing', 'code' => 'DEPT-MKT', 'cost_center_id' => $salesCC->id, 'parent_id' => null],
            ['name' => 'Operations', 'code' => 'DEPT-OPS', 'cost_center_id' => $opsCC->id, 'parent_id' => null],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['code' => $dept['code']],
                array_merge($dept, ['created_by' => $admin->id])
            );
        }

        // Create sub-departments
        $itDept = Department::where('code', 'DEPT-IT')->first();
        $subDepartments = [
            ['name' => 'Software Development', 'code' => 'DEPT-IT-DEV', 'parent_id' => $itDept->id, 'cost_center_id' => $techCC->id],
            ['name' => 'Infrastructure & Support', 'code' => 'DEPT-IT-INF', 'parent_id' => $itDept->id, 'cost_center_id' => $techCC->id],
        ];

        foreach ($subDepartments as $dept) {
            Department::firstOrCreate(
                ['code' => $dept['code']],
                array_merge($dept, ['created_by' => $admin->id])
            );
        }

        // Create Business Units
        $businessUnits = [
            ['name' => 'Corporate Services', 'code' => 'BU-CORP'],
            ['name' => 'Product Division', 'code' => 'BU-PROD'],
            ['name' => 'Service Division', 'code' => 'BU-SERV'],
        ];

        foreach ($businessUnits as $bu) {
            BusinessUnit::firstOrCreate(
                ['code' => $bu['code']],
                array_merge($bu, ['created_by' => $admin->id])
            );
        }

        // Create Document Types
        $documentTypes = [
            ['name' => 'National ID Card', 'category' => 'kyc', 'is_required' => true, 'order' => 1],
            ['name' => 'Passport', 'category' => 'kyc', 'is_required' => false, 'order' => 2],
            ['name' => 'Birth Certificate', 'category' => 'kyc', 'is_required' => false, 'order' => 3],
            ['name' => 'Tax Identification Number', 'category' => 'kyc', 'is_required' => false, 'order' => 4],
            ['name' => 'Resume/CV', 'category' => 'employment', 'is_required' => true, 'order' => 5],
            ['name' => 'Educational Certificates', 'category' => 'education', 'is_required' => true, 'order' => 6],
            ['name' => 'Experience Certificates', 'category' => 'employment', 'is_required' => false, 'order' => 7],
            ['name' => 'Professional Certifications', 'category' => 'certification', 'is_required' => false, 'order' => 8],
            ['name' => 'Bank Account Details', 'category' => 'employment', 'is_required' => true, 'order' => 9],
            ['name' => 'Medical Certificate', 'category' => 'other', 'is_required' => false, 'order' => 10],
        ];

        foreach ($documentTypes as $docType) {
            DocumentType::firstOrCreate(
                ['name' => $docType['name']],
                array_merge($docType, ['created_by' => $admin->id])
            );
        }

        // Create Skills
        $skills = [
            // Technical Skills
            ['name' => 'PHP', 'category' => 'technical'],
            ['name' => 'Laravel', 'category' => 'technical'],
            ['name' => 'JavaScript', 'category' => 'technical'],
            ['name' => 'React', 'category' => 'technical'],
            ['name' => 'Vue.js', 'category' => 'technical'],
            ['name' => 'MySQL', 'category' => 'technical'],
            ['name' => 'PostgreSQL', 'category' => 'technical'],
            ['name' => 'MongoDB', 'category' => 'technical'],
            ['name' => 'AWS', 'category' => 'technical'],
            ['name' => 'Docker', 'category' => 'technical'],
            ['name' => 'Git', 'category' => 'technical'],
            ['name' => 'Python', 'category' => 'technical'],
            ['name' => 'Java', 'category' => 'technical'],
            ['name' => 'Node.js', 'category' => 'technical'],
            
            // Soft Skills
            ['name' => 'Communication', 'category' => 'soft'],
            ['name' => 'Leadership', 'category' => 'soft'],
            ['name' => 'Team Management', 'category' => 'soft'],
            ['name' => 'Problem Solving', 'category' => 'soft'],
            ['name' => 'Time Management', 'category' => 'soft'],
            ['name' => 'Project Management', 'category' => 'soft'],
            ['name' => 'Negotiation', 'category' => 'soft'],
            ['name' => 'Presentation', 'category' => 'soft'],
            
            // Languages
            ['name' => 'English', 'category' => 'language'],
            ['name' => 'Bengali', 'category' => 'language'],
            ['name' => 'Hindi', 'category' => 'language'],
            
            // Certifications
            ['name' => 'PMP', 'category' => 'certification'],
            ['name' => 'AWS Certified', 'category' => 'certification'],
            ['name' => 'Scrum Master', 'category' => 'certification'],
        ];

        foreach ($skills as $skill) {
            Skill::firstOrCreate(
                ['name' => $skill['name']],
                array_merge($skill, ['created_by' => $admin->id])
            );
        }

        // Create Shifts
        $shifts = [
            [
                'name' => 'Morning Shift',
                'code' => 'SHIFT-MOR',
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'grace_period_minutes' => 15,
                'half_day_hours' => 4,
                'full_day_hours' => 8,
                'break_duration_minutes' => 60,
            ],
            [
                'name' => 'Evening Shift',
                'code' => 'SHIFT-EVE',
                'start_time' => '14:00:00',
                'end_time' => '23:00:00',
                'grace_period_minutes' => 15,
                'half_day_hours' => 4,
                'full_day_hours' => 8,
                'break_duration_minutes' => 60,
            ],
            [
                'name' => 'Night Shift',
                'code' => 'SHIFT-NGT',
                'start_time' => '22:00:00',
                'end_time' => '07:00:00',
                'grace_period_minutes' => 15,
                'half_day_hours' => 4,
                'full_day_hours' => 8,
                'break_duration_minutes' => 60,
            ],
            [
                'name' => 'Flexible Shift',
                'code' => 'SHIFT-FLX',
                'start_time' => '10:00:00',
                'end_time' => '19:00:00',
                'grace_period_minutes' => 30,
                'half_day_hours' => 4,
                'full_day_hours' => 8,
                'break_duration_minutes' => 60,
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::firstOrCreate(
                ['code' => $shift['code']],
                array_merge($shift, ['created_by' => $admin->id])
            );
        }

        // Create Sample Employees
        $headOffice = Branch::where('code', 'BR-HO')->first();
        $hrDept = Department::where('code', 'DEPT-HR')->first();
        $itDept = Department::where('code', 'DEPT-IT')->first();
        $salesDept = Department::where('code', 'DEPT-SAL')->first();

        $employees = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@company.com',
                'phone' => '+880-1711-111111',
                'date_of_birth' => '1985-05-15',
                'gender' => 'male',
                'blood_group' => 'A+',
                'marital_status' => 'married',
                'nationality' => 'Bangladeshi',
                'department_id' => $hrDept->id,
                'branch_id' => $headOffice->id,
                'designation' => 'HR Manager',
                'employment_type' => 'full_time',
                'joining_date' => '2020-01-15',
                'confirmation_date' => '2020-04-15',
                'status' => 'confirmed',
                'basic_salary' => 80000,
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@company.com',
                'phone' => '+880-1711-222222',
                'date_of_birth' => '1990-08-20',
                'gender' => 'female',
                'blood_group' => 'B+',
                'marital_status' => 'single',
                'nationality' => 'Bangladeshi',
                'department_id' => $itDept->id,
                'branch_id' => $headOffice->id,
                'designation' => 'Senior Software Engineer',
                'employment_type' => 'full_time',
                'joining_date' => '2021-03-01',
                'confirmation_date' => '2021-06-01',
                'status' => 'confirmed',
                'basic_salary' => 75000,
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'michael.johnson@company.com',
                'phone' => '+880-1711-333333',
                'date_of_birth' => '1992-12-10',
                'gender' => 'male',
                'blood_group' => 'O+',
                'marital_status' => 'single',
                'nationality' => 'Bangladeshi',
                'department_id' => $salesDept->id,
                'branch_id' => $headOffice->id,
                'designation' => 'Sales Executive',
                'employment_type' => 'full_time',
                'joining_date' => '2023-06-01',
                'probation_end_date' => '2023-09-01',
                'status' => 'probation',
                'basic_salary' => 45000,
            ],
        ];

        foreach ($employees as $empData) {
            Employee::firstOrCreate(
                ['email' => $empData['email']],
                array_merge($empData, ['created_by' => $admin->id])
            );
        }

        // Create Sample Jobs
        $jobs = [
            [
                'title' => 'Senior Laravel Developer',
                'department_id' => $itDept->id,
                'branch_id' => $headOffice->id,
                'employment_type' => 'full_time',
                'experience_required' => 5,
                'salary_range_min' => 70000,
                'salary_range_max' => 100000,
                'description' => 'We are looking for an experienced Laravel developer to join our team.',
                'requirements' => '- 5+ years of PHP/Laravel experience\n- Strong knowledge of MySQL\n- Experience with Vue.js or React',
                'responsibilities' => '- Develop and maintain web applications\n- Write clean, maintainable code\n- Collaborate with team members',
                'vacancies' => 2,
                'posted_date' => now(),
                'closing_date' => now()->addDays(30),
                'status' => 'active',
                'is_published' => true,
            ],
            [
                'title' => 'Sales Manager',
                'department_id' => $salesDept->id,
                'branch_id' => $headOffice->id,
                'employment_type' => 'full_time',
                'experience_required' => 3,
                'salary_range_min' => 60000,
                'salary_range_max' => 90000,
                'description' => 'Seeking an experienced sales professional to lead our sales team.',
                'requirements' => '- 3+ years of sales experience\n- Proven track record\n- Excellent communication skills',
                'responsibilities' => '- Lead sales team\n- Develop sales strategies\n- Meet sales targets',
                'vacancies' => 1,
                'posted_date' => now(),
                'closing_date' => now()->addDays(45),
                'status' => 'active',
                'is_published' => true,
            ],
        ];

        foreach ($jobs as $jobData) {
            Job::firstOrCreate(
                ['title' => $jobData['title']],
                array_merge($jobData, ['created_by' => $admin->id])
            );
        }

        $this->command->info('HRM demo data seeded successfully!');
    }
}
