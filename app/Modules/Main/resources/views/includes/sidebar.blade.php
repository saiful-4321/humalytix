<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">

                <!-- <li class="text-center first-profile-n">
                    <a href="{{ route('dashboard.user.profile') }}">
                    <img class="img-thumbnail rounded-circle avatar-md" alt="200x200" src="{{ asset('assets/images/users/avatar-1.png') }}" >
                    <div class="mt-1">
                        <span class="mt-2 mb-1 color-black"><b data-key="t-dashboard">{{ auth()->user()->name ?? null }}</b></span><br>
                        <span data-key="t-email">
                            @foreach(auth()->user()->getRoleNames() as $role) 
                                <span class="badge bg-soft-primary text-primary">{{ $role ?? null }}</span>
                            @endforeach        
                        </span>
                    </div>
                  </a>
                </li>  -->

                <li>
                    <a href="{{ route('dashboard.home') }}">
                    <i data-feather="home"></i>
                    <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>

                {{-- Users --}}
                @canany(['user-list', 'user-create', 'user-update', 'user-reset-password', 'user-pretend-login', 'user-session'])
                <li>
                    <a href="#" class="has-arrow">
                        <i data-feather="grid"></i>
                        <span data-key="t-user">User Management</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @canany(['user-list', 'user-create', 'user-update', 'user-reset-password', 'user-pretend-login'])
                            <li><a href="{{ route("dashboard.user") }}" data-key="t-user-list">Users</a></li>
                        @endcanany 
                        @canany(['user-session'])
                        <li><a href="{{ route("dashboard.user.session") }}" data-key="t-register">User Session</a></li>
                        @endcanany
                    </ul>
                </li>
                @endcanany

                {{-- Role & Permissions --}}
                @canany(['module-list', 'module-create', 'module-update', 'role-list', 'role-create', 'role-update', 'permission-list', 'permission-create', 'permission-update'])
                <li>
                    <a href="#" class="has-arrow">
                        <i data-feather="user-check"></i>
                        <span data-key="t-role-permission">Role & Permission</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @canany(['module-list', 'module-create', 'module-update'])
                            <li><a href="{{ route("dashboard.role.module") }}" data-key="t-module">Modules</a></li>
                        @endcanany  
                        @canany(['permission-list', 'permission-create', 'permission-update'])
                            <li><a href="{{ route("dashboard.role.permission") }}" data-key="t-permission">Permissions</a></li>
                        @endcanany  
                        @canany(['role-list', 'role-create', 'role-update'])
                            <li><a href="{{ route("dashboard.role") }}" data-key="t-role">Roles</a></li>
                        @endcanany  
                    </ul>
                </li>
                @endcanany

                {{-- Employee Self Service --}}
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="user"></i>
                        <span data-key="t-ess">Self Service</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('hrm.ess.dashboard') }}" data-key="t-ess-dashboard">Dashboard</a></li>
                        <li><a href="{{ route('hrm.ess.profile') }}" data-key="t-my-profile">My Profile</a></li>
                        <li><a href="{{ route('hrm.ess.attendance') }}" data-key="t-my-attendance">Attendance & Reg.</a></li>
                        <li><a href="{{ route('hrm.leaves.my-leaves') }}" data-key="t-my-leaves">My Leaves</a></li>
                        <li><a href="{{ route('hrm.ess.payslips') }}" data-key="t-my-payslips">Payslips</a></li>
                        <li><a href="{{ route('hrm.ess.assets') }}" data-key="t-my-assets">My Assets</a></li>
                        <li><a href="{{ route('hrm.performance-goals.my-goals') }}" data-key="t-my-goals">Performance Goals</a></li>
                        <li><a href="{{ route('hrm.ess.holidays') }}" data-key="t-holidays">Holiday Calendar</a></li>
                        <li><a href="{{ route('hrm.ess.salary-certificate') }}" data-key="t-salary-cert">Salary Certificate</a></li>
                    </ul>
                </li>

                {{-- HRM System --}}
                @canany(['hrm.employees.view', 'hrm.departments.view', 'hrm.branches.view', 'hrm.attendance.view', 'hrm.leaves.view', 'hrm.jobs.view', 'hrm.payroll.view', 'hrm.appraisals.view'])
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="users"></i>
                        <span data-key="t-hrm">HRM System</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('hrm.dashboard') }}" data-key="t-hrm-dashboard">Dashboard</a></li>

                        {{-- People & Organization --}}
                        <li>
                            <a href="javascript: void(0);" class="has-arrow" data-key="t-organization">People & Org</a>
                            <ul class="sub-menu" aria-expanded="true">
                                @can('hrm.employees.view')
                                <li><a href="{{ route('hrm.employees.index') }}" data-key="t-employees">Employees</a></li>
                                @endcan
                                <li><a href="{{ route('hrm.assets.index') }}" data-key="t-assets">Assets</a></li>
                                <li><a href="{{ route('hrm.resignations.index') }}" data-key="t-resignations">Resignations</a></li>
                                <li><a href="{{ route('hrm.departments.index') }}" data-key="t-settings">Settings</a></li>
                            </ul>
                        </li>

                        {{-- Time & Attendance --}}
                        <li>
                            <a href="javascript: void(0);" class="has-arrow" data-key="t-attendance">Time & Attendance</a>
                            <ul class="sub-menu" aria-expanded="true">
                                @can('hrm.attendance.view')
                                <li><a href="{{ route('hrm.attendance.my-attendance') }}" data-key="t-my-attendance">My Attendance</a></li>
                                <li><a href="{{ route('hrm.attendance.index') }}" data-key="t-daily-log">Daily Logs</a></li>
                                @endcan
                                <li><a href="{{ route('hrm.rosters.index') }}" data-key="t-rosters">Weekly Roster</a></li>
                                <li><a href="{{ route('hrm.shifts.index') }}" data-key="t-settings">Settings</a></li>
                            </ul>
                        </li>

                        {{-- Leave Management --}}
                        @can('hrm.leaves.view')
                        <li>
                            <a href="javascript: void(0);" class="has-arrow" data-key="t-leaves">Leave Management</a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="{{ route('hrm.leaves.my-leaves') }}" data-key="t-my-leaves">My Leaves</a></li>
                                <li><a href="{{ route('hrm.leaves.index') }}" data-key="t-leave-requests">Leave Requests</a></li>
                                <li><a href="{{ route('hrm.leaves.allocations.index') }}" data-key="t-allocations">Allocations</a></li>
                                <li><a href="{{ route('hrm.settings.leave-types.index') }}" data-key="t-settings">Settings</a></li>
                            </ul>
                        </li>
                        @endcan

                        {{-- Payroll --}}
                        @can('hrm.payroll.view')
                        <li>
                            <a href="javascript: void(0);" class="has-arrow" data-key="t-payroll">Payroll</a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="{{ route('hrm.payroll.index') }}" data-key="t-payroll-main">Run Payroll</a></li>
                                <li><a href="{{ route('hrm.reports.salary-register') }}" data-key="t-salary-register">Salary Register</a></li>
                                <li><a href="{{ route('hrm.loans.index') }}" data-key="t-loans">Loans</a></li>
                                <li><a href="{{ route('hrm.advances.index') }}" data-key="t-advances">Advances</a></li>
                                <li><a href="{{ route('hrm.overtime.index') }}" data-key="t-overtime">Overtime</a></li>
                                <li><a href="{{ route('hrm.bonuses.index') }}" data-key="t-bonuses">Bonuses</a></li>
                                <li><a href="{{ route('hrm.bank-transfers.index') }}" data-key="t-bank-transfers">Bank Transfer</a></li>
                                <li><a href="{{ route('hrm.gratuity.calculator') }}" data-key="t-gratuity">Gratuity</a></li>
                                <li><a href="{{ route('hrm.settings.salary-components.index') }}" data-key="t-settings">Settings</a></li>
                            </ul>
                        </li>
                        @endcan

                        {{-- Performance --}}
                        {{-- Performance Management --}}
                        @can('hrm.performance.view')
                        <li>
                            <a href="javascript: void(0);" class="has-arrow" data-key="t-performance">Performance</a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="{{ route('hrm.kpis.index') }}" data-key="t-kpis">KPIs / KRAs</a></li>
                                <li><a href="{{ route('hrm.okrs.index') }}" data-key="t-okrs">OKRs</a></li>
                                <li><a href="{{ route('hrm.performance-goals.index') }}" data-key="t-goals">Goals</a></li>
                                <li><a href="{{ route('hrm.appraisals-360.index') }}" data-key="t-appraisals-360">360° Appraisals</a></li>
                                <li><a href="{{ route('hrm.competencies.index') }}" data-key="t-competencies">Competencies</a></li>
                                <li><a href="{{ route('hrm.pips.index') }}" data-key="t-pips">PIPs</a></li>
                            </ul>
                        </li>
                        @endcan

                        {{-- Recruitment --}}
                        @can('hrm.jobs.view')
                        <li>
                            <a href="javascript: void(0);" class="has-arrow" data-key="t-recruitment">Recruitment</a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="{{ route('hrm.jobs.index') }}" data-key="t-jobs">Jobs</a></li>
                                <li><a href="{{ route('hrm.candidates.index') }}" data-key="t-candidates">Candidates</a></li>
                                <li><a href="{{ route('hrm.letters.index') }}" data-key="t-letters">Letters</a></li>
                                <li><a href="{{ route('hrm.settings.letter-templates.index') }}" data-key="t-settings">Settings</a></li>
                            </ul>
                        </li>
                        @endcan

                        {{-- Settings --}}
                        <li>
                            <a href="{{ route('hrm.settings.index') }}" data-key="t-hrm-settings">General Settings</a>
                        </li>
                    </ul>
                </li>
                @endcanany

                {{-- Logs --}}
                @canany(['log-viewer', 'activity-log'])
                <li>
                    <a href="#" class="has-arrow">
                        <i class="mdi mdi-filter-outline"></i>
                        <span data-key="t-logs">Logs</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can("activity-log")
                        <li><a href="{{ route('dashboard.log.activity-log') }}" data-key="t-activity-log">Activity Log</a></li>
                        @endcan
                        @can("log-viewer")
                        <li><a href="{{ url('log-viewer') }}" target="_blank" data-key="t-log-viewer">Log Viewer</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                {{-- System Info --}}
                <li>
                    <a href="#" class="has-arrow">
                        <i class="mdi mdi-server-network"></i>
                        <span data-key="t-system-info">System Info</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('backup-management')
                        <li><a href="{{ route('dashboard.system-health') }}" data-key="t-system-health">Health Status</a></li>
                        @endcan
                        <li><a href="{{ url('docs/api') }}" target="_blank" data-key="t-api-docs">API Documentation</a></li>
                    </ul>
                </li>

                {{-- Backup --}}
                @can('backup-management')
                <li>
                    <a href="#" class="has-arrow">
                        <i data-feather="database"></i>
                        <span data-key="t-backup">Backup</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('dashboard.backup') }}" data-key="t-backup-management">Backup Management</a></li>
                        <li><a href="{{ route('dashboard.backup.schedule') }}" data-key="t-backup-schedule">Schedule Settings</a></li>
                    </ul>
                </li>
                @endcan

                {{-- Settings --}}
                @canany(['settings-list', 'company-settings-update', 'theme-settings-update', 'cache-management'])
                <li>
                    <a href="#" class="has-arrow">
                        <i data-feather="settings"></i>
                        <span data-key="t-settings">Settings</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can("company-settings-update")
                        <li><a href="{{ route('dashboard.settings.company.index') }}" data-key="t-company-settings">Company Settings</a></li>
                        @endcan
                        @can("theme-settings-update")
                        <li><a href="{{ route('dashboard.settings.theme.index') }}" data-key="t-theme-settings">Theme Settings</a></li>
                        @endcan
                        @can("cache-management")
                        <li><a href="{{ route('dashboard.cache') }}" data-key="t-cache-management">Cache Management</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany



                {{-- Logout --}}
                <li>
                    <a href="{{ route('auth.logout') }}">
                        <i class="bx bx-log-out-circle"></i>
                        <span data-key="t-logout">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>