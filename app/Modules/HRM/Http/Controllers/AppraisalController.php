<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Appraisal;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;

class AppraisalController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.appraisals.view')->only(['index', 'show']);
        $this->middleware('permission:hrm.appraisals.create')->only(['create', 'store']);
        $this->middleware('permission:hrm.appraisals.edit')->only(['edit', 'update']);
    }

    public function index(Request $request)
    {
        $query = Appraisal::with(['employee', 'reviewer']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appraisals = $query->orderBy('review_date', 'desc')->paginate(20);
        $employees = Employee::active()->get();

        return view('HRM::pages.appraisals.index', compact('appraisals', 'employees'));
    }

    public function create()
    {
        $employees = Employee::active()->get();
        $reviewers = Employee::active()->get();
        
        return view('HRM::pages.appraisals.create', compact('employees', 'reviewers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'reviewer_id' => 'required|exists:hrm_employees,id',
            'review_period' => 'required|string',
            'review_date' => 'required|date',
            'performance_score' => 'required|integer|min:1|max:10',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'goals' => 'nullable|string',
            'comments' => 'nullable|string',
            'status' => 'required|in:draft,completed',
        ]);

        Appraisal::create($validated);

        return redirect()->route('hrm.appraisals.index')->with('success', 'Appraisal created successfully!');
    }

    public function show(Appraisal $appraisal)
    {
        $appraisal->load(['employee', 'reviewer']);
        return view('HRM::pages.appraisals.show', compact('appraisal'));
    }

    public function edit(Appraisal $appraisal)
    {
        $employees = Employee::active()->get();
        $reviewers = Employee::active()->get();
        
        return view('HRM::pages.appraisals.edit', compact('appraisal', 'employees', 'reviewers'));
    }

    public function update(Request $request, Appraisal $appraisal)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'reviewer_id' => 'required|exists:hrm_employees,id',
            'review_period' => 'required|string',
            'review_date' => 'required|date',
            'performance_score' => 'required|integer|min:1|max:10',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'goals' => 'nullable|string',
            'comments' => 'nullable|string',
            'status' => 'required|in:draft,completed',
        ]);

        $appraisal->update($validated);

        return redirect()->route('hrm.appraisals.index')->with('success', 'Appraisal updated successfully!');
    }

    public function destroy(Appraisal $appraisal)
    {
        $appraisal->delete();
        return redirect()->route('hrm.appraisals.index')->with('success', 'Appraisal deleted successfully!');
    }
}
