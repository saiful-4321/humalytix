@extends('Main::layouts.app')

@section('content')

{{-- Modern Filter Bar --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="filter-card">
            <div class="row align-items-center g-3">
                <div class="col-md-6">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="filter-label">Period:</span>
                        <a href="{{ route('finance.dashboard', ['preset' => 'this_week', 'interval' => 'day']) }}" 
                           class="filter-btn {{ $preset == 'this_week' ? 'active' : '' }}">This Week</a>
                        <a href="{{ route('finance.dashboard', ['preset' => 'this_month', 'interval' => 'week']) }}" 
                           class="filter-btn {{ $preset == 'this_month' ? 'active' : '' }}">This Month</a>
                        <a href="{{ route('finance.dashboard', ['preset' => 'this_quarter', 'interval' => 'month']) }}" 
                           class="filter-btn {{ $preset == 'this_quarter' ? 'active' : '' }}">This Quarter</a>
                        <a href="{{ route('finance.dashboard', ['preset' => 'this_year', 'interval' => 'month']) }}" 
                           class="filter-btn {{ $preset == 'this_year' ? 'active' : '' }}">This Year</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('finance.dashboard') }}" method="GET" class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap">
                        <input type="hidden" name="preset" value="custom">
                        
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="submit" name="interval" value="day" class="btn {{ $interval == 'day' ? 'btn-primary' : 'btn-outline-primary' }}">Day</button>
                            <button type="submit" name="interval" value="week" class="btn {{ $interval == 'week' ? 'btn-primary' : 'btn-outline-primary' }}">Week</button>
                            <button type="submit" name="interval" value="month" class="btn {{ $interval == 'month' ? 'btn-primary' : 'btn-outline-primary' }}">Month</button>
                            <button type="submit" name="interval" value="quarter" class="btn {{ $interval == 'quarter' ? 'btn-primary' : 'btn-outline-primary' }}">Quarter</button>
                        </div>

                        <div class="date-range-picker">
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                            <span class="range-separator">to</span>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="mdi mdi-check"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modern KPI Cards --}}
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="modern-stat-card gradient-blue">
            <div class="stat-icon">
                <i class="bx bx-trending-up"></i>
            </div>
            <div class="stat-content">
                <p class="stat-label">Total Revenue</p>
                <h3 class="stat-value">{{ number_format($totalRevenue, 2) }}</h3>
                <div class="stat-sparkline">
                    <canvas id="spark-revenue" height="40"></canvas>
                </div>
            </div>
            <div class="stat-wave"></div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="modern-stat-card gradient-red">
            <div class="stat-icon">
                <i class="bx bx-trending-down"></i>
            </div>
            <div class="stat-content">
                <p class="stat-label">Cost of Goods Sold</p>
                <h3 class="stat-value">{{ number_format($totalCOGS, 2) }}</h3>
                <div class="stat-sparkline">
                    <canvas id="spark-cogs" height="40"></canvas>
                </div>
            </div>
            <div class="stat-wave"></div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="modern-stat-card gradient-orange">
            <div class="stat-icon">
                <i class="bx bx-line-chart"></i>
            </div>
            <div class="stat-content">
                <p class="stat-label">Gross Profit</p>
                <h3 class="stat-value">{{ number_format($grossProfit, 2) }}</h3>
                <div class="stat-sparkline">
                    <canvas id="spark-gross" height="40"></canvas>
                </div>
            </div>
            <div class="stat-wave"></div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="modern-stat-card gradient-green">
            <div class="stat-icon">
                <i class="bx bx-dollar-circle"></i>
            </div>
            <div class="stat-content">
                <p class="stat-label">Net Profit</p>
                <h3 class="stat-value">{{ number_format($netProfit, 2) }}</h3>
                <div class="stat-sparkline">
                    <canvas id="spark-net" height="40"></canvas>
                </div>
            </div>
            <div class="stat-wave"></div>
        </div>
    </div>
