@extends('Main::layouts.app')

@section('content')

    <div class="row align-items-center mb-4">
        <div class="col-lg-12">
            <div class="card bg-white p-2">
                <div class="row align-items-center">
                    <div class="col-md-6">
                         <div class="d-flex flex-wrap gap-2">
                            <span class="fw-bold text-muted align-self-center me-2">Period:</span>
                            <a href="{{ route('hrm.leaves.dashboard', ['preset' => 'this_week', 'interval' => 'day']) }}" 
                               class="btn btn-sm {{ $preset == 'this_week' ? 'btn-primary' : 'btn-soft-primary' }}">This Week</a>
                            <a href="{{ route('hrm.leaves.dashboard', ['preset' => 'this_month', 'interval' => 'week']) }}" 
                               class="btn btn-sm {{ $preset == 'this_month' ? 'btn-primary' : 'btn-soft-primary' }}">This Month</a>
                            <a href="{{ route('hrm.leaves.dashboard', ['preset' => 'this_quarter', 'interval' => 'month']) }}" 
                               class="btn btn-sm {{ $preset == 'this_quarter' ? 'btn-primary' : 'btn-soft-primary' }}">This Quarter</a>
                            <a href="{{ route('hrm.leaves.dashboard', ['preset' => 'this_year', 'interval' => 'month']) }}" 
                               class="btn btn-sm {{ $preset == 'this_year' ? 'btn-primary' : 'btn-soft-primary' }}">This Year</a>
                         </div>
                    </div>
                    <div class="col-md-6 text-md-end mt-2 mt-md-0">
                         <form action="{{ route('hrm.leaves.dashboard') }}" method="GET" class="d-flex justify-content-md-end align-items-center gap-2">
                            <input type="hidden" name="preset" value="custom">
                            
                            <!-- Interval Toggle -->
                            <div class="btn-group btn-group-sm me-2" role="group">
                                <button type="submit" name="interval" value="day" class="btn {{ $interval == 'day' ? 'btn-secondary' : 'btn-light' }}">Day</button>
                                <button type="submit" name="interval" value="week" class="btn {{ $interval == 'week' ? 'btn-secondary' : 'btn-light' }}">Week</button>
                                <button type="submit" name="interval" value="month" class="btn {{ $interval == 'month' ? 'btn-secondary' : 'btn-light' }}">Month</button>
                            </div>

                            <!-- Custom Date -->
                            <div class="input-group input-group-sm" style="max-width: 300px;">
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                                <span class="input-group-text">-</span>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                                <button type="submit" class="btn btn-primary"><i class="mdi mdi-check"></i></button>
                            </div>

                            <a href="{{ route('hrm.leaves.create') }}" class="btn btn-sm btn-success ms-2">
                                <i class="mdi mdi-plus me-1"></i> Apply Leave
                            </a>
                         </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- TOP ROW: KPI CARDS WITH SPARKLINES -->
