@extends('Main::layouts.app')

@section('content')

    <div class="row align-items-center mb-4">
        <div class="col-lg-12">
            <div class="card bg-white p-2">
                <div class="row align-items-center">
                    <div class="col-md-6">
                         <div class="d-flex flex-wrap gap-2">
                            <span class="fw-bold text-muted align-self-center me-2">Period:</span>
                            <a href="{{ route('finance.dashboard', ['preset' => 'this_week', 'interval' => 'day']) }}" 
                               class="btn btn-sm {{ $preset == 'this_week' ? 'btn-primary' : 'btn-soft-primary' }}">This Week</a>
                            <a href="{{ route('finance.dashboard', ['preset' => 'this_month', 'interval' => 'week']) }}" 
                               class="btn btn-sm {{ $preset == 'this_month' ? 'btn-primary' : 'btn-soft-primary' }}">This Month</a>
                            <a href="{{ route('finance.dashboard', ['preset' => 'this_quarter', 'interval' => 'month']) }}" 
                               class="btn btn-sm {{ $preset == 'this_quarter' ? 'btn-primary' : 'btn-soft-primary' }}">This Quarter</a>
                            <a href="{{ route('finance.dashboard', ['preset' => 'this_year', 'interval' => 'month']) }}" 
                               class="btn btn-sm {{ $preset == 'this_year' ? 'btn-primary' : 'btn-soft-primary' }}">This Year</a>
                         </div>
                    </div>
                    <div class="col-md-6 text-md-end mt-2 mt-md-0">
                         <form action="{{ route('finance.dashboard') }}" method="GET" class="d-flex justify-content-md-end align-items-center gap-2">
                            <input type="hidden" name="preset" value="custom">
                            
                            <!-- Interval Toggle -->
                            <div class="btn-group btn-group-sm me-2" role="group">
                                <button type="submit" name="interval" value="day" class="btn {{ $interval == 'day' ? 'btn-secondary' : 'btn-light' }}">Day</button>
                                <button type="submit" name="interval" value="week" class="btn {{ $interval == 'week' ? 'btn-secondary' : 'btn-light' }}">Week</button>
                                <button type="submit" name="interval" value="month" class="btn {{ $interval == 'month' ? 'btn-secondary' : 'btn-light' }}">Month</button>
                                <button type="submit" name="interval" value="quarter" class="btn {{ $interval == 'quarter' ? 'btn-secondary' : 'btn-light' }}">Quarter</button>
                            </div>

                            <!-- Custom Date -->
                            <div class="input-group input-group-sm" style="max-width: 300px;">
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                                <span class="input-group-text">-</span>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                                <button type="submit" class="btn btn-primary"><i class="mdi mdi-check"></i></button>
                            </div>
                         </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- TOP ROW: KPI CARDS WITH SPARKLINES -->
