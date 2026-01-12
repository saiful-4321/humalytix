<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\Attendance;
use App\Modules\HRM\Models\AttendanceRegularization;
use App\Modules\HRM\Models\Payroll;
use App\Modules\HRM\Models\Asset;
use App\Modules\HRM\Models\LetterRequest;
use App\Modules\HRM\Models\Holiday;
use App\Modules\HRM\Models\Leave; 
use App\Modules\HRM\Models\ExpenseClaim;
use Illuminate\Support\Facades\DB;
use App\Modules\HRM\Enums\AttendanceStatusEnum;
use App\Modules\HRM\Models\PerformanceGoal;
use App\Modules\HRM\Models\LeaveAllocation;

class EmployeeServiceController extends Controller
{
    protected function getEmployee()
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            abort(403, 'No employee record linked to your account.');
        }
        return $employee;
    }

    public function dashboard()
    {
        $employee = $this->getEmployee();
        
        // Basic Stats
        $pendingLeaves = Leave::where('employee_id', $employee->id)->where('status', 'pending')->count();
        $attendanceToday = Attendance::where('employee_id', $employee->id)->whereDate('date', today())->first();
        $lastPayroll = Payroll::where('employee_id', $employee->id)->orderBy('month', 'desc')->first();

        // 1. Leave Balances (Personal)
        $leaveAllocations = \App\Modules\HRM\Models\LeaveAllocation::where('employee_id', $employee->id)
            ->where('year', now()->year)
            ->with('leaveType')
            ->get();

        $sickLeave = $leaveAllocations->where('leave_type_id', 2)->first();
        $casualLeave = $leaveAllocations->where('leave_type_id', 1)->first();
        $totalLeaveBalance = $leaveAllocations->sum(function($alloc) {
            return $alloc->balance;
        });

        $leaveStats = [
            'sick' => $sickLeave ? $sickLeave->balance : 0,
            'casual' => $casualLeave ? $casualLeave->balance : 0,
            'total' => $totalLeaveBalance
        ];

        // 2. Office Presence Stats (Today)
        $totalActiveEmployees = Employee::active()->count();
        $presentCount = Attendance::whereDate('date', today())->distinct('employee_id')->count();
        $leaveTodayCount = Leave::where('status', 'approved')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->count();
        $lateInCount = Attendance::whereDate('date', today())
            ->where('status', AttendanceStatusEnum::LATE)
            ->count();
        $absentCount = max(0, $totalActiveEmployees - ($presentCount + $leaveTodayCount));

        $officePresence = [
            'present' => $presentCount,
            'absent' => $absentCount,
            'on_leave' => $leaveTodayCount,
            'late' => $lateInCount
        ];

        // 3. Attendance Trend (Last 7 Days)
        $attendanceTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $att = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $date)
                ->first();
            
            $hours = 0;
            if ($att && $att->check_in && $att->check_out) {
                $hours = \Carbon\Carbon::parse($att->check_in)->diffInHours(\Carbon\Carbon::parse($att->check_out));
            }
            $attendanceTrend[] = [
                'day' => now()->subDays($i)->format('D'),
                'hours' => $hours
            ];
        }

        // 4. Happiness Index (Goal Progress Proxy)
        $avgProgress = PerformanceGoal::where('employee_id', $employee->id)
            ->whereIn('status', ['not_started', 'in_progress', 'completed'])
            ->avg('progress') ?? 0;
        
        $happinessRate = round($avgProgress);

        // Upcoming Events
        $upcomingHolidays = Holiday::active()
            ->whereDate('start_date', '>=', today())
            ->orderBy('start_date', 'asc')
            ->limit(3)
            ->get();

        $upcomingBirthdays = Employee::active()
            ->whereNotNull('date_of_birth')
            ->get()
            ->filter(function($emp) {
                $bday = $emp->date_of_birth->copy()->year(now()->year);
                if ($bday->isPast()) $bday->addYear();
                return $bday->diffInDays(now()) <= 30;
            })
            ->take(3);

        // 5. Notice Board (Actual Notifications)
        $notices = auth()->user()->notifications()->latest()->limit(5)->get()->map(function($notif) {
            $data = is_array($notif->data) ? $notif->data : json_decode($notif->data, true);
            return [
                'title' => $data['title'] ?? ($data['subject'] ?? 'Notification'),
                'date' => $notif->created_at->format('d M Y'),
                'description' => \Illuminate\Support\Str::limit($data['message'] ?? ($data['body'] ?? ''), 100),
                'image' => $this->getNotificationIcon($data['type'] ?? 'info')
            ];
        });

        return view('HRM::pages.ess.dashboard', compact(
            'employee', 
            'pendingLeaves', 
            'attendanceToday', 
            'lastPayroll',
            'leaveStats',
            'officePresence',
            'attendanceTrend',
            'happinessRate',
            'upcomingHolidays',
            'upcomingBirthdays',
            'notices'
        ));
    }

    private function getNotificationIcon($type)
    {
        $icons = [
            'leave' => 'https://img.icons8.com/color/96/leave.png',
            'payroll' => 'https://img.icons8.com/color/96/payroll.png',
            'event' => 'https://img.icons8.com/color/96/event-accepted.png',
            'holiday' => 'https://img.icons8.com/color/96/beach-umbrella.png',
            'announcement' => 'https://img.icons8.com/color/96/megaphone.png',
            'info' => 'https://img.icons8.com/color/96/info.png',
        ];

        return $icons[$type] ?? $icons['info'];
    }

    public function profile()
    {
        $employee = $this->getEmployee();
        return view('HRM::pages.ess.profile', compact('employee'));
    }

    public function updateProfile(Request $request)
    {
        $employee = $this->getEmployee();
        
        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('employees', 'public');
            $validated['photo'] = $path;
        }

        $employee->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function attendance()
    {
        $employee = $this->getEmployee();
        
        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->paginate(15);
            
        $regularizationRequests = AttendanceRegularization::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('HRM::pages.ess.attendance', compact('employee', 'attendances', 'regularizationRequests'));
    }

    public function regularizeAttendance(Request $request)
    {
        $employee = $this->getEmployee();
        
        $validated = $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'reason' => 'required|string|max:500',
        ]);

        $validated['employee_id'] = $employee->id;
        $validated['status'] = 'pending';

        AttendanceRegularization::create($validated);

        return back()->with('success', 'Regularization request submitted successfully!');
    }

    public function payslips()
    {
        $employee = $this->getEmployee();
        
        $payrolls = Payroll::where('employee_id', $employee->id)
            ->orderBy('month', 'desc')
            ->paginate(12);

        return view('HRM::pages.ess.payslips', compact('employee', 'payrolls'));
    }

    public function assets()
    {
        $employee = $this->getEmployee();
        
        $assets = Asset::where('employee_id', $employee->id)
            ->orderBy('assigned_date', 'desc')
            ->get();

        return view('HRM::pages.ess.assets', compact('employee', 'assets'));
    }

    public function salaryCertificate()
    {
        $employee = $this->getEmployee();
        
        $requests = LetterRequest::where('employee_id', $employee->id)
            ->with('letter')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('HRM::pages.ess.salary-certificate', compact('employee', 'requests'));
    }

    public function requestSalaryCertificate(Request $request)
    {
        $employee = $this->getEmployee();
        
        $validated = $request->validate([
            'type' => 'required|string',
            'reason' => 'required|string',
        ]);

        $validated['employee_id'] = $employee->id;
        $validated['status'] = 'pending';

        LetterRequest::create($validated);

        return back()->with('success', 'Request submitted successfully!');
    }

    public function holidays()
    {
        $employee = $this->getEmployee();
        $year = now()->year;

        // 1. Fetch Holidays
        // 1. Fetch Holidays for Calendar (All active)
        $holidays = Holiday::active()
            // ->whereYear('start_date', $year) // Optional: restrict to current year? functionality wise better to show all.
            ->get();

        // 2. Fetch Upcoming Holidays for List (Grouped by Month)
        // Adjust logic to handle recurring holidays properly for the list view if needed, 
        // but for now let's assume standard start_date ordering for non-recurring or already projected ones.
        // Actually, the current logic calculates events for recurring. 
        // Best approach: Use the calculated $events (which handles recurring) to build the list?
        // No, $events structure is for FullCalendar. 
        // Let's create a collection for display.
        
        $displayHolidays = collect();
        $processedForList = [];

        foreach ($holidays as $holiday) {
             if ($holiday->is_recurring) {
                // Prevent duplicates matching existing logic
                if (in_array($holiday->name, $processedForList)) {
                    continue;
                }
                $processedForList[] = $holiday->name;

                $startDate = $holiday->start_date->copy()->year($year);
             } else {
                $startDate = $holiday->start_date;
             }
             
             // Only show if in current year and in future (or including today)
             if ($startDate->year == $year && $startDate->gte(today())) {
                 $displayHolidays->push([
                     'name' => $holiday->name,
                     'date' => $startDate,
                     'month' => $startDate->format('F Y'),
                     'day' => $startDate->format('l')
                 ]);
             }
        }
        
        $groupedHolidays = $displayHolidays->sortBy('date')->groupBy('month');

        $events = [];

        $processedRecurring = [];

        foreach ($holidays as $holiday) {
            // If recurring, project to current year
            if ($holiday->is_recurring) {
                // Prevent duplicates if multiple past years' records exist for the same recurring holiday
                if (in_array($holiday->name, $processedRecurring)) {
                    continue;
                }
                $processedRecurring[] = $holiday->name;

                // Create a date for the current year with the same month and day
                $startDate = $holiday->start_date->copy()->year($year);
                
                // Calculate duration to adjust end date
                $duration = $holiday->start_date->diffInDays($holiday->end_date ?? $holiday->start_date);
                $endDate = $startDate->copy()->addDays($duration);

                $events[] = [
                    'title' => $holiday->name,
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->addDay()->format('Y-m-d'), // Exclusive end date
                    'className' => 'bg-danger text-white',
                    'allDay' => true,
                ];
            } else {
                // If not recurring, only show if it falls in the current year
                if ($holiday->start_date->year == $year) {
                    $events[] = [
                        'title' => $holiday->name,
                        'start' => $holiday->start_date->format('Y-m-d'),
                        'end' => $holiday->end_date ? $holiday->end_date->addDay()->format('Y-m-d') : $holiday->start_date->format('Y-m-d'),
                        'className' => 'bg-danger text-white', 
                        'allDay' => true,
                    ];
                }
            }
        }

        // 2. Fetch Employee's Approved Leaves
        $leaves = Leave::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->with('leaveType')
            ->get();

        foreach ($leaves as $leave) {
            $events[] = [
                'title' => 'Leave: ' . ($leave->leaveType->name ?? 'Leave'),
                'start' => $leave->start_date->format('Y-m-d'),
                'end' => $leave->end_date ? $leave->end_date->addDay()->format('Y-m-d') : $leave->start_date->format('Y-m-d'),
                'className' => 'bg-warning text-dark', // Orange/Yellow for leaves
                'allDay' => true,
            ];
        }

        // 3. Fetch Birthdays of Active Employees
        $birthdays = Employee::active()
            ->whereNotNull('date_of_birth')
            ->select('first_name', 'last_name', 'date_of_birth')
            ->get();

        foreach ($birthdays as $bday) {
            // Repeat birthday for current year and next year to cover crossover
            $currentYearBday = $bday->date_of_birth->copy()->year($year);
            $events[] = [
                'title' => '🎂 ' . $bday->full_name,
                'start' => $currentYearBday->format('Y-m-d'),
                'className' => 'bg-info text-white', // Blue for birthdays
                'allDay' => true,
            ];
            
            // Add next year too
             $nextYearBday = $bday->date_of_birth->copy()->year($year + 1);
             $events[] = [
                'title' => '🎂 ' . $bday->full_name,
                'start' => $nextYearBday->format('Y-m-d'),
                'className' => 'bg-info text-white',
                'allDay' => true,
            ];
        }

        // 4. Generate Weekly Holidays (Weekends)
        // Assuming Friday (5) and Saturday (6) are weekends for now. 
        // Ideally this should come from a setting.
        $startOfYear = \Carbon\Carbon::createFromDate($year, 1, 1);
        $endOfYear = \Carbon\Carbon::createFromDate($year, 12, 31);
        
        $weekendDays = [ \Carbon\Carbon::FRIDAY, \Carbon\Carbon::SATURDAY ]; // Adjust based on region

        $currentDate = $startOfYear->copy();
        while ($currentDate->lte($endOfYear)) {
            if (in_array($currentDate->dayOfWeek, $weekendDays)) {
                $events[] = [
                    'title' => 'Weekly Holiday',
                    'start' => $currentDate->format('Y-m-d'),
                    'className' => 'bg-secondary text-white', // Grey for weekends
                    'allDay' => true,
                    // 'rendering' => 'background', // Optional: make it a background event
                ];
            }
            $currentDate->addDay();
        }

        return view('HRM::pages.ess.holidays', compact('holidays', 'events', 'groupedHolidays'));
    }
    public function expenses()
    {
        $employee = $this->getEmployee();
        
        $expenses = ExpenseClaim::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('HRM::pages.ess.expenses', compact('employee', 'expenses'));
    }

    public function storeExpense(Request $request)
    {
        $employee = $this->getEmployee();
        
        $validated = $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('expenses', 'public');
            $validated['attachment'] = $path;
        }

        $validated['employee_id'] = $employee->id;
        $validated['status'] = 'pending';

        ExpenseClaim::create($validated);

        return back()->with('success', 'Expense claim submitted successfully!');
    }
}
