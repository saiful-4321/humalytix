<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">

                <li>
                    <a href="{{ route('dashboard.home') }}">
                    <i data-feather="home"></i>
                    <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>

                {{-- Access Control --}}
                @canany(['user-list', 'user-create', 'user-update', 'user-reset-password', 'user-pretend-login', 'user-session', 'module-list', 'module-create', 'module-update', 'role-list', 'role-create', 'role-update', 'permission-list', 'permission-create', 'permission-update'])
                <li>
                    <a href="#" class="has-arrow">
                        <i data-feather="shield"></i>
                        <span data-key="t-access-control">Access Control</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @canany(['user-list', 'user-create', 'user-update', 'user-reset-password', 'user-pretend-login'])
                            <li><a href="{{ route("dashboard.user") }}" data-key="t-user-list">Users</a></li>
                        @endcanany 
                        @canany(['user-session'])
                        <li><a href="{{ route("dashboard.user.session") }}" data-key="t-register">User Session</a></li>
                        @endcanany
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
                        <li><a href="{{ route('hrm.ess.expenses') }}" data-key="t-my-expenses">My Expenses</a></li>
                        <li><a href="{{ route('hrm.performance-goals.my-goals') }}" data-key="t-my-goals">Performance Goals</a></li>
                        <li><a href="{{ route('hrm.ess.holidays') }}" data-key="t-holidays">Holiday Calendar</a></li>
                        <li><a href="{{ route('hrm.ess.salary-certificate') }}" data-key="t-salary-cert">Salary Certificate</a></li>
                    </ul>
                </li>

                {{-- Manager Self Service --}}
                @if(auth()->check() && auth()->user()->employee && auth()->user()->employee->subordinates()->exists())
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="briefcase"></i>
                        <span data-key="t-mss">Manager Portal</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('hrm.mss.dashboard') }}" data-key="t-mss-dashboard">Dashboard</a></li>
                        <li><a href="{{ route('hrm.mss.team') }}" data-key="t-mss-team">My Team</a></li>
                        <li><a href="{{ route('hrm.mss.approvals') }}" data-key="t-mss-approvals">Approvals</a></li>
                        <li><a href="{{ route('hrm.mss.roster') }}" data-key="t-mss-roster">Manage Roster</a></li>
                    </ul>
                </li>
                @endif

                {{-- Core HR --}}
                @canany(['hrm.employees.view', 'hrm.departments.view', 'hrm.branches.view'])
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-id-card"></i>
                        <span data-key="t-core-hr">Core HR</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('hrm.dashboard') }}" data-key="t-hrm-dashboard">Dashboard</a></li>
                        @can('hrm.employees.view')
                        <li><a href="{{ route('hrm.employees.index') }}">Employees</a></li>
                        @endcan
                        @can('hrm.departments.view')
                        <li><a href="{{ route('hrm.departments.index') }}">Departments</a></li>
                        @endcan
                        @can('hrm.branches.view')
                        <li><a href="{{ route('hrm.branches.index') }}">Branches</a></li>
                        @endcan
                        @can('hrm.business-units.view')
                        <li><a href="{{ route('hrm.business-units.index') }}">Business Units</a></li>
                        @endcan
                        @can('hrm.document-types.view')
                        <li><a href="{{ route('hrm.document-types.index') }}">Document Types</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                {{-- Recruitment --}}
                @can('hrm.jobs.view')
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-user-plus"></i>
                        <span data-key="t-recruitment">Recruitment</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('hrm.jobs.index') }}" data-key="t-jobs">Jobs</a></li>
                        <li><a href="{{ route('hrm.candidates.index') }}" data-key="t-candidates">Candidates</a></li>
                        <li><a href="{{ route('hrm.letters.index') }}" data-key="t-letters">Letters</a></li>
                        <li><a href="{{ route('hrm.settings.letter-templates.index') }}" data-key="t-templates">Templates</a></li>
                    </ul>
                </li>
                @endcan

                {{-- Time & Attendance --}}
                @can('hrm.attendance.view')
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-time-five"></i>
                        <span data-key="t-attendance">Time & Attendance</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('hrm.attendance.my-attendance') }}">My Attendance</a></li>
                        <li><a href="{{ route('hrm.attendance.index') }}">Daily Logs</a></li>
                        <li><a href="{{ route('hrm.rosters.index') }}">Weekly Roster</a></li>
                        <li><a href="{{ route('hrm.shifts.index') }}">Shift Settings</a></li>
                        <li><a href="{{ route('hrm.ess.holidays') }}">Holidays</a></li>
                    </ul>
                </li>
                @endcan

                {{-- Leave Management --}}
                @can('hrm.leaves.view')
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-calendar-event"></i>
                        <span data-key="t-leave">Leave Management</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('hrm.leaves.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('hrm.leaves.my-leaves') }}">My Leaves</a></li>
                        <li><a href="{{ route('hrm.leaves.index') }}">Leave Requests</a></li>
                        <li><a href="{{ route('hrm.leaves.allocations.index') }}">Allocations</a></li>
                        <li><a href="{{ route('hrm.settings.leave-types.index') }}">Settings</a></li>
                    </ul>
                </li>
                @endcan

                {{-- Payroll --}}
                @can('hrm.payroll.view')
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-money"></i>
                        <span data-key="t-payroll">Payroll</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('hrm.payroll.index') }}">Process Payroll</a></li>
                        <li><a href="{{ route('hrm.bank-transfers.index') }}">Bank Transfer</a></li>
                        <li><a href="{{ route('hrm.bonuses.index') }}">Bonuses</a></li>
                        <li><a href="{{ route('hrm.overtime.index') }}">Overtime</a></li>
                        <li><a href="{{ route('hrm.loans.index') }}">Loans & Advances</a></li>
                        <li><a href="{{ route('hrm.gratuity.index') }}">Gratuity</a></li>
                        <li><a href="{{ route('hrm.settings.salary-components.index') }}">Settings</a></li>
                    </ul>
                </li>
                @endcan

                {{-- Expenses & Assets --}}
                @canany(['hrm.expenses.view', 'hrm.assets.view'])
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-wallet"></i>
                        <span data-key="t-expenses-assets">Expenses & Assets</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('hrm.expenses.view')
                        <li><a href="{{ route('hrm.expenses.index') }}">Expenses</a></li>
                        @endcan
                        @can('hrm.assets.view')
                        <li><a href="{{ route('hrm.assets.index') }}">Assets</a></li>
                        @endcan
                        @can('hrm.settings.expenses.view')
                        <li><a href="{{ route('hrm.settings.expenses.index') }}">Expense Categories</a></li>
                        @endcan
                        @can('hrm.settings.assets.view')
                        <li><a href="{{ route('hrm.settings.assets.index') }}">Asset Categories</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                {{-- Talent & Learning --}}
                @canany(['hrm.performance.view', 'hrm.trainings.view'])
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-trophy"></i>
                        <span data-key="t-talent">Talent & Learning</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        {{-- Performance & Evaluation --}}
                        @can('hrm.performance.view')
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <span>Performance & Evaluation</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('hrm.kpis.index') }}">KPIs / KRAs</a></li>
                                <li><a href="{{ route('hrm.okrs.index') }}">OKRs</a></li>
                                <li><a href="{{ route('hrm.performance-goals.index') }}">Goals</a></li>
                                <li><a href="{{ route('hrm.appraisals-360.index') }}">360° Appraisals</a></li>
                                <li><a href="{{ route('hrm.competencies.index') }}">Competencies</a></li>
                                <li><a href="{{ route('hrm.pips.index') }}">PIPs</a></li>
                            </ul>
                        </li>
                        @endcan

                        {{-- Training & L&D --}}
                        @can('hrm.trainings.view')
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <span>Training & L&D</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('hrm.trainings.dashboard') }}">Dashboard</a></li>
                                <li><a href="{{ route('hrm.trainings.index') }}">Training Programs</a></li>
                                <li><a href="{{ route('hrm.trainings.my-trainings') }}">My Trainings</a></li>
                                @can('hrm.skills.view')
                                <li><a href="{{ route('hrm.skills.matrix') }}">Skill Matrix</a></li>
                                <li><a href="{{ route('hrm.skills.index') }}">Skills List</a></li>
                                @endcan
                                @can('hrm.certifications.view')
                                <li><a href="{{ route('hrm.certifications.index') }}">Certifications</a></li>
                                @endcan
                            </ul>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                {{-- Compliance --}}
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-shield"></i>
                        <span data-key="t-compliance">Compliance</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('hrm.policies.index') }}">Policy Library</a></li>
                        @can('hrm.contracts.view')
                        <li><a href="{{ route('hrm.contracts.index') }}">Contracts (Admin)</a></li>
                        @endcan
                        <li><a href="{{ route('hrm.contracts.my') }}">My Contracts</a></li>
                        <li><a href="{{ route('hrm.documents.expiry') }}">Expiry Tracker</a></li>
                    </ul>
                </li>



                {{-- HRM Reports --}}
                @can('hrm.reports.view')
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-bar-chart-alt-2"></i>
                        <span data-key="t-hrm-reports">HRM Reports</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('hrm.analytics.hr') }}">HR Analytics</a></li>
                        @can('hrm.reports.payroll')
                        <li><a href="{{ route('hrm.reports.salary-register') }}">Salary Register</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan

                {{-- HRM Settings & Automation --}}
                @can('hrm.settings.view')
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-cog"></i>
                        <span data-key="t-hrm-settings">HRM Settings</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('hrm.settings.index') }}">General Settings</a></li>
                        @can('hrm.workflows.view')
                        <li><a href="{{ route('hrm.workflows.index') }}">Workflows</a></li>
                        <li><a href="{{ route('hrm.settings.automation') }}">Automation</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan

                {{-- Finance Module --}}
                @canany(['finance-module'])
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-dollar-circle"></i>
                        <span data-key="t-finance">Finance</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('finance.dashboard') }}" data-key="t-finance-dashboard">Dashboard</a></li>
                        
                        @can('finance-coa-list')
                        <li><a href="{{ route('finance.accounts.index') }}" data-key="t-finance-accounts">Chart of Accounts</a></li>
                        @endcan
                        
                        @can('finance-journal-list')
                        <li><a href="{{ route('finance.journals.index') }}" data-key="t-finance-journals">Journals</a></li>
                        @endcan
                        
                        {{-- Finance Reports --}}
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <span data-key="t-finance-reports">Reports</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @can('finance-report-trial-balance')
                                <li><a href="{{ route('finance.reports.trial-balance') }}" data-key="t-finance-tb">Trial Balance</a></li>
                                @endcan
                                @can('finance-report-balance-sheet')
                                <li><a href="{{ route('finance.reports.balance-sheet') }}" data-key="t-finance-bs">Balance Sheet</a></li>
                                @endcan
                                @can('finance-report-pl')
                                <li><a href="{{ route('finance.reports.profit-loss') }}" data-key="t-finance-pl">Profit & Loss</a></li>
                                @endcan
                                @can('finance-report-ledger')
                                <li><a href="{{ route('finance.reports.ledger') }}" data-key="t-finance-ledger">General Ledger</a></li>
                                @endcan
                                @can('finance-report-cashbook')
                                <li><a href="{{ route('finance.reports.cashbook') }}" data-key="t-finance-cashbook">Cashbook</a></li>
                                @endcan
                                @can('finance-report-bankbook')
                                <li><a href="{{ route('finance.reports.bankbook') }}" data-key="t-finance-bankbook">Bankbook</a></li>
                                @endcan
                                @can('finance-report-summary')
                                <li><a href="{{ route('finance.reports.summary') }}" data-key="t-finance-summary">Payment/Receipt Summary</a></li>
                                @endcan
                                @can('finance-report-dishonoured')
                                <li><a href="{{ route('finance.reports.dishonoured') }}" data-key="t-finance-dishonoured">Cheque Dishonour</a></li>
                                @endcan
                                <li><a href="{{ route('finance.reports.retained-earnings') }}" data-key="t-finance-re">Retained Earnings</a></li>
                                <li><a href="{{ route('finance.reports.bank-transfer') }}" data-key="t-finance-transfer">Bank Transfer</a></li>
                                <li><a href="{{ route('finance.reports.voucher-wise') }}" data-key="t-finance-voucher">Voucher-wise Transactions</a></li>
                            </ul>
                        </li>

                        @can('finance-settings-manage')
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <span data-key="t-finance-settings">Settings</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('finance.settings.mapping') }}" data-key="t-finance-mapping">Mappings</a></li>
                            </ul>
                        </li>
                        @endcan
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

                {{-- System Administration --}}
                <li>
                    <a href="#" class="has-arrow">
                        <i class="mdi mdi-server-network"></i>
                        <span data-key="t-system-admin">System Administration</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('backup-management')
                        <li><a href="{{ route('dashboard.system-health') }}" data-key="t-system-health">Health Status</a></li>
                        <li><a href="{{ route('dashboard.backup') }}" data-key="t-backup-management">Backup Management</a></li>
                        <li><a href="{{ route('dashboard.backup.schedule') }}" data-key="t-backup-schedule">Backup Schedule</a></li>
                        @endcan
                        <li><a href="{{ url('docs/api') }}" target="_blank" data-key="t-api-docs">API Documentation</a></li>
                    </ul>
                </li>

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