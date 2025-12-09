<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $payroll->employee->full_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .payslip-title {
            font-size: 18px;
            color: #7f8c8d;
            margin-top: 10px;
        }
        .period {
            font-size: 14px;
            color: #95a5a6;
            margin-top: 5px;
        }
        .employee-info {
            margin: 20px 0;
            background: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
        }
        .employee-info table {
            width: 100%;
        }
        .employee-info td {
            padding: 5px;
        }
        .label {
            font-weight: bold;
            width: 30%;
            color: #34495e;
        }
        .salary-breakdown {
            margin-top: 30px;
        }
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .breakdown-table th {
            background: #34495e;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        .breakdown-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ecf0f1;
        }
        .breakdown-table tr:hover {
            background: #f8f9fa;
        }
        .earning {
            color: #27ae60;
        }
        .deduction {
            color: #e74c3c;
        }
        .total-row {
            font-weight: bold;
            background: #ecf0f1;
        }
        .net-salary {
            margin-top: 30px;
            text-align: right;
            padding: 20px;
            background: #2c3e50;
            color: white;
            border-radius: 5px;
        }
        .net-label {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .net-amount {
            font-size: 24px;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #95a5a6;
            border-top: 1px solid #ecf0f1;
            padding-top: 15px;
        }
        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 20px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 11px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ config('app.name', 'Company Name') }}</div>
        <div class="payslip-title">SALARY SLIP</div>
        <div class="period">{{ date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) }}</div>
    </div>

    <!-- Employee Information -->
    <div class="employee-info">
        <table>
            <tr>
                <td class="label">Employee Name:</td>
                <td>{{ $payroll->employee->full_name }}</td>
                <td class="label">Employee Code:</td>
                <td>{{ $payroll->employee->employee_code }}</td>
            </tr>
            <tr>
                <td class="label">Designation:</td>
                <td>{{ $payroll->employee->designation }}</td>
                <td class="label">Department:</td>
                <td>{{ $payroll->employee->department->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Joining Date:</td>
                <td>{{ $payroll->employee->joining_date?->format('d M, Y') }}</td>
                <td class="label">Payment Date:</td>
                <td>{{ $payroll->payment_date?->format('d M, Y') ?? 'Pending' }}</td>
            </tr>
        </table>
    </div>

    <!-- Salary Breakdown -->
    <div class="salary-breakdown">
        <table class="breakdown-table">
            <thead>
                <tr>
                    <th style="width: 50%;">EARNINGS</th>
                    <th style="text-align: right;">AMOUNT (BDT)</th>
                    <th style="width: 50%; border-left: 2px solid white;">DEDUCTIONS</th>
                    <th style="text-align: right;">AMOUNT (BDT)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $earnings = $payroll->items->where('type', 'earning');
                    $deductions = $payroll->items->where('type', 'deduction');
                    $maxRows = max($earnings->count() + 1, $deductions->count() + 1); // +1 for Basic/Tax
                    $earningsArray = $earnings->values()->all();
                    $deductionsArray = $deductions->values()->all();
                @endphp
                
                <!-- Basic Salary Row -->
                <tr>
                    <td class="earning">Basic Salary</td>
                    <td class="earning" style="text-align: right;">{{ number_format($payroll->basic_salary, 2) }}</td>
                    <td style="border-left: 2px solid #ecf0f1;"></td>
                    <td></td>
                </tr>

                @for($i = 0; $i < $maxRows; $i++)
                    <tr>
                        <td class="earning">
                            {{ isset($earningsArray[$i]) ? $earningsArray[$i]->component_name : '' }}
                        </td>
                        <td class="earning" style="text-align: right;">
                            {{ isset($earningsArray[$i]) ? number_format($earningsArray[$i]->amount, 2) : '' }}
                        </td>
                        <td class="deduction" style="border-left: 2px solid #ecf0f1;">
                            {{ isset($deductionsArray[$i]) ? $deductionsArray[$i]->component_name : '' }}
                        </td>
                        <td class="deduction" style="text-align: right;">
                            {{ isset($deductionsArray[$i]) ? number_format($deductionsArray[$i]->amount, 2) : '' }}
                        </td>
                    </tr>
                @endfor

                <!-- Tax Row -->
                @if($payroll->tax > 0)
                <tr>
                    <td></td>
                    <td></td>
                    <td class="deduction" style="border-left: 2px solid #ecf0f1;">Income Tax</td>
                    <td class="deduction" style="text-align: right;">{{ number_format($payroll->tax, 2) }}</td>
                </tr>
                @endif

                <!-- Totals -->
                <tr class="total-row">
                    <td>GROSS SALARY</td>
                    <td style="text-align: right;">{{ number_format($payroll->gross_salary, 2) }}</td>
                    <td style="border-left: 2px solid #34495e;">TOTAL DEDUCTIONS</td>
                    <td style="text-align: right;">{{ number_format($payroll->deductions + $payroll->tax, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Net Salary -->
    <div class="net-salary">
        <div class="net-label">NET SALARY (IN HAND)</div>
        <div class="net-amount">BDT {{ number_format($payroll->net_salary, 2) }}</div>
    </div>

    <!-- Signatures -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">Employee Signature</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Authorized Signatory</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is a computer-generated payslip and does not require a signature.</p>
        <p>Generated on: {{ now()->format('d M, Y h:i A') }}</p>
    </div>
</body>
</html>
