<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\Leave;
use App\Modules\HRM\Models\AttendanceRegularization;
use App\Modules\HRM\Models\ExpenseClaim;
use App\Modules\HRM\Models\Roster;
use App\Modules\HRM\Models\Shift;
use Illuminate\Support\Facades\DB;

class ManagerServiceController extends Controller
{
    protected function getManager()
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            abort(403, 'No employee record linked to your account.');
        }
        return $employee;
    }

    protected function getSubordinates($managerId)
    {
        return Employee::where('reporting_to', $managerId)->active()->get();
    }

    public function dashboard()
    {
        $manager = $this->getManager();
        $subordinates = $this->getSubordinates($manager->id);
        $subordinateIds = $subordinates->pluck('id');

        $pendingLeaves = Leave::whereIn('employee_id', $subordinateIds)->where('status', 'pending')->count();
        $pendingRegularizations = AttendanceRegularization::whereIn('employee_id', $subordinateIds)->where('status', 'pending')->count();
        $pendingExpenses = ExpenseClaim::whereIn('employee_id', $subordinateIds)->where('status', 'pending')->count();
        
        $totalTeam = $subordinates->count();

        return view('HRM::pages.mss.dashboard', compact('manager', 'subordinates', 'pendingLeaves', 'pendingRegularizations', 'pendingExpenses', 'totalTeam'));
    }

    public function team()
    {
        $manager = $this->getManager();
        $subordinates = Employee::where('reporting_to', $manager->id)
            ->with(['department']) // Designation is a column, not a relation
            ->active()
            ->paginate(15);
            
        return view('HRM::pages.mss.team', compact('manager', 'subordinates'));
    }

    public function approvals()
    {
        $manager = $this->getManager();
        $subordinates = $this->getSubordinates($manager->id);
        $subordinateIds = $subordinates->pluck('id');

        $leaves = Leave::whereIn('employee_id', $subordinateIds)
            ->where('status', 'pending')
            ->with('employee')
            ->get();
            
        $regularizations = AttendanceRegularization::whereIn('employee_id', $subordinateIds)
            ->where('status', 'pending')
            ->with('employee')
            ->get();
            
        $expenses = ExpenseClaim::whereIn('employee_id', $subordinateIds)
            ->where('status', 'pending')
            ->with('employee')
            ->get();

        return view('HRM::pages.mss.approvals', compact('leaves', 'regularizations', 'expenses'));
    }

    // Leave Actions
    public function approveLeave(Request $request, Leave $leave)
    {
        // Simple approval for Manager (Subject to Policy, here we assume Manager has authority)
        $leave->update([
            'status' => 'approved', 
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'is_completed' => true
        ]);

        return back()->with('success', 'Leave approved.');
    }

    public function rejectLeave(Request $request, Leave $leave)
    {
        $leave->update([
            'status' => 'rejected', 
            'rejection_reason' => $request->remarks,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'is_completed' => true
        ]);
        return back()->with('success', 'Leave rejected.');
    }

    // Regularization Actions
    public function approveRegularization(Request $request, AttendanceRegularization $regularization)
    {
        $regularization->update(['status' => 'approved', 'approved_by' => auth()->id()]);
        
        // Sync to Attendance
        \App\Modules\HRM\Models\Attendance::updateOrCreate(
            ['employee_id' => $regularization->employee_id, 'date' => $regularization->date],
            [
                'check_in' => $regularization->check_in,
                'check_out' => $regularization->check_out,
                'status' => 'present',
                'updated_by' => auth()->id(),
            ]
        );

        return back()->with('success', 'Regularization approved and attendance updated.');
    }

    public function rejectRegularization(Request $request, AttendanceRegularization $regularization)
    {
        $regularization->update(['status' => 'rejected', 'remarks' => $request->remarks]);
        return back()->with('success', 'Regularization rejected.');
    }

    // Expense Actions
    public function approveExpense(Request $request, ExpenseClaim $expense)
    {
        $expense->update(['status' => 'approved', 'approved_by' => auth()->id()]);
        return back()->with('success', 'Expense claim approved.');
    }

    public function rejectExpense(Request $request, ExpenseClaim $expense)
    {
        $expense->update(['status' => 'rejected', 'remarks' => $request->remarks]);
        return back()->with('success', 'Expense claim rejected.');
    }

    // Roster / Shifts
    public function roster()
    {
        $manager = $this->getManager();
        $subordinates = Employee::where('reporting_to', $manager->id)->active()->get();
        $shifts = Shift::all();
        // Maybe fetch next 7 days roster
        
        return view('HRM::pages.mss.roster', compact('subordinates', 'shifts'));
    }

    public function storeRoster(Request $request)
    {
        // Simple assignment: Employee, Shift, Date Range
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'shift_id' => 'required|exists:hrm_shifts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        // Ensure employee is subordinate
        $employee = Employee::find($validated['employee_id']);
        if ($employee->reporting_to != auth()->user()->employee->id) {
            return back()->with('error', 'Unauthorized.');
        }

        // Create rosters for each day loops
        $start = \Carbon\Carbon::parse($validated['start_date']);
        $end = \Carbon\Carbon::parse($validated['end_date']);

        while ($start <= $end) {
            Roster::updateOrCreate(
                [
                    'employee_id' => $validated['employee_id'],
                    'date' => $start->format('Y-m-d'),
                ],
                [
                    'shift_id' => $validated['shift_id'],
                    'week_number' => $start->weekOfYear,
                    'status' => 'published',
                    'updated_by' => auth()->id(),
                ]
            );
            $start->addDay();
        }

        return back()->with('success', 'Shift assigned successfully.');
    }
}