<div class="row clearfix">
    <!-- Revenue -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate overflow-hidden bg-white p-3">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-medium mb-0">Revenue</p>
                    </div>
                </div>
                <h3 class="fw-bold mb-2">{{ number_format($totalRevenue, 2) }}</h3>
                <div class="mb-0" style="height: 50px;">
                    <canvas id="spark-revenue"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- COGS -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate overflow-hidden p-3 bg-white">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-medium mb-0">COGS</p>
                    </div>
                </div>
                <h3 class="fw-bold mb-2 text-danger">{{ number_format($totalCOGS, 2) }}</h3>
                <div class="mb-0" style="height: 50px;">
                    <canvas id="spark-cogs"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gross Profit -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate overflow-hidden p-3 bg-white">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-medium mb-0">Gross Profit</p>
                    </div>
                </div>
                <h3 class="fw-bold mb-2 text-info">{{ number_format($grossProfit, 2) }}</h3>
                <div class="mb-0" style="height: 50px;">
                    <canvas id="spark-gross"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Profit -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate overflow-hidden p-3 bg-white">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase fw-medium mb-0">Net Profit</p>
                    </div>
                </div>
                <h3 class="fw-bold mb-2 text-success">{{ number_format($netProfit, 2) }}</h3>
                <div class="mb-0" style="height: 50px;">
                    <canvas id="spark-net"></canvas>
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
                
                <!-- Gross Profit Margin -->
                <div class="text-center pt-3">
                    <p class="text-muted mb-2 fw-bold">Gross Profit Margin</p>
                    <div style="position: relative; width: 120px; height: 120px; margin: 0 auto;">
                        <canvas id="gauge-gross"></canvas>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; font-size: 1.2rem;" class="text-body">
                            {{ number_format($grossProfitMargin, 1) }}%
                        </div>
                    </div>
                </div>

                <div class="d-none d-md-block" style="width: 1px; height: 80px; background: var(--bs-border-color);"></div>

                <!-- Operating Expense Ratio -->
                <div class="text-center pt-3">
                    <p class="text-muted mb-2 fw-bold">Operating Expense Ratio</p>
                    <div style="position: relative; width: 120px; height: 120px; margin: 0 auto;">
                        <canvas id="gauge-opex"></canvas>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; font-size: 1.2rem;" class="text-body">
                            {{ number_format($opexRatio, 1) }}%
                        </div>
                    </div>
                </div>

                <div class="d-none d-md-block" style="width: 1px; height: 80px; background: var(--bs-border-color);"></div>

                <!-- Net Profit Margin -->
                <div class="text-center pt-3">
                    <p class="text-muted mb-2 fw-bold">Net Profit Margin</p>
                    <div style="position: relative; width: 120px; height: 120px; margin: 0 auto;">
                        <canvas id="gauge-net"></canvas>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; font-size: 1.2rem;" class="text-body">
                            {{ number_format($netProfitMargin, 1) }}%
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
            <!-- Chart 1: Revenue vs COGS -->
            <div class="col-lg-12">
                <div class="card bg-white p-3">
                    <div class="card-body">
                        <h6 class="card-title mb-4">Revenue vs COGS (Trend)</h6>
                        <div style="height: 300px;">
                            <canvas id="trend-chart-1"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chart 2: Profitability -->
            <div class="col-lg-12">
                 <div class="card bg-white p-3">
                    <div class="card-body">
                        <h6 class="card-title mb-4">Gross Profit vs Net Profit</h6>
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
                    $firstMonthRev = $trendData['revenue'][0] ?? 0;
                    $lastMonthRev = end($trendData['revenue']);
                    $diff = $lastMonthRev - $firstMonthRev;
                    $growthPercent = $firstMonthRev > 0 ? ($diff / $firstMonthRev) * 100 : ($lastMonthRev > 0 ? 100 : 0);
                    $trendText = $diff > 0 ? 'Growth' : ($diff < 0 ? 'Decline' : 'Stability');
                    $trendColor = $diff > 0 ? 'text-success' : ($diff < 0 ? 'text-danger' : 'text-warning');
                @endphp
                <p>
                    Between <span class="fw-semibold">{{ $trendData['labels'][0] ?? 'Start' }}</span> and <span class="fw-semibold">{{ end($trendData['labels']) }}</span>, 
                    Revenue has shown a trend of 
                    <span class="{{ $trendColor }}">{{ $trendText }}</span>
                    @if($diff != 0)
                        ({{ number_format(abs($growthPercent), 1) }}%)
                    @endif
                    .
                </p>
                <div class="border-top my-3"></div>
                
                <p>
                    <strong>OPEX Analysis:</strong><br>
                    Calculated average operating expense ratio is {{ number_format($opexRatio, 1) }}%. 
                    Keep monitoring variable costs to improve Net Profit Margins.
                </p>
                
                <div class="alert alert-soft-info mt-4">
                    <i class="mdi mdi-information-outline me-2"></i> 
                    Net Profit represents {{ number_format($netProfitMargin, 1) }}% of total revenue.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- BOTTOM ROW: ASSETS & LIABILITIES -->
