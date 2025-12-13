<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            // Required / KYC
            [
                'name' => 'NID / Smart Card',
                'category' => 'kyc',
                'is_required' => true,
                'urgency' => 'required',
                'description' => 'National Identity Card',
                'order' => 1
            ],
            [
                'name' => 'Passport',
                'category' => 'kyc',
                'is_required' => false,
                'urgency' => 'nice_to_have',
                'description' => 'Valid Passport Copy',
                'order' => 2
            ],
            [
                'name' => 'TIN Certificate',
                'category' => 'kyc',
                'is_required' => true,
                'urgency' => 'required',
                'description' => 'Tax Identification Number',
                'order' => 3
            ],
            [
                'name' => 'Photo',
                'category' => 'kyc',
                'is_required' => true,
                'urgency' => 'required',
                'description' => 'Recent Passport Size Photo',
                'order' => 4
            ],
            // Employment
            [
                'name' => 'Update Resume / CV',
                'category' => 'employment',
                'is_required' => true,
                'urgency' => 'required',
                'description' => 'Updated Curriculum Vitae',
                'order' => 5
            ],
            [
                'name' => 'Appointment Letter',
                'category' => 'employment',
                'is_required' => true,
                'urgency' => 'required',
                'description' => 'Signed Appointment Letter',
                'order' => 6
            ],
            [
                'name' => 'Joining Letter',
                'category' => 'employment',
                'is_required' => true,
                'urgency' => 'required',
                'description' => 'Signed Joining Letter',
                'order' => 7
            ],
            // Education
            [
                'name' => 'Educational Certificates',
                'category' => 'education',
                'is_required' => false,
                'urgency' => 'nice_to_have',
                'description' => 'All Academic Certificates',
                'order' => 8
            ],
            [
                'name' => 'Experience Certificates',
                'category' => 'employment',
                'is_required' => false,
                'urgency' => 'nice_to_have',
                'description' => 'Previous Employment Certificates',
                'order' => 9
            ],
            [
                'name' => 'Clearance Certificate',
                'category' => 'employment',
                'is_required' => false,
                'urgency' => 'nice_to_have',
                'description' => 'Clearance from previous employer',
                'order' => 10
            ],
        ];

        foreach ($types as $type) {
            \App\Modules\HRM\Models\DocumentType::updateOrCreate(
                ['name' => $type['name']],
                [
                    'category' => $type['category'],
                    'is_required' => $type['is_required'],
                    'urgency' => $type['urgency'],
                    'description' => $type['description'],
                    'order' => $type['order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
