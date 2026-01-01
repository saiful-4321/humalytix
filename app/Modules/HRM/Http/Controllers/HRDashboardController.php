<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\Department;
use App\Modules\HRM\Models\Leave;
use App\Modules\HRM\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use PDF;
use Excel;

class HRDashboardController extends Controller
{
    /**
     * Display HR Dashboard
     */
    public function index(Request $request)
    {
        // Date range filter
        $startDate = $request->get('start_date', now()->startOfYear()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $departmentId = $request->get('department_id');

        // Get all metrics
        $data = [
            'headcount' => $this->getHeadcountData($departmentId),
            'attrition' => $this->getAttritionData($startDate, $endDate, $departmentId),
            'diversity' => $this->getDiversityData($departmentId),
            'growth' => $this->getGrowthTrend($startDate, $endDate, $departmentId),
            'departments' => $this->getDepartmentDistribution(),
            'tenure' => $this->getTenureData($departmentId),
        ];

        // Get departments for filter
        $departments = Department::all();

        return view('HRM::pages.analytics.hr-dashboard', compact('data', 'departments', 'startDate', 'endDate', 'departmentId'));
    }

    /**
     * Get headcount data
     */
    private function getHeadcountData($departmentId = null)
    {
        $query = Employee::query();
        
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        return [
            'total' => $query->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'confirmed' => (clone $query)->where('status', 'confirmed')->count(),
            'probation' => (clone $query)->where('status', 'probation')->count(),
            'notice_period' => (clone $query)->where('status', 'notice_period')->count(),
            'on_leave' => Leave::whereIn('employee_id', $query->pluck('id'))
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->count(),
        ];
    }

    /**
     * Get attrition data
     */
    private function getAttritionData($startDate, $endDate, $departmentId = null)
    {
        $query = Employee::onlyTrashed()
            ->whereBetween('deleted_at', [$startDate, $endDate]);
        
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $exits = $query->count();
        $avgHeadcount = Employee::when($departmentId, function($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        })->count();

        $attritionRate = $avgHeadcount > 0 ? ($exits / $avgHeadcount) * 100 : 0;

        return [
            'total_exits' => $exits,
            'attrition_rate' => round($attritionRate, 2),
            'resignations' => 0, // Not tracked in current schema
            'terminations' => 0, // Not tracked in current schema
            'retirements' => 0,  // Not tracked in current schema
        ];
    }

    /**
     * Get diversity data
     */
    private function getDiversityData($departmentId = null)
    {
        $query = Employee::query();
        
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        // Gender distribution
        $genderData = (clone $query)
            ->selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();

        // Age distribution
        $ageRanges = [
            '18-25' => 0,
            '26-35' => 0,
            '36-45' => 0,
            '46-55' => 0,
            '56+' => 0,
        ];

        $employees = (clone $query)->get();
        foreach ($employees as $employee) {
            if ($employee->date_of_birth) {
                $age = Carbon::parse($employee->date_of_birth)->age;
                if ($age >= 18 && $age <= 25) $ageRanges['18-25']++;
                elseif ($age >= 26 && $age <= 35) $ageRanges['26-35']++;
                elseif ($age >= 36 && $age <= 45) $ageRanges['36-45']++;
                elseif ($age >= 46 && $age <= 55) $ageRanges['46-55']++;
                elseif ($age >= 56) $ageRanges['56+']++;
            }
        }

        return [
            'gender' => $genderData,
            'age_ranges' => $ageRanges,
        ];
    }

    /**
     * Get growth trend data
     */
    private function getGrowthTrend($startDate, $endDate, $departmentId = null)
    {
        $months = [];
        $newHires = [];
        $exits = [];

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($start <= $end) {
            $monthStart = $start->copy()->startOfMonth();
            $monthEnd = $start->copy()->endOfMonth();

            $months[] = $start->format('M Y');

            // New hires
            $hires = Employee::whereBetween('joining_date', [$monthStart, $monthEnd])
                ->when($departmentId, function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })
                ->count();
            $newHires[] = $hires;

            // Exits
            $exitsCount = Employee::onlyTrashed()
                ->whereBetween('deleted_at', [$monthStart, $monthEnd])
                ->when($departmentId, function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })
                ->count();
            $exits[] = $exitsCount;

            $start->addMonth();
        }

        return [
            'months' => $months,
            'new_hires' => $newHires,
            'exits' => $exits,
        ];
    }

    /**
     * Get department distribution
     */
    private function getDepartmentDistribution()
    {
        return Department::withCount('employees')
            ->having('employees_count', '>', 0)
            ->get()
            ->map(function($dept) {
                return [
                    'name' => $dept->name,
                    'count' => $dept->employees_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get tenure data
     */
    private function getTenureData($departmentId = null)
    {
        $employees = Employee::when($departmentId, function($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        })->get();

        $tenureRanges = [
            '0-1 years' => 0,
            '1-3 years' => 0,
            '3-5 years' => 0,
            '5-10 years' => 0,
            '10+ years' => 0,
        ];

        $totalTenure = 0;

        foreach ($employees as $employee) {
            if ($employee->joining_date) {
                $tenure = Carbon::parse($employee->joining_date)->diffInYears(now());
                $totalTenure += $tenure;

                if ($tenure < 1) $tenureRanges['0-1 years']++;
                elseif ($tenure >= 1 && $tenure < 3) $tenureRanges['1-3 years']++;
                elseif ($tenure >= 3 && $tenure < 5) $tenureRanges['3-5 years']++;
                elseif ($tenure >= 5 && $tenure < 10) $tenureRanges['5-10 years']++;
                else $tenureRanges['10+ years']++;
            }
        }

        $avgTenure = $employees->count() > 0 ? $totalTenure / $employees->count() : 0;

        return [
            'average' => round($avgTenure, 1),
            'ranges' => $tenureRanges,
        ];
    }

    /**
     * Export dashboard to PDF
     */
    public function exportPDF(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfYear()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $departmentId = $request->get('department_id');

        $data = [
            'headcount' => $this->getHeadcountData($departmentId),
            'attrition' => $this->getAttritionData($startDate, $endDate, $departmentId),
            'diversity' => $this->getDiversityData($departmentId),
            'growth' => $this->getGrowthTrend($startDate, $endDate, $departmentId),
            'departments' => $this->getDepartmentDistribution(),
            'tenure' => $this->getTenureData($departmentId),
        ];

        $pdf = PDF::loadView('HRM::pages.analytics.hr-dashboard-pdf', compact('data', 'startDate', 'endDate'));
        return $pdf->download('hr-dashboard-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export dashboard to Excel
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfYear()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $departmentId = $request->get('department_id');

        $data = [
            'headcount' => $this->getHeadcountData($departmentId),
            'attrition' => $this->getAttritionData($startDate, $endDate, $departmentId),
            'diversity' => $this->getDiversityData($departmentId),
            'departments' => $this->getDepartmentDistribution(),
            'tenure' => $this->getTenureData($departmentId),
        ];

        return Excel::download(new \App\Exports\HRDashboardExport($data), 'hr-dashboard-' . now()->format('Y-m-d') . '.xlsx');
    }
}
