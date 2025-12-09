<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\EmployeeAdvance;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;

class AdvanceController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeAdvance::with(['employee']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $advances = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // For offcanvas form
        $employees = Employee::active()->orderBy('first_name')->get();

        return view('HRM::pages.advances.index', compact('advances', 'employees'));
    }

    public function create()
    {
        $employees = Employee::active()->orderBy('first_name')->get();
        return view('HRM::pages.advances.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'amount' => 'required|numeric|min:1',
            'disbursement_date' => 'required|date',
            'deduction_months' => 'required|integer|min:1|max:12',
            'reason' => 'nullable|string',
        ]);

        $monthlyDeduction = $validated['amount'] / $validated['deduction_months'];

        EmployeeAdvance::create([
            'employee_id' => $validated['employee_id'],
            'amount' => $validated['amount'],
            'disbursement_date' => $validated['disbursement_date'],
            'deduction_months' => $validated['deduction_months'],
            'monthly_deduction' => $monthlyDeduction,
            'outstanding_balance' => $validated['amount'],
            'reason' => $validated['reason'],
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('hrm.advances.index')->with('success', 'Advance request created.');
    }

    public function approve(EmployeeAdvance $advance)
    {
        if ($advance->status !== 'pending') {
            return back()->with('error', 'Only pending advances can be approved.');
        }

        $advance->update([
            'status' => 'active',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Advance approved.');
    }

    public function reject(EmployeeAdvance $advance)
    {
        if ($advance->status !== 'pending') {
            return back()->with('error', 'Only pending advances can be rejected.');
        }

        $advance->update(['status' => 'rejected']);

        return back()->with('success', 'Advance rejected.');
    }

    public function destroy(EmployeeAdvance $advance)
    {
        if ($advance->status === 'active') {
            return back()->with('error', 'Cannot delete active advances.');
        }

        $advance->delete();
        return redirect()->route('hrm.advances.index')->with('success', 'Advance deleted.');
    }
}
