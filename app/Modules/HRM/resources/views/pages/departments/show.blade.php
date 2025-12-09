@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12">
            <h2>Department Details</h2>
        </div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.departments.index') }}">Departments</a></li>
                <li class="breadcrumb-item active">{{ $department->name }}</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <!-- Department Header -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1">{{ $department->name }}</h4>
                        <p class="text-muted mb-2">{{ $department->code }}</p>
                        <div class="mb-2">
                            @if($department->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                            @if($department->parent)
                            <span class="badge bg-soft-info text-info ms-1">Sub-department of {{ $department->parent->name }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        @can('hrm.departments.edit')
                        <a href="{{ route('hrm.departments.edit', $department) }}" class="btn btn-primary">
                            <i class="bx bx-edit"></i> Edit Department
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm flex-shrink-0 me-3">
                        <span class="avatar-title bg-soft-primary text-primary rounded-circle font-size-18">
                            <i class="bx bx-user"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Total Employees</p>
                        <h4 class="mb-0">{{ $stats['total_employees'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm flex-shrink-0 me-3">
                        <span class="avatar-title bg-soft-success text-success rounded-circle font-size-18">
                            <i class="bx bx-check-circle"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Active Employees</p>
                        <h4 class="mb-0">{{ $stats['active_employees'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm flex-shrink-0 me-3">
                        <span class="avatar-title bg-soft-info text-info rounded-circle font-size-18">
                            <i class="bx bx-building"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Sub-Departments</p>
                        <h4 class="mb-0">{{ $stats['sub_departments'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Information -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Department Information</h5>
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Code:</th>
                        <td><strong>{{ $department->code }}</strong></td>
                    </tr>
                    <tr>
                        <th>Name:</th>
                        <td>{{ $department->name }}</td>
                    </tr>
                    <tr>
                        <th>Parent Department:</th>
                        <td>{{ $department->parent->name ?? 'None (Root)' }}</td>
                    </tr>
                    <tr>
                        <th>Department Head:</th>
                        <td>{{ $department->headEmployee->full_name ?? 'Not Assigned' }}</td>
                    </tr>
                    <tr>
                        <th>Cost Center:</th>
                        <td>{{ $department->costCenter->name ?? 'Not Assigned' }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $department->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td>{{ $department->phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            @if($department->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                </table>

                @if($department->description)
                <div class="mt-3">
                    <h6>Description:</h6>
                    <p class="text-muted">{{ $department->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sub-Departments -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Sub-Departments ({{ $department->children->count() }})</h5>
                @if($department->children->count() > 0)
                <div class="list-group">
                    @foreach($department->children as $child)
                    <a href="{{ route('hrm.departments.show', $child) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $child->name }}</h6>
                                <small class="text-muted">{{ $child->code }}</small>
                            </div>
                            <div>
                                <span class="badge bg-soft-primary text-primary">{{ $child->employees_count ?? 0 }} employees</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="bx bx-building bx-lg d-block mb-2"></i>
                    No sub-departments
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Employees -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Employees ({{ $department->employees->count() }})</h5>
                @if($department->employees->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($department->employees as $emp)
                            <tr>
                                <td><strong>{{ $emp->employee_code }}</strong></td>
                                <td>{{ $emp->full_name }}</td>
                                <td>{{ $emp->designation }}</td>
                                <td>{{ $emp->email }}</td>
                                <td>{!! $emp->status_badge !!}</td>
                                <td>
                                    <a href="{{ route('hrm.employees.show', $emp) }}" class="btn btn-sm btn-soft-info">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="bx bx-user bx-lg d-block mb-2"></i>
                    No employees in this department
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
