<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Payroll;
use Illuminate\Http\Request;

class BankTransferController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));
        
        $payrolls = Payroll::with(['employee'])
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'paid')
            ->get();
        
        return view('HRM::pages.bank-transfer.index', compact('payrolls', 'month', 'year'));
    }
    
    public function generate(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $format = $request->input('format', 'npsb'); // npsb, beftn, csv
        
        $payrolls = Payroll::with(['employee'])
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'paid')
            ->get();
        
        if ($payrolls->isEmpty()) {
            return back()->with('error', 'No paid payrolls found for selected period.');
        }
        
        switch ($format) {
            case 'beftn':
                return $this->generateBEFTN($payrolls, $month, $year);
            case 'npsb':
                return $this->generateNPSB($payrolls, $month, $year);
            default:
                return $this->generateCSV($payrolls, $month, $year);
        }
    }
    
    protected function generateBEFTN($payrolls, $month, $year)
    {
        $filename = 'beftn_' . date('F_Y', mktime(0, 0, 0, $month, 1, $year)) . '.txt';
        
        $content = "BEFTN SALARY TRANSFER\n";
        $content .= "MONTH: " . date('F Y', mktime(0, 0, 0, $month, 1, $year)) . "\n";
        $content .= str_repeat("=", 80) . "\n\n";
        
        $totalAmount = 0;
        
        foreach ($payrolls as $payroll) {
            $employee = $payroll->employee;
            $bankAccount = $employee->bank_account ?? 'N/A';
            $bankName = $employee->bank_name ?? 'N/A';
            $routingNumber = $employee->routing_number ?? 'N/A';
            
            $content .= sprintf(
                "%-20s\t%-30s\t%-20s\t%-15s\t%15.2f\n",
                $employee->employee_code,
                $employee->full_name,
                $bankAccount,
                $routingNumber,
                $payroll->net_salary
            );
            
            $totalAmount += $payroll->net_salary;
        }
        
        $content .= "\n" . str_repeat("=", 80) . "\n";
        $content .= sprintf("TOTAL EMPLOYEES: %d\n", $payrolls->count());
        $content .= sprintf("TOTAL AMOUNT: %.2f\n", $totalAmount);
        
        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
    
    protected function generateNPSB($payrolls, $month, $year)
    {
        $filename = 'npsb_' . date('F_Y', mktime(0, 0, 0, $month, 1, $year)) . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($payrolls) {
            $file = fopen('php://output', 'w');
            
            // NPSB Format Headers
            fputcsv($file, [
                'SL',
                'Account Number',
                'Account Name',
                'Bank Name',
                'Branch Name',
                'Routing Number',
                'Amount',
                'Reference'
            ]);
            
            $sl = 1;
            foreach ($payrolls as $payroll) {
                $employee = $payroll->employee;
                
                fputcsv($file, [
                    $sl++,
                    $employee->bank_account ?? '',
                    $employee->full_name,
                    $employee->bank_name ?? '',
                    $employee->bank_branch ?? '',
                    $employee->routing_number ?? '',
                    number_format($payroll->net_salary, 2, '.', ''),
                    'Salary - ' . date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year))
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    protected function generateCSV($payrolls, $month, $year)
    {
        $filename = 'bank_transfer_' . date('F_Y', mktime(0, 0, 0, $month, 1, $year)) . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($payrolls) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Employee Code',
                'Employee Name',
                'Bank Account',
                'Bank Name',
                'Branch',
                'Routing Number',
                'Net Salary',
                'Status'
            ]);
            
            foreach ($payrolls as $payroll) {
                $employee = $payroll->employee;
                
                fputcsv($file, [
                    $employee->employee_code,
                    $employee->full_name,
                    $employee->bank_account ?? 'N/A',
                    $employee->bank_name ?? 'N/A',
                    $employee->bank_branch ?? 'N/A',
                    $employee->routing_number ?? 'N/A',
                    $payroll->net_salary,
                    $payroll->status
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
