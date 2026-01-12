@extends('Main::layouts.app')

@section('title', 'HR Dashboard')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.2);
        --card-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        --card-bg: #ffffff;
        --card-text: #1f2937;
        --card-subtext: #4b5563;
        --card-border: transparent;
        --accent-blue: #3b82f6;
        --accent-green: #10b981;
        --accent-yellow: #f59e0b;
        --accent-red: #ef4444;
    }

    body[data-layout-mode="dark"] {
        --card-bg: #2a3042;
        --card-text: #eff2f7;
        --card-subtext: #a6b0cf;
        --card-border: rgba(255, 255, 255, 0.05);
        --glass-bg: rgba(42, 48, 66, 0.7);
        --glass-border: rgba(255, 255, 255, 0.1);
    }

    .analytics-header {
        margin-bottom: 2rem;
    }

    .analytics-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--card-text);
        letter-spacing: -0.025em;
    }

    .analytics-subtitle {
        color: var(--card-subtext);
        font-size: 1rem;
    }

    .kpi-card-modern {
        background: var(--card-bg);
        border-radius: 1.25rem;
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--card-border);
        transition: all 0.3s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .kpi-card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.12);
    }

    .kpi-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .kpi-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--card-subtext);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .kpi-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--card-text);
        margin-bottom: 0.25rem;
    }

    .kpi-trend {
        font-size: 0.813rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .chart-card-modern {
        background: var(--card-bg);
        border-radius: 1.5rem;
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--card-border);
        height: 100%;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .chart-title-modern {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--card-text);
    }

    .modern-table {
        margin-bottom: 0;
    }

    .modern-table thead th {
        background: transparent;
        border-bottom: 2px solid var(--card-border);
        color: var(--card-subtext);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem;
    }

    .modern-table tbody td {
        padding: 1rem;
        color: var(--card-text);
        border-bottom: 1px solid var(--card-border);
        vertical-align: middle;
    }

    .avatar-label {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary-gradient);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    .btn-action {
        border-radius: 0.75rem;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.2s;
    }

    /* Filter Card */
    .filter-card-modern {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 1.25rem;
        padding: 1.25rem;
        margin-bottom: 2rem;
    }

    .form-label-modern {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--card-subtext);
        margin-bottom: 0.5rem;
        display: block;
        text-transform: uppercase;
    }

    .form-control-modern, .form-select-modern {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        border-radius: 0.75rem;
        padding: 0.625rem 1rem;
        color: var(--card-text);
        transition: all 0.2s;
    }

    .form-control-modern:focus, .form-select-modern:focus {
        background: rgba(255, 255, 255, 0.1);
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
</style>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="analytics-header row align-items-center">
        <div class="col-lg-7">
            <h1 class="analytics-title">HR Analytics Insights</h1>
            <p class="analytics-subtitle">Driving strategic decisions with workforce data intelligence</p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
            <div class="d-flex gap-2 justify-content-lg-end flex-wrap">
                <button type="button" class="btn btn-light btn-action shadow-sm" onclick="window.print()">
                    <i class="bx bx-printer me-2"></i>Print
                </button>
                <a href="{{ route('hrm.analytics.hr.export-pdf', request()->all()) }}" class="btn btn-soft-danger btn-action shadow-sm">
                    <i class="bx bxs-file-pdf me-2"></i>PDF
                </a>
                <a href="{{ route('hrm.analytics.hr.export-excel', request()->all()) }}" class="btn btn-soft-success btn-action shadow-sm">
                    <i class="bx bxs-file me-2"></i>Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card-modern shadow-soft">
        <form method="GET" action="{{ route('hrm.analytics.hr') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label-modern">Analysis Start</label>
                <input type="date" name="start_date" class="form-control-modern w-100" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-modern">Analysis End</label>
                <input type="date" name="end_date" class="form-control-modern w-100" value="{{ $endDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label-modern">Organizational Unit</label>
                <select name="department_id" class="form-select-modern w-100">
                    <option value="">Full Organization</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-action w-100 shadow-lg" style="background: var(--primary-gradient); border: none;">
                    <i class="bx bx-refresh me-2"></i>Update
                </button>
            </div>
        </form>
    </div>

    <!-- KPI Metrics -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card-modern">
                <div class="kpi-icon-wrapper" style="background: rgba(59, 130, 246, 0.1); color: var(--accent-blue);">
                    <i class="bx bx-group"></i>
                </div>
                <div class="kpi-label">Workforce Strength</div>
                <div class="kpi-value">{{ number_format($data['headcount']['total']) }}</div>
                <div class="kpi-trend text-success">
                    <i class="bx bx-up-arrow-alt"></i> Total Records
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="kpi-card-modern">
                <div class="kpi-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: var(--accent-green);">
                    <i class="bx bx-briefcase"></i>
                </div>
                <div class="kpi-label">Active Openings</div>
                <div class="kpi-value">{{ number_format($data['recruitment']['active_jobs']) }}</div>
                <div class="kpi-trend text-primary">
                    <i class="bx bx-user-plus"></i> Recruitment Active
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="kpi-card-modern">
                <div class="kpi-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: var(--accent-yellow);">
                    <i class="bx bx-money"></i>
                </div>
                <div class="kpi-label">Month Payroll</div>
                <div class="kpi-value">${{ number_format($data['payroll']['total_cost'] / 1000, 1) }}k</div>
                <div class="kpi-trend text-warning">
                    <i class="bx bx-stats"></i> Current Cycle
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="kpi-card-modern">
                <div class="kpi-icon-wrapper" style="background: rgba(239, 68, 68, 0.1); color: var(--accent-red);">
                    <i class="bx bx-target-lock"></i>
                </div>
                <div class="kpi-label">Avg Performance</div>
                <div class="kpi-value">{{ $data['performance']['avg_progress'] }}%</div>
                <div class="kpi-trend text-danger">
                    <i class="bx bx-trending-up"></i> Goal Velocity
                </div>
            </div>
        </div>
    </div>

    <!-- Main Analytics Grid -->
    <div class="row g-4 mb-5">
        <!-- Recruitment & Talent -->
        <div class="col-xl-4">
            <div class="chart-card-modern">
                <div class="chart-header">
                    <h5 class="chart-title-modern">Recruitment Funnel</h5>
                    <span class="badge bg-soft-info text-info">{{ $data['recruitment']['total_candidates'] }} Total</span>
                </div>
                <div id="recruitmentFunnelChart" style="min-height: 300px;"></div>
            </div>
        </div>

        <!-- Attendance & Leave -->
        <div class="col-xl-4">
            <div class="chart-card-modern">
                <div class="chart-header">
                    <h5 class="chart-title-modern">Leave Utilization</h5>
                </div>
                <div id="leaveTypeChart" style="min-height: 300px;"></div>
            </div>
        </div>

        <!-- Attendance Breakdown -->
        <div class="col-xl-4">
            <div class="chart-card-modern">
                <div class="chart-header">
                    <h5 class="chart-title-modern">Attendance Health</h5>
                </div>
                <div class="d-flex flex-column gap-3 mt-2">
                    <div class="p-3 rounded-4 bg-soft-success border border-success-subtle d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-success small fw-bold mb-0">Present Rate</p>
                            <h4 class="mb-0 fw-bold">{{ $data['attendance']['present_count'] }} <span class="fs-6 fw-normal">days</span></h4>
                        </div>
                        <i class="bx bx-check-circle fs-2 text-success opacity-50"></i>
                    </div>
                    <div class="p-3 rounded-4 bg-soft-warning border border-warning-subtle d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-warning small fw-bold mb-0">Late Arrivals</p>
                            <h4 class="mb-0 fw-bold">{{ $data['attendance']['late_count'] }} <span class="fs-6 fw-normal">incidents</span></h4>
                        </div>
                        <i class="bx bx-time-five fs-2 text-warning opacity-50"></i>
                    </div>
                    <div class="p-3 rounded-4 bg-soft-danger border border-danger-subtle d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-danger small fw-bold mb-0">Absences</p>
                            <h4 class="mb-0 fw-bold">{{ $data['attendance']['absent_count'] }} <span class="fs-6 fw-normal">records</span></h4>
                        </div>
                        <i class="bx bx-x-circle fs-2 text-danger opacity-50"></i>
                    </div>
                </div>
                <div class="text-center mt-4 pt-2 border-top">
                    <p class="text-muted small mb-0">Average Daily Shift: <strong>{{ $data['attendance']['avg_working_hours'] }} hrs</strong></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 & 2 -->
    <div class="row g-4 mb-5">
        <div class="col-xl-8">
            <div class="chart-card-modern">
                <div class="chart-header">
                    <h5 class="chart-title-modern">Workforce Growth Dynamics</h5>
                    <div class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">New Hires vs Exits</div>
                </div>
                <div id="headcountTrendChart" style="min-height: 350px;"></div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="chart-card-modern">
                <div class="chart-header">
                    <h5 class="chart-title-modern">Organizational Units</h5>
                </div>
                <div id="departmentChart" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-xl-4">
            <div class="chart-card-modern">
                <div class="chart-header">
                    <h5 class="chart-title-modern">Gender Diversity</h5>
                </div>
                <div id="genderChart" style="min-height: 300px;"></div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="chart-card-modern">
                <div class="chart-header">
                    <h5 class="chart-title-modern">Age Demographics</h5>
                </div>
                <div id="ageChart" style="min-height: 300px;"></div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="chart-card-modern">
                <div class="chart-header">
                    <h5 class="chart-title-modern">Retention (Years)</h5>
                </div>
                <div id="tenureChart" style="min-height: 250px;"></div>
                <div class="text-center mt-4 p-3 bg-light rounded-4" style="background: rgba(0,0,0,0.02) !important;">
                    <p class="text-muted small fw-bold text-uppercase mb-1">Average Tenure</p>
                    <h3 class="mb-0 fw-bold" style="color: var(--accent-blue);">{{ $data['tenure']['average'] }} <span class="fs-6 fw-normal">Years</span></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Inventory & Performance -->
    <div class="row g-4">
        <div class="col-xl-6">
            <div class="chart-card-modern">
                <div class="chart-header">
                    <h5 class="chart-title-modern">Employee Status Inventory</h5>
                </div>
                <div class="table-responsive">
                    <table class="table modern-table table-hover">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Headcount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(['active' => 'success', 'confirmed' => 'primary', 'probation' => 'warning', 'notice_period' => 'info', 'on_leave' => 'secondary'] as $status => $color)
                            <tr>
                                <td><span class="badge bg-soft-{{ $color }} text-{{ $color }} me-2"><i class="bx bxs-circle"></i></span> {{ ucfirst(str_replace('_', ' ', $status)) }}</td>
                                <td class="text-end fw-bold">{{ $data['headcount'][$status] ?? 0 }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="chart-card-modern">
                <div class="chart-header">
                    <h5 class="chart-title-modern">Performance Benchmarks</h5>
                </div>
                <div class="p-4 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center p-4 rounded-circle mb-4" style="background: rgba(99, 102, 241, 0.1); width: 120px; height: 120px;">
                        <h2 class="mb-0 fw-800 text-indigo">{{ $data['performance']['avg_progress'] }}%</h2>
                    </div>
                    <h6>Average Individual Goal Progress</h6>
                    <div class="progress mt-3 rounded-pill" style="height: 10px;">
                        <div class="progress-bar bg-primary" style="width: {{ $data['performance']['avg_progress'] }}%"></div>
                    </div>
                </div>
                <div class="row text-center mt-2 g-0 border-top mt-4">
                    <div class="col-6 p-3 border-end">
                        <h4 class="mb-1 fw-bold">{{ $data['performance']['completed_count'] }}</h4>
                        <p class="text-muted small mb-0">Goals Completed</p>
                    </div>
                    <div class="col-6 p-3">
                        <h4 class="mb-1 fw-bold text-danger">{{ $data['attrition']['attrition_rate'] }}%</h4>
                        <p class="text-muted small mb-0">Annual Attrition</p>
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
document.addEventListener("DOMContentLoaded", function() {
    const isDarkMode = document.body.getAttribute('data-layout-mode') === 'dark';
    const chartTheme = { mode: isDarkMode ? 'dark' : 'light' };
    const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.05)' : '#f1f1f1';

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
            type: 'area',
            height: 350,
            toolbar: { show: false },
            background: 'transparent'
        },
        theme: chartTheme,
        colors: ['#10b981', '#ef4444'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100, 100, 100]
            }
        },
        xaxis: {
            categories: @json($data['growth']['months']),
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: { labels: { style: { colors: isDarkMode ? '#a6b0cf' : '#4b5563' } } },
        legend: { position: 'top', horizontalAlign: 'right' },
        grid: { borderColor: gridColor, strokeDashArray: 4 }
    };
    new ApexCharts(document.querySelector("#headcountTrendChart"), headcountOptions).render();

    // Department Distribution Chart
    var deptOptions = {
        series: @json(array_column($data['departments'], 'count')),
        chart: {
            type: 'donut',
            height: 300,
            background: 'transparent'
        },
        theme: chartTheme,
        labels: @json(array_column($data['departments'], 'name')),
        colors: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#3b82f6', '#ec4899'],
        legend: { position: 'bottom' },
        dataLabels: { enabled: false },
        stroke: { show: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '75%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            color: isDarkMode ? '#eff2f7' : '#1f2937'
                        }
                    }
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#departmentChart"), deptOptions).render();

    // Gender Chart
    var genderOptions = {
        series: @json(array_values($data['diversity']['gender'])),
        chart: {
            type: 'pie',
            height: 300,
            background: 'transparent'
        },
        theme: chartTheme,
        labels: @json(array_keys($data['diversity']['gender'])),
        colors: ['#3b82f6', '#ec4899', '#6b7280'],
        legend: { position: 'bottom' },
        stroke: { show: false },
        dataLabels: {
            enabled: true,
            dropShadow: { enabled: false }
        }
    };
    new ApexCharts(document.querySelector("#genderChart"), genderOptions).render();

    // Age Distribution Chart
    var ageOptions = {
        series: [{
            name: 'Employees',
            data: @json(array_values($data['diversity']['age_ranges']))
        }],
        chart: {
            type: 'bar',
            height: 300,
            toolbar: { show: false },
            background: 'transparent'
        },
        theme: chartTheme,
        plotOptions: {
            bar: {
                borderRadius: 8,
                columnWidth: '50%',
                distributed: true
            }
        },
        dataLabels: { enabled: false },
        colors: ['#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899'],
        xaxis: {
            categories: @json(array_keys($data['diversity']['age_ranges'])),
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        grid: { borderColor: gridColor, strokeDashArray: 4 },
        legend: { show: false }
    };
    new ApexCharts(document.querySelector("#ageChart"), ageOptions).render();

    // Tenure Chart
    var tenureOptions = {
        series: [{
            name: 'Employees',
            data: @json(array_values($data['tenure']['ranges']))
        }],
        chart: {
            type: 'bar',
            height: 250,
            toolbar: { show: false },
            background: 'transparent'
        },
        theme: chartTheme,
        plotOptions: {
            bar: {
                borderRadius: 6,
                horizontal: true,
                barHeight: '60%'
            }
        },
        colors: ['#3b82f6'],
        dataLabels: { enabled: false },
        xaxis: {
            categories: @json(array_keys($data['tenure']['ranges'])),
            axisBorder: { show: false }
        },
        grid: { borderColor: gridColor, strokeDashArray: 4 }
    };
    new ApexCharts(document.querySelector("#tenureChart"), tenureOptions).render();

    // New: Recruitment Funnel Chart
    var funnelOptions = {
        series: [{
            name: 'Candidates',
            data: @json(array_values($data['recruitment']['funnel']))
        }],
        chart: {
            type: 'bar',
            height: 300,
            toolbar: { show: false },
            background: 'transparent'
        },
        theme: chartTheme,
        plotOptions: {
            bar: {
                borderRadius: 6,
                horizontal: true,
                distributed: true,
                barHeight: '70%',
            }
        },
        colors: ['#6366f1', '#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: { colors: ['#fff'] },
            formatter: function(val, opt) {
                return opt.w.globals.labels[opt.dataPointIndex] + ": " + val
            },
            offsetX: 0,
        },
        xaxis: {
            categories: @json(array_keys($data['recruitment']['funnel'])),
            labels: { show: false }
        },
        yaxis: { labels: { show: false } },
        grid: { show: false },
        legend: { show: false }
    };
    new ApexCharts(document.querySelector("#recruitmentFunnelChart"), funnelOptions).render();

    // New: Leave Type Chart
    var leaveTypeOptions = {
        series: @json(array_values($data['leaves']['type_distribution'])),
        chart: {
            type: 'donut',
            height: 300,
            background: 'transparent'
        },
        theme: chartTheme,
        labels: @json(array_keys($data['leaves']['type_distribution'])),
        colors: ['#fbbf24', '#34d399', '#60a5fa', '#f87171', '#a78bfa'],
        legend: { position: 'bottom' },
        dataLabels: { enabled: false },
        stroke: { show: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Days',
                            color: isDarkMode ? '#eff2f7' : '#1f2937'
                        }
                    }
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#leaveTypeChart"), leaveTypeOptions).render();
});
</script>
@endsection
