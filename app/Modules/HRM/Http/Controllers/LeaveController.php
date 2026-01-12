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

    public function dashboard(Request $request)
    {
        $today = \Carbon\Carbon::today();
        
        // --- 1. Filter Logic (Presets & Intervals) ---
        $preset = $request->input('preset', 'this_year');
        $interval = $request->input('interval', 'month');
        
        switch ($preset) {
            case 'today':
                $startDate = $today->copy()->startOfDay();
                $endDate = $today->copy()->endOfDay();
                $interval = 'day';
                break;
            case 'this_week':
                $startDate = $today->copy()->startOfWeek();
                $endDate = $today->copy()->endOfWeek();
                if ($request->missing('interval')) $interval = 'day';
                break;
            case 'this_month':
                $startDate = $today->copy()->startOfMonth();
                $endDate = $today->copy()->endOfMonth();
                if ($request->missing('interval')) $interval = 'week';
                break;
            case 'last_month':
                $startDate = $today->copy()->subMonth()->startOfMonth();
                $endDate = $today->copy()->subMonth()->endOfMonth();
                if ($request->missing('interval')) $interval = 'week';
                break;
            case 'this_quarter':
                $startDate = $today->copy()->startOfQuarter();
                $endDate = $today->copy()->endOfQuarter();
                if ($request->missing('interval')) $interval = 'month';
                break;
            case 'this_year':
                $startDate = $today->copy()->startOfYear();
                $endDate = $today->copy()->endOfYear();
                if ($request->missing('interval')) $interval = 'month';
                break;
            case 'custom':
                $startDate = \Carbon\Carbon::parse($request->input('start_date', $today->copy()->startOfYear()));
                $endDate = \Carbon\Carbon::parse($request->input('end_date', $today->copy()->endOfYear()));
                break;
            default:
                $startDate = $today->copy()->startOfYear();
                $endDate = $today->copy()->endOfYear();
                break;
        }

        // --- 2. KPI Totals (Aggregate in Range) ---
        // Employees on Leave Today (always today-centric for this KPI)
        $onLeaveToday = Leave::where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        // Pending Requests (Total outstanding)
        $pendingRequests = Leave::where('status', 'pending')->count();

        // Total Requests in Selected Period
        $periodRequests = Leave::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // Approved Requests in Selected Period
        $periodApproved = Leave::where('status', 'approved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Total Active Employees
        $totalEmployees = Employee::where('status', 'confirmed')->count();

        // --- 3. Trend & Sparkline Data ---
        $trendData = $this->getTrendData($startDate, $endDate, $interval);
        
        // Sparklines are based on trend data points
        $sparklineRequests = $trendData['requests'];
        $sparklineApproved = $trendData['approved'];
        $sparklinePending = $trendData['pending'];
        $sparklineRejected = $trendData['rejected'];

        // --- 4. Extra Stats ---
        // Leave Type Distribution (Approved in period)
        $typeStats = Leave::selectRaw('leave_type_id, count(*) as count')
            ->where('status', 'approved')
            ->whereBetween('start_date', [$startDate, $endDate])
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
            
        // Who is Out Today
        $whoIsOut = Leave::with('employee')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get();

        $calendarEvents = $this->getCalendarEvents($today->year);
        
        $weeklyHolidays = \Illuminate\Support\Facades\DB::table('hrm_settings')->where('name', 'weekly_holidays')->value('payload');
        $weeklyHolidays = $weeklyHolidays ? json_decode($weeklyHolidays, true) : ['Friday'];

        // Gauge Data Calculations
        $approvalRate = $periodRequests > 0 ? ($periodApproved / $periodRequests) * 100 : 0;
        $utilizationRate = $totalEmployees > 0 ? ($onLeaveToday / $totalEmployees) * 100 : 0;
        $rejectionRate = $periodRequests > 0 ? (Leave::where('status', 'rejected')->whereBetween('created_at', [$startDate, $endDate])->count() / $periodRequests) * 100 : 0;

        return view('HRM::pages.leaves.dashboard', compact(
            'onLeaveToday', 'pendingRequests', 'periodRequests', 'periodApproved', 'totalEmployees',
            'startDate', 'endDate', 'preset', 'interval', 'today',
            'sparklineRequests', 'sparklineApproved', 'sparklinePending', 'sparklineRejected',
            'typeStats', 'upcomingHolidays', 'recentRequests', 'whoIsOut',
            'calendarEvents', 'weeklyHolidays', 'trendData',
            'approvalRate', 'utilizationRate', 'rejectionRate'
        ));
    }

    private function getTrendData($start, $end, $interval)
    {
        $labels = [];
        $buckets = [];
        $current = $start->copy();
        $endC = $end->copy();
        
        while ($current <= $endC) {
            if ($interval == 'day') {
                $key = $current->format('Y-m-d');
                $label = $current->format('d M');
                $next = $current->copy()->addDay();
            } elseif ($interval == 'week') {
                $key = $current->format('o-W');
                $label = 'W' . $current->format('W') . ' ' . $current->format('M');
                $next = $current->copy()->addWeek();
            } else { // month default
                $key = $current->format('Y-m');
                $label = $current->format('M Y');
                $next = $current->copy()->addMonth();
            }
            
            $labels[] = $label;
            $buckets[$key] = [
                'start' => $current->copy(),
                'end' => $interval == 'day' ? $current->copy()->endOfDay() : ($interval == 'week' ? $current->copy()->endOfWeek() : $current->copy()->endOfMonth())
            ];
            
            $current = $next;
        }

        $requestsData = [];
        $approvedData = [];
        $pendingData = [];
        $rejectedData = [];
        
        foreach ($buckets as $bucket) {
            $requestsData[] = Leave::whereBetween('created_at', [$bucket['start'], $bucket['end']])->count();
            $approvedData[] = Leave::where('status', 'approved')->whereBetween('created_at', [$bucket['start'], $bucket['end']])->count();
            $pendingData[] = Leave::where('status', 'pending')->whereBetween('created_at', [$bucket['start'], $bucket['end']])->count();
            $rejectedData[] = Leave::where('status', 'rejected')->whereBetween('created_at', [$bucket['start'], $bucket['end']])->count();
        }
        
        return [
            'labels' => $labels,
            'requests' => $requestsData,
            'approved' => $approvedData,
            'pending' => $pendingData,
            'rejected' => $rejectedData
        ];
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
