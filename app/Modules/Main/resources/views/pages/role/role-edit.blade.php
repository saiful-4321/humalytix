<div class="card-body">
{{ Form::open(['route' => 'dashboard.role.has-permission.update', 'id' => 'rolePermissionForm']) }}
{{ Form::hidden('_method', 'put') }}
{{ Form::hidden('id', $role->id) }}

<style>
/* Inline styles for offcanvas */
.modern-role-form .role-name-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
    color: white;
}

.modern-role-form .role-name-input {
    background: rgba(255,255,255,0.2);
    border: 2px solid rgba(255,255,255,0.3);
    color: white;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 16px;
    font-weight: 600;
}

.modern-role-form .role-name-input::placeholder {
    color: rgba(255,255,255,0.7);
}

.modern-role-form .role-name-input:focus {
    background: rgba(255,255,255,0.3);
    border-color: white;
    color: white;
    box-shadow: 0 0 0 4px rgba(255,255,255,0.1);
}

.modern-role-form .stats-badge {
    background: rgba(255,255,255,0.2);
    border-radius: 10px;
    padding: 12px 20px;
    text-align: center;
}

.modern-role-form .stats-value {
    font-size: 28px;
    font-weight: 700;
    display: block;
}

.modern-role-form .stats-label {
    font-size: 12px;
    opacity: 0.9;
}

.modern-role-form .action-bar {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 20px;
}

.modern-role-form .search-box-modern {
    position: relative;
}

.modern-role-form .search-box-modern i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #adb5bd;
    font-size: 18px;
}

.modern-role-form .search-box-modern input {
    padding-left: 45px;
    border-radius: 10px;
    border: 2px solid #e9ecef;
}

.modern-role-form .search-box-modern input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.modern-role-form .action-btn {
    border-radius: 8px;
    padding: 8px 16px;
    font-weight: 500;
}

.modern-role-form .permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.modern-role-form .permission-module-card {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.modern-role-form .permission-module-card:hover {
    border-color: #667eea;
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.12);
    transform: translateY(-2px);
}

.modern-role-form .module-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 14px 16px;
    cursor: pointer;
}

.modern-role-form .module-header:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
}

