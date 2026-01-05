@extends('Main::layouts.app')

@section('title', 'Employee Self Service')

@section('content')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.2);
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        --card-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        --card-bg: #ffffff;
        --card-text: #1f2937;
        --card-subtext: #4b5563;
        --card-border: transparent;
        
        /* Presence Colors from Reference */
        --color-present: #55C9FF;
        --color-absent: #FFB76B;
        --color-leave: #99F5FF;
        --color-late: #ff8080;
    }

    body[data-layout-mode="dark"] {
        --card-bg: #2a3042;
        --card-text: #eff2f7;
        --card-subtext: #a6b0cf;
        --card-border: rgba(255, 255, 255, 0.05);
        --glass-bg: rgba(42, 48, 66, 0.7);
        --glass-border: rgba(255, 255, 255, 0.1);
    }

    .dashboard-greeting {
        font-size: 2rem;
        font-weight: 700;
        color: var(--card-text);
        margin-bottom: 0.5rem;
    }

    .dashboard-subtext {
        color: var(--card-subtext);
        margin-bottom: 2rem;
    }

    .kpi-card-refined {
        background: var(--card-bg);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        transition: transform 0.3s ease;
        height: 100%;
        border: 1px solid var(--card-border);
    }

    .kpi-leave-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
    }
    
    body[data-layout-mode="light"] .kpi-leave-card {
        background: #F8FAFF;
        border: 1px solid #EBF1FF;
    }

    .kpi-title-refined {
        font-size: 1rem;
        font-weight: 500;
        color: var(--card-subtext);
        margin-bottom: 1rem;
    }

    .kpi-value-refined {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--card-text);
    }

    /* Presence Styles */
    .presence-card {
        color: #000;
        border: none;
    }
    .bg-present { background-color: var(--color-present); }
    .bg-absent { background-color: var(--color-absent); }
    .bg-leave { background-color: var(--color-leave); }
    .bg-late { background-color: var(--color-late); }

    .chart-card {
        background: var(--card-bg);
        border-radius: 1.25rem;
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        margin-bottom: 1.5rem;
        height: calc(100% - 1.5rem);
        border: 1px solid var(--card-border);
    }

    .chart-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--card-text);
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notice-item {
        display: flex;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid var(--card-border);
    }

    .notice-item:last-child {
        border-bottom: none;
    }

    .notice-img {
        width: 60px;
        height: 60px;
        border-radius: 0.75rem;
        object-fit: cover;
    }

    .notice-content h6 {
        margin-bottom: 0.25rem;
        font-weight: 600;
        color: var(--card-text);
    }

    .notice-content p {
        font-size: 0.75rem;
        color: var(--card-subtext);
        margin-bottom: 0;
    }
</style>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="dashboard-greeting">
                    @php
                        $hour = date('H');
                        $greeting = 'Good Morning';
                        if ($hour >= 12 && $hour < 18) $greeting = 'Good Afternoon';
                        elseif ($hour >= 18) $greeting = 'Good Evening';
                        
                        $displayName = $employee->first_name;
                        // Fallback to User name if Employee record name is generic or specifically 'Rahim' (likely a seeder default)
                        if (strtolower($displayName) == 'rahim' || empty($displayName)) {
                            $displayName = explode(' ', auth()->user()->name)[0];
                        }
                    @endphp
                    {{ $greeting }}, {{ $displayName }}!
                </h1>
                <p class="dashboard-subtext">Keep your face always toward the sunshine, and shadows will fall behind you.</p>
            </div>
            <div class="d-none d-md-block">
                <div class="dropdown">
                    <button class="btn btn-white shadow-sm dropdown-toggle rounded-pill px-4" type="button" data-bs-toggle="dropdown">
                        Today <i class="mdi mdi-chevron-down ms-1"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Yesterday</a></li>
                        <li><a class="dropdown-item" href="#">Last 7 Days</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 1: Leave Balances -->
