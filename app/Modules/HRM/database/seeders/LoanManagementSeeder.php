<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\HRM\Models\LoanType;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LoanManagementSeeder extends Seeder
{
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // Loan Types
            'hrm.loan-types.view',
            'hrm.loan-types.create',
            'hrm.loan-types.update',
            'hrm.loan-types.delete',
            
            // Employee Loans
            'hrm.loans.view',
            'hrm.loans.create',
            'hrm.loans.update',
            'hrm.loans.delete',
            'hrm.loans.approve',
            'hrm.loans.disburse',
            
            // Employee Advances
            'hrm.advances.view',
            'hrm.advances.create',
            'hrm.advances.update',
            'hrm.advances.delete',
            'hrm.advances.approve',
        ];

        // Get HRM Module ID
        $hrmModule = \App\Modules\Main\Models\Module::where('name', 'HRM')->first();
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['module_id' => $hrmModule?->id]
            );
        }

        // Assign to Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo($permissions);

        // Sample Loan Types
        $loanTypes = [
            [
                'name' => 'Personal Loan',
                'description' => 'General purpose personal loan for employees',
                'max_amount' => 500000,
                'interest_rate' => 12.00,
                'interest_type' => 'reducing',
                'max_tenure_months' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Emergency Loan',
                'description' => 'Quick loan for emergency situations',
                'max_amount' => 100000,
                'interest_rate' => 8.00,
                'interest_type' => 'flat',
                'max_tenure_months' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Education Loan',
                'description' => 'Loan for education and skill development',
                'max_amount' => 300000,
                'interest_rate' => 6.00,
                'interest_type' => 'reducing',
                'max_tenure_months' => 36,
                'is_active' => true,
            ],
            [
                'name' => 'Housing Loan',
                'description' => 'Loan for housing and property purchase',
                'max_amount' => 2000000,
                'interest_rate' => 10.00,
                'interest_type' => 'reducing',
                'max_tenure_months' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Vehicle Loan',
                'description' => 'Loan for purchasing vehicle',
                'max_amount' => 800000,
                'interest_rate' => 9.00,
                'interest_type' => 'reducing',
                'max_tenure_months' => 48,
                'is_active' => true,
            ],
        ];

        foreach ($loanTypes as $type) {
            LoanType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }

        $this->command->info('Loan Management seeded successfully!');
    }
}
