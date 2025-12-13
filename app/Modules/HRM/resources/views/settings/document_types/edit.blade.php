@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Edit Document Type</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.settings.index') }}">Settings</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.document-types.index') }}">Document Types</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-md-12">
        <div class="card bg-white">
            <div class="card-header">
                <h6 class="font-weight-medium mb-0">Edit Document Type: {{ $documentType->name }}</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('hrm.document-types.update', $documentType) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $documentType->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="kyc" {{ old('category', $documentType->category) == 'kyc' ? 'selected' : '' }}>KYC</option>
                                <option value="employment" {{ old('category', $documentType->category) == 'employment' ? 'selected' : '' }}>Employment</option>
                                <option value="education" {{ old('category', $documentType->category) == 'education' ? 'selected' : '' }}>Education</option>
                                <option value="certification" {{ old('category', $documentType->category) == 'certification' ? 'selected' : '' }}>Certification</option>
                                <option value="other" {{ old('category', $documentType->category) == 'other' ? 'selected' : '' }}>Other</option>
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
                                <option value="required" {{ old('urgency', $documentType->urgency) == 'required' ? 'selected' : '' }}>Required</option>
                                <option value="nice_to_have" {{ old('urgency', $documentType->urgency) == 'nice_to_have' ? 'selected' : '' }}>Nice to Have</option>
                                <option value="not_required" {{ old('urgency', $documentType->urgency) == 'not_required' ? 'selected' : '' }}>Not Required</option>
                            </select>
                            @error('urgency')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="order" class="form-label">Display Order</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $documentType->order) }}" min="0">
                            @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="is_required" value="0">
                                <input class="form-check-input" type="checkbox" id="is_required" name="is_required" value="1" {{ old('is_required', $documentType->is_required) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_required">Mandatory Upload</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $documentType->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $documentType->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hrm.document-types.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Document Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
