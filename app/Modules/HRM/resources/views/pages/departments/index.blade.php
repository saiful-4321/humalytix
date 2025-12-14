@extends("HRM::layouts.settings")

@section("title", "Departments")
@section("breadcrumb")
    <li class="breadcrumb-item active">Organization Settings</li>
    <li class="breadcrumb-item active">Departments</li>
@endsection

@section("settings-content")
    <div class="card bg-white">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between py-1">
                <h6 class="font-weight-medium mb-0">Departments List</h6>
                
                <div class="d-flex align-items-center gap-2">
                    <a href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#addDepartmentOffcanvas" class="btn btn-info btn-sm d-flex align-items-center font-weight-medium">
                        <i class="mdi mdi-plus me-1"></i> Add New Department
                    </a>
                    
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#departmentFilter" aria-controls="departmentFilter">
                        <i class="mdi mdi-filter-variant me-1"></i> Filter
                    </button>

                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-export me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Excel (XLSX)</a></li>
                            <li><a class="dropdown-item" href="#">CSV</a></li>
                            <li><a class="dropdown-item" href="#">PDF</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Filters Section -->
        @if(request()->hasAny(['search', 'parent_id', 'status']))
            <x-Main::active-filters :url="route('hrm.departments.index')">
                <x-Main::active-filter-item key="search" label="Search" :value="request('search')" />
                <x-Main::active-filter-item key="parent_id" label="Parent" :value="request('parent_id')" />
                <x-Main::active-filter-item key="status" label="Status" :value="request('status')" />
            </x-Main::active-filters>
        @endif

        <div class="card-body p-0">
            <div class="table-responsive rounded-10 border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Head</th>
                            <th>Parent Dept</th>
                            <th>Employees</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $dept)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-2">
                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12">
                                            {{ substr($dept->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <h6 class="mb-0 font-size-14">{{ $dept->name }}</h6>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $dept->code ?? '-' }}</span></td>
                            <td>
                                @if($dept->headEmployee)
                                    <div class="d-flex align-items-center">
                                        @if($dept->headEmployee->profile_img)
                                             <img src="{{ asset($dept->headEmployee->profile_img) }}" class="rounded-circle avatar-xs me-2" alt="">
                                        @else
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-soft-info text-info font-size-10">
                                                    {{ substr($dept->headEmployee->first_name, 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0 font-size-12">{{ $dept->headEmployee->full_name }}</h6>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>{{ $dept->parent->name ?? 'Root' }}</td>
                            <td><span class="badge bg-soft-secondary text-secondary rounded-pill">{{ $dept->employees->count() }} Members</span></td>
                            <td>
                                @if($dept->is_active)
                                    <span class="badge bg-soft-success text-success">Active</span>
                                @else
                                    <span class="badge bg-soft-danger text-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form action="{{ route('hrm.departments.destroy', $dept->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('hrm.departments.show', $dept->id) }}" class="btn btn-sm btn-soft-primary">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-soft-info" data-bs-toggle="offcanvas" data-bs-target="#editDepartmentOffcanvas{{ $dept->id }}">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </button>
                                        <button type="submit" class="btn btn-sm btn-soft-danger" onclick="return confirm('Are you sure?')">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="mdi mdi-folder-open-outline font-size-24 d-block mb-2"></i>
                                    No departments found.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($departments->count())
        <div class="card-footer bg-transparent border-top">
            <div class="d-flex justify-content-end">
                {{ $departments->links() }}
            </div>
        </div>
        @endif
    </div>

    {{-- Add Department Offcanvas --}}
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="addDepartmentOffcanvas" aria-labelledby="addDepartmentLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="addDepartmentLabel">Add New Department</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.departments.store') }}" method="POST" id="addDepartmentForm">
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
                    <select class="form-select select2" name="parent_id">
                        <option value="">None (Root Department)</option>
                        @foreach($parentDepartments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="head_employee_id" class="form-label">Department Head</label>
                    <select class="form-select select2" name="head_employee_id">
                        <option value="">Select Department Head</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->designation }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="cost_center_id" class="form-label">Cost Center</label>
                    <select class="form-select select2" name="cost_center_id">
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

                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Create Department</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Filter Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="departmentFilter" aria-labelledby="departmentFilterLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="departmentFilterLabel">Filter Departments</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.departments.index') }}" method="GET" id="filterDepartmentForm">
                <div class="mb-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by name or code...">
                </div>
                <div class="mb-3">
                    <label class="form-label">Parent Department</label>
                    <select class="form-select select2" name="parent_id">
                        <option value="">All</option>
                        @foreach($parentDepartments as $dept)
                            <option value="{{ $dept->id }}" {{ request('parent_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                    <a href="{{ route('hrm.departments.index') }}" class="btn btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Offcanvases Loop --}}
    @foreach($departments as $dept)
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="editDepartmentOffcanvas{{ $dept->id }}" aria-labelledby="editDeptLabel{{ $dept->id }}">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="editDeptLabel{{ $dept->id }}">Edit Department</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.departments.update', $dept->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Department Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ $dept->name }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Department Code</label>
                    <input type="text" class="form-control" name="code" value="{{ $dept->code }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Parent Department</label>
                    <select class="form-select select2" name="parent_id">
                        <option value="">None (Root Department)</option>
                        @foreach($parentDepartments as $pDept)
                            @if($pDept->id != $dept->id)
                                <option value="{{ $pDept->id }}" {{ $dept->parent_id == $pDept->id ? 'selected' : '' }}>{{ $pDept->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Department Head</label>
                    <select class="form-select select2" name="head_employee_id">
                        <option value="">Select Department Head</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ $dept->head_employee_id == $emp->id ? 'selected' : '' }}>
                            {{ $emp->full_name }} ({{ $emp->designation }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cost Center</label>
                    <select class="form-select select2" name="cost_center_id">
                        <option value="">Select Cost Center</option>
                        @foreach($costCenters as $cc)
                        <option value="{{ $cc->id }}" {{ $dept->cost_center_id == $cc->id ? 'selected' : '' }}>{{ $cc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3">{{ $dept->description }}</textarea>
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $dept->is_active ? 'checked' : '' }}>
                        <label class="form-check-label">Active Status</label>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Update Department</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endsection
