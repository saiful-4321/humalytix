<!-- Horizontal Menu for Horizontal Layout -->
<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
            <div class="collapse navbar-collapse show" id="topnav-menu-content">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard.home') ? 'active' : '' }}" href="{{ route('dashboard.home') }}">
                            <i data-feather="home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    {{-- Users --}}
                    @canany(['user-list', 'user-create', 'user-update', 'user-reset-password', 'user-pretend-login', 'user-session'])
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-users" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="grid"></i>
                            <span>User Management</span>
                            <i class="mdi mdi-chevron-down ms-1"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-users">
                            @canany(['user-list', 'user-create', 'user-update', 'user-reset-password', 'user-pretend-login'])
                                <a href="{{ route("dashboard.user") }}" class="dropdown-item {{ request()->routeIs('dashboard.user') ? 'active' : '' }}">
                                    <i data-feather="users" class="icon-dual icon-xs me-2"></i>Users
                                </a>
                            @endcanany
                            @canany(['user-session'])
                                <a href="{{ route("dashboard.user.session") }}" class="dropdown-item {{ request()->routeIs('dashboard.user.session') ? 'active' : '' }}">
                                    <i data-feather="activity" class="icon-dual icon-xs me-2"></i>User Session
                                </a>
                            @endcanany
                        </div>
                    </li>
                    @endcanany

                    {{-- Role & Permissions --}}
                    @canany(['module-list', 'module-create', 'module-update', 'role-list', 'role-create', 'role-update', 'permission-list', 'permission-create', 'permission-update'])
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-roles" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="user-check"></i>
                            <span>Role & Permission</span>
                            <i class="mdi mdi-chevron-down ms-1"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-roles">
                            @canany(['module-list', 'module-create', 'module-update'])
                                <a href="{{ route("dashboard.role.module") }}" class="dropdown-item {{ request()->routeIs('dashboard.role.module') ? 'active' : '' }}">
                                    <i data-feather="package" class="icon-dual icon-xs me-2"></i>Modules
                                </a>
                            @endcanany
                            @canany(['permission-list', 'permission-create', 'permission-update'])
                                <a href="{{ route("dashboard.role.permission") }}" class="dropdown-item {{ request()->routeIs('dashboard.role.permission') ? 'active' : '' }}">
                                    <i data-feather="shield" class="icon-dual icon-xs me-2"></i>Permissions
                                </a>
                            @endcanany
                            @canany(['role-list', 'role-create', 'role-update'])
                                <a href="{{ route("dashboard.role") }}" class="dropdown-item {{ request()->routeIs('dashboard.role') ? 'active' : '' }}">
                                    <i data-feather="user-plus" class="icon-dual icon-xs me-2"></i>Roles
                                </a>
                            @endcanany
                        </div>
                    </li>
                    @endcanany

                    {{-- Logs --}}
                    @canany(['log-viewer', 'activity-log'])
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-logs" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-filter-outline"></i>
                            <span>Logs</span>
                            <i class="mdi mdi-chevron-down ms-1"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-logs">
                            @can("activity-log")
                                <a href="{{ route('dashboard.log.activity-log') }}" class="dropdown-item {{ request()->routeIs('dashboard.log.activity-log') ? 'active' : '' }}">
                                    <i data-feather="list" class="icon-dual icon-xs me-2"></i>Activity Log
                                </a>
                            @endcan
                            @can("log-viewer")
                                <a href="{{ url('log-viewer') }}" target="_blank" class="dropdown-item">
                                    <i data-feather="eye" class="icon-dual icon-xs me-2"></i>Log Viewer
                                </a>
                            @endcan
                        </div>
                    </li>
                    @endcanany

                    {{-- System Info --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-system" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-server-network"></i>
                            <span>System Info</span>
                            <i class="mdi mdi-chevron-down ms-1"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-system">
                            @can('backup-management')
                                <a href="{{ route('dashboard.system-health') }}" class="dropdown-item {{ request()->routeIs('dashboard.system-health') ? 'active' : '' }}">
                                    <i data-feather="cpu" class="icon-dual icon-xs me-2"></i>Health Status
                                </a>
                            @endcan
                            <a href="{{ url('docs/api') }}" target="_blank" class="dropdown-item">
                                <i data-feather="book" class="icon-dual icon-xs me-2"></i>API Documentation
                            </a>
                        </div>
                    </li>

                    {{-- Backup --}}
                    @can('backup-management')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-backup" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="database"></i>
                            <span>Backup</span>
                            <i class="mdi mdi-chevron-down ms-1"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-backup">
                            <a href="{{ route('dashboard.backup') }}" class="dropdown-item {{ request()->routeIs('dashboard.backup') ? 'active' : '' }}">
                                <i data-feather="hard-drive" class="icon-dual icon-xs me-2"></i>Backup Management
                            </a>
                            <a href="{{ route('dashboard.backup.schedule') }}" class="dropdown-item {{ request()->routeIs('dashboard.backup.schedule') ? 'active' : '' }}">
                                <i data-feather="clock" class="icon-dual icon-xs me-2"></i>Schedule Settings
                            </a>
                        </div>
                    </li>
                    @endcan

                    {{-- Settings --}}
                    @canany(['settings-list', 'company-settings-update', 'theme-settings-update', 'cache-management'])
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-settings" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="settings"></i>
                            <span>Settings</span>
                            <i class="mdi mdi-chevron-down ms-1"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-settings">
                            @can("company-settings-update")
                                <a href="{{ route('dashboard.settings.company.index') }}" class="dropdown-item {{ request()->routeIs('dashboard.settings.company.index') ? 'active' : '' }}">
                                    <i data-feather="briefcase" class="icon-dual icon-xs me-2"></i>Company Settings
                                </a>
                            @endcan
                            @can("theme-settings-update")
                                <a href="{{ route('dashboard.settings.theme.index') }}" class="dropdown-item {{ request()->routeIs('dashboard.settings.theme.index') ? 'active' : '' }}">
                                    <i data-feather="droplet" class="icon-dual icon-xs me-2"></i>Theme Settings
                                </a>
                            @endcan
                            @can("cache-management")
                                <a href="{{ route('dashboard.cache') }}" class="dropdown-item {{ request()->routeIs('dashboard.cache') ? 'active' : '' }}">
                                    <i data-feather="zap" class="icon-dual icon-xs me-2"></i>Cache Management
                                </a>
                            @endcan
                        </div>
                    </li>
                    @endcanany

                    {{-- Logout --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('auth.logout') }}">
                            <i class="bx bx-log-out-circle"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>

<script>
// Horizontal menu toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const horizontalMenuBtn = document.getElementById('horizontal-menu-btn');
    const topnavContent = document.getElementById('topnav-menu-content');
    
    if (horizontalMenuBtn && topnavContent) {
        horizontalMenuBtn.addEventListener('click', function() {
            topnavContent.classList.toggle('show');
        });
    }
});
</script>
