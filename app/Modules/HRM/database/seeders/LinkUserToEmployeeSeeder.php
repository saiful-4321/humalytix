<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Modules\HRM\Models\Employee;

class LinkUserToEmployeeSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        $employee = Employee::where('email', 'rahim.uddin@sylnovia.com')->first();

        if ($user && $employee) {
            $employee->user_id = $user->id;
            $employee->save();
            $this->command->info("Linked User {$user->email} to Employee {$employee->full_name}");
        } else {
            $this->command->error("User or Employee not found for linking.");
        }
        
        // Ensure reverse link (should be fine as we updated employee table)
    }
}
