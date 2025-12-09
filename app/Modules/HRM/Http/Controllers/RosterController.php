<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Roster;
use App\Modules\HRM\Models\Shift;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RosterController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.attendance.view')->only(['index']);
        $this->middleware('permission:hrm.attendance.create')->only(['store', 'generate']);
    }

    public function index(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $startOfWeek = Carbon::parse($date)->startOfWeek(); // Defaults to Monday
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        $query = Employee::active()->with(['rosters' => function($q) use ($startOfWeek, $endOfWeek) {
            $q->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')]);
        }]);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $employees = $query->orderBy('first_name')->paginate(20);
        $shifts = Shift::active()->get();
        $departments = \App\Modules\HRM\Models\Department::active()->get();

        // Prepare dates array
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = $startOfWeek->copy()->addDays($i);
        }

        return view('HRM::pages.rosters.index', compact('employees', 'shifts', 'departments', 'dates', 'startOfWeek'));
    }

    public function store(Request $request)
    {
        $input = $request->input('roster'); // array [emp_id][date] = shift_id
        $weekDate = $request->input('week_date'); // To track week number
        $weekNumber = Carbon::parse($weekDate)->weekOfYear;

        if (is_array($input)) {
            foreach ($input as $employeeId => $dates) {
                foreach ($dates as $date => $shiftId) {
                    if (!$shiftId) {
                         // Delete if unselected? Or set as Off?
                         // Assuming empty means Off Day or Delete
                         Roster::where('employee_id', $employeeId)->where('date', $date)->delete();
                         continue;
                    }

                    $isOffDay = ($shiftId == 'off'); 

                    Roster::updateOrCreate(
                        [
                            'employee_id' => $employeeId,
                            'date' => $date,
                        ],
                        [
                            'shift_id' => $isOffDay ? null : $shiftId,
                            'is_off_day' => $isOffDay,
                            'week_number' => $weekNumber,
                            'status' => 'published',
                            'updated_by' => auth()->id(),
                        ]
                    );
                }
            }
        }

        return redirect()->back()->with('success', 'Roster updated successfully.');
    }
}
