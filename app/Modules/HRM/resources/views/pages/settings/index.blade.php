@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>HRM Settings</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Settings</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-12">
        <!-- General & Automation -->
        <div class="settings-section mb-5">
            <div class="section-header mb-4">
                <h5 class="section-title">General & Automation</h5>
                <div class="section-divider"></div>
            </div>
            <div class="row g-4">
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.settings.general') }}" target="_blank" class="modern-card gradient-primary">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-cog-outline"></i>
                            </div>
                        </div>
                        <h6 class="card-title">General Settings</h6>
                        <p class="card-description">Configure system preferences and core settings</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.settings.automation') }}" target="_blank" class="modern-card gradient-warning">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-robot"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Automation</h6>
                        <p class="card-description">Manage schedulers and automated actions</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.workflows.index') }}" target="_blank" class="modern-card gradient-info">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-sitemap"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Workflows</h6>
                        <p class="card-description">Design process automation flows</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Organization Structure -->
        <div class="settings-section mb-5">
            <div class="section-header mb-4">
                <h5 class="section-title">Organization Structure</h5>
                <div class="section-divider"></div>
            </div>
            <div class="row g-4">
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.departments.index') }}" target="_blank" class="modern-card gradient-purple">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-domain"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Departments</h6>
                        <p class="card-description">Manage organizational departments</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.branches.index') }}" target="_blank" class="modern-card gradient-purple">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-office-building-marker"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Branches</h6>
                        <p class="card-description">Configure office locations and branches</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.business-units.index') }}" target="_blank" class="modern-card gradient-purple">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-briefcase-outline"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Business Units</h6>
                        <p class="card-description">Manage strategic business units</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Payroll Configuration -->
        <div class="settings-section mb-5">
            <div class="section-header mb-4">
                <h5 class="section-title">Payroll Configuration</h5>
                <div class="section-divider"></div>
            </div>
            <div class="row g-4">
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.settings.salary-components.index') }}" target="_blank" class="modern-card gradient-success">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-cash-plus"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Salary Components</h6>
                        <p class="card-description">Configure earnings and deductions</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.settings.salary-structures.index') }}" target="_blank" class="modern-card gradient-success">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-file-tree"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Salary Structures</h6>
                        <p class="card-description">Define templates and salary grades</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.settings.tax-slabs.index') }}" target="_blank" class="modern-card gradient-success">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-percent"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Tax Slabs</h6>
                        <p class="card-description">Setup income tax rules and slabs</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Leave & Attendance -->
        <div class="settings-section mb-5">
            <div class="section-header mb-4">
                <h5 class="section-title">Leave & Attendance</h5>
                <div class="section-divider"></div>
            </div>
            <div class="row g-4">
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.settings.leave-types.index') }}" target="_blank" class="modern-card gradient-danger">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-calendar-clock"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Leave Policies</h6>
                        <p class="card-description">Configure leave types and rules</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.settings.approval-chains.index') }}" target="_blank" class="modern-card gradient-danger">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-account-multiple-check"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Approval Chains</h6>
                        <p class="card-description">Setup approval workflow chains</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.settings.holidays.index') }}" target="_blank" class="modern-card gradient-danger">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-calendar-star"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Holidays</h6>
                        <p class="card-description">Manage public and weekly holidays</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Documents & Assets -->
        <div class="settings-section mb-5">
            <div class="section-header mb-4">
                <h5 class="section-title">Documents & Assets</h5>
                <div class="section-divider"></div>
            </div>
            <div class="row g-4">
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.policies.index') }}" target="_blank" class="modern-card gradient-teal">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-shield-check"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Policies</h6>
                        <p class="card-description">Manage company policies</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.settings.letter-templates.index') }}" target="_blank" class="modern-card gradient-teal">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-text-box-multiple"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Letter Templates</h6>
                        <p class="card-description">Create digital document templates</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.document-types.index') }}" target="_blank" class="modern-card gradient-teal">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-file-cog"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Document Types</h6>
                        <p class="card-description">Define upload categories</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.settings.assets.index') }}" target="_blank" class="modern-card gradient-teal">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-laptop"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Asset Categories</h6>
                        <p class="card-description">Manage equipment types</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.settings.expenses.index') }}" target="_blank" class="modern-card gradient-teal">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-receipt"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Expense Categories</h6>
                        <p class="card-description">Setup reimbursement types</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Talent & Performance -->
        <div class="settings-section mb-5">
            <div class="section-header mb-4">
                <h5 class="section-title">Talent & Performance</h5>
                <div class="section-divider"></div>
            </div>
            <div class="row g-4">
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.skills.index') }}" target="_blank" class="modern-card gradient-orange">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-school"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Skills</h6>
                        <p class="card-description">Define competencies and qualifications</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.competencies.index') }}" target="_blank" class="modern-card gradient-orange">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-certificate"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Competencies</h6>
                        <p class="card-description">Manage core competencies</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <a href="{{ route('hrm.certifications.index') }}" target="_blank" class="modern-card gradient-orange">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i class="mdi mdi-seal"></i>
                            </div>
                        </div>
                        <h6 class="card-title">Certifications</h6>
                        <p class="card-description">Track professional certifications</p>
                        <div class="card-arrow">
                            <i class="mdi mdi-arrow-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Card Styles - Theme Aware */