<div class="row clearfix">
    <!-- Total Requests -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate overflow-hidden bg-white p-3">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-medium mb-0">Total Requests</p>
                    </div>
                </div>
                <h3 class="fw-bold mb-2">{{ number_format($periodRequests) }}</h3>
                <div class="mb-0" style="height: 50px;">
                    <canvas id="spark-requests"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate overflow-hidden p-3 bg-white">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-medium mb-0">Pending Requests</p>
                    </div>
                </div>
                <h3 class="fw-bold mb-2 text-warning">{{ number_format($pendingRequests) }}</h3>
                <div class="mb-0" style="height: 50px;">
                    <canvas id="spark-pending"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Approved Leaves -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate overflow-hidden p-3 bg-white">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-medium mb-0">Approved Leaves</p>
                    </div>
                </div>
                <h3 class="fw-bold mb-2 text-success">{{ number_format($periodApproved) }}</h3>
                <div class="mb-0" style="height: 50px;">
                    <canvas id="spark-approved"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejections -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate overflow-hidden p-3 bg-white">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-medium mb-0">Rejections</p>
                    </div>
                </div>
                <h3 class="fw-bold mb-2 text-danger">{{ number_format(array_sum($sparklineRejected)) }}</h3>
                <div class="mb-0" style="height: 50px;">
                    <canvas id="spark-rejected"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECOND ROW: MARGIN GAUGES -->
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white p-3">
            <div class="card-body d-flex justify-content-around align-items-center flex-wrap gap-3">
                
                <!-- Approval Rate -->
                <div class="text-center pt-3">
                    <p class="text-muted mb-2 fw-bold">Approval Rate</p>
                    <div style="position: relative; width: 120px; height: 120px; margin: 0 auto;">
                        <canvas id="gauge-approval"></canvas>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; font-size: 1.2rem;" class="text-body">
                            {{ number_format($approvalRate, 1) }}%
                        </div>
                    </div>
                </div>

                <div class="d-none d-md-block" style="width: 1px; height: 80px; background: var(--bs-border-color);"></div>

                <!-- Utilization Rate -->
                <div class="text-center pt-3">
                    <p class="text-muted mb-2 fw-bold">Daily Utilization</p>
                    <div style="position: relative; width: 120px; height: 120px; margin: 0 auto;">
                        <canvas id="gauge-utilization"></canvas>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; font-size: 1.2rem;" class="text-body">
                            {{ number_format($utilizationRate, 1) }}%
                        </div>
                    </div>
                </div>

                <div class="d-none d-md-block" style="width: 1px; height: 80px; background: var(--bs-border-color);"></div>

                <!-- Rejection Rate -->
                <div class="text-center pt-3">
                    <p class="text-muted mb-2 fw-bold">Rejection Rate</p>
                    <div style="position: relative; width: 120px; height: 120px; margin: 0 auto;">
                        <canvas id="gauge-rejection"></canvas>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; font-size: 1.2rem;" class="text-body">
                            {{ number_format($rejectionRate, 1) }}%
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- THIRD ROW: TRENDS & SIDEBAR -->
<div class="row clearfix">
    <!-- Main Charts -->
    <div class="col-xl-9">
        <div class="row">
            <!-- Chart 1: Request Trends -->
            <div class="col-lg-12">
                <div class="card bg-white p-3">
                    <div class="card-body">
                        <h6 class="card-title mb-4">Leave Request Trends</h6>
                        <div style="height: 300px;">
                            <canvas id="trend-chart-1"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chart 2: Status Breakdown -->
            <div class="col-lg-12">
                 <div class="card bg-white p-3">
                    <div class="card-body">
                        <h6 class="card-title mb-4">Approved vs Rejected</h6>
                        <div style="height: 300px;">
                            <canvas id="trend-chart-2"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar: Smart Insights -->
    <div class="col-xl-3">
        <div class="card bg-white p-3 h-100">
            <div style="padding: 10px 0px !important" class="card-header border-bottom" >
                <h6 class="card-title mb-0">Smart Insights</h6>
            </div>
            <div class="card-body font-size-13">
                @php
                    $firstVal = $trendData['requests'][0] ?? 0;
                    $lastVal = end($trendData['requests']);
                    $diff = $lastVal - $firstVal;
                    $growthPercent = $firstVal > 0 ? ($diff / $firstVal) * 100 : ($lastVal > 0 ? 100 : 0);
                    $trendText = $diff > 0 ? 'Increase' : ($diff < 0 ? 'Decrease' : 'Stability');
                    $trendColor = $diff > 0 ? 'text-primary' : ($diff < 0 ? 'text-success' : 'text-warning');
                @endphp
                <p>
                    Between <span class="fw-semibold">{{ $trendData['labels'][0] ?? 'Start' }}</span> and <span class="fw-semibold">{{ end($trendData['labels']) }}</span>, 
                    Leave applications have shown a trend of 
                    <span class="{{ $trendColor }}">{{ $trendText }}</span>
                    @if($diff != 0)
                        ({{ number_format(abs($growthPercent), 1) }}%)
                    @endif
                    .
                </p>
                <div class="border-top my-3"></div>
                
                <p>
                    <strong>Approval Policy:</strong><br>
                    Current approval rate is {{ number_format($approvalRate, 1) }}%. 
                    High approval rates indicate efficient resource planning.
                </p>
                
                <div class="alert alert-soft-info mt-4">
                    <i class="mdi mdi-information-outline me-2"></i> 
                    {{ $onLeaveToday }} employees are away today, representing {{ number_format($utilizationRate, 1) }}% of the workforce.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FOURTH ROW: CALENDAR -->
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white p-3">
            <div class="card-header border-bottom">
                <h6 class="card-title mb-0">Leave Calendar</h6>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- BOTTOM ROW: DETAILS -->
