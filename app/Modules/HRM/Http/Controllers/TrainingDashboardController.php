<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\HRM\Models\Training;
use App\Modules\HRM\Models\TrainingSession;
use App\Modules\HRM\Models\TrainingParticipant;
use App\Modules\HRM\Models\Certification;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\Skill;
use Illuminate\Support\Facades\DB;

class TrainingDashboardController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('hrm.trainings.view')) {
            abort(403);
        }

        // Widgets Logic
        $stats = [
            'total_trainings' => Training::count(),
            'active_sessions' => TrainingSession::where('status', 'scheduled')
                                    ->orWhere('status', 'in_progress')
                                    ->count(),
            'participants_trained' => TrainingParticipant::where('status', 'completed')->count(),
            'certified_employees' => Certification::distinct('employee_id')->count('employee_id'),
        ];

        // Recent Activity / Upcoming Sessions
        $upcomingSessions = TrainingSession::with('training')
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();

        // Skill Matrix Preview (Top 5 skills vs Top 5 Employees for a quick view)
        $topSkills = Skill::withCount('employees')->orderByDesc('employees_count')->take(5)->get();
        $sampleEmployees = Employee::active()->take(5)->get();

        return view('HRM::pages.training.dashboard', compact('stats', 'upcomingSessions', 'topSkills', 'sampleEmployees'));
    }
}
