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

    public function dashboard()
    {
        $today = \Carbon\Carbon::today();
        $currentYear = $today->year;

        // KPI: Employees on Leave Today
        $onLeaveToday = Leave::where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        // KPI: Pending Requests
        $pendingRequests = Leave::where('status', 'pending')->count();

        // KPI: Monthly Leave Requests (for Chart)
        $leaveTrends = Leave::selectRaw('MONTH(start_date) as month, COUNT(*) as count')
            ->whereYear('start_date', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
        
        // Fill missing months with 0
        $monthlyTrendData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyTrendData[] = $leaveTrends[$i] ?? 0;
        }

        // Leave Type Distribution (Approved)
        $typeStats = Leave::selectRaw('leave_type_id, count(*) as count')
            ->where('status', 'approved')
            ->whereYear('start_date', $currentYear)
            ->groupBy('leave_type_id')
            ->with('leaveType')
            ->get()
            ->map(function($item) {
                return [
                    'label' => $item->leaveType->name ?? 'Unknown',
                    'value' => $item->count
                ];
            });

        // Upcoming Holidays
        $upcomingHolidays = \App\Modules\HRM\Models\Holiday::whereDate('start_date', '>=', $today)
            ->orderBy('start_date')
            ->take(5)
            ->get();

        // Recent Requests
        $recentRequests = Leave::with(['employee', 'leaveType'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Employees on Leave Today List (for display)
        $whoIsOut = Leave::with('employee')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get();

        $calendarEvents = $this->getCalendarEvents($currentYear);
        
        // Settings for Weekly Holiday (e.g. ['Friday'])
        $weeklyHolidays = \Illuminate\Support\Facades\DB::table('hrm_settings')->where('name', 'weekly_holidays')->value('payload');
        $weeklyHolidays = $weeklyHolidays ? json_decode($weeklyHolidays, true) : ['Friday'];

        return view('HRM::pages.leaves.dashboard', compact(
            'onLeaveToday', 
            'pendingRequests', 
            'monthlyTrendData', 
            'typeStats', 
            'upcomingHolidays', 
            'recentRequests',
            'whoIsOut',
            'calendarEvents',
            'weeklyHolidays'
        ));
    }

    private function getCalendarEvents($currentYear)
    {
        $events = [];

        // 1. Holidays
        $holidays = \App\Modules\HRM\Models\Holiday::whereYear('start_date', $currentYear)->get();
        foreach ($holidays as $holiday) {
            $events[] = [
                'title' => $holiday->name,
                'start' => $holiday->start_date->format('Y-m-d'),
                'end' => $holiday->end_date ? $holiday->end_date->addDay()->format('Y-m-d') : $holiday->start_date->format('Y-m-d'), // FullCalendar end is exclusive
                'className' => 'bg-danger text-white',
                'allDay' => true
            ];
        }

        // 2. Approved Leaves
        $leaves = Leave::with('employee')
            ->where('status', 'approved')
            ->whereYear('start_date', $currentYear)
            ->get();
            
        foreach ($leaves as $leave) {
            $events[] = [
                'title' => $leave->employee->full_name . ' (' . ($leave->leaveType->code ?? 'L') . ')',
                'start' => $leave->start_date->format('Y-m-d'),
                'end' => $leave->end_date->addDay()->format('Y-m-d'),
                'className' => 'bg-info text-white',
                'allDay' => true,
                'url' => route('hrm.leaves.show', $leave->id)
            ];
        }
        
        return $events;
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

        $leave = Leave::create($validated);

        // Trigger Workflow Engine
        try {
            (new \App\Modules\HRM\Services\WorkflowEngine)->process($leave, 'created');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Workflow Error: " . $e->getMessage());
        }

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
