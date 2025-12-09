<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Shift;
use App\Modules\HRM\Models\Roster;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::all();
        $rosters = Roster::with(['employee', 'shift'])
            ->whereDate('date', '>=', now())
            ->orderBy('date')
            ->paginate(50);
        $employees = Employee::active()->get();
        
        return view('HRM::pages.shifts.index', compact('shifts', 'rosters', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'break_duration' => 'nullable|integer',
        ]);

        Shift::create($validated);
        return back()->with('success', 'Shift created successfully!');
    }

    public function assignRoster(Request $request)
    {
        $validated = $request->validate([
            'employee_ids' => 'required|array',
            'shift_id' => 'required|exists:hrm_shifts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $period = \Carbon\CarbonPeriod::create($validated['start_date'], $validated['end_date']);
        
        foreach ($validated['employee_ids'] as $empId) {
            foreach ($period as $date) {
                Roster::updateOrCreate(
                    ['employee_id' => $empId, 'date' => $date->format('Y-m-d')],
                    ['shift_id' => $validated['shift_id']]
                );
            }
        }

        return back()->with('success', 'Roster assigned successfully!');
    }
}
