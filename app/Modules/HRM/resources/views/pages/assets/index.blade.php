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
            @if(request()->hasAny(['category_id', 'status']))
                <x-Main::active-filters :url="route('hrm.assets.index')">
                    @if(request('category_id'))
                        <x-Main::active-filter-item key="category_id" label="Category" :value="$categories->find(request('category_id'))->name ?? 'Unknown'" />
                    @endif
                    <x-Main::active-filter-item key="status" label="Status" :value="ucfirst(request('status'))" />
                </x-Main::active-filters>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Name/Code</th>
                                <th>Category</th>
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
                                <td><span class="badge bg-light text-dark">{{ $asset->category->name ?? '-' }}</span></td>
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
                                                <li>
                                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#assignAssetOffcanvas{{ $asset->id }}">
                                                        <i class="bx bx-user-check me-2"></i> Assign
                                                    </a>
                                                </li>
                                            @elseif($asset->status == 'assigned')
                                                <li>
                                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#returnAssetOffcanvas{{ $asset->id }}">
                                                        <i class="bx bx-subdirectory-left me-2"></i> Return
                                                    </a>
                                                </li>
                                            @endif
                                            
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#editAssetOffcanvas{{ $asset->id }}">
                                                    <i class="bx bx-edit me-2"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('hrm.assets.destroy', $asset) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')"><i class="bx bx-trash me-2"></i> Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            <!-- Assign Offcanvas -->
                            <div class="offcanvas offcanvas-end" tabindex="-1" id="assignAssetOffcanvas{{ $asset->id }}" aria-labelledby="assignAssetLabel{{ $asset->id }}">
                                <div class="offcanvas-header">
                                    <h5 class="offcanvas-title" id="assignAssetLabel{{ $asset->id }}">Assign Asset: {{ $asset->code }}</h5>
                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body">
                                    <form action="{{ route('hrm.assets.store-assignment', $asset) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Assign To <span class="text-danger">*</span></label>
                                            <select class="form-select" name="employee_id" required>
                                                <option value="">Select Employee</option>
                                                @foreach($employees as $emp)
                                                    <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Assigned Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="assigned_date" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Current Condition <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="assigned_condition" value="{{ $asset->condition }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Notes</label>
                                            <textarea class="form-control" name="notes" rows="3" placeholder="Additional notes..."></textarea>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">Assign Asset</button>
                                            <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Return Offcanvas -->
                            <div class="offcanvas offcanvas-end" tabindex="-1" id="returnAssetOffcanvas{{ $asset->id }}" aria-labelledby="returnAssetLabel{{ $asset->id }}">
                                <div class="offcanvas-header">
                                    <h5 class="offcanvas-title" id="returnAssetLabel{{ $asset->id }}">Return Asset: {{ $asset->code }}</h5>
                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body">
                                    <form action="{{ route('hrm.assets.return', $asset) }}" method="POST">
                                        @csrf
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
                                            <textarea class="form-control" name="notes" rows="3" placeholder="Return remarks..."></textarea>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">Confirm Return</button>
                                            <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Edit Offcanvas -->
                            <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="editAssetOffcanvas{{ $asset->id }}" aria-labelledby="editAssetLabel{{ $asset->id }}">
                                <div class="offcanvas-header">
                                    <h5 class="offcanvas-title" id="editAssetLabel{{ $asset->id }}">Edit Asset: {{ $asset->code }}</h5>
                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body">
                                    <form action="{{ route('hrm.assets.update', $asset) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        
                                        <h5 class="font-size-14 text-uppercase mb-3">Asset Details</h5>
                                        <div class="mb-3">
                                            <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" value="{{ $asset->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Asset Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="code" value="{{ $asset->code }}" required>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                                <select class="form-select" name="asset_category_id" required>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ $asset->asset_category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Serial Number</label>
                                                <input type="text" class="form-control" name="serial_number" value="{{ $asset->serial_number }}">
                                            </div>
                                        </div>

                                        <h5 class="font-size-14 text-uppercase mb-3 mt-4">Purchase Information</h5>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Purchase Date</label>
                                                <input type="date" class="form-control" name="purchase_date" value="{{ $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '' }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Purchase Cost</label>
                                                <input type="number" class="form-control" name="purchase_cost" value="{{ $asset->purchase_cost }}" step="0.01">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Salvage Value</label>
                                                <input type="number" class="form-control" name="salvage_value" value="{{ $asset->salvage_value }}" step="0.01">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Condition <span class="text-danger">*</span></label>
                                                <select class="form-select" name="condition" required>
                                                    <option value="new" {{ $asset->condition == 'new' ? 'selected' : '' }}>New</option>
                                                    <option value="good" {{ $asset->condition == 'good' ? 'selected' : '' }}>Good</option>
                                                    <option value="fair" {{ $asset->condition == 'fair' ? 'selected' : '' }}>Fair</option>
                                                    <option value="poor" {{ $asset->condition == 'poor' ? 'selected' : '' }}>Poor</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                                <select class="form-select" name="status" required>
                                                    <option value="available" {{ $asset->status == 'available' ? 'selected' : '' }}>Available</option>
                                                    <option value="assigned" {{ $asset->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                                    <option value="maintenance" {{ $asset->status == 'maintenance' ? 'selected' : '' }}>In Maintenance</option>
                                                    <option value="lost" {{ $asset->status == 'lost' ? 'selected' : '' }}>Lost/Stolen</option>
                                                    <option value="retired" {{ $asset->status == 'retired' ? 'selected' : '' }}>Retired</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Location</label>
                                                <input type="text" class="form-control" name="location" value="{{ $asset->location }}">
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2 mt-4">
                                            <button type="submit" class="btn btn-primary">Update Asset</button>
                                            <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select class="form-select" name="asset_category_id" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Serial Number</label>
                    <input type="text" class="form-control" name="serial_number" placeholder="e.g. SN123456789">
                </div>
            </div>

            <h5 class="font-size-14 text-uppercase mb-3 mt-4">Purchase Information</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Purchase Date</label>
                    <input type="date" class="form-control" name="purchase_date">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Purchase Cost</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" name="purchase_cost" step="0.01">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Salvage Value</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" name="salvage_value" step="0.01">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Condition <span class="text-danger">*</span></label>
                    <select class="form-select" name="condition" required>
                        <option value="new">New</option>
                        <option value="good">Good</option>
                        <option value="fair">Fair</option>
                        <option value="poor">Poor</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="status" required>
                        <option value="available">Available</option>
                        <option value="assigned">Assigned</option>
                        <option value="maintenance">In Maintenance</option>
                        <option value="lost">Lost/Stolen</option>
                        <option value="retired">Retired</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" class="form-control" name="location" placeholder="e.g. Server Room">
                </div>
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
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="retired" {{ request('status') == 'retired' ? 'selected' : '' }}>Retired</option>
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
