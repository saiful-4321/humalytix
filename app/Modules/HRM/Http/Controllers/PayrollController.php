<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Payroll;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.payroll.view')->only(['index', 'show']);
        $this->middleware('permission:hrm.payroll.create')->only(['create', 'store']);
        $this->middleware('permission:hrm.payroll.process')->only(['process']);
    }

    public function index(Request $request)
    {
        $query = Payroll::with(['employee']);

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(20);

        return view('HRM::pages.payroll.index', compact('payrolls'));
    }

    public function create()
    {
        $employees = Employee::active()->with('employeeSalary')->get();
        return view('HRM::pages.payroll.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
        ]);

        // Check if payroll already exists
        $existing = Payroll::where('employee_id', $validated['employee_id'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Payroll already exists for this employee and period!');
        }

        $grossSalary = $validated['basic_salary'] + ($validated['allowances'] ?? 0) + ($validated['bonuses'] ?? 0);
        $totalDeductions = ($validated['deductions'] ?? 0) + ($validated['tax'] ?? 0);
        $netSalary = $grossSalary - $totalDeductions;

        $validated['gross_salary'] = $grossSalary;
        $validated['net_salary'] = $netSalary;
        $validated['status'] = 'pending';
        $validated['payment_date'] = null;

        Payroll::create($validated);

        return redirect()->route('hrm.payroll.index')->with('success', 'Payroll created successfully!');
    }

    public function show(Payroll $payroll)
    {
        $payroll->load(['employee', 'employee.department', 'employee.branch']);
        return view('HRM::pages.payroll.show', compact('payroll'));
    }

    public function process(Payroll $payroll)
    {
        if ($payroll->status == 'paid') {
            return redirect()->back()->with('error', 'Payroll already processed!');
        }

        $payroll->update([
            'status' => 'paid',
            'payment_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Payroll processed successfully!');
    }

    public function bulkGenerate(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ]);

        $employees = Employee::active()->with('employeeSalary')->get();
        $count = 0;

        foreach ($employees as $employee) {
            if (!$employee->employeeSalary) continue;

            $existing = Payroll::where('employee_id', $employee->id)
                ->where('month', $validated['month'])
                ->where('year', $validated['year'])
                ->first();

            if ($existing) continue;

            $salary = $employee->employeeSalary;
            $grossSalary = $salary->basic_salary + $salary->house_rent + $salary->medical_allowance + $salary->transport_allowance;
            $totalDeductions = $salary->tax + $salary->provident_fund;
            $netSalary = $grossSalary - $totalDeductions;

            Payroll::create([
                'employee_id' => $employee->id,
                'month' => $validated['month'],
                'year' => $validated['year'],
                'basic_salary' => $salary->basic_salary,
                'allowances' => $salary->house_rent + $salary->medical_allowance + $salary->transport_allowance,
                'bonuses' => 0,
                'deductions' => $salary->provident_fund,
                'tax' => $salary->tax,
                'gross_salary' => $grossSalary,
                'net_salary' => $netSalary,
                'status' => 'pending',
            ]);

            $count++;
        }

        return redirect()->route('hrm.payroll.index')->with('success', "Generated payroll for {$count} employees!");
    }
}
