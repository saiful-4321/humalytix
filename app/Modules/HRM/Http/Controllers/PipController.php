<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Pip;
use App\Modules\HRM\Models\PipActionItem;
use App\Modules\HRM\Models\PipReview;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PipController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.pips.view')->only(['index', 'show', 'myPips']);
        $this->middleware('permission:hrm.pips.create')->only(['store', 'addActionItem']);
        $this->middleware('permission:hrm.pips.edit')->only(['update', 'updateActionItem', 'addReview', 'updateStatus']);
        $this->middleware('permission:hrm.pips.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Pip::with(['employee', 'manager', 'actionItems']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('reason', 'like', "%{$request->search}%");
            });
        }

        $pips = $query->orderBy('start_date', 'desc')->paginate(20);
        $employees = Employee::active()->orderBy('first_name')->get();

        return view('HRM::pages.performance.pips.index', compact('pips', 'employees'));
    }

    public function show(Pip $pip)
    {
        $pip->load(['employee', 'manager', 'actionItems', 'reviews.reviewedBy']);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $pip
            ]);
        }

        return view('HRM::pages.performance.pips.show', compact('pip'));
    }

    public function myPips(Request $request)
    {
        $employee = auth()->user()->employee;
        
        if (!$employee) {
            return redirect()->route('hrm.dashboard')->with('error', 'No employee profile linked to your account.');
        }

        $activePips = Pip::where('employee_id', $employee->id)
            ->where('status', 'active')
            ->with(['manager', 'actionItems', 'reviews'])
            ->get();

        $completedPips = Pip::where('employee_id', $employee->id)
            ->whereIn('status', ['successful', 'unsuccessful'])
            ->with(['manager'])
            ->orderBy('end_date', 'desc')
            ->take(5)
            ->get();

        return view('HRM::pages.performance.pips.my-pips', compact('activePips', 'completedPips', 'employee'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'manager_id' => 'required|exists:hrm_employees,id',
            'title' => 'required|string|max:255',
            'reason' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'review_date' => 'nullable|date|after:start_date|before:end_date',
            'success_criteria' => 'nullable|string',
            'status' => 'required|in:active,successful,unsuccessful,extended,cancelled',
            'action_items' => 'nullable|array',
            'action_items.*.action_required' => 'required|string',
            'action_items.*.expected_outcome' => 'nullable|string',
            'action_items.*.due_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $validated['created_by'] = auth()->id();
            $actionItems = $validated['action_items'] ?? [];
            unset($validated['action_items']);

            $pip = Pip::create($validated);

            // Create action items
            foreach ($actionItems as $item) {
                $pip->actionItems()->create($item);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'PIP created successfully!',
                    'data' => $pip->load(['employee', 'manager', 'actionItems'])
                ]);
            }

            return redirect()->route('hrm.pips.show', $pip)->with('success', 'PIP created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create PIP: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Failed to create PIP: ' . $e->getMessage());
        }
    }

    public function addActionItem(Request $request, Pip $pip)
    {
        $validated = $request->validate([
            'action_required' => 'required|string',
            'expected_outcome' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        $actionItem = $pip->actionItems()->create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Action item added successfully!',
                'data' => $actionItem
            ]);
        }

        return back()->with('success', 'Action item added successfully!');
    }

    public function updateActionItem(Request $request, PipActionItem $actionItem)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
            'evidence' => 'nullable|string',
        ]);

        $actionItem->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Action item updated successfully!',
                'data' => $actionItem
            ]);
        }

        return back()->with('success', 'Action item updated successfully!');
    }

    public function addReview(Request $request, Pip $pip)
    {
        $validated = $request->validate([
            'review_date' => 'required|date',
            'progress_summary' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:10',
            'manager_comments' => 'nullable|string',
            'employee_comments' => 'nullable|string',
        ]);

        $validated['reviewed_by'] = auth()->user()->employee->id ?? $pip->manager_id;

        $review = $pip->reviews()->create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Review added successfully!',
                'data' => $review->load('reviewedBy')
            ]);
        }

        return back()->with('success', 'Review added successfully!');
    }

    public function updateStatus(Request $request, Pip $pip)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,successful,unsuccessful,extended,cancelled',
            'outcomes' => 'nullable|string',
            'final_review' => 'nullable|string',
            'end_date' => 'required_if:status,extended|nullable|date',
        ]);

        $pip->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'PIP status updated successfully!',
                'data' => $pip
            ]);
        }

        return back()->with('success', 'PIP status updated successfully!');
    }

    public function update(Request $request, Pip $pip)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'reason' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'review_date' => 'nullable|date',
            'success_criteria' => 'nullable|string',
            'status' => 'required|in:active,successful,unsuccessful,extended,cancelled',
            'outcomes' => 'nullable|string',
            'final_review' => 'nullable|string',
        ]);

        $pip->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'PIP updated successfully!',
                'data' => $pip->load(['employee', 'manager'])
            ]);
        }

        return redirect()->route('hrm.pips.index')->with('success', 'PIP updated successfully!');
    }

    public function destroy(Pip $pip)
    {
        $pip->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'PIP deleted successfully!'
            ]);
        }

        return redirect()->route('hrm.pips.index')->with('success', 'PIP deleted successfully!');
    }
}
