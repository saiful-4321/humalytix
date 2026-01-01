@extends("Main::layouts.app")

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ __('Role Permissions') }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}"><i class="fas fa-home"></i></a></li>
                    <li class="breadcrumb-item">Access Control</li>
                    <li class="breadcrumb-item active">Edit Role</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-shield-quarter me-2"></i>
                        Configure Role Permissions
                    </h5>
                    @can('role-list')
                    <a href="{{ route('dashboard.role') }}" class="btn btn-soft-secondary btn-sm">
                        <i class="bx bx-list-ul me-1"></i> Back to Roles
                    </a>
                    @endcan
                </div>
            </div>

            @include('Main::widgets.message.sweet-alert')

            <div class="card-body">
                {{ Form::open(['route' => 'dashboard.role.has-permission.update', 'id' => 'rolePermissionForm']) }}
                {{ Form::hidden('_method', 'put') }}
                {{ Form::hidden('id', $role->id) }}

                <!-- Role Name Section -->
                <!-- Role Name and Stats - Compact -->
                <div class="row mb-3 g-2">
                    <div class="col-lg-9">
                        <div class="card border shadow-none bg-light mb-0">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="flex-grow-1">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="bx bx-shield"></i></span>
                                            <input type="text" name="name" class="form-control" 
                                                   placeholder="Role Name" 
                                                   value="{{ $role->name ?? old('name') }}" 
                                                   required>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                         <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="bx bx-save me-1"></i> Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card border shadow-none bg-primary bg-gradient mb-0 h-100">
                            <div class="card-body p-2 text-white d-flex align-items-center justify-content-between">
                                <span class="fs-12">Selected:</span>
                                <span class="fw-bold fs-14" id="selectedCount">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-soft-success btn-sm" id="selectAllBtn">
                                <i class="bx bx-check-double me-1"></i> Select All
                            </button>
                            <button type="button" class="btn btn-soft-danger btn-sm" id="deselectAllBtn">
                                <i class="bx bx-x me-1"></i> Deselect All
                            </button>
                            <button type="button" class="btn btn-soft-info btn-sm" id="expandAllBtn">
                                <i class="bx bx-expand me-1"></i> Expand All
                            </button>
                            <button type="button" class="btn btn-soft-warning btn-sm" id="collapseAllBtn">
                                <i class="bx bx-collapse me-1"></i> Collapse All
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search Box -->
                <div class="row mb-2">
                    <div class="col-lg-4">
                        <div class="search-box">
                            <input type="text" class="form-control form-control-sm" id="searchPermissions" 
                                   placeholder="Search permissions...">
                            <i class="bx bx-search search-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Permissions Grid -->
                <div class="row g-2" id="permissionsGrid">
                    @foreach($modules as $module => $permissions)
                    <div class="col-lg-6 col-xl-3 permission-module" data-module="{{ strtolower($module) }}">
                        <div class="card border shadow-none h-100 module-card mb-0">
                            <!-- Added data-toggle for BS4 and data-bs-toggle for BS5 -->
                            <div class="card-header bg-light-subtle p-2 cursor-pointer" 
                                 data-bs-toggle="collapse" data-toggle="collapse" 
                                 data-bs-target="#module{{ $loop->index }}" data-target="#module{{ $loop->index }}" 
                                 aria-expanded="true">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input module-checkbox" type="checkbox" 
                                               id="module_{{ $loop->index }}" data-module-id="{{ $loop->index }}">
                                        <label class="form-check-label fw-semibold fs-13" for="module_{{ $loop->index }}">
                                            {{ $module ?? '' }}
                                        </label>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="badge bg-primary-subtle text-primary permission-count">
                                            {{ count($permissions) }}
                                        </span>
                                        <i class="bx bx-chevron-down fs-14 text-muted"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="module{{ $loop->index }}" class="collapse show">
                                <div class="card-body p-2">
                                    <div class="permission-list">
                                        @foreach($permissions as $permission)
                                        <div class="form-check mb-1 permission-item" 
                                             data-permission="{{ strtolower($permission->name) }}">
                                            <input class="form-check-input permission-checkbox" 
                                                   type="checkbox" 
                                                   name="permissions[]" 
                                                   id="perm_{{ $permission->id }}" 
                                                   value="{{ $permission->name }}" 
                                                   data-module="{{ $loop->parent->index }}"
                                                   {{ $permission->checked }}>
                                            <label class="form-check-label text-muted fs-12" for="perm_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- No Results Message -->
                <div class="row d-none" id="noResults">
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bx bx-search-alt display-4 text-muted"></i>
                            <h5 class="mt-3">No permissions found</h5>
                            <p class="text-muted">Try adjusting your search terms</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="text-end">
                            <a href="{{ route('dashboard.role') }}" class="btn btn-light me-2">
                                <i class="bx bx-x me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Save Permissions
                            </button>
                        </div>
                    </div>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

<style>
.module-card {
    transition: all 0.3s ease;
}

.module-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    transform: translateY(-2px);
}

.cursor-pointer {
    cursor: pointer;
}

.permission-item {
    padding: 4px 8px;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.permission-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.form-check-input:checked ~ .form-check-label {
    color: var(--bs-primary) !important;
    font-weight: 500;
}

.search-box {
    position: relative;
}

.search-box .search-icon {
    position: absolute;
    right: 13px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 18px;
    color: #adb5bd;
}

.card-header i.bx-chevron-down {
    transition: transform 0.3s ease;
}

.card-header[aria-expanded="false"] i.bx-chevron-down {
    transform: rotate(-90deg);
}

.permission-count {
    font-size: 11px;
    padding: 2px 8px;
}
</style>
@endsection
