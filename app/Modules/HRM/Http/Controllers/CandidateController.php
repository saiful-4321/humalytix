<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Candidate;
use App\Modules\HRM\Models\Job;
use App\Modules\HRM\Models\Interview;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.jobs.view')->only(['index', 'show']);
        $this->middleware('permission:hrm.jobs.create')->only(['create', 'store', 'updateStatus', 'scheduleInterview']);
    }

    public function index(Request $request)
    {
        $query = Candidate::with(['job', 'interviews']);

        if ($request->filled('job_id')) {
            $query->where('job_id', $request->job_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $candidates = $query->orderBy('created_at', 'desc')->paginate(20);
        $jobs = Job::where('status', 'open')->get();

        return view('HRM::pages.candidates.index', compact('candidates', 'jobs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_id' => 'required|exists:hrm_jobs,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:hrm_candidates,email',
            'phone' => 'required|string|max:20',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'cover_letter' => 'nullable|string',
        ]);

        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('resumes', 'public');
            $validated['resume_path'] = $path;
        }

        $validated['status'] = 'applied';
        $validated['application_date'] = now();

        Candidate::create($validated);

        return redirect()->back()->with('success', 'Candidate added successfully!');
    }

    public function show(Candidate $candidate)
    {
        $candidate->load(['job', 'interviews.interviewers', 'offerLetter']);
        $interviewers = Employee::active()->get();
        return view('HRM::pages.candidates.show', compact('candidate', 'interviewers'));
    }

    public function updateStatus(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'status' => 'required|in:applied,screening,shortlisted,interview,offered,hired,rejected',
        ]);

        $candidate->update(['status' => $validated['status']]);

        // Logic for "One Click Conversion" could go here if status is 'hired'
        if ($validated['status'] == 'hired' && !$candidate->employee_id) {
            // Redirect to employee create page with candidate data pre-filled
            return redirect()->route('hrm.employees.create', ['candidate_id' => $candidate->id])->with('success', 'Candidate marked as hired! Please complete employee profile.');
        }

        return redirect()->back()->with('success', 'Candidate status updated successfully!');
    }

    public function scheduleInterview(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'interview_type' => 'required|string',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:15',
            'location' => 'nullable|string',
            'interviewer_ids' => 'required|array',
            'interviewer_ids.*' => 'exists:hrm_employees,id',
        ]);

        $interview = Interview::create([
            'candidate_id' => $candidate->id,
            'job_id' => $candidate->job_id,
            'interview_type' => $validated['interview_type'],
            'scheduled_at' => $validated['scheduled_at'],
            'duration_minutes' => $validated['duration_minutes'],
            'location' => $validated['location'],
            'status' => 'scheduled',
        ]);

        $interview->interviewers()->sync($validated['interviewer_ids']);

        $candidate->update(['status' => 'interview']);

        return redirect()->back()->with('success', 'Interview scheduled successfully!');
    }
}
