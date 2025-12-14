@extends("HRM::layouts.settings")

@section("title", "Document Types")
@section("breadcrumb")
    <li class="breadcrumb-item active">Settings</li>
    <li class="breadcrumb-item active">Document Types</li>
@endsection

@section("settings-content")
    <div class="card bg-white">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between py-1">
                <h6 class="font-weight-medium mb-0">Document Types List</h6>
                
                @can('hrm.settings.create')
                <button class="btn btn-info btn-sm d-flex align-items-center font-weight-medium" data-bs-toggle="offcanvas" data-bs-target="#addDocumentTypeOffcanvas">
                    <i class="mdi mdi-plus me-1"></i> Add New Type
                </button>
                @endcan
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive rounded-10 border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Required</th>
                            <th>Urgency</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentTypes as $type)
                        <tr>
                            <td>
                                <h6 class="mb-0 font-size-14">{{ $type->name }}</h6>
                                @if($type->description)
                                <small class="text-muted">{{ Str::limit($type->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-soft-primary text-primary">{{ ucfirst($type->category) }}</span>
                            </td>
                            <td>
                                @if($type->is_required)
                                <span class="badge bg-soft-danger text-danger">Yes</span>
                                @else
                                <span class="badge bg-soft-secondary text-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                @if($type->urgency == 'required')
                                <span class="badge bg-soft-danger text-danger">Required</span>
                                @elseif($type->urgency == 'nice_to_have')
                                <span class="badge bg-soft-info text-info">Nice to Have</span>
                                @else
                                <span class="badge bg-soft-secondary text-secondary">Not Required</span>
                                @endif
                            </td>
                            <td>{{ $type->order }}</td>
                            <td>
                                @if($type->is_active)
                                <span class="badge bg-soft-success text-success">Active</span>
                                @else
                                <span class="badge bg-soft-danger text-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form action="{{ route('hrm.document-types.destroy', $type->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="d-flex gap-2 justify-content-end">
                                        @can('hrm.settings.edit')
                                        <button type="button" class="btn btn-sm btn-soft-info" data-bs-toggle="offcanvas" data-bs-target="#editDocumentTypeOffcanvas{{ $type->id }}">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </button>
                                        @endcan
                                        
                                        @can('hrm.settings.delete')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" onclick="return confirm('Are you sure?')">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="mdi mdi-file-document-outline font-size-24 d-block mb-2"></i>
                                    No document types found.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($documentTypes->hasPages())
        <div class="card-footer bg-transparent border-top">
            <div class="d-flex justify-content-end">
                {{ $documentTypes->links() }}
            </div>
        </div>
        @endif
    </div>

    {{-- Add Document Type Offcanvas --}}
    @can('hrm.settings.create')
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="addDocumentTypeOffcanvas" aria-labelledby="addDTLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="addDTLabel">Add Document Type</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.document-types.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required placeholder="e.g. Passport">
                </div>
                <div class="mb-3">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select class="form-select select2" name="category" required>
                        <option value="">Select Category</option>
                        <option value="kyc">KYC</option>
                        <option value="employment">Employment</option>
                        <option value="education">Education</option>
                        <option value="certification">Certification</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Urgency <span class="text-danger">*</span></label>
                        <select class="form-select" name="urgency" required>
                            <option value="required">Required</option>
                            <option value="nice_to_have">Nice to Have</option>
                            <option value="not_required">Not Required</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" class="form-control" name="order" value="0" min="0">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="isReqSwitch" name="is_required" value="1">
                        <label class="form-check-label" for="isReqSwitch">Mandatory Upload</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Create Document Type</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    {{-- Edit Offcanvases Loop --}}
    @can('hrm.settings.edit')
    @foreach($documentTypes as $type)
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="editDocumentTypeOffcanvas{{ $type->id }}" aria-labelledby="editDTLabel{{ $type->id }}">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="editDTLabel{{ $type->id }}">Edit Document Type</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.document-types.update', $type->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ $type->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select class="form-select select2" name="category" required>
                        <option value="">Select Category</option>
                        <option value="kyc" {{ $type->category == 'kyc' ? 'selected' : '' }}>KYC</option>
                        <option value="employment" {{ $type->category == 'employment' ? 'selected' : '' }}>Employment</option>
                        <option value="education" {{ $type->category == 'education' ? 'selected' : '' }}>Education</option>
                        <option value="certification" {{ $type->category == 'certification' ? 'selected' : '' }}>Certification</option>
                        <option value="other" {{ $type->category == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Urgency <span class="text-danger">*</span></label>
                        <select class="form-select" name="urgency" required>
                            <option value="required" {{ $type->urgency == 'required' ? 'selected' : '' }}>Required</option>
                            <option value="nice_to_have" {{ $type->urgency == 'nice_to_have' ? 'selected' : '' }}>Nice to Have</option>
                            <option value="not_required" {{ $type->urgency == 'not_required' ? 'selected' : '' }}>Not Required</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" class="form-control" name="order" value="{{ $type->order }}" min="0">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_required" value="1" {{ $type->is_required ? 'checked' : '' }}>
                        <label class="form-check-label">Mandatory Upload</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3">{{ $type->description }}</textarea>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $type->is_active ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Update Document Type</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
    @endcan
@endsection
