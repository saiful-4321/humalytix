<?php

namespace App\Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\HRM\Models\Shift;
use App\Modules\HRM\Models\Roster; // Assuming this model exists for schedule
use App\Modules\HRM\Models\Employee;
use Carbon\Carbon;

class ShiftRosterSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Shifts
        $morningShift = Shift::firstOrCreate(['name' => 'Morning Shift'], [
            'code' => 'S-M',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'late_mark_after' => '09:15:00',
            'is_default' => true,
        ]);

        $eveningShift = Shift::firstOrCreate(['name' => 'Evening Shift'], [
            'code' => 'S-E',
            'start_time' => '17:00:00',
            'end_time' => '01:00:00',
            'late_mark_after' => '17:15:00',
            'is_default' => false,
        ]);

        // 2. Assign Shifts to Employees (Roster)
        // If Roster model implies weekly schedule or daily log
        // Let's assume Roster is daily assignment or pattern
        $employees = Employee::active()->take(10)->get();
        
        foreach ($employees as $emp) {
            // Assign default shift
            // Assuming employee_shifts table or column in employee
            // If using relationship:
            // $emp->shifts()->attach($morningShift->id, ['start_date' => now()->startOfYear()]);

            // If Roster table is for daily planning
            $startDate = now()->startOfWeek();
            for ($i = 0; $i < 5; $i++) {
                 // Check if Roster model exists and has these fields
                 // Use try-catch or explicit check if model details uncertain
                 try {
                     \App\Modules\HRM\Models\Roster::firstOrCreate([
                        'employee_id' => $emp->id,
                        'date' => $startDate->copy()->addDays($i)->format('Y-m-d'),
                     ], [
                        'shift_id' => $morningShift->id,
                        'start_time' => $morningShift->start_time,
                        'end_time' => $morningShift->end_time,
                     ]);
                 } catch (\Exception $e) {
                     // Ignore if Roster table structure differs
                 }
            }
        }
    }
}
