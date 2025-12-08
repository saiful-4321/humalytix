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