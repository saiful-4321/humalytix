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
use App\Modules\HRM\Models\Leave; // Correct model for leave applications
use Illuminate\Support\Facades\DB;
use App\Modules\HRM\Enums\AttendanceStatusEnum;

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
        
        // Stats
        $pendingLeaves = Leave::where('employee_id', $employee->id)->where('status', 'pending')->count();
        $attendanceToday = Attendance::where('employee_id', $employee->id)->whereDate('date', today())->first();
        $lastPayroll = Payroll::where('employee_id', $employee->id)->orderBy('month', 'desc')->first();
        
        // Recent Activities or similar could go here

        return view('HRM::pages.ess.dashboard', compact('employee', 'pendingLeaves', 'attendanceToday', 'lastPayroll'));
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
        
        $assets = Asset::where('assigned_to', $employee->id)
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
        // Fetch upcoming holidays
        $holidays = Holiday::whereDate('date', '>=', today())
            ->orderBy('date', 'asc')
            ->get();
            
        return view('HRM::pages.ess.holidays', compact('holidays'));
    }
}
