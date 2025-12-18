<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\HRM\Models\Policy;
use App\Modules\HRM\Models\Contract;
use App\Modules\HRM\Models\Employee;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class ComplianceSeeder extends Seeder
{
    public function run()
    {
        // 1. Create/Get Compliance Module
        $module = \App\Modules\Main\Models\Module::firstOrCreate(['name' => 'Compliance', 'status' => 1]);

        // 2. Seed Permissions
        $permissions = [
            'hrm.policies.view', 'hrm.policies.create', 'hrm.policies.edit', 'hrm.policies.delete',
            'hrm.contracts.view_all', 'hrm.contracts.create', 'hrm.contracts.delete',
            'hrm.documents.view_all',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate([
                'name' => $p, 
                'guard_name' => 'web',
                'module_id' => $module->id // Fixed: Included module_id
            ]);
        }

        // Assign to Admin Role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
             // Spatie uses name to sync, but we inserted with module_id. 
             // Ideally we pass objects or names. Spatie handles connection via model.
             $adminRole->givePermissionTo($permissions);
        }

        // 2. Seed Policies
        if (Policy::count() == 0) {
            Policy::create([
                'title' => 'Employee Handbook 2024',
                'description' => 'Comprehensive guide to company rules, benefits, and culture.',
                'file_path' => 'policies/handbook.pdf', // Placeholder
                'version' => '2.0',
                'effective_date' => now()->subMonths(6),
                'status' => 'published',
                'created_by' => 1,
            ]);

            Policy::create([
                'title' => 'Remote Work Policy',
                'description' => 'Guidelines for working from home and remote attendance.',
                'file_path' => 'policies/remote_work.pdf', // Placeholder
                'version' => '1.1',
                'effective_date' => now()->subMonth(),
                'status' => 'published',
                'created_by' => 1,
            ]);
            
            Policy::create([
                'title' => 'Travel Expense Policy',
                'description' => 'Rules for claiming travel reimbursements.',
                'file_path' => 'policies/travel_expense.pdf', // Placeholder
                'version' => '1.0',
                'effective_date' => now()->startOfYear(),
                'status' => 'published',
                'created_by' => 1,
            ]);
        }

        // 3. Seed Contracts
        $employees = Employee::active()->take(5)->get();
        foreach ($employees as $employee) {
            // Employment Contract
            Contract::create([
                'employee_id' => $employee->id,
                'title' => 'Employment Agreement - ' . $employee->full_name,
                'file_path' => 'contracts/sample_contract.pdf', // Placeholder
                'start_date' => $employee->joining_date,
                'type' => 'Employment',
                'status' => 'signed',
                'created_by' => 1,
            ]);

            // NDA
            Contract::create([
                'employee_id' => $employee->id,
                'title' => 'Non-Disclosure Agreement',
                'file_path' => 'contracts/sample_nda.pdf', // Placeholder
                'start_date' => $employee->joining_date,
                'type' => 'NDA',
                'status' => 'sent', // Pending signature
                'created_by' => 1,
            ]);
        }
        
        // 4. Seed Employee Documents (for Expiry Tracker)
        $docTypes = DB::table('hrm_document_types')->pluck('id')->toArray();
        if (!empty($docTypes)) {
            foreach ($employees as $emp) {
                // Passport (Expiring Soon)
                DB::table('hrm_employee_documents')->insert([
                    'employee_id' => $emp->id,
                    'document_type_id' => $docTypes[array_rand($docTypes)], // Assuming 1 is Passport or similar
                    'document_number' => 'P' . rand(1000000, 9999999),
                    'file_path' => 'documents/sample_passport.jpg',
                    'issue_date' => now()->subYears(4),
                    'expiry_date' => now()->addDays(rand(5, 60)), // Expiring in 5-60 days
                    'verification_status' => 'verified',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Visa (Expired)
                DB::table('hrm_employee_documents')->insert([
                    'employee_id' => $emp->id,
                    'document_type_id' => $docTypes[array_rand($docTypes)],
                    'document_number' => 'V' . rand(1000000, 9999999),
                    'file_path' => 'documents/sample_visa.jpg',
                    'issue_date' => now()->subYears(2),
                    'expiry_date' => now()->subDays(10), // Expired
                    'verification_status' => 'verified',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
