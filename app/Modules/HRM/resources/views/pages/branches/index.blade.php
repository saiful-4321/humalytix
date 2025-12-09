@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12">
            <h2>Branches</h2>
        </div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Branches</li>
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
                    <form action="{{ route('hrm.branches.index') }}" method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control" placeholder="Search branches..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-secondary"><i class="bx bx-search"></i></button>
                    </form>
                    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#addBranchOffcanvas">
                        <i class="bx bx-plus"></i> Add New Branch
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Branch Name</th>
                                <th>Code</th>
                                <th>Manager</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($branches as $branch)
                            <tr>
                                <td><h6 class="mb-0">{{ $branch->name }}</h6></td>
                                <td>{{ $branch->code }}</td>
                                <td>{{ $branch->manager ? $branch->manager->full_name : 'N/A' }}</td>
                                <td>{{ $branch->city ? $branch->city : 'N/A' }}</td>
                                <td>
                                    @if($branch->is_active)
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
                                <td colspan="6" class="text-center py-4">No branches found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $branches->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Branch Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="addBranchOffcanvas" aria-labelledby="addBranchLabel" style="width: 600px;">
    <div class="offcanvas-header">
        <h5 id="addBranchLabel">Add New Branch</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.branches.store') }}" method="POST">
            @csrf
            
            <h5 class="mb-3">Basic Information</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Branch Code</label>
                    <input type="text" class="form-control" name="code" placeholder="Auto-generated if empty">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Branch Manager</label>
                    <select class="form-select" name="manager_id">
                        <option value="">Select Manager</option>
                        @foreach($managers as $mgr)
                        <option value="{{ $mgr->id }}">{{ $mgr->full_name }} ({{ $mgr->designation }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
            </div>

            <h5 class="mb-3 mt-4">Location Details</h5>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea class="form-control" name="address" rows="2"></textarea>
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

            <h5 class="mb-3 mt-4">Geofence Settings (Optional)</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Latitude</label>
                    <input type="number" step="any" class="form-control" name="latitude">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Longitude</label>
                    <input type="number" step="any" class="form-control" name="longitude">
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Create Branch</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>

@endsection
