<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Salary Register - {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 16px; font-weight: bold; }
        .subtitle { font-size: 12px; color: #666; }
        .summary-table { width: 50%; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ config('app.name') }}</div>
        <div class="subtitle">Salary Register - {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</div>
    </div>

    <!-- Summary -->
    <table class="summary-table">
        <tr>
            <th>Total Employees</th>
            <td>{{ $summary['total_employees'] }}</td>
        </tr>
        <tr>
            <th>Total Gross Salary</th>
            <td>BDT {{ number_format($summary['total_gross'], 2) }}</td>
        </tr>
        <tr>
            <th>Total Net Payable</th>
            <td>BDT {{ number_format($summary['total_net'], 2) }}</td>
        </tr>
    </table>

    <!-- Detailed Register -->
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Department</th>
                <th class="text-right">Basic</th>
                <th class="text-right">Allowances</th>
                <th class="text-right">Bonuses</th>
                <th class="text-right">Gross</th>
                <th class="text-right">Deductions</th>
                <th class="text-right">Tax</th>
                <th class="text-right">Net Salary</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payrolls as $payroll)
            <tr>
                <td>{{ $payroll->employee->employee_code }}</td>
                <td>{{ $payroll->employee->full_name }}</td>
                <td>{{ $payroll->employee->department->name ?? '-' }}</td>
                <td class="text-right">{{ number_format($payroll->basic_salary, 2) }}</td>
                <td class="text-right">{{ number_format($payroll->allowances, 2) }}</td>
                <td class="text-right">{{ number_format($payroll->bonuses, 2) }}</td>
                <td class="text-right">{{ number_format($payroll->gross_salary, 2) }}</td>
                <td class="text-right">{{ number_format($payroll->deductions, 2) }}</td>
                <td class="text-right">{{ number_format($payroll->tax, 2) }}</td>
                <td class="text-right">{{ number_format($payroll->net_salary, 2) }}</td>
                <td>{{ ucfirst($payroll->status) }}</td>
            </tr>
            @endforeach
            <tr style="background: #e9ecef; font-weight: bold;">
                <td colspan="3" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format($summary['total_basic'], 2) }}</td>
                <td class="text-right">{{ number_format($summary['total_allowances'], 2) }}</td>
                <td class="text-right">{{ number_format($summary['total_bonuses'], 2) }}</td>
                <td class="text-right">{{ number_format($summary['total_gross'], 2) }}</td>
                <td class="text-right">{{ number_format($summary['total_deductions'], 2) }}</td>
                <td class="text-right">{{ number_format($summary['total_tax'], 2) }}</td>
                <td class="text-right">{{ number_format($summary['total_net'], 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
