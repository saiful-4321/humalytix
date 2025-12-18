@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Edit Asset</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.assets.index') }}">Assets</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ul>
        </div>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hrm.assets.update', $asset) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $asset->name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Asset Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" value="{{ old('code', $asset->code) }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="asset_category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $asset->asset_category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Serial Number</label>
                            <input type="text" class="form-control" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Purchase Date</label>
                            <input type="date" class="form-control" name="purchase_date" value="{{ $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Purchase Cost</label>
                            <input type="number" class="form-control" name="purchase_cost" value="{{ old('purchase_cost', $asset->purchase_cost) }}" step="0.01">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Salvage Value</label>
                            <input type="number" class="form-control" name="salvage_value" value="{{ old('salvage_value', $asset->salvage_value) }}" step="0.01">
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
                            <input type="text" class="form-control" name="location" value="{{ old('location', $asset->location) }}">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hrm.assets.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Asset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