<div class="row clearfix pb-5">
    <!-- Assets -->
    <div class="col-xl-6">
        <div class="card bg-white p-3">
            <div class="card-header border-bottom d-flex justify-content-between">
                <h5 class="card-title mb-0">Total Assets</h5>
                <h5 class="card-title mb-0">{{ number_format($totalAssets, 2) }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    @foreach($assetsBreakdown as $asset)
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ $asset->account->name }}</span>
                            <span>{{ number_format($asset->total, 0) }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            @php $percent = $totalAssets > 0 ? ($asset->total / $totalAssets) * 100 : 0; @endphp
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Liabilities -->
    <div class="col-xl-6">
        <div class="card bg-white p-3">
            <div class="card-header border-bottom d-flex justify-content-between">
                <h5 class="card-title mb-0">Total Liabilities</h5>
                <h5 class="card-title mb-0">{{ number_format($totalLiabilities, 2) }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    @foreach($liabilitiesBreakdown as $liab)
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ $liab->account->name }}</span>
                            <span>{{ number_format($liab->total, 0) }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            @php $percent = $totalLiabilities > 0 ? ($liab->total / $totalLiabilities) * 100 : 0; @endphp
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Detect theme via body attribute or simplistic assumption (defaulting to standard text colors)
        // Ideally we fetch CSS variables, but for compatibility we use standard muted colors
        const textColor = '#878a99'; 
        const gridColor = 'rgba(0, 0, 0, 0.05)';

        Chart.defaults.color = textColor;
        Chart.defaults.borderColor = gridColor;

        // 1. SPARKLINES
        const sparkConfig = (ctx, data, color) => {
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
                            // Proper gradient handling
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

        sparkConfig(document.getElementById('spark-revenue'), {!! json_encode($sparklineRevenue) !!}, 'rgb(41, 156, 219)'); // Blue
        sparkConfig(document.getElementById('spark-cogs'), {!! json_encode($sparklineCOGS) !!}, 'rgb(240, 101, 72)'); // Red
        sparkConfig(document.getElementById('spark-gross'), {!! json_encode($sparklineGrossProfit) !!}, 'rgb(247, 184, 75)'); // Orange
        sparkConfig(document.getElementById('spark-net'), {!! json_encode($sparklineNetProfit) !!}, 'rgb(10, 179, 156)'); // Green

        // 2. GAUGES (Doughnut)
        const gaugeConfig = (ctx, value, color) => {
            return new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Value', 'Remaining'],
                    datasets: [{
                        data: [value, 100-value],
                        backgroundColor: [color, 'rgba(135, 138, 153, 0.1)'], // Neutral track
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

        gaugeConfig(document.getElementById('gauge-gross'), {{ $grossProfitMargin }}, '#299cdb');
        gaugeConfig(document.getElementById('gauge-opex'), {{ $opexRatio }}, '#f06548');
        gaugeConfig(document.getElementById('gauge-net'), {{ $netProfitMargin }}, '#0ab39c');

        // 3. TREND CHARTS
        
        // Chart 1: Rev vs COGS
        new Chart(document.getElementById('trend-chart-1'), {
            type: 'line',
            data: {
                labels: {!! json_encode($trendData['labels']) !!},
                datasets: [
                    {
                        label: 'Revenue',
                        data: {!! json_encode($trendData['revenue']) !!},
                        borderColor: '#299cdb', // Blue
                        backgroundColor: 'rgba(41, 156, 219, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'COGS',
                        data: {!! json_encode($sparklineCOGS) !!}, // Placeholder Array
                        borderColor: '#f06548', // Red
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
                    y: { beginAtZero: true }
                }
            }
        });
        
        // Chart 2: Gross vs Net
        new Chart(document.getElementById('trend-chart-2'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($trendData['labels']) !!},
                datasets: [
                    {
                        label: 'Gross Profit',
                        data: {!! json_encode($sparklineGrossProfit) !!},
                        backgroundColor: '#299cdb',
                        barPercentage: 0.6
                    },
                    {
                        label: 'Net Profit',
                        data: {!! json_encode($sparklineNetProfit) !!},
                        backgroundColor: '#0ab39c',
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
                    y: { beginAtZero: true }
                }
            }
        });

    });
</script>
@endsection
