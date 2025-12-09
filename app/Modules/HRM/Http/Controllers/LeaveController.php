<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\LeaveAllocation;
use App\Modules\HRM\Models\Leave;
use App\Modules\HRM\Models\LeaveType;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.leaves.view')->only(['index', 'show']);
        $this->middleware('permission:hrm.leaves.create')->only(['create', 'store']);
        $this->middleware('permission:hrm.leaves.approve')->only(['approve', 'reject']);
    }

    public function index(Request $request)
    {
        $query = Leave::with(['employee', 'leaveType', 'approvedBy']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(20);
        $employees = Employee::active()->get();
        $leaveTypes = LeaveType::active()->get();

        return view('HRM::pages.leaves.index', compact('leaves', 'employees', 'leaveTypes'));
    }

    public function create()
    {
        $leaveTypes = LeaveType::active()->get();
        $employees = Employee::active()->get();
        
        return view('HRM::pages.leaves.create', compact('leaveTypes', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'leave_type_id' => 'required|exists:hrm_leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('leaves', 'public');
        }

        $leaveType = LeaveType::find($validated['leave_type_id']);

        $validated['days'] = \Carbon\Carbon::parse($validated['start_date'])
            ->diffInDays(\Carbon\Carbon::parse($validated['end_date'])) + 1;
        $validated['status'] = 'pending';
        
        // Attach Chain
        if ($leaveType && $leaveType->approval_chain_id) {
            $validated['approval_chain_id'] = $leaveType->approval_chain_id;
            $validated['current_level'] = 1;
        }

        Leave::create($validated);

        return redirect()->route('hrm.leaves.index')->with('success', 'Leave application submitted successfully!');
    }

    public function show(Leave $leave)
    {
        $leave->load(['employee', 'leaveType', 'approvedBy', 'approvalChain', 'approvalLogs.approver']);
        return view('HRM::pages.leaves.show', compact('leave'));
    }

    public function approve(Leave $leave)
    {
        // Check if using Chain
        if ($leave->approval_chain_id) {
            $chain = $leave->approvalChain;
            $levels = $chain->levels;
            $currentLevel = $leave->current_level;
            
            // Log this approval
            \App\Modules\HRM\Models\LeaveApprovalLog::create([
                'leave_id' => $leave->id,
                'approver_id' => auth()->id(),
                'level' => $currentLevel,
                'status' => 'approved',
                'comments' => 'Approved via system',
            ]);

            // Check if there is a next level
            $nextLevel = $currentLevel + 1;
            $hasNext = $levels->where('level', $nextLevel)->first();

            if ($hasNext) {
                // Move to next level
                $leave->update([
                    'current_level' => $nextLevel,
                    'status' => 'pending' // Still pending final approval
                ]);
                return redirect()->back()->with('success', "Leave approved (Level $currentLevel). Waiting for Level $nextLevel approval.");
            } else {
                // Final Approval
                $leave->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'is_completed' => true
                ]);
                return redirect()->back()->with('success', 'Leave fully approved!');
            }
        } 
        
        // Legacy/Simple Approval
        $leave->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'is_completed' => true
        ]);

        return redirect()->back()->with('success', 'Leave approved successfully!');
    }

    public function reject(Request $request, Leave $leave)
    {
        // Log rejection if chain
        if ($leave->approval_chain_id) {
             \App\Modules\HRM\Models\LeaveApprovalLog::create([
                'leave_id' => $leave->id,
                'approver_id' => auth()->id(),
                'level' => $leave->current_level,
                'status' => 'rejected',
                'comments' => $request->rejection_reason,
            ]);
        }

        $leave->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(), // Rejected by
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
            'is_completed' => true // Ended
        ]);

        return redirect()->back()->with('success', 'Leave rejected!');
    }

    public function myLeaves()
    {
        $employee = auth()->user()->employee;
        
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee profile not found!');
        }

        $leaves = Leave::where('employee_id', $employee->id)
            ->with(['leaveType', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => Leave::where('employee_id', $employee->id)->count(),
            'approved' => Leave::where('employee_id', $employee->id)->where('status', 'approved')->count(),
            'pending' => Leave::where('employee_id', $employee->id)->where('status', 'pending')->count(),
            'rejected' => Leave::where('employee_id', $employee->id)->where('status', 'rejected')->count(),
        ];

            $allocations = LeaveAllocation::with('leaveType')
            ->where('employee_id', $employee->id)
            ->where('year', now()->year)
            ->get();

        return view('HRM::pages.leaves.my-leaves', compact('leaves', 'stats', 'allocations'));
    }
}