<div class="row clearfix pb-5">
    <!-- Who's Out & Recent Requests -->
    <div class="col-xl-6">
        <div class="card bg-white p-3 h-100">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">Who is Out Today</h6>
                <span class="badge bg-soft-primary">{{ $whoIsOut->count() }} People</span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
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

    <!-- Upcoming Holidays -->
    <div class="col-xl-6">
        <div class="card bg-white p-3 h-100">
            <div class="card-header border-bottom">
                <h6 class="card-title mb-0">Upcoming Holidays</h6>
            </div>
            <div class="card-body p-0">
                 <table class="table table-hover mb-0 align-middle">
                    <tbody>
                        @forelse($upcomingHolidays as $holiday)
                        <tr>
                            <td style="width: 50px;" class="text-center border-0">
                                <div class="bg-soft-danger text-danger rounded p-1">
                                    <span class="d-block fw-bold">{{ $holiday->start_date->format('d') }}</span>
                                    <small class="text-uppercase">{{ $holiday->start_date->format('M') }}</small>
                                </div>
                            </td>
                            <td class="border-0">
                                <h6 class="mb-0">{{ $holiday->name }}</h6>
                                <small class="text-muted">{{ ucfirst($holiday->type) }}</small>
                            </td>
                            <td class="text-end text-muted small border-0">
                                {{ $holiday->start_date->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted border-0">No upcoming holidays found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textColor = '#878a99'; 
        const gridColor = 'rgba(0, 0, 0, 0.05)';

        Chart.defaults.color = textColor;
        Chart.defaults.borderColor = gridColor;

        // 1. SPARKLINES
        const sparkConfig = (ctx, data, color) => {
            if (!ctx) return;
            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($trendData['labels']) !!},
                    datasets: [{
                        data: data,
                        borderColor: color,
                        borderWidth: 2,
                        fill: true,
                        backgroundColor: (context) => {
                            const ctx = context.chart.ctx;
                            const gradient = ctx.createLinearGradient(0, 0, 0, 50);
                            gradient.addColorStop(0, color.replace('rgb', 'rgba').replace(')', ', 0.2)')); 
                            gradient.addColorStop(1, 'rgba(255,255,255,0)');
                            return gradient;
                        },
                        pointRadius: 0,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    scales: {
                        x: { display: false },
                        y: { display: false }
                    }
                }
            });
        };

        sparkConfig(document.getElementById('spark-requests'), {!! json_encode($sparklineRequests) !!}, 'rgb(41, 156, 219)'); // Blue
        sparkConfig(document.getElementById('spark-pending'), {!! json_encode($sparklinePending) !!}, 'rgb(247, 184, 75)'); // Orange
        sparkConfig(document.getElementById('spark-approved'), {!! json_encode($sparklineApproved) !!}, 'rgb(10, 179, 156)'); // Green
        sparkConfig(document.getElementById('spark-rejected'), {!! json_encode($sparklineRejected) !!}, 'rgb(240, 101, 72)'); // Red

        // 2. GAUGES (Doughnut)
        const gaugeConfig = (ctx, value, color) => {
            if (!ctx) return;
            return new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Value', 'Remaining'],
                    datasets: [{
                        data: [value, Math.max(0, 100-value)],
                        backgroundColor: [color, 'rgba(135, 138, 153, 0.1)'], 
                        borderWidth: 0,
                        cutout: '80%',
                        circumference: 240,
                        rotation: -120
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } }
                }
            });
        };

        gaugeConfig(document.getElementById('gauge-approval'), {{ $approvalRate }}, '#0ab39c');
        gaugeConfig(document.getElementById('gauge-utilization'), {{ $utilizationRate }}, '#299cdb');
        gaugeConfig(document.getElementById('gauge-rejection'), {{ $rejectionRate }}, '#ec5b5b');

        // 3. TREND CHARTS
        new Chart(document.getElementById('trend-chart-1'), {
            type: 'line',
            data: {
                labels: {!! json_encode($trendData['labels']) !!},
                datasets: [
                    {
                        label: 'Requests',
                        data: {!! json_encode($trendData['requests']) !!},
                        borderColor: '#299cdb',
                        backgroundColor: 'rgba(41, 156, 219, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Pending',
                        data: {!! json_encode($trendData['pending']) !!},
                        borderColor: '#f7b84b',
                        borderDash: [5, 5],
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { align: 'end' } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
        
        new Chart(document.getElementById('trend-chart-2'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($trendData['labels']) !!},
                datasets: [
                    {
                        label: 'Approved',
                        data: {!! json_encode($trendData['approved']) !!},
                        backgroundColor: '#0ab39c',
                        barPercentage: 0.6
                    },
                    {
                        label: 'Rejected',
                        data: {!! json_encode($trendData['rejected']) !!},
                        backgroundColor: '#f06548',
                        barPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { align: 'end' } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // 4. FULL CALENDAR
        var calendarEl = document.getElementById('calendar');
        var weeklyHolidays = @json($weeklyHolidays);
        var holidayDays = [];
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
                right: 'dayGridMonth,listWeek'
            },
            events: @json($calendarEvents).concat(
                holidayDays.map(day => ({
                    daysOfWeek: [day],
                    display: 'background',
                    color: 'rgba(236, 91, 91, 0.05)',
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

    });
</script>
@endsection
