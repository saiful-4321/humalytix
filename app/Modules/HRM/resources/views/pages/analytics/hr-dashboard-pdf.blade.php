<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>HR Dashboard Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h1 {
            color: #2563eb;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        h2 {
            color: #1e40af;
            margin-top: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .metric-box {
            display: inline-block;
            width: 23%;
            padding: 15px;
            margin: 5px;
            background: #f9fafb;
            border-left: 4px solid #2563eb;
        }
        .metric-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }
        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <h1>HR Analytics Dashboard Report</h1>
    <p><strong>Period:</strong> {{ $startDate }} to {{ $endDate }}</p>
    <p><strong>Generated:</strong> {{ now()->format('F d, Y H:i') }}</p>

    <h2>Key Metrics</h2>
    <div class="metric-box">
        <div class="metric-label">Total Employees</div>
        <div class="metric-value">{{ $data['headcount']['total'] }}</div>
    </div>
    <div class="metric-box">
        <div class="metric-label">Active</div>
        <div class="metric-value">{{ $data['headcount']['active'] }}</div>
    </div>
    <div class="metric-box">
        <div class="metric-label">On Leave</div>
        <div class="metric-value">{{ $data['headcount']['on_leave'] }}</div>
    </div>
    <div class="metric-box">
        <div class="metric-label">Attrition Rate</div>
        <div class="metric-value">{{ $data['attrition']['attrition_rate'] }}%</div>
    </div>

    <h2>Headcount Breakdown</h2>
    <table>
        <tr>
            <th>Status</th>
            <th>Count</th>
        </tr>
        <tr>
            <td>Active</td>
            <td>{{ $data['headcount']['active'] }}</td>
        </tr>
        <tr>
            <td>Confirmed</td>
            <td>{{ $data['headcount']['confirmed'] }}</td>
        </tr>
        <tr>
            <td>Probation</td>
            <td>{{ $data['headcount']['probation'] }}</td>
        </tr>
        <tr>
            <td>Notice Period</td>
            <td>{{ $data['headcount']['notice_period'] }}</td>
        </tr>
    </table>

    <h2>Attrition Analysis</h2>
    <table>
        <tr>
            <th>Metric</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>Total Exits</td>
            <td>{{ $data['attrition']['total_exits'] }}</td>
        </tr>
        <tr>
            <td>Resignations</td>
            <td>{{ $data['attrition']['resignations'] }}</td>
        </tr>
        <tr>
            <td>Terminations</td>
            <td>{{ $data['attrition']['terminations'] }}</td>
        </tr>
        <tr>
            <td>Retirements</td>
            <td>{{ $data['attrition']['retirements'] }}</td>
        </tr>
    </table>

    <h2>Department Distribution</h2>
    <table>
        <tr>
            <th>Department</th>
            <th>Employee Count</th>
        </tr>
        @foreach($data['departments'] as $dept)
        <tr>
            <td>{{ $dept['name'] }}</td>
            <td>{{ $dept['count'] }}</td>
        </tr>
        @endforeach
    </table>

    <h2>Diversity Metrics</h2>
    <table>
        <tr>
            <th>Gender</th>
            <th>Count</th>
        </tr>
        @foreach($data['diversity']['gender'] as $gender => $count)
        <tr>
            <td>{{ ucfirst($gender) }}</td>
            <td>{{ $count }}</td>
        </tr>
        @endforeach
    </table>

    <h2>Tenure Distribution</h2>
    <table>
        <tr>
            <th>Range</th>
            <th>Count</th>
        </tr>
        @foreach($data['tenure']['ranges'] as $range => $count)
        <tr>
            <td>{{ $range }}</td>
            <td>{{ $count }}</td>
        </tr>
        @endforeach
    </table>
    <p><strong>Average Tenure:</strong> {{ $data['tenure']['average'] }} years</p>

    <div class="footer">
        <p>HR Analytics Dashboard | Generated on {{ now()->format('F d, Y') }}</p>
    </div>
</body>
</html>
