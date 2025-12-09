@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12">
            <h2>Departments</h2>
        </div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Departments</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <form action="{{ route('hrm.departments.index') }}" method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control" placeholder="Search departments..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-secondary"><i class="bx bx-search"></i></button>
                    </form>
                    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#addDepartmentOffcanvas" aria-controls="addDepartmentOffcanvas">
                        <i class="bx bx-plus"></i> Add New Department
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Head</th>
                                <th>Parent Dept</th>
                                <th>Employees</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departments as $dept)
                            <tr>
                                <td><h6 class="mb-0">{{ $dept->name }}</h6></td>
                                <td>{{ $dept->code ?? '-' }}</td>
                                <td>
                                    @if($dept->headEmployee)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12">
                                                    {{ substr($dept->headEmployee->first_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 font-size-12">{{ $dept->headEmployee->full_name }}</h6>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $dept->parent->name ?? 'Root' }}</td>
                                <td><span class="badge bg-soft-info text-info">{{ $dept->employees->count() }} Members</span></td>
                                <td>
                                    @if($dept->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-horizontal"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('hrm.departments.show', $dept->id) }}"><i class="bx bx-show me-2"></i> View Details</a></li>
                                            <li><a class="dropdown-item" href="{{ route('hrm.departments.edit', $dept->id) }}"><i class="bx bx-edit me-2"></i> Edit</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('hrm.departments.destroy', $dept->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')"><i class="bx bx-trash me-2"></i> Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">No departments found.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $departments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Department Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="addDepartmentOffcanvas" aria-labelledby="addDepartmentLabel" style="width: 500px;">
    <div class="offcanvas-header">
        <h5 id="addDepartmentLabel">Add New Department</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.departments.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Department Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" required placeholder="e.g. Human Resources">
            </div>

            <div class="mb-3">
                <label for="code" class="form-label">Department Code</label>
                <input type="text" class="form-control" name="code" placeholder="e.g. HR-001 (Auto-generated if empty)">
            </div>

            <div class="mb-3">
                <label for="parent_id" class="form-label">Parent Department</label>
                <select class="form-select" name="parent_id">
                    <option value="">None (Root Department)</option>
                    @foreach($parentDepartments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="head_employee_id" class="form-label">Department Head</label>
                <select class="form-select" name="head_employee_id">
                    <option value="">Select Department Head</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->designation }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="cost_center_id" class="form-label">Cost Center</label>
                <select class="form-select" name="cost_center_id">
                    <option value="">Select Cost Center</option>
                    @foreach($costCenters as $cc)
                    <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3"></textarea>
            </div>
            
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="isActiveSwitch" name="is_active" value="1" checked>
                    <label class="form-check-label" for="isActiveSwitch">Active Status</label>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Create Department</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>

@endsection
