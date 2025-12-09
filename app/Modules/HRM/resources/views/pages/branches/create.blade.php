@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12">
            <h2>Add New Branch</h2>
        </div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.branches.index') }}">Branches</a></li>
                <li class="breadcrumb-item active">Add New</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hrm.branches.store') }}" method="POST">
                    @csrf
                    
                    <h5 class="mb-3">Basic Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Branch Code</label>
                            <input type="text" class="form-control" name="code" value="{{ old('code') }}" placeholder="Auto-generated if empty">
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
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="2">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" value="{{ old('city') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" name="state" value="{{ old('state') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Country</label>
                            <input type="text" class="form-control" name="country" value="{{ old('country', 'Bangladesh') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Postal Code</label>
                            <input type="text" class="form-control" name="postal_code" value="{{ old('postal_code') }}">
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Contact Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Geofence Settings (Optional)</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="number" step="any" class="form-control" name="latitude" value="{{ old('latitude') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="number" step="any" class="form-control" name="longitude" value="{{ old('longitude') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Radius (meters)</label>
                            <input type="number" class="form-control" name="radius" value="{{ old('radius', 100) }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('hrm.branches.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success">Create Branch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
