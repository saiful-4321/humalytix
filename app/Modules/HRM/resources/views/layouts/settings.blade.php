@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>@yield('title', 'Settings')</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                @yield('breadcrumb')
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-3">
        <div class="card bg-white">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @php
                        $route = Route::currentRouteName();
                        $section = '';

                        if (Str::startsWith($route, ['hrm.departments', 'hrm.branches', 'hrm.business-units', 'hrm.document-types', 'hrm.skills'])) {
                            $section = 'people';
                        } elseif (Str::startsWith($route, ['hrm.settings.leave-types', 'hrm.settings.approval-chains', 'hrm.settings.general', 'hrm.settings.holidays'])) {
                            $section = 'leave';
                        } elseif (Str::startsWith($route, ['hrm.settings.salary-components', 'hrm.settings.salary-structures', 'hrm.settings.tax-slabs'])) {
                            $section = 'payroll';
                        } elseif (Str::startsWith($route, ['hrm.settings.letter-templates'])) {
                            $section = 'recruitment';
                        } elseif (Str::startsWith($route, ['hrm.shifts'])) {
                            $section = 'attendance';
                        } elseif (Str::startsWith($route, ['hrm.settings.expenses'])) {
                            $section = 'expense';
                        } elseif (Str::startsWith($route, ['hrm.settings.assets'])) {
                            $section = 'asset';
                        }
                    @endphp

                    @if($section === 'people')
                        <div class="p-3 border-bottom bg-light">
                            <h6 class="font-size-13 text-uppercase text-muted mb-0">Organization Settings</h6>
                        </div>
                        <a href="{{ route('hrm.departments.index') }}" class="list-group-item list-group-item-action {{ Str::startsWith($route, 'hrm.departments') ? 'active' : '' }}">
                            <i class="bx bx-building-house me-2 align-middle font-size-16"></i> Departments
                        </a>
                        <a href="{{ route('hrm.branches.index') }}" class="list-group-item list-group-item-action {{ Str::startsWith($route, 'hrm.branches') ? 'active' : '' }}">
                            <i class="bx bx-store me-2 align-middle font-size-16"></i> Branches
                        </a>
                        
                        <a href="{{ route('hrm.business-units.index') }}" class="list-group-item list-group-item-action {{ Str::startsWith($route, 'hrm.business-units') ? 'active' : '' }}">
                            <i class="bx bx-briefcase-alt-2 me-2 align-middle font-size-16"></i> Business Units
                        </a>
                        <a href="{{ route('hrm.document-types.index') }}" class="list-group-item list-group-item-action {{ Str::startsWith($route, 'hrm.document-types') ? 'active' : '' }}">
                            <i class="bx bx-file me-2 align-middle font-size-16"></i> Document Types
                        </a>
                        <a href="{{ route('hrm.skills.index') }}" class="list-group-item list-group-item-action {{ Str::startsWith($route, 'hrm.skills') ? 'active' : '' }}">
                            <i class="bx bx-file me-2 align-middle font-size-16"></i> Skills
                        </a>
                    @elseif($section === 'leave')
                        <div class="p-3 border-bottom bg-light">
                            <h6 class="font-size-13 text-uppercase text-muted mb-0">Leave Configuration</h6>
                        </div>
                        <a href="{{ route('hrm.settings.leave-types.index') }}" class="list-group-item list-group-item-action {{ Str::startsWith($route, 'hrm.settings.leave-types') ? 'active' : '' }}">
                            <i class="bx bx-list-ul me-2 align-middle font-size-16"></i> Leave Policies
                        </a>
                        <a href="{{ route('hrm.settings.general') }}" class="list-group-item list-group-item-action {{ Str::startsWith($route, 'hrm.settings.general') ? 'active' : '' }}">
                            <i class="bx bx-calendar-x me-2 align-middle font-size-16"></i> Weekly Holidays
                        </a>
                        <a href="{{ route('hrm.settings.approval-chains.index') }}" class="list-group-item list-group-item-action {{ Str::startsWith($route, 'hrm.settings.approval-chains') ? 'active' : '' }}">
                            <i class="bx bx-git-pull-request me-2 align-middle font-size-16"></i> Approval Chains
                        </a>
                        <a href="{{ route('hrm.settings.holidays.index') }}" class="list-group-item list-group-item-action {{ Str::startsWith($route, 'hrm.settings.holidays') ? 'active' : '' }}">
                            <i class="bx bx-calendar-event me-2 align-middle font-size-16"></i> Holiday Calendar
                        </a>

                    @elseif($section === 'payroll')
                        <div class="p-3 border-bottom bg-light">
                            <h6 class="font-size-13 text-uppercase text-muted mb-0">Payroll Configuration</h6>
                        </div>
                        <a href="{{ route('hrm.settings.salary-components.index') }}" class="list-group-item list-group-item-action {{ Str::startsWith($route, 'hrm.settings.salary-components') ? 'active' : '' }}">
                            <i class="bx bx-layer me-2 align-middle font-size-16"></i> Salary Components
                        </a>
                        <a href="{{ route('hrm.settings.salary-structures.index') }}" class="list-group-item list-group-item-action {{ Str::startsWith($route, 'hrm.settings.salary-structures') ? 'active' : '' }}">
                            <i class="bx bx-sitemap me-2 align-middle font-size-16"></i> Salary Structures
                        </a>
                        <a href="{{ route('hrm.settings.tax-slabs.index') }}" class="list-group-item list-group-item-action {{ Str::startsWith($route, 'hrm.settings.tax-slabs') ? 'active' : '' }}">
                            <i class="bx bx-receipt me-2 align-middle font-size-16"></i> Tax Slabs
                        </a>

                    @elseif($section === 'recruitment')
                        <div class="p-3 border-bottom bg-light">
                            <h6 class="font-size-13 text-uppercase text-muted mb-0">Recruitment Settings</h6>
                        </div>
                        <a href="{{ route('hrm.settings.letter-templates.index') }}" class="list-group-item list-group-item-action {{ Str::startsWith($route, 'hrm.settings.letter-templates') ? 'active' : '' }}">
                            <i class="bx bx-file me-2 align-middle font-size-16"></i> Letter Templates
                        </a>

                    @elseif($section === 'attendance')
                        <div class="p-3 border-bottom bg-light">
                            <h6 class="font-size-13 text-uppercase text-muted mb-0">Attendance Settings</h6>
                        </div>
                        <a href="{{ route('hrm.shifts.index') }}" class="list-group-item list-group-item-action {{ Str::startsWith($route, 'hrm.shifts') ? 'active' : '' }}">
                            <i class="bx bx-time-five me-2 align-middle font-size-16"></i> Shift Setup
                        </a>
                    @elseif($section === 'expense')
                        <div class="p-3 border-bottom bg-light">
                            <h6 class="font-size-13 text-uppercase text-muted mb-0">Expense Configuration</h6>
                        </div>
                        <a href="{{ route('hrm.settings.expenses.index') }}" class="list-group-item list-group-item-action {{ Str::startsWith($route, 'hrm.settings.expenses') ? 'active' : '' }}">
                            <i class="bx bx-category me-2 align-middle font-size-16"></i> Expense Categories
                        </a>

                    @elseif($section === 'asset')
                        <div class="p-3 border-bottom bg-light">
                            <h6 class="font-size-13 text-uppercase text-muted mb-0">Asset Configuration</h6>
                        </div>
                        <a href="{{ route('hrm.settings.assets.index') }}" class="list-group-item list-group-item-action {{ Str::startsWith($route, 'hrm.settings.assets') ? 'active' : '' }}">
                            <i class="bx bx-category-alt me-2 align-middle font-size-16"></i> Asset Categories
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-9">
        @yield('settings-content')
    </div>
</div>
@endsection
