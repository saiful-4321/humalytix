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

                {{-- HRM System --}}
                @canany(['hrm.employees.view', 'hrm.departments.view', 'hrm.branches.view', 'hrm.attendance.view', 'hrm.leaves.view', 'hrm.jobs.view', 'hrm.payroll.view', 'hrm.appraisals.view'])
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="users"></i>
                        <span data-key="t-hrm">HRM System</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('hrm.dashboard') }}" data-key="t-hrm-dashboard">Dashboard</a></li>

                        {{-- Organization --}}
                        <li>
                            <a href="javascript: void(0);" class="has-arrow" data-key="t-organization">Organization</a>
                            <ul class="sub-menu" aria-expanded="true">
                                @can('hrm.departments.view')
                                <li><a href="{{ route('hrm.departments.index') }}" data-key="t-departments">Departments</a></li>
                                @endcan
                                @can('hrm.branches.view')
                                <li><a href="{{ route('hrm.branches.index') }}" data-key="t-branches">Branches</a></li>
                                @endcan
                            </ul>
                        </li>

                        {{-- Recruitment --}}
                        @can('hrm.jobs.view')
                        <li>
                            <a href="javascript: void(0);" class="has-arrow" data-key="t-recruitment">Recruitment</a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="{{ route('hrm.jobs.index') }}" data-key="t-jobs">Job Postings</a></li>
                                <li><a href="{{ route('hrm.candidates.index') }}" data-key="t-candidates">Candidates</a></li>
                                <li><a href="{{ route('hrm.letters.index') }}" data-key="t-letters">Digital Letters</a></li>
                            </ul>
                        </li>
                        @endcan

                        {{-- Human Resource --}}
                        <li>
                            <a href="javascript: void(0);" class="has-arrow" data-key="t-hr">Human Resource</a>
                            <ul class="sub-menu" aria-expanded="true">
                                @can('hrm.employees.view')
                                <li><a href="{{ route('hrm.employees.index') }}" data-key="t-employees">Employees</a></li>
                                @endcan
                                <li><a href="{{ route('hrm.assets.index') }}" data-key="t-assets">Asset Management</a></li>
                                <li><a href="{{ route('hrm.resignations.index') }}" data-key="t-resignations">Resignations</a></li>
                            </ul>
                        </li>

                        {{-- Time & Leave --}}
                        <li>
                        {{-- Attendance & Shifts --}}
                        <li>
                            <a href="javascript: void(0);" class="has-arrow" data-key="t-attendance-shifts">
                                <i data-feather="clock"></i>
                                <span data-key="t-attendance">Attendance</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="true">
                                @can('hrm.attendance.view')
                                <li><a href="{{ route('hrm.attendance.index') }}" data-key="t-daily-log">Daily Log</a></li>
                                <li><a href="{{ route('hrm.attendance.my-attendance') }}" data-key="t-my-attendance">My Attendance</a></li>
                                @endcan
                                <li><a href="{{ route('hrm.shifts.index') }}" data-key="t-shifts">Shifts & Rosters</a></li>
                            </ul>
                        </li>

                        {{-- Leave Management --}}
                        @can('hrm.leaves.view')
                        <li>
                            <a href="javascript: void(0);" class="has-arrow" data-key="t-leave-management">
                                <i data-feather="calendar"></i>
                                <span data-key="t-leaves">Leave Management</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="{{ route('hrm.leaves.index') }}" data-key="t-leave-requests">Leave Requests</a></li>
                                <li><a href="{{ route('hrm.leaves.my-leaves') }}" data-key="t-my-leaves">My Leaves</a></li>
                                <li><a href="{{ route('hrm.leaves.allocations.index') }}" data-key="t-allocations">Allocations</a></li>
                                <hr class="my-1 border-light">
                                <li><a href="{{ route('hrm.settings.leave-types.index') }}" data-key="t-policies">Leave Policies</a></li>
                                <li><a href="{{ route('hrm.settings.approval-chains.index') }}" data-key="t-approval-chains">Approval Chains</a></li>
                            </ul>
                        </li>
                        @endcan

                        {{-- Payroll & Performance --}}
                        <li>
                            <a href="javascript: void(0);" class="has-arrow" data-key="t-compensation">Compensation</a>
                            <ul class="sub-menu" aria-expanded="true">
                                @can('hrm.payroll.view')
                                <li><a href="{{ route('hrm.payroll.index') }}" data-key="t-payroll">Payroll</a></li>
                                @endcan
                                @can('hrm.appraisals.view')
                                <li><a href="{{ route('hrm.appraisals.index') }}" data-key="t-appraisals">Performance</a></li>
                                @endcan
                            </ul>
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