</div>

{{-- Margin Gauges --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card modern-card">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <p class="text-muted mb-3 fw-semibold">Gross Profit Margin</p>
                        <div class="gauge-container">
                            <canvas id="gauge-gross"></canvas>
                            <div class="gauge-value">{{ number_format($grossProfitMargin, 1) }}%</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-3 fw-semibold">Operating Expense Ratio</p>
                        <div class="gauge-container">
                            <canvas id="gauge-opex"></canvas>
                            <div class="gauge-value">{{ number_format($opexRatio, 1) }}%</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-3 fw-semibold">Net Profit Margin</p>
                        <div class="gauge-container">
                            <canvas id="gauge-net"></canvas>
                            <div class="gauge-value">{{ number_format($netProfitMargin, 1) }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts and Top Performers --}}
<div class="row g-4">
    {{-- Main Charts --}}
    <div class="col-xl-8">
        <div class="card modern-card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Revenue vs COGS Trend</h5>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="trend-chart-1"></canvas>
                </div>
            </div>
        </div>
        
        <div class="card modern-card">
            <div class="card-header">
                <h5 class="card-title mb-0">Profitability Analysis</h5>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="trend-chart-2"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Top Performers Sidebar --}}
    <div class="col-xl-4">
        {{-- Smart Insights --}}
        <div class="card modern-card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bx bx-bulb me-2"></i>Smart Insights</h5>
            </div>
            <div class="card-body">
                @php
                    $firstMonthRev = $trendData['revenue'][0] ?? 0;
                    $lastMonthRev = end($trendData['revenue']);
                    $diff = $lastMonthRev - $firstMonthRev;
                    $growthPercent = $firstMonthRev > 0 ? ($diff / $firstMonthRev) * 100 : ($lastMonthRev > 0 ? 100 : 0);
                    $trendText = $diff > 0 ? 'Growth' : ($diff < 0 ? 'Decline' : 'Stability');
                    $trendColor = $diff > 0 ? 'text-success' : ($diff < 0 ? 'text-danger' : 'text-warning');
                @endphp
                
                <div class="insight-item">
                    <i class="bx bx-trending-up insight-icon text-primary"></i>
                    <div>
                        <p class="mb-1">Revenue has shown <strong class="{{ $trendColor }}">{{ $trendText }}</strong> 
                        @if($diff != 0) ({{ number_format(abs($growthPercent), 1) }}%) @endif</p>
                        <small class="text-muted">From {{ $trendData['labels'][0] ?? 'Start' }} to {{ end($trendData['labels']) }}</small>
                    </div>
                </div>
                
                <div class="insight-item">
                    <i class="bx bx-pie-chart-alt insight-icon text-warning"></i>
                    <div>
                        <p class="mb-1">Operating expense ratio: <strong>{{ number_format($opexRatio, 1) }}%</strong></p>
                        <small class="text-muted">Monitor variable costs to improve margins</small>
                    </div>
                </div>
                
                <div class="insight-item">
                    <i class="bx bx-check-circle insight-icon text-success"></i>
                    <div>
                        <p class="mb-1">Net profit represents <strong>{{ number_format($netProfitMargin, 1) }}%</strong> of revenue</p>
                        <small class="text-muted">{{ $netProfitMargin > 15 ? 'Excellent performance!' : 'Room for improvement' }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Top Revenue Accounts --}}
        <div class="card modern-card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bx bx-trophy me-2"></i>Top Accounts</h5>
            </div>
            <div class="card-body">
                <h6 class="text-muted mb-3">Highest Revenue</h6>
                <div class="top-accounts-list">
                    @foreach($assetsBreakdown->take(3) as $index => $asset)
                    <div class="top-account-item">
                        <div class="account-rank rank-{{ $index + 1 }}">{{ $index + 1 }}</div>
                        <div class="account-info">
                            <p class="account-name">{{ Str::limit($asset->account->name, 20) }}</p>
                            <div class="account-progress">
                                @php $percent = $totalAssets > 0 ? ($asset->total / $totalAssets) * 100 : 0; @endphp
                                <div class="progress-bar" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                        <div class="account-value">{{ number_format($asset->total, 0) }}</div>
                    </div>
                    @endforeach
                </div>
                
                <h6 class="text-muted mb-3 mt-4">Highest Liabilities</h6>
                <div class="top-accounts-list">
                    @foreach($liabilitiesBreakdown->take(3) as $index => $liab)
                    <div class="top-account-item">
                        <div class="account-rank rank-danger">{{ $index + 1 }}</div>
                        <div class="account-info">
                            <p class="account-name">{{ Str::limit($liab->account->name, 20) }}</p>
                            <div class="account-progress danger">
                                @php $percent = $totalLiabilities > 0 ? ($liab->total / $totalLiabilities) * 100 : 0; @endphp
                                <div class="progress-bar" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                        <div class="account-value text-danger">{{ number_format($liab->total, 0) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Assets & Liabilities Breakdown --}}
