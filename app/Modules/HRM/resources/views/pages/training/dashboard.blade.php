@extends('Main::layouts.app')

@section('title', 'L&D Dashboard')

@section('content')
<div class="container-fluid">
    {{-- Block Header --}}
    <div class="block-header">
        <div class="row">
            <div class="col-lg-5 col-md-8 col-sm-12">
                <h2>Learning & Development Dashboard</h2>
            </div>
            <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                <ul class="breadcrumb justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                    <li class="breadcrumb-item active">L&D Dashboard</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-card">
                <div class="row align-items-center g-3">
                    <div class="col-md-6">
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <span class="filter-label">Period:</span>
                            <a href="{{ route('hrm.trainings.dashboard', ['preset' => 'this_week']) }}" 
                               class="filter-btn {{ $preset == 'this_week' ? 'active' : '' }}">This Week</a>
                            <a href="{{ route('hrm.trainings.dashboard', ['preset' => 'this_month']) }}" 
                               class="filter-btn {{ $preset == 'this_month' ? 'active' : '' }}">This Month</a>
                            <a href="{{ route('hrm.trainings.dashboard', ['preset' => 'this_quarter']) }}" 
                               class="filter-btn {{ $preset == 'this_quarter' ? 'active' : '' }}">This Quarter</a>
                            <a href="{{ route('hrm.trainings.dashboard', ['preset' => 'this_year']) }}" 
                               class="filter-btn {{ $preset == 'this_year' ? 'active' : '' }}">This Year</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <form action="{{ route('hrm.trainings.dashboard') }}" method="GET" class="d-flex justify-content-md-end align-items-center gap-2 flex-wrap">
                            <input type="hidden" name="preset" value="custom">
                            
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

    {{-- Modern Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="modern-stat-card gradient-purple">
                <div class="stat-icon">
                    <i class="bx bx-book-open"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $stats['total_trainings'] }}</h3>
                    <p class="stat-label">Training Programs</p>
                    <div class="stat-trend">
                        <i class="bx bx-trending-up"></i>
                        <span>Active programs</span>
                    </div>
                </div>
                <div class="stat-wave"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="modern-stat-card gradient-blue">
                <div class="stat-icon">
                    <i class="bx bx-calendar-event"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $stats['active_sessions'] }}</h3>
                    <p class="stat-label">Upcoming Sessions</p>
                    <div class="stat-trend">
                        <i class="bx bx-time"></i>
                        <span>Scheduled</span>
                    </div>
                </div>
                <div class="stat-wave"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="modern-stat-card gradient-green">
                <div class="stat-icon">
                    <i class="bx bx-user-check"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $stats['participants_trained'] }}</h3>
                    <p class="stat-label">Employees Trained</p>
                    <div class="stat-trend">
                        <i class="bx bx-trending-up"></i>
                        <span>This period</span>
                    </div>
                </div>
                <div class="stat-wave"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="modern-stat-card gradient-orange">
                <div class="stat-icon">
                    <i class="bx bx-certification"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $stats['certified_employees'] }}</h3>
                    <p class="stat-label">Certified Employees</p>
                    <div class="stat-trend">
                        <i class="bx bx-badge-check"></i>
                        <span>Certified</span>
                    </div>
                </div>
                <div class="stat-wave"></div>
            </div>
        </div>
    </div>

    {{-- Completion & Effectiveness Metrics --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card modern-card">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <p class="text-muted mb-3 fw-semibold">Completion Rate</p>
                            <div class="gauge-container">
                                <canvas id="gauge-completion"></canvas>
                                <div class="gauge-value">{{ $analytics['completion_rate'] }}%</div>
                            </div>
                            <p class="text-muted small mt-2">Overall completion</p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted mb-3 fw-semibold">Attendance Rate</p>
                            <div class="gauge-container">
                                <canvas id="gauge-attendance"></canvas>
                                <div class="gauge-value">{{ $analytics['attendance_rate'] }}%</div>
                            </div>
                            <p class="text-muted small mt-2">Session attendance</p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted mb-3 fw-semibold">Satisfaction Score</p>
                            <div class="gauge-container">
                                <canvas id="gauge-satisfaction"></canvas>
                                <div class="gauge-value">{{ $analytics['satisfaction_score'] }}</div>
                            </div>
                            <p class="text-muted small mt-2">Out of 5.0</p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted mb-3 fw-semibold">Skill Improvement</p>
                            <div class="gauge-container">
                                <canvas id="gauge-improvement"></canvas>
                                <div class="gauge-value">{{ $analytics['skill_improvement'] }}%</div>
                            </div>
                            <p class="text-muted small mt-2">Average growth</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Main Analytics --}}
        <div class="col-xl-8">
            {{-- Training Participation Trend --}}
            <div class="card modern-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Training Participation Trend</h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="participation-chart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Training by Category --}}
            <div class="card modern-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Training Distribution by Category</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div style="height: 300px;">
                                <canvas id="category-chart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="category-stats">
                                @foreach($categoryData['labels'] as $index => $label)
                                <div class="category-item">
                                    <div class="category-info">
                                        <span class="category-dot" style="background: {{ ['#667eea', '#2196f3', '#11998e', '#f2994a', '#ff6b6b'][$index] }};"></span>
                                        <span class="category-name">{{ $label }}</span>
                                    </div>
                                    <span class="category-value">{{ $categoryData['values'][$index] }}%</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Upcoming Sessions --}}
            <div class="card modern-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Upcoming Training Sessions</h5>
                        <p class="text-muted mb-0 small">Next scheduled training programs</p>
                    </div>
                    <a href="{{ route('hrm.trainings.index') }}" class="btn btn-sm btn-soft-primary">
                        View All <i class="bx bx-right-arrow-alt ms-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="training-sessions-list">
                        @forelse($upcomingSessions as $session)
                        <div class="session-item">
                            <div class="session-avatar">
                                <div class="avatar-circle gradient-{{ ['purple', 'blue', 'green', 'orange'][($loop->index % 4)] }}">
                                    {{ substr($session->training->title, 0, 1) }}
                                </div>
                            </div>
                            <div class="session-info">
                                <h6 class="session-title">{{ $session->training->title }}</h6>
                                <div class="session-meta">
                                    <span><i class="bx bx-calendar me-1"></i>{{ $session->start_date->format('d M Y') }}</span>
                                    <span><i class="bx bx-time me-1"></i>{{ $session->start_date->format('h:i A') }}</span>
                                </div>
                            </div>
                            <div class="session-status">
                                <span class="badge badge-soft-{{ $session->status === 'scheduled' ? 'warning' : 'success' }}">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="bx bx-calendar-x"></i>
                            <p>No upcoming sessions scheduled</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar: Insights & Top Performers --}}
        <div class="col-xl-4">
            {{-- Smart Insights --}}
            <div class="card modern-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bx bx-bulb me-2"></i>Smart Insights</h5>
                </div>
                <div class="card-body">
                    <div class="insight-item">
                        <i class="bx bx-trending-up insight-icon {{ $analytics['completion_rate'] >= 80 ? 'text-success' : 'text-warning' }}"></i>
                        <div>
                            <p class="mb-1"><strong>{{ $analytics['completion_rate'] }}% completion rate</strong> - {{ $analytics['completion_rate'] >= 80 ? 'Above' : 'Below' }} industry average</p>
                            <small class="text-muted">{{ $analytics['completion_rate'] >= 80 ? 'Employees are highly engaged' : 'Room for improvement' }}</small>
                        </div>
                    </div>
                    
                    <div class="insight-item">
                        <i class="bx bx-star insight-icon text-warning"></i>
                        <div>
                            <p class="mb-1">Average satisfaction: <strong>{{ $analytics['satisfaction_score'] }}/5.0</strong></p>
                            <small class="text-muted">{{ $analytics['satisfaction_score'] >= 4.0 ? 'Excellent' : 'Good' }} training quality</small>
                        </div>
                    </div>
                    
                    <div class="insight-item">
                        <i class="bx bx-time-five insight-icon text-primary"></i>
                        <div>
                            <p class="mb-1"><strong>{{ $stats['active_sessions'] }} sessions</strong> scheduled</p>
                            <small class="text-muted">{{ $stats['active_sessions'] > 10 ? 'Peak' : 'Normal' }} training period</small>
                        </div>
                    </div>

                    <div class="insight-item">
                        <i class="bx bx-badge-check insight-icon text-info"></i>
                        <div>
                            <p class="mb-1"><strong>{{ $stats['certified_employees'] }} employees</strong> earned certifications</p>
                            <small class="text-muted">{{ $stats['participants_trained'] > 0 ? round(($stats['certified_employees'] / $stats['participants_trained']) * 100) : 0 }}% certification rate</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Performers --}}
            <div class="card modern-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bx bx-trophy me-2"></i>Top Learners</h5>
                </div>
                <div class="card-body">
                    <div class="top-performers-list">
                        @forelse($topLearners as $index => $learner)
                        <div class="performer-item">
                            <div class="performer-rank rank-{{ min($index + 1, 3) }}">{{ $index + 1 }}</div>
                            <div class="performer-info">
                                <p class="performer-name">{{ $learner->employee->first_name }} {{ $learner->employee->last_name }}</p>
                                <div class="performer-stats">
                                    <span class="stat-badge"><i class="bx bx-book-open"></i> {{ $learner->training_count }} courses</span>
                                </div>
                            </div>
                            <div class="performer-score">
                                <div class="score-circle">{{ min(100, $learner->training_count * 10) }}</div>
                                <small class="text-muted">Score</small>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="bx bx-trophy"></i>
                            <p>No training data available</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Skill Matrix Preview --}}
            <div class="card modern-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Skill Matrix</h5>
                        <p class="text-muted mb-0 small">Proficiency overview</p>
                    </div>
                    <a href="{{ route('hrm.skills.matrix') }}" class="btn btn-sm btn-soft-primary">
                        Full Matrix <i class="bx bx-right-arrow-alt ms-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="skill-matrix-wrapper">
                        <div class="table-responsive">
                            <table class="skill-matrix-table">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        @foreach($topSkills as $skill)
                                            <th class="text-center">{{ Str::limit($skill->name, 10) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sampleEmployees as $emp)
                                    <tr>
                                        <td class="employee-name">{{ $emp->first_name }}</td>
                                        @foreach($topSkills as $skill)
                                            @php
                                                $empSkill = $emp->skills->firstWhere('id', $skill->id);
                                                $level = $empSkill->pivot->proficiency_level ?? null;
                                                $badgeClass = match($level) {
                                                    'beginner' => 'level-beginner',
                                                    'intermediate' => 'level-intermediate',
                                                    'advanced' => 'level-advanced',
                                                    'expert' => 'level-expert',
                                                    default => 'level-none',
                                                };
                                            @endphp
                                            <td class="text-center">
                                                <div class="skill-level {{ $badgeClass }}" title="{{ ucfirst($level ?? 'Not assessed') }}">
                                                    <span class="level-dot"></span>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="skill-legend">
                            <span class="legend-item">
                                <span class="legend-dot level-beginner"></span> Beginner
                            </span>
                            <span class="legend-item">
                                <span class="legend-dot level-intermediate"></span> Intermediate
                            </span>
                            <span class="legend-item">
                                <span class="legend-dot level-advanced"></span> Advanced
                            </span>
                            <span class="legend-item">
                                <span class="legend-dot level-expert"></span> Expert
                            </span>
                        </div>
                    </div>
                </div>
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
    padding: 30px;
    border-radius: 16px;
    overflow: hidden;
    color: white;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.modern-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.gradient-purple {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.gradient-blue {
    background: linear-gradient(135deg, #2196f3 0%, #00bcd4 100%);
}

.gradient-green {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.gradient-orange {
    background: linear-gradient(135deg, #ff6b6b 0%, #feca57 100%);
}

.stat-icon {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    backdrop-filter: blur(10px);
}

.stat-icon i {
    font-size: 30px;
    color: white;
}

.stat-content {
    position: relative;
    z-index: 2;
}

.stat-value {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 5px;
    color: white;
}

.stat-label {
    font-size: 14px;
    color: rgba(255,255,255,0.9);
    margin: 0 0 8px 0;
    font-weight: 500;
}

.stat-trend {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: rgba(255,255,255,0.8);
}

.stat-trend i {
    font-size: 14px;
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
    transition: box-shadow 0.3s ease;
}

body[data-layout-mode="dark"] .modern-card {
    background: #1a1d2e;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.modern-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
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
    font-size: 18px;
    font-weight: 700;
    color: #2d3748;
}

body[data-layout-mode="dark"] .modern-card .card-title {
    color: #e2e8f0;
}

.modern-card .card-body {
    padding: 24px;
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

/* Category Stats */
.category-stats {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding: 20px;
}

.category-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: rgba(0,0,0,0.02);
    border-radius: 8px;
}

body[data-layout-mode="dark"] .category-item {
    background: rgba(255,255,255,0.05);
}

.category-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.category-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.category-name {
    font-size: 14px;
    font-weight: 500;
    color: #2d3748;
}

body[data-layout-mode="dark"] .category-name {
    color: #e2e8f0;
}

.category-value {
    font-size: 16px;
    font-weight: 700;
    color: #667eea;
}

/* Training Sessions List */
.training-sessions-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.session-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: rgba(0,0,0,0.02);
    border-radius: 12px;
    transition: all 0.3s ease;
}

body[data-layout-mode="dark"] .session-item {
    background: rgba(255,255,255,0.05);
}

.session-item:hover {
    background: rgba(0,0,0,0.04);
    transform: translateX(5px);
}

body[data-layout-mode="dark"] .session-item:hover {
    background: rgba(255,255,255,0.08);
}

.session-avatar {
    flex-shrink: 0;
}

.avatar-circle {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
    color: white;
}

.session-info {
    flex: 1;
}

.session-title {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 4px;
    color: #2d3748;
}

body[data-layout-mode="dark"] .session-title {
    color: #e2e8f0;
}

.session-meta {
    display: flex;
    gap: 16px;
    font-size: 13px;
    color: #718096;
}

body[data-layout-mode="dark"] .session-meta {
    color: #a0aec0;
}

.session-meta span {
    display: flex;
    align-items: center;
}

.session-status {
    flex-shrink: 0;
}

.badge-soft-warning {
    background: rgba(242, 153, 74, 0.1);
    color: #f2994a;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.badge-soft-success {
    background: rgba(56, 239, 125, 0.1);
    color: #38ef7d;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #a0aec0;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 14px;
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

/* Top Performers */
.top-performers-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.performer-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: rgba(0,0,0,0.02);
    border-radius: 8px;
    transition: all 0.3s ease;
}

body[data-layout-mode="dark"] .performer-item {
    background: rgba(255,255,255,0.05);
}

.performer-item:hover {
    background: rgba(0,0,0,0.04);
    transform: translateX(5px);
}

body[data-layout-mode="dark"] .performer-item:hover {
    background: rgba(255,255,255,0.08);
}

.performer-rank {
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

.performer-info {
    flex: 1;
}

.performer-name {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 4px;
    color: #2d3748;
}

body[data-layout-mode="dark"] .performer-name {
    color: #e2e8f0;
}

.performer-stats {
    display: flex;
    gap: 8px;
}

.stat-badge {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    color: #718096;
    background: rgba(0,0,0,0.05);
    padding: 2px 8px;
    border-radius: 4px;
}

body[data-layout-mode="dark"] .stat-badge {
    background: rgba(255,255,255,0.1);
    color: #a0aec0;
}

.performer-score {
    text-align: center;
}

.score-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 4px;
}

/* Skill Matrix */
.skill-matrix-wrapper {
    overflow-x: auto;
}

.skill-matrix-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 8px;
}

.skill-matrix-table thead th {
    font-size: 12px;
    font-weight: 600;
    color: #718096;
    text-transform: uppercase;
    padding: 8px 12px;
    border-bottom: 2px solid rgba(0,0,0,0.05);
}

body[data-layout-mode="dark"] .skill-matrix-table thead th {
    color: #a0aec0;
    border-bottom-color: rgba(255,255,255,0.1);
}

.skill-matrix-table tbody tr {
    background: rgba(0,0,0,0.02);
    transition: background 0.2s ease;
}

body[data-layout-mode="dark"] .skill-matrix-table tbody tr {
    background: rgba(255,255,255,0.03);
}

.skill-matrix-table tbody tr:hover {
    background: rgba(0,0,0,0.04);
}

body[data-layout-mode="dark"] .skill-matrix-table tbody tr:hover {
    background: rgba(255,255,255,0.06);
}

.skill-matrix-table tbody td {
    padding: 12px;
}

.skill-matrix-table tbody td:first-child {
    border-radius: 8px 0 0 8px;
}

.skill-matrix-table tbody td:last-child {
    border-radius: 0 8px 8px 0;
}

.employee-name {
    font-weight: 600;
    color: #2d3748;
}

body[data-layout-mode="dark"] .employee-name {
    color: #e2e8f0;
}

.skill-level {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    transition: transform 0.2s ease;
}

.skill-level:hover {
    transform: scale(1.1);
}

.level-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.level-beginner .level-dot {
    background: #cbd5e0;
}

.level-intermediate .level-dot {
    background: #4299e1;
}

.level-advanced .level-dot {
    background: #667eea;
}

.level-expert .level-dot {
    background: #38ef7d;
}

.level-none .level-dot {
    background: transparent;
    border: 2px dashed #e2e8f0;
}

/* Skill Legend */
.skill-legend {
    display: flex;
    gap: 20px;
    justify-content: flex-end;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid rgba(0,0,0,0.05);
}

body[data-layout-mode="dark"] .skill-legend {
    border-top-color: rgba(255,255,255,0.1);
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #718096;
}

body[data-layout-mode="dark"] .legend-item {
    color: #a0aec0;
}

.legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
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
        padding: 24px;
    }
    
    .stat-value {
        font-size: 28px;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
    }
    
    .stat-icon i {
        font-size: 24px;
    }
    
    .session-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .skill-legend {
        flex-wrap: wrap;
        gap: 12px;
    }
    
    .category-stats {
        padding: 10px;
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

    // Gauges
    const gaugeConfig = (ctx, value, color, max = 100) => {
        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [value, max - value],
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

    gaugeConfig(document.getElementById('gauge-completion'), {{ $analytics['completion_rate'] }}, '#667eea');
    gaugeConfig(document.getElementById('gauge-attendance'), {{ $analytics['attendance_rate'] }}, '#2196f3');
    gaugeConfig(document.getElementById('gauge-satisfaction'), {{ $analytics['satisfaction_score'] * 20 }}, '#11998e', 100);
    gaugeConfig(document.getElementById('gauge-improvement'), {{ $analytics['skill_improvement'] }}, '#f2994a');

    // Participation Trend Chart
    new Chart(document.getElementById('participation-chart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($trendLabels) !!},
            datasets: [{
                label: 'Participants',
                data: {!! json_encode($participationTrend) !!},
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: gridColor } },
                x: { grid: { display: false } }
            }
        }
    });

    // Category Distribution Chart
    new Chart(document.getElementById('category-chart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categoryData['labels']) !!},
            datasets: [{
                data: {!! json_encode($categoryData['values']) !!},
                backgroundColor: ['#667eea', '#2196f3', '#11998e', '#f2994a', '#ff6b6b'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
@endsection
