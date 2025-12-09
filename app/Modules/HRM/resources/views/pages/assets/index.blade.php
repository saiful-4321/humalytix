@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12">
            <h2>Assets Management</h2>
        </div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
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
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <form action="{{ route('hrm.assets.index') }}" method="GET" class="d-flex gap-2">
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="laptop" {{ request('type') == 'laptop' ? 'selected' : '' }}>Laptop</option>
                            <option value="mobile" {{ request('type') == 'mobile' ? 'selected' : '' }}>Mobile</option>
                            <option value="furniture" {{ request('type') == 'furniture' ? 'selected' : '' }}>Furniture</option>
                            <option value="vehicle" {{ request('type') == 'vehicle' ? 'selected' : '' }}>Vehicle</option>
                        </select>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                        <button type="submit" class="btn btn-secondary">Filter</button>
                    </form>
                    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#addAssetOffcanvas">
                        <i class="bx bx-plus"></i> Add New Asset
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Name/Code</th>
                                <th>Type</th>
                                <th>Assigned To</th>
                                <th>Condition</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assets as $asset)
                            <tr>
                                <td>
                                    <strong>{{ $asset->name }}</strong><br>
                                    <small class="text-muted">{{ $asset->code }}</small>
                                </td>
                                <td>{{ ucfirst($asset->type) }}</td>
                                <td>
                                    @if($asset->currentAssignment)
                                    {{ $asset->currentAssignment->employee->full_name }}<br>
                                    <small>{{ $asset->currentAssignment->assigned_date->format('d M, Y') }}</small>
                                    @else
                                    <span class="text-muted">Not Assigned</span>
                                    @endif
                                </td>
                                <td>{{ ucfirst($asset->condition) }}</td>
                                <td>
                                    @if($asset->status == 'available')<span class="badge bg-success">Available</span>
                                    @elseif($asset->status == 'assigned')<span class="badge bg-info">Assigned</span>
                                    @elseif($asset->status == 'lost')<span class="badge bg-danger">Lost</span>
                                    @else<span class="badge bg-warning">{{ ucfirst($asset->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($asset->status == 'available')
                                    <a href="{{ route('hrm.assets.assign', $asset) }}" class="btn btn-sm btn-soft-primary">Assign</a>
                                    @elseif($asset->status == 'assigned')
                                    <button class="btn btn-sm btn-soft-warning" data-bs-toggle="modal" data-bs-target="#returnModal{{ $asset->id }}">Return</button>
                                    <!-- Return Modal (Keep as Modal since it's small) -->
                                    <div class="modal fade" id="returnModal{{ $asset->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('hrm.assets.return', $asset) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header"><h5 class="modal-title">Return Asset</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                    <div class="modal-body">
                                                        <div class="mb-3"><label class="form-label">Return Date</label><input type="date" class="form-control" name="return_date" value="{{ date('Y-m-d') }}" required></div>
                                                        <div class="mb-3"><label class="form-label">Condition</label><select class="form-select" name="return_condition"><option value="good">Good</option><option value="fair">Fair</option><option value="poor">Poor</option><option value="damaged">Damaged</option></select></div>
                                                        <div class="mb-3"><label class="form-label">Notes</label><textarea class="form-control" name="notes"></textarea></div>
                                                    </div>
                                                    <div class="modal-footer"><button type="submit" class="btn btn-primary">Confirm Return</button></div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No assets found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $assets->links() }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Add Asset Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="addAssetOffcanvas" aria-labelledby="addAssetLabel" style="width: 500px;">
    <div class="offcanvas-header">
        <h5 id="addAssetLabel">Add New Asset</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.assets.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Asset Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="code" required>
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

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Purchase Date</label>
                    <input type="date" class="form-control" name="purchase_date">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Purchase Cost</label>
                    <input type="number" class="form-control" name="purchase_cost" step="0.01">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Serial Number</label>
                <input type="text" class="form-control" name="serial_number">
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
                <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
