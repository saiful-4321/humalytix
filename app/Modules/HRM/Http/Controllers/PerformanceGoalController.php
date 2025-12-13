<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\PerformanceGoal;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\Kpi;
use Illuminate\Http\Request;

class PerformanceGoalController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.performance.view')->only(['index', 'myGoals']);
        $this->middleware('permission:hrm.performance.create')->only(['store']);
        $this->middleware('permission:hrm.performance.edit')->only(['update', 'updateProgress']);
        $this->middleware('permission:hrm.performance.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = PerformanceGoal::with(['employee', 'kpi']);

        // Filters
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('kpi_id')) {
            $query->where('kpi_id', $request->kpi_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $goals = $query->orderBy('due_date', 'asc')->paginate(20);
        $employees = Employee::active()->orderBy('first_name')->get();
        $kpis = Kpi::active()->orderBy('name')->get();

        return view('HRM::pages.performance.goals.index', compact('goals', 'employees', 'kpis'));
    }

    public function show(PerformanceGoal $performanceGoal)
    {
        $performanceGoal->load(['employee', 'kpi']);
        
        if (request()->ajax()) {
            return response()->json($performanceGoal);
        }
        
        return response()->json($performanceGoal);
    }

    public function myGoals(Request $request)
    {
        $employee = auth()->user()->employee;
        
        if (!$employee) {
            return redirect()->route('hrm.dashboard')->with('error', 'No employee profile linked to your account.');
        }

        $query = PerformanceGoal::where('employee_id', $employee->id)->with(['kpi']);

        $activeGoals = (clone $query)->active()->get();
        $completedGoals = (clone $query)->completed()->orderBy('updated_at', 'desc')->take(10)->get();
        $overdueGoals = (clone $query)->overdue()->get();

        return view('HRM::pages.performance.goals.my-goals', compact('activeGoals', 'completedGoals', 'overdueGoals', 'employee'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'kpi_id' => 'nullable|exists:hrm_kpis,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after:start_date',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:not_started,in_progress,completed,cancelled',
            'progress' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        $goal = PerformanceGoal::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Performance goal created successfully!',
                'data' => $goal->load(['employee', 'kpi'])
            ]);
        }

        return redirect()->route('hrm.performance-goals.index')->with('success', 'Performance goal created successfully!');
    }

    public function update(Request $request, PerformanceGoal $performanceGoal)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'kpi_id' => 'nullable|exists:hrm_kpis,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after:start_date',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:not_started,in_progress,completed,cancelled',
            'progress' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $performanceGoal->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Performance goal updated successfully!',
                'data' => $performanceGoal->load(['employee', 'kpi'])
            ]);
        }

        return redirect()->route('hrm.performance-goals.index')->with('success', 'Performance goal updated successfully!');
    }

    public function updateProgress(Request $request, PerformanceGoal $performanceGoal)
    {
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        // Auto-update status based on progress
        if ($validated['progress'] == 100 && $performanceGoal->status != 'completed') {
            $validated['status'] = 'completed';
        } elseif ($validated['progress'] > 0 && $performanceGoal->status == 'not_started') {
            $validated['status'] = 'in_progress';
        }

        $performanceGoal->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Progress updated successfully!',
                'data' => $performanceGoal
            ]);
        }

        return back()->with('success', 'Progress updated successfully!');
    }

    public function destroy(PerformanceGoal $performanceGoal)
    {
        $performanceGoal->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Performance goal deleted successfully!'
            ]);
        }

        return redirect()->route('hrm.performance-goals.index')->with('success', 'Performance goal deleted successfully!');
    }
}
