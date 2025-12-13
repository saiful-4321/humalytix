<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Appraisal360;
use App\Modules\HRM\Models\AppraisalReviewer;
use App\Modules\HRM\Models\AppraisalCompetencyRating;
use App\Modules\HRM\Models\Competency;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Appraisal360Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.performance.view')->only(['index', 'show', 'myAppraisals']);
        $this->middleware('permission:hrm.performance.create')->only(['store', 'addReviewers']);
        $this->middleware('permission:hrm.performance.edit')->only(['update', 'submitReview']);
        $this->middleware('permission:hrm.performance.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Appraisal360::with(['employee', 'reviewers']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('appraisal_name', 'like', "%{$request->search}%")
                  ->orWhere('review_period', 'like', "%{$request->search}%");
            });
        }

        $appraisals = $query->orderBy('start_date', 'desc')->paginate(20);
        $employees = Employee::active()->orderBy('first_name')->get();

        return view('HRM::pages.performance.appraisals-360.index', compact('appraisals', 'employees'));
    }

    public function show(Appraisal360 $appraisal360)
    {
        $appraisal360->load(['employee', 'reviewers.reviewer', 'reviewers.competencyRatings.competency']);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $appraisal360
            ]);
        }

        $competencies = Competency::active()->get();
        return view('HRM::pages.performance.appraisals-360.show', compact('appraisal360', 'competencies'));
    }

    public function myAppraisals(Request $request)
    {
        $employee = auth()->user()->employee;
        
        if (!$employee) {
            return redirect()->route('hrm.dashboard')->with('error', 'No employee profile linked to your account.');
        }

        // Appraisals where I'm the subject
        $myAppraisals = Appraisal360::where('employee_id', $employee->id)
            ->with(['reviewers' => function($q) {
                $q->where('status', 'completed');
            }])
            ->orderBy('start_date', 'desc')
            ->get();

        // Appraisals where I need to review others
        $pendingReviews = AppraisalReviewer::where('reviewer_id', $employee->id)
            ->where('status', '!=', 'completed')
            ->with(['appraisal.employee'])
            ->get();

        return view('HRM::pages.performance.appraisals-360.my-appraisals', compact('myAppraisals', 'pendingReviews', 'employee'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'appraisal_name' => 'required|string|max:255',
            'review_period' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:draft,in_progress,completed,cancelled',
        ]);

        $validated['created_by'] = auth()->id();

        $appraisal = Appraisal360::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '360° Appraisal created successfully!',
                'data' => $appraisal->load('employee')
            ]);
        }

        return redirect()->route('hrm.appraisals-360.show', $appraisal)->with('success', '360° Appraisal created successfully! Now add reviewers.');
    }

    public function addReviewers(Request $request, Appraisal360 $appraisal360)
    {
        $validated = $request->validate([
            'reviewers' => 'required|array|min:1',
            'reviewers.*.reviewer_id' => 'required|exists:hrm_employees,id',
            'reviewers.*.reviewer_type' => 'required|in:self,manager,peer,subordinate,customer',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['reviewers'] as $reviewer) {
                // Prevent duplicates
                $exists = $appraisal360->reviewers()
                    ->where('reviewer_id', $reviewer['reviewer_id'])
                    ->exists();

                if (!$exists) {
                    $appraisal360->reviewers()->create($reviewer);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Reviewers added successfully!',
                    'data' => $appraisal360->load('reviewers.reviewer')
                ]);
            }

            return back()->with('success', 'Reviewers added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add reviewers: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to add reviewers: ' . $e->getMessage());
        }
    }

    public function submitReview(Request $request, AppraisalReviewer $reviewer)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:10',
            'feedback' => 'nullable|string',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'competency_ratings' => 'nullable|array',
            'competency_ratings.*.competency_id' => 'required|exists:hrm_competencies,id',
            'competency_ratings.*.rating' => 'required|integer|min:1|max:5',
            'competency_ratings.*.comments' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update reviewer feedback
            $reviewer->update([
                'rating' => $validated['rating'],
                'feedback' => $validated['feedback'] ?? null,
                'strengths' => $validated['strengths'] ?? null,
                'areas_for_improvement' => $validated['areas_for_improvement'] ?? null,
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Add competency ratings
            if (isset($validated['competency_ratings'])) {
                foreach ($validated['competency_ratings'] as $rating) {
                    AppraisalCompetencyRating::updateOrCreate(
                        [
                            'appraisal_reviewer_id' => $reviewer->id,
                            'competency_id' => $rating['competency_id'],
                        ],
                        [
                            'rating' => $rating['rating'],
                            'comments' => $rating['comments'] ?? null,
                        ]
                    );
                }
            }

            // Update overall appraisal score
            $reviewer->appraisal->updateOverallScore();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Review submitted successfully!',
                    'data' => $reviewer->fresh(['competencyRatings'])
                ]);
            }

            return redirect()->route('hrm.appraisals-360.my-appraisals')->with('success', 'Review submitted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit review: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to submit review: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Appraisal360 $appraisal360)
    {
        $validated = $request->validate([
            'appraisal_name' => 'required|string|max:255',
            'review_period' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:draft,in_progress,completed,cancelled',
            'summary' => 'nullable|string',
        ]);

        $appraisal360->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '360° Appraisal updated successfully!',
                'data' => $appraisal360->load('employee')
            ]);
        }

        return redirect()->route('hrm.appraisals-360.index')->with('success', '360° Appraisal updated successfully!');
    }

    public function destroy(Appraisal360 $appraisal360)
    {
        $appraisal360->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '360° Appraisal deleted successfully!'
            ]);
        }

        return redirect()->route('hrm.appraisals-360.index')->with('success', '360° Appraisal deleted successfully!');
    }
}
