@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Assets</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Assets</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                 <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Assets List</h6>
                    
                    <div class="d-flex align-items-center gap-2">
                        <a href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#addAssetOffcanvas" class="btn btn-info btn-sm d-flex align-items-center font-weight-medium">
                            <i class="mdi mdi-plus me-1"></i> Add New Asset
                        </a>
                        
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#assetFilter" aria-controls="assetFilter">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
                        </button>

                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-export me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Excel (XLSX)</a></li>
                                <li><a class="dropdown-item" href="#">PDF</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Filters Section -->
            @if(request()->hasAny(['type', 'status']))
                <x-Main::active-filters :url="route('hrm.assets.index')">
                    <x-Main::active-filter-item key="type" label="Type" :value="ucfirst(request('type'))" />
                    <x-Main::active-filter-item key="status" label="Status" :value="ucfirst(request('status'))" />
                </x-Main::active-filters>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Name/Code</th>
                                <th>Type</th>
                                <th>Assigned To</th>
                                <th>Condition</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assets as $asset)
                            <tr>
                                <td>
                                    <div>
                                        <h6 class="mb-0 font-size-14">{{ $asset->name }}</h6>
                                        <small class="text-muted">{{ $asset->code }}</small>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark">{{ ucfirst($asset->type) }}</span></td>
                                <td>
                                    @if($asset->currentAssignment)
                                    <div class="d-flex align-items-center">
                                             <div class="avatar-xs me-2">
                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12">
                                                {{ substr(optional($asset->currentAssignment->employee)->first_name ?? 'U', 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 font-size-14">{{ optional($asset->currentAssignment->employee)->full_name ?? 'Unknown User' }}</h6>
                                            <small class="text-muted">{{ $asset->currentAssignment->assigned_date->format('d M, Y') }}</small>
                                        </div>
                                    </div>
                                    @else
                                    <span class="text-muted small">Not Assigned</span>
                                    @endif
                                </td>
                                <td>{{ ucfirst($asset->condition) }}</td>
                                <td>
                                    @if($asset->status == 'available')<span class="badge bg-soft-success text-success">Available</span>
                                    @elseif($asset->status == 'assigned')<span class="badge bg-soft-info text-info">Assigned</span>
                                    @elseif($asset->status == 'lost')<span class="badge bg-soft-danger text-danger">Lost</span>
                                    @else<span class="badge bg-soft-warning text-warning">{{ ucfirst($asset->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-muted font-size-16 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            {{-- Asset Actions --}}
                                            @if($asset->status == 'available')
                                                <li><a class="dropdown-item" href="{{ route('hrm.assets.assign', $asset) }}"><i class="bx bx-user-check me-2"></i> Assign</a></li>
                                            @elseif($asset->status == 'assigned')
                                                <li>
                                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#returnModal{{ $asset->id }}">
                                                        <i class="bx bx-subdirectory-left me-2"></i> Return
                                                    </a>
                                                </li>
                                            @endif
                                            
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ route('hrm.assets.edit', $asset) }}"><i class="bx bx-edit me-2"></i> Edit</a></li>
                                            <li>
                                                <form action="{{ route('hrm.assets.destroy', $asset) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')"><i class="bx bx-trash me-2"></i> Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Return Modal (Nested to keep ID unique) -->
                                    <div class="modal fade" id="returnModal{{ $asset->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="{{ route('hrm.assets.return', $asset) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Return Asset</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label">Return Date <span class="text-danger">*</span></label>
                                                            <input type="date" class="form-control" name="return_date" value="{{ date('Y-m-d') }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Condition <span class="text-danger">*</span></label>
                                                            <select class="form-select" name="return_condition" required>
                                                                <option value="good">Good</option>
                                                                <option value="fair">Fair</option>
                                                                <option value="poor">Poor</option>
                                                                <option value="damaged">Damaged</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Notes</label>
                                                            <textarea class="form-control" name="notes" rows="2" placeholder="Any remarks..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Confirm Return</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="mdi mdi-monitor-off font-size-24 d-block mb-2"></i>
                                    No assets found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($assets->count())
            <div class="card-footer bg-transparent border-top">
                <div class="d-flex justify-content-end">
                    {{ $assets->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Add Asset Offcanvas --}}
<div class="offcanvas offcanvas-end w-50" tabindex="-1" id="addAssetOffcanvas" aria-labelledby="addAssetLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="addAssetLabel">Add New Asset</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.assets.store') }}" method="POST">
            @csrf
            
            <h5 class="font-size-14 text-uppercase mb-3">Asset Details</h5>
            <div class="mb-3">
                <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" required placeholder="e.g. MacBook Pro M1">
            </div>
            <div class="mb-3">
                <label class="form-label">Asset Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="code" required placeholder="e.g. AST-001">
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="type" required>
                        <option value="laptop">Laptop/Computer</option>
                        <option value="mobile">Mobile Phone</option>
                        <option value="furniture">Furniture</option>
                        <option value="vehicle">Vehicle</option>
                        <option value="license">Software License</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Condition <span class="text-danger">*</span></label>
                    <select class="form-select" name="condition" required>
                        <option value="new">New</option>
                        <option value="good">Good</option>
                        <option value="fair">Fair</option>
                        <option value="poor">Poor</option>
                    </select>
                </div>
            </div>

            <h5 class="font-size-14 text-uppercase mb-3 mt-4">Purchase Information</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Purchase Date</label>
                    <input type="date" class="form-control" name="purchase_date">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Purchase Cost</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" name="purchase_cost" step="0.01">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Serial Number</label>
                <input type="text" class="form-control" name="serial_number" placeholder="e.g. SN123456789">
            </div>

            <div class="mb-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select" name="status" required>
                    <option value="available">Available</option>
                    <option value="assigned">Assigned</option>
                    <option value="maintenance">In Maintenance</option>
                    <option value="lost">Lost/Stolen</option>
                </select>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Save Asset</button>
            </div>
        </form>
    </div>
</div>

{{-- Filter Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="assetFilter" aria-labelledby="assetFilterLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="assetFilterLabel">Filter Assets</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.assets.index') }}" method="GET">
            <div class="mb-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="laptop" {{ request('type') == 'laptop' ? 'selected' : '' }}>Laptop</option>
                    <option value="mobile" {{ request('type') == 'mobile' ? 'selected' : '' }}>Mobile</option>
                    <option value="furniture" {{ request('type') == 'furniture' ? 'selected' : '' }}>Furniture</option>
                    <option value="vehicle" {{ request('type') == 'vehicle' ? 'selected' : '' }}>Vehicle</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Apply Filter</button>
                <a href="{{ route('hrm.assets.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>
</div>
@endsection
