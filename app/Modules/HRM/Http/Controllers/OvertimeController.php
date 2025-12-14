<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\EmployeeOvertime;
use App\Modules\HRM\Models\OTPolicy;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeOvertime::with(['employee', 'policy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->whereMonth('ot_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('ot_date', $request->year);
        }

        $overtimes = $query->orderBy('ot_date', 'desc')->paginate(20);
        
        // For offcanvas form
        $employees = Employee::active()->orderBy('first_name')->get();
        $policies = OTPolicy::active()->get();

        return view('HRM::pages.overtime.index', compact('overtimes', 'employees', 'policies'));
    }

    public function create()
    {
        $employees = Employee::active()->orderBy('first_name')->get();
        $policies = OTPolicy::active()->get();

        return view('HRM::pages.overtime.create', compact('employees', 'policies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'ot_policy_id' => 'required|exists:hrm_ot_policies,id',
            'ot_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'ot_type' => 'required|in:regular,weekend,holiday,night_shift',
        ]);

        $policy = OTPolicy::find($validated['ot_policy_id']);
        $employee = Employee::find($validated['employee_id']);

        $start = Carbon::parse($validated['ot_date'] . ' ' . $validated['start_time']);
        $end = Carbon::parse($validated['ot_date'] . ' ' . $validated['end_time']);
        
        if ($end < $start) {
            $end->addDay();
        }

        $totalMinutes = $start->diffInMinutes($end);
        $totalHours = $totalMinutes / 60;

        // Get hourly rate (basic / 208 hours per month as standard)
        $basicSalary = $employee->salary?->basic_salary ?? 0;
        $hourlyRate = $basicSalary / 208;

        // Get multiplier based on OT type
        $multiplier = match($validated['ot_type']) {
            'weekend' => $policy->weekend_multiplier,
            'holiday' => $policy->holiday_multiplier,
            'night_shift' => $policy->night_shift_multiplier,
            default => $policy->multiplier,
        };

        $otAmount = $hourlyRate * $totalHours * $multiplier;

        EmployeeOvertime::create([
            'employee_id' => $validated['employee_id'],
            'ot_policy_id' => $validated['ot_policy_id'],
            'ot_date' => $validated['ot_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'total_minutes' => $totalMinutes,
            'total_hours' => round($totalHours, 2),
            'ot_type' => $validated['ot_type'],
            'multiplier' => $multiplier,
            'hourly_rate' => round($hourlyRate, 2),
            'ot_amount' => round($otAmount, 2),
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('hrm.overtime.index')->with('success', 'Overtime record created.');
    }

    public function approve(EmployeeOvertime $overtime)
    {
        $overtime->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Overtime approved.');
    }

    public function reject(EmployeeOvertime $overtime)
    {
        $overtime->update(['status' => 'rejected']);
        return back()->with('success', 'Overtime rejected.');
    }

    public function destroy(EmployeeOvertime $overtime)
    {
        if ($overtime->status === 'paid') {
            return back()->with('error', 'Cannot delete paid overtime.');
        }

        $overtime->delete();
        return redirect()->route('hrm.overtime.index')->with('success', 'Overtime deleted.');
    }
}
