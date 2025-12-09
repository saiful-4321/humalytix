<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Salary Slip</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">Salary Slip - {{ date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) }}</h2>
        
        <p>Dear {{ $employee->full_name }},</p>
        
        <p>Please find attached your salary slip for {{ date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) }}.</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr style="background: #f8f9fa;">
                <td style="padding: 10px; border: 1px solid #dee2e6;"><strong>Gross Salary:</strong></td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">${{ number_format($payroll->gross_salary, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6;"><strong>Deductions:</strong></td>
                <td style="padding: 10px; border: 1px solid #dee2e6; color: #e74c3c;">${{ number_format($payroll->deductions + $payroll->tax, 2) }}</td>
            </tr>
            <tr style="background: #d4edda;">
                <td style="padding: 10px; border: 1px solid #dee2e6;"><strong>Net Salary:</strong></td>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">${{ number_format($payroll->net_salary, 2) }}</td>
            </tr>
        </table>
        
        <p style="color: #7f8c8d; font-size: 12px;">
            This is an auto-generated email. Please do not reply to this message.<br>
            For any queries, please contact the HR department.
        </p>
        
        <hr style="border: none; border-top: 1px solid #dee2e6; margin: 20px 0;">
        
        <p style="color: #95a5a6; font-size: 11px; text-align: center;">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</body>
</html>
