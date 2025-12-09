<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Payroll;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollReportController extends Controller
{
    public function salaryRegister(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));
        
        $payrolls = Payroll::with(['employee', 'employee.department'])
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $summary = [
            'total_employees' => $payrolls->count(),
            'total_basic' => $payrolls->sum('basic_salary'),
            'total_allowances' => $payrolls->sum('allowances'),
            'total_bonuses' => $payrolls->sum('bonuses'),
            'total_gross' => $payrolls->sum('gross_salary'),
            'total_deductions' => $payrolls->sum('deductions'),
            'total_tax' => $payrolls->sum('tax'),
            'total_net' => $payrolls->sum('net_salary'),
        ];
        
        if ($request->has('download')) {
            return $this->downloadSalaryRegister($payrolls, $summary, $month, $year);
        }
        
        return view('HRM::pages.reports.salary-register', compact('payrolls', 'summary', 'month', 'year'));
    }
    
    protected function downloadSalaryRegister($payrolls, $summary, $month, $year)
    {
        $pdf = Pdf::loadView('HRM::pages.reports.salary-register-pdf', compact('payrolls', 'summary', 'month', 'year'))
            ->setPaper('a4', 'landscape');
        
        $filename = 'salary_register_' . date('F_Y', mktime(0, 0, 0, $month, 1, $year)) . '.pdf';
        
        return $pdf->download($filename);
    }
    
    public function exportExcel(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));
        
        $payrolls = Payroll::with(['employee', 'employee.department'])
            ->where('month', $month)
            ->where('year', $year)
            ->get();
        
        $filename = 'salary_register_' . date('F_Y', mktime(0, 0, 0, $month, 1, $year)) . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($payrolls) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Employee Code',
                'Employee Name',
                'Department',
                'Basic Salary',
                'Allowances',
                'Bonuses',
                'Gross Salary',
                'Deductions',
                'Tax',
                'Net Salary',
                'Status'
            ]);
            
            // Data
            foreach ($payrolls as $payroll) {
                fputcsv($file, [
                    $payroll->employee->employee_code,
                    $payroll->employee->full_name,
                    $payroll->employee->department->name ?? 'N/A',
                    $payroll->basic_salary,
                    $payroll->allowances,
                    $payroll->bonuses,
                    $payroll->gross_salary,
                    $payroll->deductions,
                    $payroll->tax,
                    $payroll->net_salary,
                    $payroll->status,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