<div class="row g-4 mt-2">
    <div class="col-xl-6">
        <div class="card modern-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Total Assets</h5>
                <h5 class="card-title mb-0 text-primary">{{ number_format($totalAssets, 2) }}</h5>
            </div>
            <div class="card-body">
                @foreach($assetsBreakdown as $asset)
                <div class="breakdown-item">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="breakdown-label">{{ $asset->account->name }}</span>
                        <span class="breakdown-value">{{ number_format($asset->total, 0) }}</span>
                    </div>
                    <div class="breakdown-progress">
                        @php $percent = $totalAssets > 0 ? ($asset->total / $totalAssets) * 100 : 0; @endphp
                        <div class="breakdown-bar bg-primary" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="col-xl-6">
        <div class="card modern-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Total Liabilities</h5>
                <h5 class="card-title mb-0 text-danger">{{ number_format($totalLiabilities, 2) }}</h5>
            </div>
            <div class="card-body">
                @foreach($liabilitiesBreakdown as $liab)
                <div class="breakdown-item">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="breakdown-label">{{ $liab->account->name }}</span>
                        <span class="breakdown-value">{{ number_format($liab->total, 0) }}</span>
                    </div>
                    <div class="breakdown-progress">
                        @php $percent = $totalLiabilities > 0 ? ($liab->total / $totalLiabilities) * 100 : 0; @endphp
                        <div class="breakdown-bar bg-danger" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
/* Filter Card */
.filter-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

body[data-layout-mode="dark"] .filter-card {
    background: #1a1d2e;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.filter-label {
    font-weight: 600;
    color: #718096;
    font-size: 14px;
}

