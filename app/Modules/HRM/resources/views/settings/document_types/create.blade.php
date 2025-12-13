@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Add Document Type</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.settings.index') }}">Settings</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.document-types.index') }}">Document Types</a></li>
                <li class="breadcrumb-item active">Add New</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-md-12">
        <div class="card bg-white">
            <div class="card-header">
                <h6 class="font-weight-medium mb-0">Document Type Information</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('hrm.document-types.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="kyc" {{ old('category') == 'kyc' ? 'selected' : '' }}>KYC</option>
                                <option value="employment" {{ old('category') == 'employment' ? 'selected' : '' }}>Employment</option>
                                <option value="education" {{ old('category') == 'education' ? 'selected' : '' }}>Education</option>
                                <option value="certification" {{ old('category') == 'certification' ? 'selected' : '' }}>Certification</option>
                                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="urgency" class="form-label">Urgency <span class="text-danger">*</span></label>
                            <select class="form-select @error('urgency') is-invalid @enderror" id="urgency" name="urgency" required>
                                <option value="required" {{ old('urgency') == 'required' ? 'selected' : '' }}>Required</option>
                                <option value="nice_to_have" {{ old('urgency') == 'nice_to_have' ? 'selected' : '' }}>Nice to Have</option>
                                <option value="not_required" {{ old('urgency') == 'not_required' ? 'selected' : '' }}>Not Required</option>
                            </select>
                            @error('urgency')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="order" class="form-label">Display Order</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', 0) }}" min="0">
                            @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="is_required" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_required">Mandatory Upload</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hrm.document-types.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Document Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
