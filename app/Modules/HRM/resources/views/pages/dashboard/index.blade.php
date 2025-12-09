@extends("Main::layouts.app")

@section("content")
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0 text-uppercase fw-bold text-primary">HR Analytics Dashboard</h4>
                <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}! Here's what's happening today.</p>
            </div>
            <div class="text-end">
                <span class="badge bg-white text-dark border px-3 py-2 font-size-14">
                    <i class="bx bx-calendar me-1"></i> {{ date('l, d M Y') }}
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 border-start border-4 border-primary shadow-sm hover-shadow transition-all">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-primary text-primary rounded-3 font-size-24">
                            <i class="bx bx-user-pin"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-uppercase text-muted font-size-12 mb-1">Total Employees</h6>
                        <h3 class="mb-0 fw-bold">{{ \App\Modules\HRM\Models\Employee::count() }}</h3>
                    </div>
                </div>
                <div class="d-flex align-items-center font-size-12">
                    <span class="badge bg-soft-success text-success me-2">
                        <i class="bx bx-trending-up"></i> +2.5%
                    </span>
                    <span class="text-muted">vs last month</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 border-start border-4 border-success shadow-sm hover-shadow transition-all">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-success text-success rounded-3 font-size-24">
                            <i class="bx bx-user-check"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-uppercase text-muted font-size-12 mb-1">Active Staff</h6>
                        <h3 class="mb-0 fw-bold">{{ \App\Modules\HRM\Models\Employee::active()->count() }}</h3>
                    </div>
                </div>
                <div class="d-flex align-items-center font-size-12">
                    <span class="text-muted">Currently active in system</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 border-start border-4 border-warning shadow-sm hover-shadow transition-all">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-warning text-warning rounded-3 font-size-24">
                            <i class="bx bx-briefcase-alt-2"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-uppercase text-muted font-size-12 mb-1">Open Positions</h6>
                        <h3 class="mb-0 fw-bold">{{ \App\Modules\HRM\Models\Job::where('status', 'active')->count() }}</h3>
                    </div>
                </div>
                <div class="d-flex align-items-center font-size-12">
                    <a href="{{ route('hrm.jobs.index') }}" class="text-warning text-decoration-underline">View all roles</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 border-start border-4 border-info shadow-sm hover-shadow transition-all">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-soft-info text-info rounded-3 font-size-24">
                            <i class="bx bx-buildings"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-uppercase text-muted font-size-12 mb-1">Departments</h6>
                        <h3 class="mb-0 fw-bold">{{ \App\Modules\HRM\Models\Department::count() }}</h3>
                    </div>
                </div>
                <div class="d-flex align-items-center font-size-12">
                    <span class="text-muted">Across organization</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-xl-8 col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold">Recent Joiners</h5>
                <a href="{{ route('hrm.employees.index') }}" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Employee</th>
                                <th>Department</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Modules\HRM\Models\Employee::with('department')->latest()->take(5)->get() as $employee)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-2">
                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12">
                                                {{ substr($employee->first_name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 font-size-14">{{ $employee->full_name }}</h6>
                                            <small class="text-muted">{{ $employee->employee_code }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $employee->department->name ?? '-' }}</td>
                                <td>{{ $employee->designation }}</td>
                                <td>{{ $employee->joining_date?->format('d M, Y') }}</td>
                                <td>
                                    <span class="badge bg-soft-success text-success rounded-pill px-2">New</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Department Stats -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom">
                 <h5 class="card-title mb-0 fw-bold">Headcount by Department</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach(\App\Modules\HRM\Models\Department::withCount('employees')->orderByDesc('employees_count')->take(6)->get() as $dept)
                    <div class="col-md-6">
                        <div class="p-3 border rounded d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <span class="avatar-xs me-3">
                                    <span class="avatar-title bg-light text-primary rounded-circle">
                                        {{ substr($dept->name, 0, 1) }}
                                    </span>
                                </span>
                                <div>
                                    <h6 class="mb-0">{{ $dept->name }}</h6>
                                    <small class="text-muted">Code: {{ $dept->code ?? 'N/A' }}</small>
                                </div>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $dept->employees_count }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-xl-4 col-lg-5">
        <!-- Quick Actions Grid -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0 fw-bold">Quick Actions</h5>
            </div>
            <div class="card-body">
                 <div class="row g-2">
                    @can('hrm.employees.create')
                    <div class="col-6">
                        <a href="{{ route('hrm.employees.create') }}" class="btn btn-outline-primary w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="bx bx-user-plus font-size-24 mb-2"></i>
                            <span>Add Users</span>
                        </a>
                    </div>
                    @endcan
                    
                    @can('hrm.leaves.create')
                    <div class="col-6">
                        <a href="{{ route('hrm.leaves.create') }}" class="btn btn-outline-warning w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="bx bx-coffee font-size-24 mb-2"></i>
                            <span>Apply Leave</span>
                        </a>
                    </div>
                    @endcan

                    @can('hrm.attendance.check-in')
                    <div class="col-6">
                        <a href="#" class="btn btn-outline-success w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="bx bx-time-five font-size-24 mb-2"></i>
                            <span>Check In</span>
                        </a>
                    </div>
                    @endcan

                    @can('hrm.jobs.create')
                    <div class="col-6">
                        <a href="{{ route('hrm.jobs.create') }}" class="btn btn-outline-info w-100 p-3 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="bx bx-briefcase font-size-24 mb-2"></i>
                            <span>Post Job</span>
                        </a>
                    </div>
                    @endcan
                 </div>
            </div>
        </div>

        <!-- System Status or Smaller Widgets -->
        <div class="card bg-primary text-white border-0 shadow-sm">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <i class="bx bx-support font-size-48 text-white-50"></i>
                </div>
                <h5 class="text-white mb-1">Need Help?</h5>
                <p class="text-white-50 mb-3 font-size-13">Contact HR Support for any issues regarding portal.</p>
                <button class="btn btn-light btn-sm text-primary fw-bold">Contact Support</button>
            </div>
        </div>

    </div>
</div>
@endsection