.filter-btn {
    padding: 8px 16px;
    border-radius: 8px;
    background: rgba(0,0,0,0.03);
    color: #2d3748;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

body[data-layout-mode="dark"] .filter-btn {
    background: rgba(255,255,255,0.05);
    color: #e2e8f0;
}

.filter-btn:hover {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    border-color: #667eea;
}

.filter-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.date-range-picker {
    display: flex;
    align-items: center;
    gap: 8px;
}

.range-separator {
    color: #718096;
    font-size: 14px;
}

/* Modern Stat Cards */
.modern-stat-card {
    position: relative;
    padding: 24px;
    border-radius: 16px;
    overflow: hidden;
    color: white;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    min-height: 180px;
}

.modern-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.gradient-blue {
    background: linear-gradient(135deg, #2196f3 0%, #00bcd4 100%);
}

.gradient-red {
    background: linear-gradient(135deg, #f06548 0%, #ff6b6b 100%);
}

.gradient-orange {
    background: linear-gradient(135deg, #f2994a 0%, #feca57 100%);
}

.gradient-green {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.stat-icon {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    backdrop-filter: blur(10px);
}

.stat-icon i {
    font-size: 28px;
    color: white;
}

.stat-content {
    position: relative;
    z-index: 2;
}

.stat-label {
    font-size: 13px;
    color: rgba(255,255,255,0.9);
    margin-bottom: 8px;
    font-weight: 500;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 12px;
    color: white;
}

.stat-sparkline {
    margin-top: 12px;
}

.stat-wave {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 40px;
    background: rgba(255,255,255,0.1);
    clip-path: polygon(0 50%, 10% 40%, 20% 50%, 30% 40%, 40% 50%, 50% 40%, 60% 50%, 70% 40%, 80% 50%, 90% 40%, 100% 50%, 100% 100%, 0 100%);
}

/* Modern Card */
.modern-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

body[data-layout-mode="dark"] .modern-card {
    background: #1a1d2e;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.modern-card .card-header {
    background: transparent;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    padding: 20px 24px;
}

body[data-layout-mode="dark"] .modern-card .card-header {
    border-bottom-color: rgba(255,255,255,0.1);
}

.modern-card .card-title {
    font-size: 16px;
    font-weight: 700;
    color: #2d3748;
}

body[data-layout-mode="dark"] .modern-card .card-title {
    color: #e2e8f0;
}

/* Gauge Container */
.gauge-container {
    position: relative;
    width: 140px;
    height: 140px;
    margin: 0 auto;
}

.gauge-value {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-weight: 700;
    font-size: 24px;
    color: #2d3748;
}

body[data-layout-mode="dark"] .gauge-value {
    color: #e2e8f0;
}

/* Insights */
.insight-item {
    display: flex;
    gap: 12px;
    padding: 12px;
    background: rgba(0,0,0,0.02);
    border-radius: 8px;
    margin-bottom: 12px;
}

body[data-layout-mode="dark"] .insight-item {
    background: rgba(255,255,255,0.05);
}

.insight-icon {
    font-size: 24px;
    flex-shrink: 0;
}

.insight-item p {
    margin: 0;
    font-size: 14px;
    color: #2d3748;
}

body[data-layout-mode="dark"] .insight-item p {
    color: #e2e8f0;
}

/* Top Accounts */
.top-accounts-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.top-account-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: rgba(0,0,0,0.02);
    border-radius: 8px;
    transition: all 0.3s ease;
}

body[data-layout-mode="dark"] .top-account-item {
    background: rgba(255,255,255,0.05);
}

.top-account-item:hover {
    background: rgba(0,0,0,0.04);
    transform: translateX(5px);
}

body[data-layout-mode="dark"] .top-account-item:hover {
    background: rgba(255,255,255,0.08);
}

.account-rank {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    flex-shrink: 0;
}

.rank-1 {
    background: linear-gradient(135deg, #ffd700, #ffed4e);
    color: #000;
}

.rank-2 {
    background: linear-gradient(135deg, #c0c0c0, #e8e8e8);
    color: #000;
}

.rank-3 {
    background: linear-gradient(135deg, #cd7f32, #e8a87c);
    color: #fff;
}

.rank-danger {
    background: linear-gradient(135deg, #f06548, #ff6b6b);
    color: #fff;
}

.account-info {
    flex: 1;
}

.account-name {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 4px;
    color: #2d3748;
}

body[data-layout-mode="dark"] .account-name {
    color: #e2e8f0;
}

.account-progress {
    height: 4px;
    background: rgba(0,0,0,0.1);
    border-radius: 2px;
    overflow: hidden;
}

body[data-layout-mode="dark"] .account-progress {
    background: rgba(255,255,255,0.1);
}

.account-progress .progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 2px;
}

.account-progress.danger .progress-bar {
    background: linear-gradient(90deg, #f06548, #ff6b6b);
}

.account-value {
    font-size: 14px;
    font-weight: 700;
    color: #2d3748;
}

body[data-layout-mode="dark"] .account-value {
    color: #e2e8f0;
}

/* Breakdown Items */
.breakdown-item {
    margin-bottom: 20px;
}

.breakdown-label {
    font-size: 14px;
    font-weight: 500;
    color: #2d3748;
}

body[data-layout-mode="dark"] .breakdown-label {
    color: #e2e8f0;
}

.breakdown-value {
    font-size: 14px;
    font-weight: 700;
    color: #718096;
}

body[data-layout-mode="dark"] .breakdown-value {
    color: #a0aec0;
}

.breakdown-progress {
    height: 8px;
    background: rgba(0,0,0,0.05);
    border-radius: 4px;
    overflow: hidden;
}

body[data-layout-mode="dark"] .breakdown-progress {
    background: rgba(255,255,255,0.1);
}

.breakdown-bar {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .filter-card {
        padding: 16px;
    }
    
    .date-range-picker {
        flex-direction: column;
        width: 100%;
    }
    
    .modern-stat-card {
        min-height: 160px;
    }
    
    .stat-value {
        font-size: 24px;
    }
}
</style>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textColor = '#878a99';
        const gridColor = 'rgba(0, 0, 0, 0.05)';

        Chart.defaults.color = textColor;
        Chart.defaults.borderColor = gridColor;

        // Sparklines
        const sparkConfig = (ctx, data, color) => {
            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($trendData['labels']) !!},
                    datasets: [{
                        data: data,
                        borderColor: 'rgba(255,255,255,0.8)',
                        borderWidth: 2,
                        fill: true,
                        backgroundColor: 'rgba(255,255,255,0.2)',
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

        sparkConfig(document.getElementById('spark-revenue'), {!! json_encode($sparklineRevenue) !!}, '#2196f3');
        sparkConfig(document.getElementById('spark-cogs'), {!! json_encode($sparklineCOGS) !!}, '#f06548');
        sparkConfig(document.getElementById('spark-gross'), {!! json_encode($sparklineGrossProfit) !!}, '#f2994a');
        sparkConfig(document.getElementById('spark-net'), {!! json_encode($sparklineNetProfit) !!}, '#11998e');

        // Gauges
        const gaugeConfig = (ctx, value, color) => {
            return new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [value, 100-value],
                        backgroundColor: [color, 'rgba(135, 138, 153, 0.1)'],
                        borderWidth: 0,
                        cutout: '75%',
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

        // Trend Charts
        new Chart(document.getElementById('trend-chart-1'), {
            type: 'line',
            data: {
                labels: {!! json_encode($trendData['labels']) !!},
                datasets: [
                    {
                        label: 'Revenue',
                        data: {!! json_encode($trendData['revenue']) !!},
                        borderColor: '#2196f3',
                        backgroundColor: 'rgba(33, 150, 243, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3
                    },
                    {
                        label: 'COGS',
                        data: {!! json_encode($sparklineCOGS) !!},
                        borderColor: '#f06548',
                        borderDash: [5, 5],
                        tension: 0.4,
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        align: 'end',
                        labels: { usePointStyle: true, padding: 15 }
                    } 
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: gridColor } },
                    x: { grid: { display: false } }
                }
            }
        });
        
        new Chart(document.getElementById('trend-chart-2'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($trendData['labels']) !!},
                datasets: [
                    {
                        label: 'Gross Profit',
                        data: {!! json_encode($sparklineGrossProfit) !!},
                        backgroundColor: '#299cdb',
                        borderRadius: 8,
                        barPercentage: 0.6
                    },
                    {
                        label: 'Net Profit',
                        data: {!! json_encode($sparklineNetProfit) !!},
                        backgroundColor: '#0ab39c',
                        borderRadius: 8,
                        barPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        align: 'end',
                        labels: { usePointStyle: true, padding: 15 }
                    } 
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: gridColor } }
                }
            }
        });
    });
</script>
@endsection