<div class="row mb-4 g-4">
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card-refined kpi-leave-card">
            <div class="kpi-title-refined">Sick Leave</div>
            <div class="kpi-value-refined">{{ $leaveStats['sick'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card-refined kpi-leave-card">
            <div class="kpi-title-refined">Casual Leave</div>
            <div class="kpi-value-refined">{{ $leaveStats['casual'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card-refined kpi-leave-card">
            <div class="kpi-title-refined">Total Leave</div>
            <div class="kpi-value-refined">{{ $leaveStats['total'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card-refined @if($pendingLeaves > 0) border-danger @endif">
            <div class="kpi-title-refined">Pending Approvals</div>
            <div class="kpi-value-refined @if($pendingLeaves > 0) text-danger @endif">{{ $pendingLeaves }}</div>
        </div>
    </div>
</div>

<!-- Row 2: Office Presence -->
<div class="row mb-4 g-4">
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card-refined bg-present presence-card">
            <div class="kpi-title-refined">Today's Present</div>
            <div class="kpi-value-refined">{{ $officePresence['present'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card-refined bg-absent presence-card">
            <div class="kpi-title-refined">Today's Absent</div>
            <div class="kpi-value-refined">{{ $officePresence['absent'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card-refined bg-leave presence-card">
            <div class="kpi-title-refined">Today's Leave</div>
            <div class="kpi-value-refined">{{ $officePresence['on_leave'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card-refined bg-late presence-card">
            <div class="kpi-title-refined">Today's Late In</div>
            <div class="kpi-value-refined">{{ $officePresence['late'] }}</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Personal Progress & Trends -->
    <div class="col-xl-8">
        <div class="row">
            <div class="col-md-12">
                <div class="chart-card">
                    <div class="chart-title">
                        <span>My Attendance Trend (Last 7 Days)</span>
                        <span class="badge badge-soft-success font-size-11"><i class="mdi mdi-trending-up me-1"></i>0.12% Previous Month</span>
                    </div>
                    <div id="attendance-trend-chart" style="min-height: 350px;"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">
                        <span>Office Happiness Index</span>
                    </div>
                    <div id="happiness-donut" style="min-height: 250px;"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-title">
                        <span>Leave Utilization</span>
                    </div>
                    <div id="leave-utilization-chart" style="min-height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Notice Board & Events -->
    <div class="col-xl-4">
        <div class="chart-card">
            <div class="chart-title">
                <span>Notice Board</span>
            </div>
            
            @foreach($notices as $notice)
            <div class="notice-item">
                <img src="{{ $notice['image'] }}" class="notice-img" alt="">
                <div class="notice-content">
                    <h6>{{ $notice['title'] }}</h6>
                    <p>{{ $notice['description'] }}</p>
                    <span class="small text-muted mt-2 d-block"><i class="mdi mdi-calendar-outline me-1"></i>{{ $notice['date'] }}</span>
                </div>
            </div>
            @endforeach

            <div class="notice-item">
                <div class="notice-content w-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Upcoming Birthday</h6>
                        <span class="badge border-primary text-primary">Event</span>
                    </div>
                    @forelse($upcomingBirthdays as $bday)
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar-xs me-3">
                            <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-14">
                                {{ substr($bday->first_name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 font-size-13">{{ $bday->full_name }}</h6>
                            <p class="text-muted small mb-0">{{ $bday->date_of_birth->format('d M') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted small">No upcoming birthdays</p>
                    @endforelse
                </div>
            </div>

            <div class="notice-item">
                <div class="notice-content w-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Public Holidays</h6>
                        <span class="badge border-info text-info">View All</span>
                    </div>
                    @forelse($upcomingHolidays as $holiday)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-calendar-star text-info me-2 font-size-16"></i>
                            <span class="font-size-13">{{ $holiday->name }}</span>
                        </div>
                        <span class="text-muted small">{{ $holiday->start_date->format('d M') }}</span>
                    </div>
                    @empty
                    <p class="text-muted small">No upcoming holidays</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const isDarkMode = document.body.getAttribute('data-layout-mode') === 'dark';
        const chartTheme = { mode: isDarkMode ? 'dark' : 'light' };

        // Attendance Trend Chart (Line Chart)
        var optionsTrend = {
            series: [{
                name: 'Working Hours',
                data: {!! json_encode(array_column($attendanceTrend, 'hours')) !!}
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: false },
                background: 'transparent'
            },
            theme: chartTheme,
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3, colors: ['#6366f1'] },
            xaxis: {
                categories: {!! json_encode(array_column($attendanceTrend, 'day')) !!},
                labels: { style: { colors: '#9ca3af' } }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            grid: { borderColor: isDarkMode ? '#32394e' : '#f3f4f6' }
        };
        new ApexCharts(document.querySelector("#attendance-trend-chart"), optionsTrend).render();

        // Happiness Index (Radial)
        var optionsRadial = {
            series: [{{ $happinessRate }}],
            chart: {
                height: 250,
                type: 'radialBar',
                background: 'transparent'
            },
            theme: chartTheme,
            plotOptions: {
                radialBar: {
                    hollow: { size: '70%', background: 'transparent' },
                    dataLabels: {
                        name: { show: false },
                        value: {
                            offsetY: 10,
                            fontSize: '22px',
                            fontWeight: '700',
                            color: isDarkMode ? '#eff2f7' : '#111827'
                        }
                    }
                }
            },
            colors: ['#22c55e'],
            labels: ['Happiness'],
        };
        new ApexCharts(document.querySelector("#happiness-donut"), optionsRadial).render();

        // Leave Utilization (Donut)
        var optionsLeave = {
            series: [{{ $leaveStats['sick'] }}, {{ $leaveStats['casual'] }}, {{ max(0, $leaveStats['total'] - ($leaveStats['sick'] + $leaveStats['casual'])) }}],
            chart: {
                type: 'donut',
                height: 250,
                background: 'transparent'
            },
            theme: chartTheme,
            labels: ['Sick', 'Casual', 'Others'],
            colors: ['#ef4444', '#f59e0b', '#6366f1'],
            legend: { 
                position: 'bottom',
                labels: { colors: isDarkMode ? '#a6b0cf' : '#4b5563' }
            },
            dataLabels: { enabled: false },
            stroke: { show: false }
        };
        new ApexCharts(document.querySelector("#leave-utilization-chart"), optionsLeave).render();
    });
</script>
@endsection
