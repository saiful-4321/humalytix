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

                {{-- HRM System --}}
                @canany(['hrm.employees.view', 'hrm.departments.view', 'hrm.branches.view', 'hrm.attendance.view', 'hrm.leaves.view', 'hrm.jobs.view', 'hrm.payroll.view', 'hrm.appraisals.view'])
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="users"></i>
                        <span data-key="t-hrm">HRM System</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('hrm.dashboard') }}" data-key="t-hrm-dashboard">Dashboard</a></li>

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

                    </ul>
                </li>
                @endcanany

                {{-- HRM Module --}}
                @canany(['hrm.employees.view', 'hrm.attendance.view', 'hrm.leaves.view', 'hrm.payroll.view'])
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-user-circle"></i>
                        <span data-key="t-hrm">Human Resources</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        
                        {{-- Core HR --}}
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <span data-key="t-core-hr">Core HR</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @can('hrm.employees.view')
                                <li><a href="{{ route('hrm.employees.index') }}">Employees</a></li>
                                @endcan
                                <li><a href="{{ route('hrm.departments.index') }}">Departments</a></li>
                                <li><a href="{{ route('hrm.branches.index') }}">Branches</a></li>
                                <li><a href="{{ route('hrm.business-units.index') }}">Business Units</a></li>
                                <li><a href="{{ route('hrm.document-types.index') }}">Document Types</a></li>
                            </ul>
                        </li>

                        {{-- Time & Attendance --}}
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <span data-key="t-attendance">Time & Attendance</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @can('hrm.attendance.view')
                                <li><a href="{{ route('hrm.attendance.my-attendance') }}">My Attendance</a></li>
                                <li><a href="{{ route('hrm.attendance.index') }}">Daily Logs</a></li>
                                @endcan
                                <li><a href="{{ route('hrm.rosters.index') }}">Weekly Roster</a></li>
                                <li><a href="{{ route('hrm.shifts.index') }}">Shift Settings</a></li>
                            </ul>
                        </li>

                        {{-- Leave Management --}}
                        @can('hrm.leaves.view')
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
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

                        {{-- Compensation & Benefits --}}
                        @can('hrm.payroll.view')
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <span data-key="t-compensation">Compensation</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('hrm.payroll.index') }}">Process Payroll</a></li>
                                <li><a href="{{ route('hrm.reports.salary-register') }}">Salary Sheet</a></li>
                                <li><a href="{{ route('hrm.bank-transfers.index') }}">Bank Transfer</a></li>
                                <li><a href="{{ route('hrm.bonuses.index') }}">Bonuses</a></li>
                                <li><a href="{{ route('hrm.overtime.index') }}">Overtime</a></li>
                                <li><a href="{{ route('hrm.loans.index') }}">Loans & Advances</a></li>
                                <li><a href="{{ route('hrm.gratuity.index') }}">Gratuity</a></li>
                                <li><a href="{{ route('hrm.settings.salary-components.index') }}">Settings</a></li>
                            </ul>
                        </li>
                        @endcan

                        {{-- Expense Management --}}
                        @can('hrm.expenses.view')
                        <li><a href="{{ route('hrm.expenses.index') }}">Expenses</a></li>
                        @endcan

                        {{-- Asset Management --}}
                        @can('hrm.assets.view')
                        <li><a href="{{ route('hrm.assets.index') }}">Assets</a></li>
                        @endcan
                        
                        @cannot('hrm.assets.view')
                        <li><a href="{{ route('hrm.assets.my-assets') }}">My Assets</a></li>
                        @endcannot

                        {{-- Talent Management --}}
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <span data-key="t-talent">Talent Management</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                {{-- Performance --}}
                                @can('hrm.performance.view')
                                <li>
                                    <a href="javascript: void(0);" class="has-arrow">
                                        <span>Performance</span>
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
                                @canany(['hrm.trainings.view', 'hrm.certifications.view'])
                                <li>
                                    <a href="javascript: void(0);" class="has-arrow">
                                        <span>Training & L&D</span>
                                    </a>
                                    <ul class="sub-menu" aria-expanded="false">
                                        <li><a href="{{ route('hrm.trainings.dashboard') }}">Dashboard</a></li>
                                        @can('hrm.trainings.view')
                                        <li><a href="{{ route('hrm.trainings.index') }}">Training Programs</a></li>
                                        <li><a href="{{ route('hrm.trainings.my-trainings') }}">My Trainings</a></li>
                                        @endcan
                                        @can('hrm.skills.view')
                                        <li><a href="{{ route('hrm.skills.matrix') }}">Skill Matrix</a></li>
                                        <li><a href="{{ route('hrm.skills.index') }}">Skills List</a></li>
                                        @endcan
                                        @can('hrm.certifications.view')
                                        <li><a href="{{ route('hrm.certifications.index') }}">Certifications</a></li>
                                        @endcan
                                    </ul>
                                </li>
                                @endcanany
                            </ul>
                        </li>

                        {{-- Compliance & Documents --}}
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <span data-key="t-compliance">Compliance</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('hrm.policies.index') }}">Policy Library</a></li>
                                <li><a href="{{ route('hrm.contracts.index') }}">Contracts (Admin)</a></li>
                                <li><a href="{{ route('hrm.contracts.my') }}">My Contracts</a></li>
                                <li><a href="{{ route('hrm.documents.expiry') }}">Expiry Tracker</a></li>
                            </ul>
                        </li>

                        {{-- Analytics & Reports --}}
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <span data-key="t-analytics">Analytics & Reports</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('hrm.analytics.hr') }}">HR Dashboard</a></li>
                            </ul>
                        </li>

                        {{-- Automation & Workflows --}}
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <span data-key="t-automation-workflows">Automation</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('hrm.workflows.index') }}">Workflows</a></li>
                                <li><a href="{{ route('hrm.settings.automation') }}">Settings</a></li>
                            </ul>
                        </li>

                        {{-- Settings --}}
                        <li><a href="{{ route('hrm.settings.index') }}">Settings</a></li>

                    </ul>
                </li>
                @endcanany

                {{-- Finance Module --}}
                @canany(['finance-module'])
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-money"></i>
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
                        
                        {{-- Reports --}}
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