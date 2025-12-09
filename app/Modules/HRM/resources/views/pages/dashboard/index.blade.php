@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12">
            <h2>HRM Dashboard</h2>
        </div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">Home</a></li>
                <li class="breadcrumb-item active">HRM</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <!-- Summary Cards -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary text-primary rounded-circle font-size-24">
                                <i class="bx bx-user-circle"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Total Employees</p>
                        <h4 class="mb-0">{{ \App\Modules\HRM\Models\Employee::count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-success text-success rounded-circle font-size-24">
                                <i class="bx bx-check-circle"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Active Employees</p>
                        <h4 class="mb-0">{{ \App\Modules\HRM\Models\Employee::active()->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-warning text-warning rounded-circle font-size-24">
                                <i class="bx bx-briefcase"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Open Positions</p>
                        <h4 class="mb-0">{{ \App\Modules\HRM\Models\Job::where('status', 'active')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-info text-info rounded-circle font-size-24">
                                <i class="bx bx-building"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Departments</p>
                        <h4 class="mb-0">{{ \App\Modules\HRM\Models\Department::count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <!-- Quick Actions -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @can('hrm.employees.create')
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('hrm.employees.create') }}" class="btn btn-primary w-100">
                            <i class="bx bx-user-plus me-1"></i> Add Employee
                        </a>
                    </div>
                    @endcan
                    
                    @can('hrm.employees.view')
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('hrm.employees.index') }}" class="btn btn-info w-100">
                            <i class="bx bx-list-ul me-1"></i> View Employees
                        </a>
                    </div>
                    @endcan
                    
                    @can('hrm.jobs.manage')
                    <div class="col-md-3 mb-3">
                        <a href="#" class="btn btn-warning w-100">
                            <i class="bx bx-briefcase me-1"></i> Manage Jobs
                        </a>
                    </div>
                    @endcan
                    
                    @can('hrm.attendance.view')
                    <div class="col-md-3 mb-3">
                        <a href="#" class="btn btn-success w-100">
                            <i class="bx bx-time me-1"></i> Attendance
                        </a>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <!-- Recent Employees -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Employees</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Joining Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Modules\HRM\Models\Employee::latest()->take(5)->get() as $employee)
                            <tr>
                                <td>{{ $employee->full_name }}</td>
                                <td>{{ $employee->department->name ?? 'N/A' }}</td>
                                <td>{{ $employee->joining_date?->format('d M, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Summary -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Department Summary</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th class="text-end">Employees</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Modules\HRM\Models\Department::withCount('employees')->take(5)->get() as $dept)
                            <tr>
                                <td>{{ $dept->name }}</td>
                                <td class="text-end">
                                    <span class="badge bg-soft-primary text-primary">{{ $dept->employees_count }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
