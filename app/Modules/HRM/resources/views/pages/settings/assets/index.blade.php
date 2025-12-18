@extends("HRM::layouts.settings")

@section("title", "Asset Categories")
@section("breadcrumb")
    <li class="breadcrumb-item active">Asset Configuration</li>
    <li class="breadcrumb-item active">Categories</li>
@endsection

@section("settings-content")

@include("Main::widgets.message.sweet-alert")

<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-sm-6">
                <h4 class="card-title">Asset Categories</h4>
                <p class="card-title-desc">Manage categories for company assets.</p>
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end">
                    <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="offcanvas" data-bs-target="#addCategoryOffcanvas">
                        <i class="bx bx-plus me-1"></i> Add Category
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered dt-responsive nowrap w-100">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Depreciation Rate (%)</th>
                        <th>Useful Life (Years)</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->depreciation_rate }}%</td>
                            <td>{{ $category->useful_life_years }} Years</td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge badge-pill badge-soft-success font-size-11">Active</span>
                                @else
                                    <span class="badge badge-pill badge-soft-danger font-size-11">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="offcanvas" data-bs-target="#editCategoryOffcanvas{{ $category->id }}">
                                    <i class="bx bx-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger confirm-action" data-action="{{ route('hrm.settings.assets.destroy', $category->id) }}" data-method="DELETE" data-message="Are you sure you want to delete this category?">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Edit Offcanvas -->
                        <div class="offcanvas offcanvas-end" tabindex="-1" id="editCategoryOffcanvas{{ $category->id }}" aria-labelledby="editCategoryLabel{{ $category->id }}">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="editCategoryLabel{{ $category->id }}">Edit Category</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="{{ route('hrm.settings.assets.update', $category->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" value="{{ $category->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" rows="3">{{ $category->description }}</textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Depreciation Rate (%) <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control" name="depreciation_rate" value="{{ $category->depreciation_rate }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Useful Life (Years) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="useful_life_years" value="{{ $category->useful_life_years }}" required>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="isActive{{ $category->id }}" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isActive{{ $category->id }}">Active</label>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">Update Category</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="addCategoryOffcanvas" aria-labelledby="addCategoryLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="addCategoryLabel">Add Category</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.settings.assets.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3"></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Depreciation Rate (%) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" name="depreciation_rate" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Useful Life (Years) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="useful_life_years" required>
                </div>
            </div>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="isActiveNew" name="is_active" value="1" checked>
                <label class="form-check-label" for="isActiveNew">Active</label>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Save Category</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>

@endsection
