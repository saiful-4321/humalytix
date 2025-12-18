<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\HRM\Models\Training;
use App\Modules\HRM\Models\TrainingSession;
use App\Modules\HRM\Models\TrainingParticipant;
use App\Modules\HRM\Models\Employee;

class TrainingController extends Controller
{
    /**
     * Display the Training Calendar and List.
     */
    public function index()
    {
        if (!auth()->user()->can('hrm.trainings.view')) {
            abort(403);
        }

        $trainings = Training::with('sessions')->orderBy('created_at', 'desc')->get();
        // Separate sessions for Calendar view if needed, but we can iterate in blade
        $upcomingSessions = TrainingSession::with('training')
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->get();

        return view('HRM::pages.training.index', compact('trainings', 'upcomingSessions'));
    }

    /**
     * Store a new Training Program.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('hrm.trainings.create')) {
            abort(403);
        }

        $validated = $request->validate([
            'code' => 'required|unique:hrm_trainings,code',
            'title' => 'required|string|max:255',
            'trainer' => 'required|string',
            'type' => 'required|in:internal,external,online',
            'duration_hours' => 'required|integer',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        Training::create($validated);

        return redirect()->back()->with('success', 'Training program created successfully.');
    }

    /**
     * Update a Training Program.
     */
    public function update(Request $request, Training $training)
    {
        if (!auth()->user()->can('hrm.trainings.edit')) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'trainer' => 'required|string',
            'type' => 'required|in:internal,external,online',
            'duration_hours' => 'required|integer',
            'description' => 'nullable|string',
        ]);

        $training->update($validated);

        return redirect()->back()->with('success', 'Training program updated successfully.');
    }

    /**
     * Schedule a new Training Session.
     */
    public function storeSession(Request $request)
    {
        if (!auth()->user()->can('hrm.trainings.create')) {
            abort(403);
        }

        $validated = $request->validate([
            'training_id' => 'required|exists:hrm_trainings,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string',
            'max_participants' => 'nullable|integer',
        ]);

        $validated['created_by'] = auth()->id();
        TrainingSession::create($validated);

        return redirect()->back()->with('success', 'Training session scheduled successfully.');
    }

    /**
     * Display "My Trainings" for the logged-in employee.
     */
    public function myTrainings()
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->route('dashboard.home')->with('error', 'You are not linked to an employee profile.');
        }

        $enrollments = TrainingParticipant::with(['session.training'])
            ->where('employee_id', $employee->id)
            ->orderByDesc('created_at')
            ->get();

        return view('HRM::pages.training.my_trainings', compact('enrollments'));
    }
}