.modern-role-form .module-title {
    font-size: 14px;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

.modern-role-form .permission-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.modern-role-form .toggle-icon {
    font-size: 18px;
    color: #6c757d;
    transition: transform 0.3s ease;
}

.modern-role-form .module-header[aria-expanded="false"] .toggle-icon {
    transform: rotate(-90deg);
}

.modern-role-form .module-body {
    padding: 12px;
}

.modern-role-form .permissions-list {
    display: grid;
    gap: 4px;
}

.modern-role-form .permission-item {
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.modern-role-form .permission-item:hover {
    background: #f8f9fa;
}

.modern-role-form .permission-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    margin: 0;
    font-size: 13px;
    color: #4a5568;
}

.modern-role-form .permission-type {
    font-size: 9px;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
    text-transform: uppercase;
}

.modern-role-form .permission-type.view { background: rgba(33, 150, 243, 0.1); color: #2196f3; }
.modern-role-form .permission-type.create { background: rgba(76, 175, 80, 0.1); color: #4caf50; }
.modern-role-form .permission-type.edit { background: rgba(255, 152, 0, 0.1); color: #ff9800; }
.modern-role-form .permission-type.delete { background: rgba(244, 67, 54, 0.1); color: #f44336; }
.modern-role-form .permission-type.approve { background: rgba(156, 39, 176, 0.1); color: #9c27b0; }

.modern-role-form .form-check-input {
    width: 18px;
    height: 18px;
    border-radius: 6px;
    border: 2px solid #cbd5e0;
    cursor: pointer;
}

.modern-role-form .form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.modern-role-form .form-check-input:checked ~ .permission-label {
    color: #667eea;
    font-weight: 600;
}

.modern-role-form .module-checkbox {
    width: 18px;
    height: 18px;
}

.modern-role-form .no-results {
    text-align: center;
    padding: 60px 20px;
    color: #a0aec0;
}

.modern-role-form .no-results i {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
}

/* Dark mode */
body[data-layout-mode="dark"] .modern-role-form .permission-module-card {
    background: #242736;
    border-color: rgba(255,255,255,0.1);
}

body[data-layout-mode="dark"] .modern-role-form .module-header {
    background: linear-gradient(135deg, #2d3142 0%, #242736 100%);
}

body[data-layout-mode="dark"] .modern-role-form .module-title,
body[data-layout-mode="dark"] .modern-role-form .permission-label {
    color: #e2e8f0;
}

body[data-layout-mode="dark"] .modern-role-form .action-bar {
    background: #242736;
}

@media (max-width: 768px) {
    .modern-role-form .permissions-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="modern-role-form">
    <!-- Role Name Section -->
    <div class="role-name-section">
        <div class="row">
            <div class="col-md-8 mb-3 mb-md-0">
                <label class="form-label mb-2 text-white">
                    <i class="bx bx-shield me-1"></i>
                    Role Name
                </label>
                <input type="text" name="name" class="form-control role-name-input" 
                       placeholder="e.g., Content Manager, Sales Executive" 
                       value="{{ $role->name ?? old('name') }}" 
                       required>
            </div>
            <div class="col-md-4">
                <label class="form-label mb-2 text-white opacity-75">Permissions</label>
                <div class="stats-badge" style="height: 50px; display: flex; flex-direction: column; justify-content: center;">
                    <span class="stats-value" id="selectedCount">0</span>
                    <span class="stats-label">selected</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div class="row g-2 align-items-center">
            <div class="col-lg-6">
                <div class="search-box-modern">
                    <i class="bx bx-search"></i>
                    <input type="text" class="form-control" id="searchPermissions" 
                           placeholder="Search permissions...">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-flex gap-2 justify-content-lg-end flex-wrap">
                    <button type="button" class="btn btn-soft-success btn-sm action-btn" id="selectAllBtn">
                        <i class="bx bx-check-double"></i> All
                    </button>
                    <button type="button" class="btn btn-soft-danger btn-sm action-btn" id="deselectAllBtn">
                        <i class="bx bx-x"></i> Clear
                    </button>
                    <button type="button" class="btn btn-soft-info btn-sm action-btn" id="expandAllBtn">
                        <i class="bx bx-expand"></i> Expand
                    </button>
                    <button type="button" class="btn btn-soft-warning btn-sm action-btn" id="collapseAllBtn">
                        <i class="bx bx-collapse"></i> Collapse
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Grid -->
    <div class="permissions-grid" id="permissionsGrid">
        @foreach($modules as $module => $permissions)
        <div class="permission-module-card permission-module" data-module="{{ strtolower($module) }}">
            <div class="module-header" data-bs-toggle="collapse" data-bs-target="#module{{ $loop->index }}" aria-expanded="true">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="form-check mb-0">
                        <input class="form-check-input module-checkbox" type="checkbox" 
                               id="module_{{ $loop->index }}" data-module-id="{{ $loop->index }}">
                        <label class="form-check-label module-title" for="module_{{ $loop->index }}">
                            <i class="bx bx-folder text-primary me-1"></i>
                            {{ $module ?? '' }}
                        </label>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="permission-badge">{{ count($permissions) }}</span>
                        <i class="bx bx-chevron-down toggle-icon"></i>
                    </div>
                </div>
            </div>
            
            <div id="module{{ $loop->index }}" class="collapse show">
                <div class="module-body">
                    <div class="permissions-list">
                        @foreach($permissions as $permission)
                        <div class="permission-item" data-permission="{{ strtolower($permission->name) }}">
                            <div class="form-check">
                                <input class="form-check-input permission-checkbox" 
                                       type="checkbox" 
                                       name="permissions[]" 
                                       id="perm_{{ $permission->id }}" 
                                       value="{{ $permission->name }}" 
                                       data-module="{{ $loop->parent->index }}"
                                       {{ $permission->checked }}>
                                <label class="form-check-label permission-label" for="perm_{{ $permission->id }}">
                                    <span>{{ $permission->name }}</span>
                                    @if(str_contains($permission->name, '.view'))
                                        <span class="permission-type view">View</span>
                                    @elseif(str_contains($permission->name, '.create'))
                                        <span class="permission-type create">Create</span>
                                    @elseif(str_contains($permission->name, '.edit'))
                                        <span class="permission-type edit">Edit</span>
                                    @elseif(str_contains($permission->name, '.delete'))
                                        <span class="permission-type delete">Delete</span>
                                    @elseif(str_contains($permission->name, '.approve'))
                                        <span class="permission-type approve">Approve</span>
                                    @endif
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- No Results -->
    <div class="no-results d-none" id="noResults">
        <i class="bx bx-search-alt"></i>
        <h6>No permissions found</h6>
        <p class="small">Try adjusting your search</p>
    </div>

    <!-- Submit Button -->
    <div class="text-end pt-3 border-top">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bx bx-save me-1"></i> Update Role
        </button>
    </div>
</div>

{{ Form::close() }}
</div>