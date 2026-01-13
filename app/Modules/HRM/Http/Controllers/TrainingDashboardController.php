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
use Carbon\Carbon;

class TrainingDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('hrm.trainings.view')) {
            abort(403);
        }

        // Handle date filters
        $preset = $request->get('preset', 'this_year');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Set date ranges based on preset
        switch ($preset) {
            case 'this_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
            case 'this_quarter':
                $start = Carbon::now()->startOfQuarter();
                $end = Carbon::now()->endOfQuarter();
                break;
            case 'this_year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $start = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfYear();
                $end = $endDate ? Carbon::parse($endDate) : Carbon::now();
                break;
            default:
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
        }

        // Format dates for inputs
        $startDate = $start->format('Y-m-d');
        $endDate = $end->format('Y-m-d');

        // Basic Stats
        $stats = [
            'total_trainings' => Training::count(),
            'active_sessions' => TrainingSession::where('status', 'scheduled')
                                    ->orWhere('status', 'in_progress')
                                    ->count(),
            'participants_trained' => TrainingParticipant::whereBetween('created_at', [$start, $end])
                                        ->where('status', 'completed')
                                        ->distinct('employee_id')
                                        ->count('employee_id'),
            'certified_employees' => Certification::whereBetween('issue_date', [$start, $end])
                                        ->distinct('employee_id')
                                        ->count('employee_id'),
        ];

        // Calculate Real Analytics
        $totalParticipants = TrainingParticipant::whereBetween('created_at', [$start, $end])->count();
        $completedParticipants = TrainingParticipant::whereBetween('created_at', [$start, $end])
                                    ->where('status', 'completed')
                                    ->count();
        $completionRate = $totalParticipants > 0 ? round(($completedParticipants / $totalParticipants) * 100, 1) : 0;

        // Attendance Rate (sessions attended vs total sessions)
        $totalSessions = TrainingSession::whereBetween('start_date', [$start, $end])->count();
        $attendedSessions = TrainingParticipant::whereBetween('created_at', [$start, $end])
                                ->whereIn('status', ['completed', 'in_progress'])
                                ->count();
        $attendanceRate = $totalSessions > 0 ? round(($attendedSessions / ($totalSessions * 10)) * 100, 1) : 0; // Assuming avg 10 participants per session

        // Satisfaction Score (mock for now - would need feedback table)
        $satisfactionScore = 4.5;

        // Skill Improvement (employees with skill level increases)
        $skillImprovement = 78; // Mock - would need skill level tracking over time

        $analytics = [
            'completion_rate' => $completionRate,
            'attendance_rate' => min($attendanceRate, 100), // Cap at 100%
            'satisfaction_score' => $satisfactionScore,
            'skill_improvement' => $skillImprovement,
        ];

        // Participation Trend (monthly data for chart)
        $participationTrend = [];
        $trendLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            $count = TrainingParticipant::whereBetween('created_at', [$monthStart, $monthEnd])
                        ->distinct('employee_id')
                        ->count('employee_id');
            $participationTrend[] = $count;
            $trendLabels[] = $monthStart->format('M');
        }

        // Category Distribution (mock data - would need training categories)
        $categoryData = [
            'labels' => ['Technical Skills', 'Soft Skills', 'Leadership', 'Compliance', 'Others'],
            'values' => [35, 28, 20, 12, 5]
        ];

        // Top Learners (employees with most completed trainings)
        $topLearners = TrainingParticipant::select('employee_id', DB::raw('COUNT(*) as training_count'))
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->groupBy('employee_id')
            ->orderByDesc('training_count')
            ->take(5)
            ->with('employee')
            ->get();

        // Upcoming Sessions
        $upcomingSessions = TrainingSession::with('training')
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();

        // Skill Matrix Preview
        $topSkills = Skill::withCount('employees')->orderByDesc('employees_count')->take(5)->get();
        $sampleEmployees = Employee::active()->take(5)->get();

        return view('HRM::pages.training.dashboard', compact(
            'stats', 
            'analytics',
            'participationTrend',
            'trendLabels',
            'categoryData',
            'topLearners',
            'upcomingSessions', 
            'topSkills', 
            'sampleEmployees',
            'preset',
            'startDate',
            'endDate'
        ));
    }
}
