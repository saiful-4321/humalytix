<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Payroll;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\PayrollItem;
use App\Modules\HRM\Models\TaxSlab;
use App\Modules\HRM\Models\EmployeeLoan;
use App\Modules\HRM\Models\LoanInstallment;
use App\Modules\HRM\Models\EmployeeAdvance;
use App\Modules\HRM\Models\AdvanceDeduction;
use App\Modules\HRM\Models\EmployeeOvertime;
use App\Modules\HRM\Models\EmployeeBonus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
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
        return view('HRM::pages.payroll.create');
    }

    public function bulkGenerate(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ]);
        
        $month = $validated['month'];
        $year = $validated['year'];

        // Get active employees who have a salary configuration
        $employees = Employee::active()->has('salary')->with(['salary.structure.components'])->get();
        $count = 0;
        $errors = [];

        DB::transaction(function () use ($employees, $month, $year, &$count, &$errors) {
            foreach ($employees as $employee) {
                try {
                    // Check if payroll already exists
                    $exists = Payroll::where('employee_id', $employee->id)
                        ->where('month', $month)
                        ->where('year', $year)
                        ->exists();

                    if ($exists) continue;

                    $salaryConfig = $employee->salary;
                    $structure = $salaryConfig->structure;
                    
                    if (!$structure) continue;
                    
                    $basic = $salaryConfig->basic_salary;
                    
                    // Initialize Calculation Variables
                    $earnings = [];
                    $deductions = [];
                    $items = [];

                    // 1. Add Basic Salary
                    $totalEarnings = $basic;
                    $earnings[] = ['name' => 'Basic Salary', 'amount' => $basic];

                    // 2. Calculate Structure Components
                    foreach ($structure->components as $component) {
                        $amount = 0;
                        $pivotAmount = $component->pivot->amount;
                        $pivotPercent = $component->pivot->percentage;

                        if ($component->calculation_type == 'fixed') {
                            $amount = $pivotAmount ?? $component->default_amount ?? 0;
                        } elseif ($component->calculation_type == 'percentage') {
                            $percent = $pivotPercent ?? $component->default_percentage ?? 0;
                            $amount = ($basic * $percent) / 100;
                        }

                        if ($component->type == 'earning') {
                            $totalEarnings += $amount;
                            $earnings[] = ['name' => $component->name, 'amount' => $amount];
                        } else {
                            $deductions[] = ['name' => $component->name, 'amount' => $amount];
                        }

                        // Save for payroll items
                        $items[] = [
                            'salary_component_id' => $component->id,
                            'component_name' => $component->name,
                            'type' => $component->type,
                            'amount' => $amount
                        ];
                    }

                    // 3. Add Pending Overtime
                    $otAmount = $this->calculateOvertimeForMonth($employee->id, $month, $year);
                    if ($otAmount > 0) {
                        $totalEarnings += $otAmount;
                        $earnings[] = ['name' => 'Overtime', 'amount' => $otAmount];
                    }

                    // 4. Add Approved Bonuses
                    $bonusAmount = $this->calculateBonusesForMonth($employee->id, $month, $year);
                    if ($bonusAmount > 0) {
                        $totalEarnings += $bonusAmount;
                        $earnings[] = ['name' => 'Bonus', 'amount' => $bonusAmount];
                    }

                    $gross = $totalEarnings;

                    // 5. Calculate Deductions
                    $totalDeductions = array_sum(array_column($deductions, 'amount'));

                    // 6. Deduct Loan Installments
                    $loanDeduction = $this->deductLoanInstallments($employee->id, $month, $year);
                    if ($loanDeduction > 0) {
                        $totalDeductions += $loanDeduction;
                        $deductions[] = ['name' => 'Loan Installment', 'amount' => $loanDeduction];
                    }

                    // 7. Deduct Salary Advances
                    $advanceDeduction = $this->deductAdvances($employee->id, $month, $year);
                    if ($advanceDeduction > 0) {
                        $totalDeductions += $advanceDeduction;
                        $deductions[] = ['name' => 'Salary Advance', 'amount' => $advanceDeduction];
                    }

                    // 8. Calculate Income Tax (Auto)
                    $taxAmount = $this->calculateIncomeTax($employee, $gross, $month, $year);
                    if ($taxAmount > 0) {
                        $totalDeductions += $taxAmount;
                        $deductions[] = ['name' => 'Income Tax', 'amount' => $taxAmount];
                    }

                    $net = $gross - $totalDeductions;
                    
                    // 9. Create Payroll Header
                    $payroll = Payroll::create([
                        'employee_id' => $employee->id,
                        'month' => $month,
                        'year' => $year,
                        'basic_salary' => $basic,
                        'allowances' => $totalEarnings - $basic - ($otAmount + $bonusAmount),
                        'bonuses' => $bonusAmount,
                        'deductions' => $totalDeductions - $taxAmount,
                        'tax' => $taxAmount,
                        'gross_salary' => $gross,
                        'net_salary' => $net,
                        'status' => 'pending',
                        'created_by' => auth()->id()
                    ]);

                    // 10. Create Payroll Items (only for structure components)
                    foreach ($items as $item) {
                        PayrollItem::create([
                            'payroll_id' => $payroll->id,
                            'salary_component_id' => $item['salary_component_id'],
                            'component_name' => $item['component_name'],
                            'type' => $item['type'],
                            'amount' => $item['amount']
                        ]);
                    }
                    
                    $count++;
                } catch (\Exception $e) {
                    $errors[] = "{$employee->full_name}: {$e->getMessage()}";
                }
            }
        });

        if (!empty($errors)) {
            return redirect()->route('hrm.payroll.index')
                ->with('warning', "Payroll generated for {$count} employees. Errors: " . implode(', ', $errors));
        }

        return redirect()->route('hrm.payroll.index')
            ->with('success', "Payroll generated successfully for {$count} employees.");
    }

    // Calculate Auto Tax based on Tax Slabs
    protected function calculateIncomeTax($employee, $grossMonthly, $month, $year)
    {
        $annualIncome = $grossMonthly * 12;
        $gender = $employee->gender ?? 'all';
        
        // Get applicable tax slab
        $taxSlab = TaxSlab::where('gender', $gender)
            ->orWhere('gender', 'all')
            ->where('min_income', '<=', $annualIncome)
            ->where(function($q) use ($annualIncome) {
                $q->where('max_income', '>=', $annualIncome)
                  ->orWhereNull('max_income');
            })
            ->orderBy('min_income', 'desc')
            ->first();

        if (!$taxSlab) return 0;

        $annualTax = 0;
        
        if ($taxSlab->tax_rate > 0) {
            // Progressive tax: (Income - Min) * Rate / 100
            $taxableIncome = $annualIncome - $taxSlab->min_income;
            $annualTax = ($taxableIncome * $taxSlab->tax_rate) / 100;
        }

        if ($taxSlab->fixed_deduction > 0) {
            $annualTax += $taxSlab->fixed_deduction;
        }

        // Monthly tax deduction
        return round($annualTax / 12, 2);
    }

    // Calculate OT for the month
    protected function calculateOvertimeForMonth($employeeId, $month, $year)
    {
        $ot = EmployeeOvertime::where('employee_id', $employeeId)
            ->whereMonth('ot_date', $month)
            ->whereYear('ot_date', $year)
            ->where('status', 'approved')
            ->whereNull('payroll_id')
            ->sum('ot_amount');

        return $ot ?? 0;
    }

    // Calculate bonuses for the month
    protected function calculateBonusesForMonth($employeeId, $month, $year)
    {
        $bonus = EmployeeBonus::where('employee_id', $employeeId)
            ->where('bonus_month', $month)
            ->where('bonus_year', $year)
            ->where('status', 'approved')
            ->whereNull('payroll_id')
            ->sum('amount');

        return $bonus ?? 0;
    }

    // Deduct loan installments
    protected function deductLoanInstallments($employeeId, $month, $year)
    {
        $dueDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        $installments = LoanInstallment::whereHas('loan', function($q) use ($employeeId) {
                $q->where('employee_id', $employeeId)
                  ->where('status', 'active');
            })
            ->where('due_date', '<=', $dueDate)
            ->where('status', 'pending')
            ->get();

        $totalDeduction = 0;
        foreach ($installments as $installment) {
            $totalDeduction += $installment->installment_amount;
        }

        return $totalDeduction;
    }

    // Deduct salary advances
    protected function deductAdvances($employeeId, $month, $year)
    {
        $advances = EmployeeAdvance::where('employee_id', $employeeId)
            ->where('status', 'active')
            ->where('outstanding_balance', '>', 0)
            ->get();

        $totalDeduction = 0;
        foreach ($advances as $advance) {
            $totalDeduction += min($advance->monthly_deduction, $advance->outstanding_balance);
        }

        return $totalDeduction;
    }
    
    public function show(Payroll $payroll)
    {
        $payroll->load(['employee', 'items']);
        return view('HRM::pages.payroll.show', compact('payroll'));
    }

    public function downloadPDF(Payroll $payroll)
    {
        $payroll->load(['employee', 'items', 'employee.department']);
        
        $pdf = Pdf::loadView('HRM::pages.payroll.pdf', compact('payroll'));
        
        $filename = 'payslip_' . $payroll->employee->employee_code . '_' . 
                    date('F_Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) . '.pdf';
        
        return $pdf->download($filename);
    }

    public function process(Payroll $payroll)
    {
        if ($payroll->status == 'paid') {
            return redirect()->back()->with('error', 'Already paid.');
        }

        DB::transaction(function() use ($payroll) {
            // Mark OT as paid
            EmployeeOvertime::where('employee_id', $payroll->employee_id)
                ->whereMonth('ot_date', $payroll->month)
                ->whereYear('ot_date', $payroll->year)
                ->where('status', 'approved')
                ->update(['payroll_id' => $payroll->id, 'status' => 'paid']);

            // Mark bonuses as paid
            EmployeeBonus::where('employee_id', $payroll->employee_id)
                ->where('bonus_month', $payroll->month)
                ->where('bonus_year', $payroll->year)
                ->where('status', 'approved')
                ->update(['payroll_id' => $payroll->id, 'status' => 'paid']);

            // Mark loan installments as paid
            $dueDate = Carbon::create($payroll->year, $payroll->month, 1)->endOfMonth();
            $installments = LoanInstallment::whereHas('loan', function($q) use ($payroll) {
                    $q->where('employee_id', $payroll->employee_id);
                })
                ->where('due_date', '<=', $dueDate)
                ->where('status', 'pending')
                ->get();

            foreach ($installments as $installment) {
                $installment->update([
                    'paid_date' => now(),
                    'paid_amount' => $installment->installment_amount,
                    'status' => 'paid',
                    'payroll_id' => $payroll->id
                ]);

                // Update loan totals
                $loan = $installment->loan;
                $loan->total_paid += $installment->installment_amount;
                $loan->outstanding_balance -= $installment->installment_amount;
                if ($loan->outstanding_balance <= 0) {
                    $loan->status = 'completed';
                }
                $loan->save();
            }

            // Deduct advances
            $advances = EmployeeAdvance::where('employee_id', $payroll->employee_id)
                ->where('status', 'active')
                ->where('outstanding_balance', '>', 0)
                ->get();

            foreach ($advances as $advance) {
                $deductionAmount = min($advance->monthly_deduction, $advance->outstanding_balance);
                
                AdvanceDeduction::create([
                    'advance_id' => $advance->id,
                    'amount' => $deductionAmount,
                    'deduction_date' => now(),
                    'payroll_id' => $payroll->id
                ]);

                $advance->total_deducted += $deductionAmount;
                $advance->outstanding_balance -= $deductionAmount;
                if ($advance->outstanding_balance <= 0) {
                    $advance->status = 'completed';
                }
                $advance->save();
            }

            // Mark payroll as paid
            $payroll->update([
                'status' => 'paid',
                'payment_date' => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Payroll processed and marked as paid.');
    }
    
    public function destroy(Payroll $payroll)
    {
        if ($payroll->status == 'paid') {
            return redirect()->back()->with('error', 'Cannot delete paid payroll.');
        }
        $payroll->delete();
        return redirect()->back()->with('success', 'Payroll record deleted.');
    }
    
    public function emailPayslip(Payroll $payroll)
    {
        $payroll->load(['employee', 'items', 'employee.department']);
        
        $employee = $payroll->employee;
        $email = $employee->email ?? $employee->user?->email;
        
        if (!$email) {
            return back()->with('error', 'Employee has no email address.');
        }
        
        try {
            $pdf = Pdf::loadView('HRM::pages.payroll.pdf', compact('payroll'));
            
            $filename = 'payslip_' . $employee->employee_code . '_' . 
                        date('F_Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) . '.pdf';
            
            \Mail::send('HRM::emails.payslip', compact('payroll', 'employee'), function($message) use ($email, $pdf, $filename, $payroll) {
                $message->to($email)
                    ->subject('Salary Slip - ' . date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)))
                    ->attachData($pdf->output(), $filename);
            });
            
            return back()->with('success', 'Payslip sent to ' . $email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
    
    public function bulkEmailPayslips(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer',
            'year' => 'required|integer',
        ]);
        
        $payrolls = Payroll::with(['employee', 'items'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->where('status', 'paid')
            ->get();
        
        $sent = 0;
        $failed = 0;
        
        foreach ($payrolls as $payroll) {
            $employee = $payroll->employee;
            $email = $employee->email ?? $employee->user?->email;
            
            if (!$email) {
                $failed++;
                continue;
            }
            
            try {
                $pdf = Pdf::loadView('HRM::pages.payroll.pdf', compact('payroll'));
                
                $filename = 'payslip_' . $employee->employee_code . '_' . 
                           date('F_Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) . '.pdf';
                
                \Mail::send('HRM::emails.payslip', compact('payroll', 'employee'), function($message) use ($email, $pdf, $filename, $payroll) {
                    $message->to($email)
                        ->subject('Salary Slip - ' . date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)))
                        ->attachData($pdf->output(), $filename);
                });
                
                $sent++;
            } catch (\Exception $e) {
                $failed++;
            }
        }
        
        return back()->with('success', "Sent $sent payslips. Failed: $failed");
    }
}
