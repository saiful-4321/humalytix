<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Resignation;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\ClearanceChecklist;
use App\Modules\HRM\Models\FinalSettlement;
use Illuminate\Http\Request;

class ResignationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.employees.edit')->except(['store']);
    }

    public function index()
    {
        $resignations = Resignation::with('employee')->orderBy('created_at', 'desc')->paginate(20);
        return view('HRM::pages.resignations.index', compact('resignations'));
    }

    public function create()
    {
        $employees = Employee::active()->get();
        return view('HRM::pages.resignations.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'resignation_date' => 'required|date',
            'notice_date' => 'required|date',
            'reason' => 'required|string',
        ]);

        $validated['status'] = 'pending';
        $validated['last_working_day'] = $validated['notice_date']; // Default

        Resignation::create($validated);

        return redirect()->route('hrm.resignations.index')->with('success', 'Resignation submitted successfully!');
    }

    public function show(Resignation $resignation)
    {
        $resignation->load(['employee', 'finalSettlement', 'employee.assets.asset']);
        $checklist = ClearanceChecklist::where('resignation_id', $resignation->id)->get();
        
        return view('HRM::pages.resignations.show', compact('resignation', 'checklist'));
    }

    public function updateStatus(Request $request, Resignation $resignation)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,withdrawn',
            'last_working_day' => 'required_if:status,approved|date',
        ]);

        $resignation->update([
            'status' => $validated['status'],
            'last_working_day' => $validated['last_working_day'] ?? $resignation->last_working_day,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        if ($validated['status'] == 'approved') {
            // Initialize Clearance Checklist
            $departments = ['IT', 'Admin', 'HR', 'Finance'];
            foreach ($departments as $dept) {
                ClearanceChecklist::create([
                    'resignation_id' => $resignation->id,
                    'employee_id' => $resignation->employee_id,
                    'department_name' => $dept,
                    'status' => 'pending',
                ]);
            }
        }

        return back()->with('success', 'Resignation status updated!');
    }

    public function updateClearance(Request $request, ClearanceChecklist $item)
    {
        $validated = $request->validate([
            'status' => 'required|in:cleared,uncleared',
            'remarks' => 'nullable|string',
        ]);

        $item->update([
            'status' => $validated['status'],
            'remarks' => $validated['remarks'],
            'checked_by' => auth()->id(), // assuming user is linked to employee, ideally get logged in employee id
            'cleared_at' => now(),
        ]);

        return back()->with('success', 'Checklist updated!');
    }

    public function generateSettlement(Request $request, Resignation $resignation)
    {
        // Simple logic for settlement calculation
        // In real app, calculate based on payroll, leave balance, etc.
        
        $employee = $resignation->employee;
        $basiccalls = $employee->salary; // assuming salary field or relation
        
        // Check if all clearance cleared
        $pendingClearance = ClearanceChecklist::where('resignation_id', $resignation->id)->where('status', '!=', 'cleared')->count();
        if ($pendingClearance > 0) {
            return back()->with('error', 'Cannot generate settlement. Clearance pending from ' . $pendingClearance . ' departments.');
        }

        FinalSettlement::create([
            'resignation_id' => $resignation->id,
            'employee_id' => $resignation->employee_id,
            'settlement_date' => now(),
            'basic_salary_due' => 0, // Placeholder
            'leave_encashment' => 0,
            'gratuity' => 0,
            'net_payable' => 0,
            'status' => 'pending',
            'processed_by' => auth()->id(),
        ]);
        
        return back()->with('success', 'Final settlement generated!');
    }
}
