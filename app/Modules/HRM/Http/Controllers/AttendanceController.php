<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Attendance;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Enums\AttendanceStatusEnum;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.attendance.view')->only(['index', 'show']);
        $this->middleware('permission:hrm.attendance.create')->only(['checkIn', 'checkOut']);
        $this->middleware('permission:hrm.attendance.edit')->only(['edit', 'update']);
    }

    public function index(Request $request)
    {
        $query = Attendance::with(['employee.department', 'employee.branch']);

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', today());
        }

        // Employee filter
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Department filter
        if ($request->filled('department_id')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('check_in', 'desc')->paginate(20);

        $employees = Employee::active()->get();
        $departments = \App\Modules\HRM\Models\Department::active()->get();

        return view('HRM::pages.attendance.index', compact('attendances', 'employees', 'departments'));
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        // Check if already checked in today
        $existing = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('date', today())
            ->first();

        if ($existing && $existing->check_in) {
            return redirect()->back()->with('error', 'Already checked in today!');
        }

        $attendance = Attendance::create([
            'employee_id' => $validated['employee_id'],
            'date' => today(),
            'check_in' => now(),
            'check_in_latitude' => $validated['latitude'] ?? null,
            'check_in_longitude' => $validated['longitude'] ?? null,
            'status' => AttendanceStatusEnum::PRESENT->value,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Checked in successfully!');
    }

    public function checkOut(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $attendance = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('date', today())
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'No check-in record found for today!');
        }

        $attendance->update([
            'check_out' => now(),
            'check_out_latitude' => $validated['latitude'] ?? null,
            'check_out_longitude' => $validated['longitude'] ?? null,
            'working_hours' => now()->diffInHours($attendance->check_in),
        ]);

        return redirect()->back()->with('success', 'Checked out successfully!');
    }

    public function myAttendance()
    {
        $employee = auth()->user()->employee;
        
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee profile not found!');
        }

        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->paginate(30);

        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', today())
            ->first();

        $stats = [
            'total_days' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', now()->month)
                ->count(),
            'present_days' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', now()->month)
                ->where('status', AttendanceStatusEnum::PRESENT->value)
                ->count(),
            'late_days' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', now()->month)
                ->where('status', AttendanceStatusEnum::LATE->value)
                ->count(),
        ];

        return view('HRM::pages.attendance.my-attendance', compact('attendances', 'todayAttendance', 'stats'));
    }

    public function report(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $attendances = Attendance::with(['employee'])
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('employee_id');

        $employees = Employee::active()->get();

        return view('HRM::pages.attendance.report', compact('attendances', 'employees', 'startDate', 'endDate'));
    }
}
