@extends("Main::layouts.app")

@section("content")
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
            <div class="page-title-right">
                <form action="{{ route('dashboard.home') }}" method="GET" class="d-flex align-items-center gap-2">
                    <div class="input-group">
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" placeholder="Start Date">
                        <span class="input-group-text">to</span>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" placeholder="End Date">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-filter-alt"></i> Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- System Health Monitor -->
<div class="row mb-2">
    <div class="col-xl-12">
        <div class="card bg-white shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">
                    <i class="mdi mdi-server-network me-2 text-primary"></i>System Health Status
                </h4>
                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard.system-health') }}" class="btn btn-sm btn-light me-2">View Full Report</a>
                    <span class="badge bg-soft-success text-success">
                        <i class="mdi mdi-circle-medium me-1"></i> Running
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Disk Usage -->
                    <div class="col-md-3">
                        <div class="p-3">
                            <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-soft-primary text-primary rounded-circle font-size-20">
                                    <i class="mdi mdi-harddisk"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Disk Usage</h6>
                                <p class="text-muted mb-0 font-size-12">Total: {{ $systemHealth['disk']['total'] }}</p>
                            </div>
                        </div>
                        <h4 class="mb-2">{{ $systemHealth['disk']['percentage'] }}% <small class="text-muted font-size-12 ms-1">Used</small></h4>
                        <div class="progress h-5px rounded-pill">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $systemHealth['disk']['percentage'] }}%"></div>
                        </div>
                        <p class="text-muted mt-2 mb-0 font-size-12">Free: {{ $systemHealth['disk']['free'] }}</p>
                        </div>
                    </div>

                    <!-- Memory Usage (App) -->
                    <div class="col-md-3">
                        <div class="p-3">
                            <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-soft-info text-info rounded-circle font-size-20">
                                    <i class="mdi mdi-memory"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">App Memory</h6>
                                <p class="text-muted mb-0 font-size-12">Limit: {{ $systemHealth['memory']['limit'] }}</p>
                            </div>
                        </div>
                        <h4 class="mb-2">{{ $systemHealth['memory']['usage'] }} <small class="text-muted font-size-12 ms-1">Allocated</small></h4>
                        <div class="progress h-5px rounded-pill">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 25%"></div>
                        </div>
                            <p class="text-muted mt-2 mb-0 font-size-12">Peak: {{ $systemHealth['memory']['peak'] }}</p>
                        </div>
                    </div>

                    <!-- Database Size -->
                    <div class="col-md-3">
                         <div class="p-3">
                            <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-soft-success text-success rounded-circle font-size-20">
                                    <i class="mdi mdi-database"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Database Status</h6>
                                <p class="text-muted mb-0 font-size-12">MySQL</p>
                            </div>
                        </div>
                        <h4 class="mb-2">{{ $systemHealth['database'] }} MB <small class="text-muted font-size-12 ms-1">Size</small></h4>
                         <div class="progress h-5px rounded-pill">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 15%"></div>
                        </div>
                            <p class="text-muted mt-2 mb-0 font-size-12">Tables optimized</p>
                        </div>
                    </div>

                    <!-- Environment Info -->
                    <div class="col-md-3">
                        <div class="p-3">
                            <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-3">
                                <span class="avatar-title bg-soft-warning text-warning rounded-circle font-size-20">
                                    <i class="mdi mdi-server"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Environment</h6>
                                <p class="text-muted mb-0 font-size-12">{{ app()->environment() }}</p>
                            </div>
                        </div>
                        <div class="row g-2">
                             <div class="col-6">
                                 <div class="border rounded p-2 text-center">
                                     <p class="mb-0 text-muted font-size-10">PHP</p>
                                     <h6 class="mb-0">{{ $systemHealth['system']['php'] }}</h6>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="border rounded p-2 text-center">
                                     <p class="mb-0 text-muted font-size-10">Laravel</p>
                                     <h6 class="mb-0">{{ $systemHealth['system']['laravel'] }}</h6>
                                 </div>
                             </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100 border-0 shadow-sm">
            <div class="card-body bg-light">
                <div class="d-flex align-items-center p-3">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Users</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="{{ $summary->total ?? 0 }}">{{ $summary->total ?? 0 }}</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-primary text-primary">All Time</span>
                            <span class="ms-1 text-muted font-size-13">Registered</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end dash-widget">
                        <div class="avatar-sm rounded-circle bg-primary bg-soft d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-account-group-outline font-size-24 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100 border-0 shadow-sm">
            <div class="card-body bg-light">
                <div class="d-flex align-items-center p-3">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Verified Users</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="{{ $summary->total_verified ?? 0 }}">{{ $summary->total_verified ?? 0 }}</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-success text-success">Active</span>
                            <span class="ms-1 text-muted font-size-13">Verified Accounts</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end dash-widget">
                        <div class="avatar-sm rounded-circle bg-success bg-soft d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-check-decagram font-size-24 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100 border-0 shadow-sm">
            <div class="card-body bg-light">
                <div class="d-flex align-items-center p-3">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Pending Users</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="{{ $summary->total_pending ?? 0 }}">{{ $summary->total_pending ?? 0 }}</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-warning text-warning">Action Needed</span>
                            <span class="ms-1 text-muted font-size-13">Awaiting Approval</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end dash-widget">
                        <div class="avatar-sm rounded-circle bg-warning bg-soft d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-timer-sand font-size-24 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100 border-0 shadow-sm">
            <div class="card-body bg-light">
                <div class="d-flex align-items-center p-3">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Completed Profiles</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="{{ $summary->total_completed ?? 0 }}">{{ $summary->total_completed ?? 0 }}</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-info text-info">Onboarded</span>
                            <span class="ms-1 text-muted font-size-13">Fully Completed</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end dash-widget">
                        <div class="avatar-sm rounded-circle bg-info bg-soft d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-account-check-outline font-size-24 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mb-4">
    <!-- Trend Chart -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex bg-light">
                <h4 class="card-title mb-0 flex-grow-1">User Registration Trend</h4>
            </div>
            <div class="card-body bg-light">
                <div id="user_trend_chart" class="apex-charts" dir="ltr"></div>
            </div>
        </div>
    </div>

    <!-- Status Donut Chart -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex bg-light">
                <h4 class="card-title mb-0 flex-grow-1">User Status Distribution</h4>
            </div>
            <div class="card-body bg-light">
                <div id="user_status_chart" class="apex-charts" dir="ltr"></div>
                <div class="text-center text-muted rich-list-item p-3">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item">
                            <h5 class="font-size-14 mb-0">Total: {{ $summary->total ?? 0 }}</h5>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push("scripts")
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Trend Chart Data
        var trendData = @json($trend);
        var categories = trendData.map(item => item.date);
        var seriesData = trendData.map(item => item.count);

        var options = {
            series: [{
                name: 'New Users',
                data: seriesData
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: ['#556ee6'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    inverseColors: false,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100, 100, 100]
                },
            },
            xaxis: {
                categories: categories,
                type: 'datetime',
                labels: { format: 'dd MMM' }
            },
            yaxis: {
                title: { text: 'Users' }
            },
            grid: {
                borderColor: '#f1f1f1',
            }
        };

        var chart = new ApexCharts(document.querySelector("#user_trend_chart"), options);
        chart.render();

        // Status Donut Chart
        var statusOptions = {
            series: [
                {{ $summary->total_verified ?? 0 }},
                {{ $summary->total_pending ?? 0 }},
                {{ $summary->total_inprogress ?? 0 }},
                {{ $summary->total_rejected ?? 0 }}
            ],
            labels: ['Verified', 'Pending', 'In Progress', 'Rejected'],
            chart: {
                type: 'donut',
                height: 320,
            },
            colors: ['#2af598', '#f46a6a', '#556ee6', '#343a40'],
            legend: {
                position: 'bottom'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%'
                    }
                }
            }
        };

        var statusChart = new ApexCharts(document.querySelector("#user_status_chart"), statusOptions);
        statusChart.render();
    });
</script>
@endpush