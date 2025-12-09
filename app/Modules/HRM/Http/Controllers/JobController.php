<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Job;
use App\Modules\HRM\Models\Department;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.jobs.view')->only(['index', 'show']);
        $this->middleware('permission:hrm.jobs.create')->only(['create', 'store']);
        $this->middleware('permission:hrm.jobs.edit')->only(['edit', 'update']);
        $this->middleware('permission:hrm.jobs.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Job::with(['department']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        $jobs = $query->orderBy('created_at', 'desc')->paginate(15);
        $departments = Department::active()->get();

        return view('HRM::pages.jobs.index', compact('jobs', 'departments'));
    }

    public function create()
    {
        $departments = Department::active()->get();
        return view('HRM::pages.jobs.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:hrm_jobs,code',
            'department_id' => 'required|exists:hrm_departments,id',
            'positions' => 'required|integer|min:1',
            'employment_type' => 'required|string',
            'experience_required' => 'nullable|string',
            'salary_range' => 'nullable|string',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'deadline' => 'nullable|date',
            'status' => 'required|in:open,closed,on_hold',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = 'JOB-' . str_pad(Job::count() + 1, 4, '0', STR_PAD_LEFT);
        }

        Job::create($validated);

        return redirect()->route('hrm.jobs.index')->with('success', 'Job posted successfully!');
    }

    public function show(Job $job)
    {
        $job->load(['department', 'candidates']);
        
        $stats = [
            'total_candidates' => $job->candidates()->count(),
            'shortlisted' => $job->candidates()->where('status', 'shortlisted')->count(),
            'interviewed' => $job->candidates()->where('status', 'interviewed')->count(),
        ];

        return view('HRM::pages.jobs.show', compact('job', 'stats'));
    }

    public function edit(Job $job)
    {
        $departments = Department::active()->get();
        return view('HRM::pages.jobs.edit', compact('job', 'departments'));
    }

    public function update(Request $request, Job $job)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:hrm_jobs,code,' . $job->id,
            'department_id' => 'required|exists:hrm_departments,id',
            'positions' => 'required|integer|min:1',
            'employment_type' => 'required|string',
            'experience_required' => 'nullable|string',
            'salary_range' => 'nullable|string',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'deadline' => 'nullable|date',
            'status' => 'required|in:open,closed,on_hold',
        ]);

        $job->update($validated);

        return redirect()->route('hrm.jobs.index')->with('success', 'Job updated successfully!');
    }

    public function destroy(Job $job)
    {
        if ($job->candidates()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete job with candidates!');
        }

        $job->delete();

        return redirect()->route('hrm.jobs.index')->with('success', 'Job deleted successfully!');
    }
}
