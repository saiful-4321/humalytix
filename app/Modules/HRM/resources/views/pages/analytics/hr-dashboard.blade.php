@extends('Main::layouts.app')

@section('title', 'HR Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">HR Analytics Dashboard</h4>
                    <p class="text-muted mb-0">Comprehensive workforce insights and metrics</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                        <i class="bx bx-printer me-1"></i>Print
                    </button>
                    <a href="{{ route('hrm.analytics.hr.export-pdf', request()->all()) }}" class="btn btn-outline-danger">
                        <i class="bx bxs-file-pdf me-1"></i>Export PDF
                    </a>
                    <a href="{{ route('hrm.analytics.hr.export-excel', request()->all()) }}" class="btn btn-outline-success">
                        <i class="bx bxs-file me-1"></i>Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('hrm.analytics.hr') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-filter me-1"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                    <i class="bx bx-user"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total Employees</p>
                            <h4 class="mb-0">{{ $data['headcount']['total'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Active Employees</p>
                            <h4 class="mb-0">{{ $data['headcount']['active'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                    <i class="bx bx-time"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">On Leave</p>
                            <h4 class="mb-0">{{ $data['headcount']['on_leave'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-3">
                                    <i class="bx bx-trending-down"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Attrition Rate</p>
                            <h4 class="mb-0">{{ $data['attrition']['attrition_rate'] }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row g-3 mb-4">
        <!-- Headcount Trend -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Headcount Trend</h5>
                </div>
                <div class="card-body">
                    <div id="headcountTrendChart"></div>
                </div>
            </div>
        </div>

        <!-- Department Distribution -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Department Distribution</h5>
                </div>
                <div class="card-body">
                    <div id="departmentChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-3 mb-4">
        <!-- Gender Diversity -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Gender Diversity</h5>
                </div>
                <div class="card-body">
                    <div id="genderChart"></div>
                </div>
            </div>
        </div>

        <!-- Age Distribution -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Age Distribution</h5>
                </div>
                <div class="card-body">
                    <div id="ageChart"></div>
                </div>
            </div>
        </div>

        <!-- Tenure Distribution -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Employee Tenure</h5>
                </div>
                <div class="card-body">
                    <div id="tenureChart"></div>
                    <div class="text-center mt-3">
                        <p class="text-muted mb-1">Average Tenure</p>
                        <h4 class="mb-0">{{ $data['tenure']['average'] }} years</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Stats -->
    <div class="row g-3">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Employee Status Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td>Active</td>
                                    <td class="text-end"><strong>{{ $data['headcount']['active'] }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Confirmed</td>
                                    <td class="text-end"><strong>{{ $data['headcount']['confirmed'] }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Probation</td>
                                    <td class="text-end"><strong>{{ $data['headcount']['probation'] }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Notice Period</td>
                                    <td class="text-end"><strong>{{ $data['headcount']['notice_period'] }}</strong></td>
                                </tr>
                                <tr>
                                    <td>On Leave</td>
                                    <td class="text-end"><strong>{{ $data['headcount']['on_leave'] }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Attrition Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td>Total Exits</td>
                                    <td class="text-end"><strong>{{ $data['attrition']['total_exits'] }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Resignations</td>
                                    <td class="text-end"><strong>{{ $data['attrition']['resignations'] }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Terminations</td>
                                    <td class="text-end"><strong>{{ $data['attrition']['terminations'] }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Retirements</td>
                                    <td class="text-end"><strong>{{ $data['attrition']['retirements'] }}</strong></td>
                                </tr>
                                <tr class="table-active">
                                    <td><strong>Attrition Rate</strong></td>
                                    <td class="text-end"><strong class="text-danger">{{ $data['attrition']['attrition_rate'] }}%</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
// Headcount Trend Chart
var headcountOptions = {
    series: [{
        name: 'New Hires',
        data: @json($data['growth']['new_hires'])
    }, {
        name: 'Exits',
        data: @json($data['growth']['exits'])
    }],
    chart: {
        type: 'line',
        height: 350,
        toolbar: {
            show: true
        }
    },
    colors: ['#10b981', '#ef4444'],
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: 'smooth',
        width: 3
    },
    xaxis: {
        categories: @json($data['growth']['months'])
    },
    yaxis: {
        title: {
            text: 'Count'
        }
    },
    legend: {
        position: 'top'
    },
    grid: {
        borderColor: '#f1f1f1'
    }
};
var headcountChart = new ApexCharts(document.querySelector("#headcountTrendChart"), headcountOptions);
headcountChart.render();

// Department Distribution Chart
var deptOptions = {
    series: @json(array_column($data['departments'], 'count')),
    chart: {
        type: 'donut',
        height: 300
    },
    labels: @json(array_column($data['departments'], 'name')),
    colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
    legend: {
        position: 'bottom'
    },
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                width: 200
            },
            legend: {
                position: 'bottom'
            }
        }
    }]
};
var deptChart = new ApexCharts(document.querySelector("#departmentChart"), deptOptions);
deptChart.render();

// Gender Chart
var genderOptions = {
    series: @json(array_values($data['diversity']['gender'])),
    chart: {
        type: 'pie',
        height: 300
    },
    labels: @json(array_keys($data['diversity']['gender'])),
    colors: ['#3b82f6', '#ec4899', '#6b7280'],
    legend: {
        position: 'bottom'
    }
};
var genderChart = new ApexCharts(document.querySelector("#genderChart"), genderOptions);
genderChart.render();

// Age Distribution Chart
var ageOptions = {
    series: [{
        name: 'Employees',
        data: @json(array_values($data['diversity']['age_ranges']))
    }],
    chart: {
        type: 'bar',
        height: 300
    },
    plotOptions: {
        bar: {
            borderRadius: 4,
            horizontal: false,
            columnWidth: '55%',
        }
    },
    dataLabels: {
        enabled: false
    },
    colors: ['#3b82f6'],
    xaxis: {
        categories: @json(array_keys($data['diversity']['age_ranges']))
    },
    yaxis: {
        title: {
            text: 'Count'
        }
    }
};
var ageChart = new ApexCharts(document.querySelector("#ageChart"), ageOptions);
ageChart.render();

// Tenure Chart
var tenureOptions = {
    series: [{
        name: 'Employees',
        data: @json(array_values($data['tenure']['ranges']))
    }],
    chart: {
        type: 'bar',
        height: 250
    },
    plotOptions: {
        bar: {
            borderRadius: 4,
            horizontal: true,
        }
    },
    dataLabels: {
        enabled: false
    },
    colors: ['#10b981'],
    xaxis: {
        categories: @json(array_keys($data['tenure']['ranges']))
    }
};
var tenureChart = new ApexCharts(document.querySelector("#tenureChart"), tenureOptions);
tenureChart.render();
</script>
@endsection
