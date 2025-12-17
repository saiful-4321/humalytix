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
use Carbon\Carbon;

class HRMDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding HRM real Bangladeshi demo data...');

        // Get or create admin user
        $admin = User::first();

        // Create Cost Centers
        $costCenters = [
            ['name' => 'General Administration', 'code' => 'CC-ADM', 'budget' => 5000000],
            ['name' => 'Dhaka Factory Operations', 'code' => 'CC-OPS-DHK', 'budget' => 10000000],
            ['name' => 'Chittagong Port Operations', 'code' => 'CC-OPS-CTG', 'budget' => 8000000],
            ['name' => 'Sales & Marketing', 'code' => 'CC-SAL', 'budget' => 7500000],
            ['name' => 'Information Technology', 'code' => 'CC-TECH', 'budget' => 12000000],
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
                'name' => 'Gulshan Head Office',
                'code' => 'BR-GUL',
                'address' => 'Plot-12, Road-34, Gulshan-2',
                'city' => 'Dhaka',
                'state' => 'Dhaka',
                'country' => 'Bangladesh',
                'postal_code' => '1212',
                'phone' => '+880-2-9845678',
                'email' => 'info.gulshan@sylnovia.com',
                'is_head_office' => true,
                'latitude' => 23.7925,
                'longitude' => 90.4078,
            ],
            [
                'name' => 'Motijheel Corporate Branch',
                'code' => 'BR-MOT',
                'address' => 'City Centre, Level-10, Motijheel C/A',
                'city' => 'Dhaka',
                'state' => 'Dhaka',
                'country' => 'Bangladesh',
                'postal_code' => '1000',
                'phone' => '+880-2-9556789',
                'email' => 'info.motijheel@sylnovia.com',
                'is_head_office' => false,
                'latitude' => 23.7330,
                'longitude' => 90.4172,
            ],
            [
                'name' => 'Chittagong Agrabad Office',
                'code' => 'BR-CTG',
                'address' => 'Agrabad C/A, Chattogram',
                'city' => 'Chittagong',
                'state' => 'Chittagong',
                'country' => 'Bangladesh',
                'postal_code' => '4100',
                'phone' => '+880-31-712345',
                'email' => 'info.ctg@sylnovia.com',
                'is_head_office' => false,
                'latitude' => 22.3246,
                'longitude' => 91.8101,
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
        $opsDhkCC = CostCenter::where('code', 'CC-OPS-DHK')->first();
        $salesCC = CostCenter::where('code', 'CC-SAL')->first();
        $techCC = CostCenter::where('code', 'CC-TECH')->first();

        $departments = [
            ['name' => 'Human Resources', 'code' => 'DEPT-HR', 'cost_center_id' => $adminCC->id, 'parent_id' => null],
            ['name' => 'Finance & Accounts', 'code' => 'DEPT-FIN', 'cost_center_id' => $adminCC->id, 'parent_id' => null],
            ['name' => 'Information Technology', 'code' => 'DEPT-IT', 'cost_center_id' => $techCC->id, 'parent_id' => null],
            ['name' => 'Sales & Business Development', 'code' => 'DEPT-SAL', 'cost_center_id' => $salesCC->id, 'parent_id' => null],
            ['name' => 'Marketing & Branding', 'code' => 'DEPT-MKT', 'cost_center_id' => $salesCC->id, 'parent_id' => null],
            ['name' => 'Supply Chain Management', 'code' => 'DEPT-SCM', 'cost_center_id' => $opsDhkCC->id, 'parent_id' => null],
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
            ['name' => 'Software Engineering', 'code' => 'DEPT-IT-DEV', 'parent_id' => $itDept->id, 'cost_center_id' => $techCC->id],
            ['name' => 'Network & Security', 'code' => 'DEPT-IT-NET', 'parent_id' => $itDept->id, 'cost_center_id' => $techCC->id],
        ];

        foreach ($subDepartments as $dept) {
            Department::firstOrCreate(
                ['code' => $dept['code']],
                array_merge($dept, ['created_by' => $admin->id])
            );
        }

        // Create Business Units
        $businessUnits = [
            ['name' => 'Consumer Goods', 'code' => 'BU-FMCG'],
            ['name' => 'Textile Division', 'code' => 'BU-TEXT'],
            ['name' => 'IT Services', 'code' => 'BU-ITS'],
        ];

        foreach ($businessUnits as $bu) {
            BusinessUnit::firstOrCreate(
                ['code' => $bu['code']],
                array_merge($bu, ['created_by' => $admin->id])
            );
        }

        // Create Document Types
        $documentTypes = [
            ['name' => 'National ID Card (NID)', 'category' => 'kyc', 'is_required' => true, 'order' => 1],
            ['name' => 'Passport', 'category' => 'kyc', 'is_required' => false, 'order' => 2],
            ['name' => 'Birth Certificate', 'category' => 'kyc', 'is_required' => false, 'order' => 3],
            ['name' => 'tin_certificate', 'category' => 'kyc', 'is_required' => true, 'order' => 4],
            ['name' => 'Resume/CV', 'category' => 'employment', 'is_required' => true, 'order' => 5],
            ['name' => 'Educational Certificates', 'category' => 'education', 'is_required' => true, 'order' => 6],
            ['name' => 'Experience Certificates', 'category' => 'employment', 'is_required' => false, 'order' => 7],
            ['name' => 'Professional Certifications', 'category' => 'certification', 'is_required' => false, 'order' => 8],
            ['name' => 'Bank Account Cheque/Statement', 'category' => 'employment', 'is_required' => true, 'order' => 9],
            ['name' => 'Medical Fitness Certificate', 'category' => 'other', 'is_required' => false, 'order' => 10],
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
            ['name' => 'Vue.js', 'category' => 'technical'],
            ['name' => 'React', 'category' => 'technical'],
            ['name' => 'MySQL', 'category' => 'technical'],
            ['name' => 'Docker', 'category' => 'technical'],
            ['name' => 'AWS Cloud', 'category' => 'technical'],
            
            // Soft Skills
            ['name' => 'Bangla Typing', 'category' => 'soft'],
            ['name' => 'English Proficiency', 'category' => 'soft'],
            ['name' => 'Client Communication', 'category' => 'soft'],
            ['name' => 'Leadership', 'category' => 'soft'],
            
            // Certifications
            ['name' => 'PMP', 'category' => 'certification'],
            ['name' => 'AWS Certified Solutions Architect', 'category' => 'certification'],
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
                'name' => 'General Shift',
                'code' => 'SHIFT-GEN',
                'start_time' => '10:00:00',
                'end_time' => '18:00:00',
                'grace_period_minutes' => 15,
                'half_day_hours' => 4,
                'full_day_hours' => 8,
                'break_duration_minutes' => 60,
            ],
            [
                'name' => 'Morning Shift',
                'code' => 'SHIFT-MOR',
                'start_time' => '07:00:00',
                'end_time' => '15:00:00',
                'grace_period_minutes' => 15,
                'half_day_hours' => 4,
                'full_day_hours' => 8,
                'break_duration_minutes' => 30,
            ],
            [
                'name' => 'Evening Shift',
                'code' => 'SHIFT-EVE',
                'start_time' => '15:00:00',
                'end_time' => '23:00:00',
                'grace_period_minutes' => 15,
                'half_day_hours' => 4,
                'full_day_hours' => 8,
                'break_duration_minutes' => 30,
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::firstOrCreate(
                ['code' => $shift['code']],
                array_merge($shift, ['created_by' => $admin->id])
            );
        }

        // Create Realistic Bangladeshi Employees
        $gulshanHO = Branch::where('code', 'BR-GUL')->first();
        $motijheelBr = Branch::where('code', 'BR-MOT')->first();
        
        $hrDept = Department::where('code', 'DEPT-HR')->first();
        $itDept = Department::where('code', 'DEPT-IT')->first();
        $itDevDept = Department::where('code', 'DEPT-IT-DEV')->first();
        $salesDept = Department::where('code', 'DEPT-SAL')->first();
        $finDept = Department::where('code', 'DEPT-FIN')->first();

        $employees = [
            // HR Dept
            [
                'first_name' => 'Rahim',
                'last_name' => 'Uddin',
                'email' => 'rahim.uddin@sylnovia.com',
                'phone' => '01711123456',
                'date_of_birth' => '1982-05-15',
                'gender' => 'male',
                'blood_group' => 'B+',
                'marital_status' => 'married',
                'nationality' => 'Bangladeshi',
                'nid_number' => '1982234567890',
                'department_id' => $hrDept->id,
                'branch_id' => $gulshanHO->id,
                'designation' => 'Head of HR',
                'employment_type' => 'full_time',
                'joining_date' => '2019-01-01',
                'confirmation_date' => '2019-07-01',
                'status' => 'confirmed',
                'basic_salary' => 120000,
                'present_address' => 'House-10, Road-5, Mirpur DOHS, Dhaka',
            ],
            [
                'first_name' => 'Nadia',
                'last_name' => 'Islam',
                'email' => 'nadia.islam@sylnovia.com',
                'phone' => '01811123456',
                'date_of_birth' => '1992-08-20',
                'gender' => 'female',
                'blood_group' => 'A+',
                'marital_status' => 'single',
                'nationality' => 'Bangladeshi',
                'nid_number' => '1992234567890',
                'department_id' => $hrDept->id,
                'branch_id' => $gulshanHO->id,
                'designation' => 'HR Executive',
                'employment_type' => 'full_time',
                'joining_date' => '2022-03-01',
                'confirmation_date' => '2022-09-01',
                'status' => 'confirmed',
                'basic_salary' => 45000,
                'present_address' => 'Flat-3A, 45 Shantinagar, Dhaka',
            ],
            
            // IT Dept
            [
                'first_name' => 'Tanvir',
                'last_name' => 'Ahmed',
                'email' => 'tanvir.ahmed@sylnovia.com',
                'phone' => '01911123456',
                'date_of_birth' => '1988-12-10',
                'gender' => 'male',
                'blood_group' => 'O+',
                'marital_status' => 'married',
                'nationality' => 'Bangladeshi',
                'nid_number' => '1988234567890',
                'department_id' => $itDevDept->id,
                'branch_id' => $gulshanHO->id,
                'designation' => 'Lead Software Engineer',
                'employment_type' => 'full_time',
                'joining_date' => '2020-05-15',
                'confirmation_date' => '2020-11-15',
                'status' => 'confirmed',
                'basic_salary' => 150000,
                'present_address' => 'Banasree Block-C, Rampura, Dhaka',
            ],
            [
                'first_name' => 'Israt',
                'last_name' => 'Jahan',
                'email' => 'israt.jahan@sylnovia.com',
                'phone' => '01611123457',
                'date_of_birth' => '1995-02-28',
                'gender' => 'female',
                'blood_group' => 'B-',
                'marital_status' => 'single',
                'nationality' => 'Bangladeshi',
                'nid_number' => '1995234567890',
                'department_id' => $itDevDept->id,
                'branch_id' => $gulshanHO->id,
                'designation' => 'Software Engineer',
                'employment_type' => 'full_time',
                'joining_date' => '2023-01-10',
                'probation_end_date' => '2023-07-10',
                'status' => 'confirmed',
                'basic_salary' => 60000,
                'present_address' => 'Uttara Sector-7, Dhaka',
            ],
            
            // Sales Dept
            [
                'first_name' => 'Kamal',
                'last_name' => 'Hossain',
                'email' => 'kamal.hossain@sylnovia.com',
                'phone' => '01722123456',
                'date_of_birth' => '1985-11-05',
                'gender' => 'male',
                'blood_group' => 'AB+',
                'marital_status' => 'married',
                'nationality' => 'Bangladeshi',
                'nid_number' => '1985234567890',
                'department_id' => $salesDept->id,
                'branch_id' => $motijheelBr->id,
                'designation' => 'Regional Sales Manager',
                'employment_type' => 'full_time',
                'joining_date' => '2021-02-01',
                'confirmation_date' => '2021-08-01',
                'status' => 'confirmed',
                'basic_salary' => 95000,
                'present_address' => 'Wari, Dhaka Old Town',
            ],

             // Finance Dept
            [
                'first_name' => 'Fahad',
                'last_name' => 'Karim',
                'email' => 'fahad.karim@sylnovia.com',
                'phone' => '01311123458',
                'date_of_birth' => '1990-06-15',
                'gender' => 'male',
                'blood_group' => 'A-',
                'marital_status' => 'married',
                'nationality' => 'Bangladeshi',
                'nid_number' => '1990234567890',
                'department_id' => $finDept->id,
                'branch_id' => $motijheelBr->id,
                'designation' => 'Accounts Manager',
                'employment_type' => 'full_time',
                'joining_date' => '2018-09-01',
                'confirmation_date' => '2019-03-01',
                'status' => 'confirmed',
                'basic_salary' => 85000,
                'present_address' => 'Mohammadpur Housing Society, Dhaka',
            ],
            [
                'first_name' => 'Sabina',
                'last_name' => 'Yasmin',
                'email' => 'sabina.yasmin@sylnovia.com',
                'phone' => '01922123459',
                'date_of_birth' => '1996-03-22',
                'gender' => 'female',
                'blood_group' => 'O+',
                'marital_status' => 'single',
                'nationality' => 'Bangladeshi',
                'nid_number' => '1996234567890',
                'department_id' => $finDept->id,
                'branch_id' => $gulshanHO->id,
                'designation' => 'Junior Accountant',
                'employment_type' => 'full_time',
                'joining_date' => '2023-11-01',
                'probation_end_date' => '2024-05-01',
                'status' => 'probation',
                'basic_salary' => 30000,
                'present_address' => 'Badda Link Road, Dhaka',
            ],
            // Additional Employees for Bank Transfer Testing
            [
                'first_name' => 'Mahmudul',
                'last_name' => 'Hasan',
                'email' => 'mahmudul.hasan@sylnovia.com',
                'phone' => '01511123450',
                'date_of_birth' => '1993-04-12',
                'gender' => 'male',
                'blood_group' => 'B+',
                'marital_status' => 'married',
                'nationality' => 'Bangladeshi',
                'nid_number' => '1993234567891',
                'department_id' => $itDevDept->id,
                'branch_id' => $gulshanHO->id,
                'designation' => 'DevOps Engineer',
                'employment_type' => 'full_time',
                'joining_date' => '2021-06-01',
                'confirmation_date' => '2021-12-01',
                'status' => 'confirmed',
                'basic_salary' => 110000,
                'present_address' => 'Mirpur-10, Dhaka',
            ],
            [
                'first_name' => 'Farhana',
                'last_name' => 'Akhter',
                'email' => 'farhana.akhter@sylnovia.com',
                'phone' => '01311123460',
                'date_of_birth' => '1997-09-05',
                'gender' => 'female',
                'blood_group' => 'A+',
                'marital_status' => 'single',
                'nationality' => 'Bangladeshi',
                'nid_number' => '1997234567892',
                'department_id' => $hrDept->id,
                'branch_id' => $gulshanHO->id,
                'designation' => 'Recruitment Specialist',
                'employment_type' => 'full_time',
                'joining_date' => '2022-07-01',
                'confirmation_date' => '2023-01-01',
                'status' => 'confirmed',
                'basic_salary' => 55000,
                'present_address' => 'Nikunja-2, Dhaka',
            ],
            [
                'first_name' => 'Rashid',
                'last_name' => 'Khan',
                'email' => 'rashid.khan@sylnovia.com',
                'phone' => '01711123499',
                'date_of_birth' => '1980-01-20',
                'gender' => 'male',
                'blood_group' => 'O-',
                'marital_status' => 'married',
                'nationality' => 'Bangladeshi',
                'nid_number' => '1980234567899',
                'department_id' => $finDept->id,
                'branch_id' => $motijheelBr->id,
                'designation' => 'Chief Financial Officer',
                'employment_type' => 'full_time',
                'joining_date' => '2015-01-01',
                'confirmation_date' => '2015-07-01',
                'status' => 'confirmed',
                'basic_salary' => 250000,
                'present_address' => 'Baridhara DOHS, Dhaka',
            ],
        ];

        foreach ($employees as $empData) {
            Employee::firstOrCreate(
                ['email' => $empData['email']],
                array_merge($empData, ['created_by' => $admin->id])
            );
        }

        // Create Vacancies/Jobs
        $jobs = [
            [
                'title' => 'Senior Laravel Developer',
                'department_id' => $itDevDept->id,
                'branch_id' => $gulshanHO->id,
                'employment_type' => 'full_time',
                'experience_required' => 5,
                'salary_range_min' => 100000,
                'salary_range_max' => 160000,
                'description' => 'We are seeking an expert Laravel Developer to lead our backend team.',
                'requirements' => '- 5+ years of PHP/Laravel experience\n- Experience with Microservices',
                'responsibilities' => '- Architect and develop scalable web applications\n- Mentor junior developers',
                'vacancies' => 2,
                'posted_date' => now(),
                'closing_date' => now()->addDays(30),
                'status' => 'active',
                'is_published' => true,
            ],
            [
                'title' => 'Territory Sales Officer',
                'department_id' => $salesDept->id,
                'branch_id' => $motijheelBr->id,
                'employment_type' => 'full_time',
                'experience_required' => 2,
                'salary_range_min' => 25000,
                'salary_range_max' => 35000,
                'description' => 'Field sales role for dynamic individuals.',
                'requirements' => '- Graduate in any discipline\n- Willingness to travel',
                'responsibilities' => '- Visit clients and collect orders\n- Maintain relationship with retailers',
                'vacancies' => 5,
                'posted_date' => now(),
                'closing_date' => now()->addDays(15),
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

        $this->command->info('HRM real Bangladeshi demo data seeded successfully!');
    }
}
