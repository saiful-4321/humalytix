@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <h2>Leave Dashboard</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Leave Dashboard</li>
            </ul>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 d-flex justify-content-end gap-2">
            <a href="{{ route('hrm.leaves.create') }}" class="btn btn-primary">
                <i class="mdi mdi-plus me-1"></i> Apply Leave
            </a>
            <a href="{{ route('hrm.leaves.my-leaves') }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-account-clock me-1"></i> My History
            </a>
        </div>
    </div>
</div>

<div class="row clearfix">
    {{-- KPI Cards --}}
    <div class="col-lg-3 col-md-6">
        <div class="card bg-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-in-bg bg-indigo text-white rounded-circle">
                        <i class="mdi mdi-account-off fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <span class="text-muted small text-uppercase fw-bold">On Leave Today</span>
                        <h3 class="mb-0 fw-bold">{{ $onLeaveToday }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card bg-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-in-bg bg-orange text-white rounded-circle">
                        <i class="mdi mdi-clock-outline fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <span class="text-muted small text-uppercase fw-bold">Pending Requests</span>
                        <h3 class="mb-0 fw-bold">{{ $pendingRequests }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card bg-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-in-bg bg-pink text-white rounded-circle">
                        <i class="mdi mdi-calendar-star fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <span class="text-muted small text-uppercase fw-bold">Next Holiday</span>
                        <h6 class="mb-0 fw-bold text-truncate" style="max-width: 150px;">
                            {{ $upcomingHolidays->first()->name ?? 'None' }}
                        </h6>
                        <small class="text-muted">{{ $upcomingHolidays->first()?->start_date->format('d M') ?? '' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card bg-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon-in-bg bg-success text-white rounded-circle">
                        <i class="mdi mdi-account-multiple-check fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <span class="text-muted small text-uppercase fw-bold">Active Employees</span>
                        <h3 class="mb-0 fw-bold">{{ \App\Modules\HRM\Models\Employee::where('status', 'confirmed')->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    {{-- Leave Trends Chart --}}
    <div class="col-lg-8 col-md-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h6 class="card-title mb-0">Leave Trends ({{ date('Y') }})</h6>
            </div>
            <div class="card-body">
                <div id="leave-trends-chart" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    {{-- Leave Distribution Chart --}}
    <div class="col-lg-4 col-md-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h6 class="card-title mb-0">Leave Type Distribution</h6>
            </div>
            <div class="card-body">
                <div id="leave-distribution-chart" style="height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h6 class="card-title mb-0">Leave Calendar</h6>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    {{-- Who's Out Today --}}
    <div class="col-lg-4 col-md-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">Who is Out Today</h6>
                <span class="badge bg-soft-primary">{{ $whoIsOut->count() }} People</span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($whoIsOut as $leave)
                    <div class="list-group-item d-flex align-items-center">
                        <div class="avatar-sm me-3">
                            <span class="avatar-title rounded-circle bg-soft-primary text-primary text-uppercase">
                                {{ substr($leave->employee->first_name, 0, 1) }}
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 text-truncate">{{ $leave->employee->full_name }}</h6>
                            <small class="text-muted">{{ $leave->employee->designation }}</small>
                        </div>
                        <span class="badge bg-light text-dark">{{ $leave->leaveType->code }}</span>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="mdi mdi-check-circle-outline fs-3"></i>
                        <p class="mb-0">Everyone is present today!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Upcoming Holidays --}}
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-header border-bottom">
                <h6 class="card-title mb-0">Upcoming Holidays</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <tbody>
                        @forelse($upcomingHolidays as $holiday)
                        <tr>
                            <td style="width: 50px;" class="text-center">
                                <div class="bg-soft-danger text-danger rounded p-1">
                                    <span class="d-block fw-bold">{{ $holiday->start_date->format('d') }}</span>
                                    <small class="text-uppercase">{{ $holiday->start_date->format('M') }}</small>
                                </div>
                            </td>
                            <td>
                                <h6 class="mb-0">{{ $holiday->name }}</h6>
                                <small class="text-muted">{{ ucfirst($holiday->type) }}</small>
                            </td>
                            <td class="text-end text-muted small">
                                {{ $holiday->start_date->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">No upcoming holidays found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Requests --}}
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-header border-bottom">
                <h6 class="card-title mb-0">Recent Requests</h6>
            </div>
            <div class="card-body p-0">
                 <div class="list-group list-group-flush">
                    @forelse($recentRequests as $req)
                    <a href="{{ route('hrm.leaves.show', $req->id) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between mb-1">
                            <h6 class="mb-0 text-truncate" style="max-width: 150px;">{{ $req->employee->full_name }}</h6>
                            <small class="text-muted">{{ $req->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="mb-1 text-muted">
                                <span class="badge bg-light text-dark me-1">{{ $req->leaveType->code }}</span>
                                {{ $req->days }} Days
                            </small>
                            @if($req->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($req->status == 'approved')
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-4 text-muted">No recent requests.</div>
                    @endforelse
                </div>
            </div>
            <div class="card-footer text-center bg-white border-top">
                <a href="{{ route('hrm.leaves.index') }}" class="text-primary fw-medium small">View All Leaves <i class="mdi mdi-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Full Calendar
        var calendarEl = document.getElementById('calendar');
        var weeklyHolidays = @json($weeklyHolidays);
        var holidayDays = [];
        
        // Map day names to FullCalendar numbers (Sun=0, Mon=1, etc.)
        const dayMap = {
            'Sunday': 0, 'Monday': 1, 'Tuesday': 2, 'Wednesday': 3, 
            'Thursday': 4, 'Friday': 5, 'Saturday': 6
        };

        weeklyHolidays.forEach(day => {
            if(dayMap[day] !== undefined) holidayDays.push(dayMap[day]);
        });

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: @json($calendarEvents).concat(
                holidayDays.map(day => ({
                    daysOfWeek: [day],
                    display: 'background',
                    color: '#ffe5e5', // Light red for weekends
                    title: 'Weekly Off' // Optional
                }))
            ),
            eventClick: function(info) {
                if (info.event.url) {
                    window.location.href = info.event.url;
                    info.jsEvent.preventDefault();
                }
            }
        });
        calendar.render();

        // Leave Trends Chart
        var trendOptions = {
            series: [{
                name: 'Leave Requests',
                data: @json($monthlyTrendData)
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },
            colors: ['#556ee6'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.9,
                    stops: [0, 90, 100]
                }
            }
        };
        new ApexCharts(document.querySelector("#leave-trends-chart"), trendOptions).render();

        // Leave Distribution Chart
        var distributionData = @json($typeStats);
        var distOptions = {
            series: distributionData.map(item => item.value),
            labels: distributionData.map(item => item.label),
            chart: {
                type: 'donut',
                height: 350
            },
            colors: ['#556ee6', '#34c38f', '#f46a6a', '#f1b44c', '#50a5f1'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                }
                            }
                        }
                    }
                }
            },
            legend: {
                position: 'bottom'
            }
        };
        new ApexCharts(document.querySelector("#leave-distribution-chart"), distOptions).render();
    });
</script>
@endsection
