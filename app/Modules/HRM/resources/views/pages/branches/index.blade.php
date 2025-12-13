@extends("HRM::layouts.settings")

@section("title", "Branches")
@section("breadcrumb")
    <li class="breadcrumb-item active">Organization Settings</li>
    <li class="breadcrumb-item active">Branches</li>
@endsection

@section("settings-content")
    <div class="card bg-white">
        <div class="card-header border-bottom">
             <div class="d-flex align-items-center justify-content-between py-1">
                <h6 class="font-weight-medium mb-0">Branches List</h6>
                
                <div class="d-flex align-items-center gap-2">
                    <a href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#addBranchOffcanvas" class="btn btn-info btn-sm d-flex align-items-center font-weight-medium">
                        <i class="mdi mdi-plus me-1"></i> Add New Branch
                    </a>
                    
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#branchFilter" aria-controls="branchFilter">
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
        @if(request()->hasAny(['search', 'manager_id', 'status']))
            <x-Main::active-filters :url="route('hrm.branches.index')">
                <x-Main::active-filter-item key="search" label="Search" :value="request('search')" />
                <x-Main::active-filter-item key="manager_id" label="Manager" :value="$managers->where('id', request('manager_id'))->first()->full_name ?? request('manager_id')" />
                <x-Main::active-filter-item key="status" label="Status" :value="request('status') == '1' ? 'Active' : (request('status') === '0' ? 'Inactive' : '')" />
            </x-Main::active-filters>
        @endif

        <div class="card-body p-0">
            <div class="table-responsive rounded-10 border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th>Branch Name</th>
                            <th>Code</th>
                            <th>Manager</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $branch)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-2">
                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12">
                                            {{ substr($branch->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <h6 class="mb-0 font-size-14">{{ $branch->name }}</h6>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $branch->code }}</span></td>
                            <td>
                                @if($branch->manager)
                                    <div class="d-flex align-items-center">
                                        @if($branch->manager->photo)
                                             <img src="{{ asset('storage/' . $branch->manager->photo) }}" class="rounded-circle avatar-xs me-2" alt="">
                                        @else
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-soft-info text-info font-size-10">
                                                    {{ substr($branch->manager->first_name, 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0 font-size-12">{{ $branch->manager->full_name }}</h6>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>{{ $branch->city ? $branch->city : 'N/A' }}</td>
                            <td>
                                @if($branch->is_active)
                                    <span class="badge bg-soft-success text-success">Active</span>
                                @else
                                    <span class="badge bg-soft-danger text-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted font-size-16 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('hrm.branches.show', $branch->id) }}"><i class="bx bx-show me-2"></i> View Details</a></li>
                                        <li><a class="dropdown-item" href="{{ route('hrm.branches.edit', $branch->id) }}"><i class="bx bx-edit me-2"></i> Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('hrm.branches.destroy', $branch->id) }}" method="POST" class="d-inline">
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
                            <td colspan="6" class="text-center py-4">
                                 <div class="text-muted">
                                    <i class="mdi mdi-office-building font-size-24 d-block mb-2"></i>
                                    No branches found.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
         
         @if($branches->count())
        <div class="card-footer bg-transparent border-top">
            <div class="d-flex justify-content-end">
                {{ $branches->links() }}
            </div>
        </div>
        @endif
    </div>

    {{-- Add Branch Offcanvas --}}
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="addBranchOffcanvas" aria-labelledby="addBranchLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="addBranchLabel">Add New Branch</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.branches.store') }}" method="POST" id="addBranchForm">
                @csrf
                
                <h5 class="font-size-14 text-uppercase mb-3">Basic Information</h5>
                <div class="mb-3">
                    <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required placeholder="e.g. Dhaka Head Office">
                </div>

                <div class="row">
                     <div class="col-md-6 mb-3">
                        <label class="form-label">Branch Code</label>
                        <input type="text" class="form-control" name="code" placeholder="Auto-generated if empty">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Branch Manager</label>
                        <select class="form-select select2" name="manager_id">
                            <option value="">Select Manager</option>
                            @foreach($managers as $mgr)
                            <option value="{{ $mgr->id }}">{{ $mgr->full_name }} ({{ $mgr->designation }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="isActiveSwitch" name="is_active" value="1" checked>
                        <label class="form-check-label" for="isActiveSwitch">Active Status</label>
                    </div>
                </div>

                <h5 class="font-size-14 text-uppercase mb-3 mt-4">Location Details</h5>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" rows="2" placeholder="Street Address"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" name="city">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Country</label>
                        <input type="text" class="form-control" name="country" value="Bangladesh">
                    </div>
                </div>

                <h5 class="font-size-14 text-uppercase mb-3 mt-4">Geofence Settings (Optional)</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="number" step="any" class="form-control" name="latitude" placeholder="e.g. 23.8103">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="number" step="any" class="form-control" name="longitude" placeholder="e.g. 90.4125">
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Create Branch</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Filter Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="branchFilter" aria-labelledby="branchFilterLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="branchFilterLabel">Filter Branches</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
             <form action="{{ route('hrm.branches.index') }}" method="GET" id="filterBranchForm">
                <div class="mb-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by name or code...">
                </div>
                <div class="mb-3">
                    <label class="form-label">Manager</label>
                    <select class="form-select select2" name="manager_id">
                        <option value="">All</option>
                        @foreach($managers as $mgr)
                            <option value="{{ $mgr->id }}" {{ request('manager_id') == $mgr->id ? 'selected' : '' }}>{{ $mgr->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                    <a href="{{ route('hrm.branches.index') }}" class="btn btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>
@endsection