.modern-card {
    position: relative;
    display: block;
    padding: 30px;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    text-decoration: none;
    border: 1px solid rgba(0,0,0,0.05);
}

/* Dark mode support */
body[data-layout-mode="dark"] .modern-card {
    background: #1a1d2e;
    border-color: rgba(255,255,255,0.1);
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.modern-card:hover::before {
    opacity: 1;
}

.modern-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.15);
    border-color: transparent;
}

body[data-layout-mode="dark"] .modern-card:hover {
    box-shadow: 0 12px 28px rgba(0,0,0,0.5);
}

.card-icon-wrapper {
    margin-bottom: 20px;
}

.card-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    transition: transform 0.3s ease;
}

.modern-card:hover .card-icon {
    transform: scale(1.1) rotate(5deg);
}

.card-icon i {
    font-size: 28px;
    color: white;
}

.card-title {
    font-size: 18px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 8px;
    transition: color 0.3s ease;
}

body[data-layout-mode="dark"] .card-title {
    color: #e2e8f0;
}

.modern-card:hover .card-title {
    color: var(--gradient-start);
}

.card-description {
    font-size: 14px;
    color: #718096;
    margin-bottom: 0;
    line-height: 1.6;
}

body[data-layout-mode="dark"] .card-description {
    color: #a0aec0;
}

.card-arrow {
    position: absolute;
    bottom: 20px;
    right: 20px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f7fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transform: translateX(-10px);
    transition: all 0.3s ease;
}

body[data-layout-mode="dark"] .card-arrow {
    background: #2d3748;
}

.modern-card:hover .card-arrow {
    opacity: 1;
    transform: translateX(0);
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
}

.card-arrow i {
    font-size: 18px;
    color: #4a5568;
}

body[data-layout-mode="dark"] .card-arrow i {
    color: #cbd5e0;
}

.modern-card:hover .card-arrow i {
    color: white;
}

/* Gradient Variations */
.gradient-primary {
    --gradient-start: #667eea;
    --gradient-end: #764ba2;
}

.gradient-success {
    --gradient-start: #11998e;
    --gradient-end: #38ef7d;
}

.gradient-danger {
    --gradient-start: #ee0979;
    --gradient-end: #ff6a00;
}

.gradient-warning {
    --gradient-start: #f2994a;
    --gradient-end: #f2c94c;
}

.gradient-info {
    --gradient-start: #2196f3;
    --gradient-end: #00bcd4;
}

.gradient-purple {
    --gradient-start: #8e2de2;
    --gradient-end: #4a00e0;
}

.gradient-teal {
    --gradient-start: #0ba360;
    --gradient-end: #3cba92;
}

.gradient-orange {
    --gradient-start: #ff6b6b;
    --gradient-end: #feca57;
}

/* Section Headers */
.section-header {
    position: relative;
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 12px;
    display: inline-block;
}

body[data-layout-mode="dark"] .section-title {
    color: #e2e8f0;
}

.section-divider {
    height: 3px;
    width: 60px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 2px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modern-card {
        padding: 24px;
    }
    
    .card-icon {
        width: 48px;
        height: 48px;
    }
    
    .card-icon i {
        font-size: 24px;
    }
    
    .card-title {
        font-size: 16px;
    }
    
    .card-arrow {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>

@endsection
