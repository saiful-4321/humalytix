<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Okr;
use App\Modules\HRM\Models\OkrKeyResult;
use App\Modules\HRM\Models\Department;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OkrController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.performance.view')->only(['index', 'show']);
        $this->middleware('permission:hrm.performance.create')->only(['store']);
        $this->middleware('permission:hrm.performance.edit')->only(['update', 'updateProgress']);
        $this->middleware('permission:hrm.performance.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Okr::with(['department', 'employee', 'keyResults']);

        // Filters
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('quarter')) {
            $query->where('quarter', $request->quarter);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $okrs = $query->orderBy('year', 'desc')
                     ->orderBy('quarter', 'desc')
                     ->paginate(20);

        $departments = Department::active()->orderBy('name')->get();
        $employees = Employee::active()->orderBy('first_name')->get();
        $currentYear = now()->year;

        return view('HRM::pages.performance.okrs.index', compact('okrs', 'departments', 'employees', 'currentYear'));
    }

    public function show(Okr $okr)
    {
        $okr->load(['department', 'employee', 'keyResults']);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $okr
            ]);
        }

        return view('HRM::pages.performance.okrs.show', compact('okr'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:company,department,individual',
            'department_id' => 'required_if:level,department|nullable|exists:hrm_departments,id',
            'employee_id' => 'required_if:level,individual|nullable|exists:hrm_employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'quarter' => 'nullable|in:Q1,Q2,Q3,Q4',
            'year' => 'required|integer|min:2020',
            'status' => 'required|in:draft,active,completed,cancelled',
            'key_results' => 'required|array|min:1',
            'key_results.*.description' => 'required|string',
            'key_results.*.measurement_unit' => 'nullable|string|max:50',
            'key_results.*.target_value' => 'required|numeric|min:0',
            'key_results.*.weightage' => 'required|integer|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $validated['created_by'] = auth()->id();
            $keyResults = $validated['key_results'];
            unset($validated['key_results']);

            $okr = Okr::create($validated);

            // Create key results
            foreach ($keyResults as $kr) {
                $okr->keyResults()->create($kr);
            }

            $okr->updateProgress();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'OKR created successfully!',
                    'data' => $okr->load('keyResults')
                ]);
            }

            return redirect()->route('hrm.okrs.index')->with('success', 'OKR created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create OKR: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Failed to create OKR: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Okr $okr)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:company,department,individual',
            'department_id' => 'required_if:level,department|nullable|exists:hrm_departments,id',
            'employee_id' => 'required_if:level,individual|nullable|exists:hrm_employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'quarter' => 'nullable|in:Q1,Q2,Q3,Q4',
            'year' => 'required|integer|min:2020',
            'status' => 'required|in:draft,active,completed,cancelled',
        ]);

        $validated['updated_by'] = auth()->id();
        $okr->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'OKR updated successfully!',
                'data' => $okr->load('keyResults')
            ]);
        }

        return redirect()->route('hrm.okrs.index')->with('success', 'OKR updated successfully!');
    }

    public function updateProgress(Request $request, Okr $okr)
    {
        $validated = $request->validate([
            'key_result_id' => 'required|exists:hrm_okr_key_results,id',
            'current_value' => 'required|numeric|min:0',
        ]);

        $keyResult = $okr->keyResults()->findOrFail($validated['key_result_id']);
        $keyResult->update(['current_value' => $validated['current_value']]);
        $keyResult->updateProgress();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Progress updated successfully!',
                'data' => [
                    'key_result' => $keyResult,
                    'okr_progress' => $okr->fresh()->progress
                ]
            ]);
        }

        return back()->with('success', 'Progress updated successfully!');
    }

    public function destroy(Okr $okr)
    {
        $okr->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'OKR deleted successfully!'
            ]);
        }

        return redirect()->route('hrm.okrs.index')->with('success', 'OKR deleted successfully!');
    }
}
