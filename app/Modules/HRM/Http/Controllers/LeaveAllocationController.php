<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\LeaveAllocation;
use App\Modules\HRM\Models\LeaveType;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;

class LeaveAllocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.leaves.view')->only(['index']);
        $this->middleware('permission:hrm.leaves.create')->only(['generate', 'update']); // Allocation generation is high privilege
    }

    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        
        $query = LeaveAllocation::with(['employee', 'leaveType'])
            ->where('year', $year);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $allocations = $query->paginate(20);
        $employees = Employee::active()->get();
        $leaveTypes = LeaveType::active()->get();

        return view('HRM::pages.leaves.allocations.index', compact('allocations', 'employees', 'leaveTypes', 'year'));
    }

    public function generate(Request $request)
    {
        $year = $request->input('year', now()->year);
        $leaveTypes = LeaveType::active()->where('is_unlimited', false)->get();
        $employees = Employee::active()->get();

        $count = 0;

        foreach ($employees as $employee) {
            foreach ($leaveTypes as $type) {
                // Check if exists
                $exists = LeaveAllocation::where('employee_id', $employee->id)
                    ->where('leave_type_id', $type->id)
                    ->where('year', $year)
                    ->exists();

                if (!$exists) {
                    LeaveAllocation::create([
                        'employee_id' => $employee->id,
                        'leave_type_id' => $type->id,
                        'year' => $year,
                        'allocated_days' => $type->days_per_year,
                        'used_days' => 0,
                        'carried_over_days' => 0, // Logic for carry over could be complex, skipped for now
                    ]);
                    $count++;
                }
            }
        }

        return redirect()->back()->with('success', "Generated $count allocations for year $year.");
    }

    public function update(Request $request, LeaveAllocation $allocation)
    {
        $request->validate([
            'allocated_days' => 'required|numeric|min:0',
        ]);

        $allocation->update([
            'allocated_days' => $request->allocated_days
        ]);

        return redirect()->back()->with('success', 'Allocation updated.');
    }
}
