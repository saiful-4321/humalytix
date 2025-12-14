@extends("HRM::layouts.settings")

@section("title", "Business Units")
@section("breadcrumb")
    <li class="breadcrumb-item active">Organization Settings</li>
    <li class="breadcrumb-item active">Business Units</li>
@endsection

@section("settings-content")
    <div class="card bg-white">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between py-1">
                <h6 class="font-weight-medium mb-0">Business Units List</h6>
                
                <div class="d-flex align-items-center gap-2">
                    <a href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#addBusinessUnitOffcanvas" class="btn btn-info btn-sm d-flex align-items-center font-weight-medium">
                        <i class="mdi mdi-plus me-1"></i> Add New Business Unit
                    </a>
                    
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#buFilter" aria-controls="buFilter">
                        <i class="mdi mdi-filter-variant me-1"></i> Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Active Filters Section -->
        @if(request()->hasAny(['search', 'status']))
            <x-Main::active-filters :url="route('hrm.business-units.index')">
                <x-Main::active-filter-item key="search" label="Search" :value="request('search')" />
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
                            <th>Head Count</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($businessUnits as $bu)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-2">
                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12">
                                            {{ substr($bu->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <h6 class="mb-0 font-size-14">{{ $bu->name }}</h6>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $bu->code ?? '-' }}</span></td>
                            <td>
                                @if($bu->head)
                                    <div class="d-flex align-items-center">
                                        @if($bu->head->photo)
                                             <img src="{{ asset('storage/' . $bu->head->photo) }}" class="rounded-circle avatar-xs me-2" alt="">
                                        @else
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-soft-info text-info font-size-10">
                                                    {{ substr($bu->head->first_name, 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0 font-size-12">{{ $bu->head->full_name }}</h6>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td><span class="badge bg-soft-secondary text-secondary rounded-pill">{{ $bu->employees_count ?? $bu->employees()->count() }} Members</span></td>
                            <td>
                                @if($bu->is_active)
                                    <span class="badge bg-soft-success text-success">Active</span>
                                @else
                                    <span class="badge bg-soft-danger text-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form action="{{ route('hrm.business-units.destroy', $bu->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button type="button" class="btn btn-sm btn-soft-info" data-bs-toggle="offcanvas" data-bs-target="#editBusinessUnitOffcanvas{{ $bu->id }}">
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
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="mdi mdi-folder-open-outline font-size-24 d-block mb-2"></i>
                                    No business units found.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($businessUnits->count())
        <div class="card-footer bg-transparent border-top">
            <div class="d-flex justify-content-end">
                {{ $businessUnits->links() }}
            </div>
        </div>
        @endif
    </div>

    {{-- Add Business Unit Offcanvas --}}
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="addBusinessUnitOffcanvas" aria-labelledby="addBULabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="addBULabel">Add New Business Unit</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.business-units.store') }}" method="POST" id="addBuForm">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required placeholder="e.g. Retail Division">
                </div>

                <div class="mb-3">
                    <label for="code" class="form-label">Code</label>
                    <input type="text" class="form-control" name="code" placeholder="e.g. BU-RET-001 (Auto-generated if empty)">
                </div>

                <div class="mb-3">
                    <label for="head_employee_id" class="form-label">Head of Unit</label>
                    <select class="form-select select2" name="head_employee_id">
                        <option value="">Select Head</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->designation }})</option>
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
                    <button type="submit" class="btn btn-primary">Create Business Unit</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Filter Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="buFilter" aria-labelledby="buFilterLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="buFilterLabel">Filter Business Units</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.business-units.index') }}" method="GET" id="filterBuForm">
                <div class="mb-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by name or code...">
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
                    <a href="{{ route('hrm.business-units.index') }}" class="btn btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Offcanvases Loop --}}
    @foreach($businessUnits as $bu)
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="editBusinessUnitOffcanvas{{ $bu->id }}" aria-labelledby="editBULabel{{ $bu->id }}">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="editBULabel{{ $bu->id }}">Edit Business Unit</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.business-units.update', $bu->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ $bu->name }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Code</label>
                    <input type="text" class="form-control" name="code" value="{{ $bu->code }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Head of Unit</label>
                    <select class="form-select select2" name="head_employee_id">
                        <option value="">Select Head</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ $bu->head_employee_id == $emp->id ? 'selected' : '' }}>
                            {{ $emp->full_name }} ({{ $emp->designation }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3">{{ $bu->description }}</textarea>
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $bu->is_active ? 'checked' : '' }}>
                        <label class="form-check-label">Active Status</label>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Update Business Unit</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endsection
