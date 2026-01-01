@extends("Main::layouts.app")


@section('content')
<div class="row">
    <div class="col-lg-5 col-md-8 col-sm-12">                        
        <h2>{{ __('Roles') }}</h2>
    </div>            
    <div class="col-lg-7 col-md-4 col-sm-12 text-right">
        <ul class="breadcrumb justify-content-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}"><i class="fas fa-home"></i></a></li>                            
            <li class="breadcrumb-item">Role & Permission</li>
            <li class="breadcrumb-item active">Roles</li>
        </ul>
    </div>

    <div class="col-lg-12 col-md-12">
        <div class="card bg-white"> 
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Role List - (showing {{ $result->firstItem()??0 }} to {{ $result->lastItem()??0 }} of total {{ $result->total()??0 }} entries)</h6>

                    <div class="d-flex align-items-center gap-2">
                        @can('role-create')
                        <a href="javascript:void(0)" class="btn btn-info btn-sm d-flex align-items-center font-weight-medium role-create-btn">
                            <i class="mdi mdi-plus me-1"></i> Add New Role
                        </a>
                        @endcan
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#roleFilter" aria-controls="roleFilter">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter Roles
                        </button>
                    </div>
                </div> 
            </div>

            <!-- Active Filters Section -->
            @if(request()->hasAny(['name', 'start_date', 'end_date']))
                <x-Main::active-filters :url="route('dashboard.role')">
                    <x-Main::active-filter-item key="name" label="Name" :value="request('name')" />
                    <x-Main::active-filter-item key="start_date" label="Start Date" :value="request('start_date')" />
                    <x-Main::active-filter-item key="end_date" label="End Date" :value="request('end_date')" />
                </x-Main::active-filters>
            @endif

            <div class="card-body p-0"> 
                <div class="table-responsive rounded-10 border">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-top-0">SL. No.</th>
                                <th class="border-top-0">Role Name</th>
                                <th class="border-top-0">Total Permission</th>
                                <th class="border-top-0">Created Date</th>
                                <th class="border-top-0">Updated Date</th>
                                <th class="border-top-0">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($result) && $result->count() > 0)
                            @foreach($result as $item)
                            <tr>
                                <td>{{ $loop->index + ($result->firstItem()??0) }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->permissions->count()??'0' }}</td>
                                <td>{{ dbToDateTime($item->created_at) }}</td>
                                <td>{{ dbToDateTime($item->updated_at) }}</td>
                                <td>
                                    @can("role-update")
                                    <a href="javascript:void(0)" 
                                       data-role-id="{{ $item->id }}" 
                                       data-role-name="{{ $item->name }}"
                                       class="btn btn-primary role-edit-btn" 
                                       title="Edit">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end mt-3 mb-0 px-3">  
                        @if (!empty($result) && $result->count() > 0)
                            {{ $result->appends($_REQUEST)->render() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include("Main::pages.role.filter")

<!-- Role Edit Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="roleEditOffcanvas" aria-labelledby="roleEditOffcanvasLabel" style="width: 80%; max-width: 1200px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="roleEditOffcanvasLabel">Edit Role & Permissions</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div id="roleEditContent">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading role data...</p>
            </div>
        </div>
    </div>
</div>

<!-- Role Create Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="roleCreateOffcanvas" aria-labelledby="roleCreateOffcanvasLabel" style="width: 80%; max-width: 1200px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="roleCreateOffcanvasLabel">Create New Role & Permissions</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div id="roleCreateContent">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading form...</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    
    // --- ROBUST UI INITIALIZATION FUNCTION ---
    window.initRolePermissionUI = function(containerSelector) {
        console.log('Initializing Role Permission UI for:', containerSelector);
        const container = document.querySelector(containerSelector);
        if (!container) return;

        // 1. Helper for Collapse
        function toggleCollapse(selector, action) {
            // Scope to container
            const elements = container.querySelectorAll(selector);
            
            // Try jQuery (Standard)
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.collapse !== 'undefined') {
                jQuery(elements).collapse(action);
                return;
            }
            
            // Try Bootstrap 5 Vanilla
            if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                elements.forEach(el => {
                    let bsCollapse = bootstrap.Collapse.getInstance(el);
                    if (!bsCollapse) {
                        bsCollapse = new bootstrap.Collapse(el, { toggle: false });
                    }
                    if (action === 'show') bsCollapse.show();
                    else if (action === 'hide') bsCollapse.hide();
                });
                return;
            }

            // Fallback
            elements.forEach(el => {
                if (action === 'show') {
                    el.classList.add('show');
                } else {
                    el.classList.remove('show');
                }
            });
        }

        // 2. Update Selected Count
        function updateSelectedCount() {
            const checkboxes = Array.from(container.querySelectorAll('.permission-checkbox'));
            const count = checkboxes.filter(c => c.checked).length;
            const total = checkboxes.length;
            const countDisplay = container.querySelector('#selectedCount');
            if(countDisplay) {
                countDisplay.textContent = `${count} of ${total} selected`;
            }
        }

        // 3. Module Checkbox Handler
        container.querySelectorAll('.module-checkbox').forEach(moduleCheckbox => {
            // Use 'click' listener for direct user interaction, better than 'change' for bubbling control here
            moduleCheckbox.addEventListener('click', function(event) {
                event.stopPropagation(); // Stop it from toggling the card header
                
                const moduleId = this.getAttribute('data-module-id');
                const isChecked = this.checked;
                
                container.querySelectorAll(`.permission-checkbox[data-module="${moduleId}"]`).forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateSelectedCount();
            });
        });

        // 4. Permission Checkbox Handler
        container.querySelectorAll('.permission-checkbox').forEach(permCheckbox => {
            permCheckbox.addEventListener('change', function() {
                const moduleId = this.getAttribute('data-module');
                const totalInModule = container.querySelectorAll(`.permission-checkbox[data-module="${moduleId}"]`).length;
                const checkedInModule = container.querySelectorAll(`.permission-checkbox[data-module="${moduleId}"]:checked`).length;
                
                const moduleCheckbox = container.querySelector(`#module_${moduleId}`);
                if (moduleCheckbox) {
                    moduleCheckbox.checked = totalInModule === checkedInModule;
                    moduleCheckbox.indeterminate = checkedInModule > 0 && checkedInModule < totalInModule;
                }
                updateSelectedCount();
            });
        });

        // 5. Global Buttons (Scoped to container)
        // Use removeEventListener trick or just clone to wipe old listeners if any
        const selectAllBtn = container.querySelector('#selectAllBtn');
        if (selectAllBtn) {
            const newBtn = selectAllBtn.cloneNode(true);
            selectAllBtn.parentNode.replaceChild(newBtn, selectAllBtn);
            newBtn.addEventListener('click', function() {
                container.querySelectorAll('.permission-checkbox, .module-checkbox').forEach(cb => {
                    cb.checked = true;
                    cb.indeterminate = false;
                });
                updateSelectedCount();
            });
        }

        const deselectAllBtn = container.querySelector('#deselectAllBtn');
        if (deselectAllBtn) {
            const newBtn = deselectAllBtn.cloneNode(true);
            deselectAllBtn.parentNode.replaceChild(newBtn, deselectAllBtn);
            newBtn.addEventListener('click', function() {
                container.querySelectorAll('.permission-checkbox, .module-checkbox').forEach(cb => {
                    cb.checked = false;
                    cb.indeterminate = false;
                });
                updateSelectedCount();
            });
        }

        const expandAllBtn = container.querySelector('#expandAllBtn');
        if (expandAllBtn) {
            const newBtn = expandAllBtn.cloneNode(true);
            expandAllBtn.parentNode.replaceChild(newBtn, expandAllBtn);
            newBtn.addEventListener('click', function(e) {
                e.preventDefault();
                toggleCollapse('.collapse', 'show');
                this.querySelector('i').classList.replace('bx-expand', 'bx-collapse');
                const collapseBtn = container.querySelector('#collapseAllBtn');
                if(collapseBtn) collapseBtn.querySelector('i').classList.replace('bx-collapse', 'bx-expand');
            });
        }

        const collapseAllBtn = container.querySelector('#collapseAllBtn');
        if (collapseAllBtn) {
            const newBtn = collapseAllBtn.cloneNode(true);
            collapseAllBtn.parentNode.replaceChild(newBtn, collapseAllBtn);
            newBtn.addEventListener('click', function(e) {
                e.preventDefault();
                toggleCollapse('.collapse', 'hide');
                this.querySelector('i').classList.replace('bx-collapse', 'bx-expand');
                 const expandBtn = container.querySelector('#expandAllBtn');
                if(expandBtn) expandBtn.querySelector('i').classList.replace('bx-collapse', 'bx-expand');
            });
        }

        // 6. Search Functionality
        const searchInput = container.querySelector('#searchPermissions');
        if (searchInput) {
            const newInput = searchInput.cloneNode(true);
            searchInput.parentNode.replaceChild(newInput, searchInput);
            newInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                let visibleCount = 0;

                const modules = container.querySelectorAll('.permission-module');
                const noResults = container.querySelector('#noResults');

                if (searchTerm === '') {
                    modules.forEach(m => m.style.display = '');
                    container.querySelectorAll('.permission-item').forEach(i => i.style.display = '');
                    if(noResults) noResults.classList.add('d-none');
                    return;
                }

                modules.forEach(module => {
                    const moduleName = module.getAttribute('data-module');
                    let hasVisiblePermission = false;

                    if (moduleName.includes(searchTerm)) {
                        module.style.display = '';
                        module.querySelectorAll('.permission-item').forEach(i => i.style.display = '');
                        // Expand
                        const collapseEl = module.querySelector('.collapse');
                        if(collapseEl) toggleCollapse(`#${collapseEl.id}`, 'show');
                        
                        hasVisiblePermission = true;
                        visibleCount++;
                    } else {
                        module.querySelectorAll('.permission-item').forEach(item => {
                            const permissionName = item.getAttribute('data-permission');
                            if (permissionName.includes(searchTerm)) {
                                item.style.display = '';
                                hasVisiblePermission = true;
                            } else {
                                item.style.display = 'none';
                            }
                        });

                        if (hasVisiblePermission) {
                            module.style.display = '';
                            const collapseEl = module.querySelector('.collapse');
                            if(collapseEl) toggleCollapse(`#${collapseEl.id}`, 'show');
                            visibleCount++;
                        } else {
                            module.style.display = 'none';
                        }
                    }
                });

                if (noResults) {
                    if (visibleCount === 0) noResults.classList.remove('d-none');
                    else noResults.classList.add('d-none');
                }
            });
        }
        
        // Initial Count Update
        updateSelectedCount();
    };

    // --- AJAX HANDLERS ---

    // Handle role create button click
    $(document).on('click', '.role-create-btn', function() {
        const offcanvas = new bootstrap.Offcanvas(document.getElementById('roleCreateOffcanvas'));
        offcanvas.show();
        
        $('#roleCreateContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading form...</p></div>');

        $.ajax({
            url: '{{ route("dashboard.role.has-permission.create") }}',
            type: 'GET',
            success: function(response) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(response, 'text/html');
                // We grab the .card-body content, assuming the view structure
                const formContent = doc.querySelector('.card-body');
                
                if (formContent) {
                    $('#roleCreateContent').html(formContent.innerHTML);
                    // Initialize the New Logic
                    initRolePermissionUI('#roleCreateContent');
                } else {
                    $('#roleCreateContent').html('<div class="alert alert-danger">Failed to load form content</div>');
                }
            },
            error: function() {
                $('#roleCreateContent').html('<div class="alert alert-danger">Error loading form. Please try again.</div>');
            }
        });
    });
    
    // Handle role edit button click
    $(document).on('click', '.role-edit-btn', function() {
        const roleId = $(this).data('role-id');
        const offcanvas = new bootstrap.Offcanvas(document.getElementById('roleEditOffcanvas'));
        offcanvas.show();
        
        $('#roleEditContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading data...</p></div>');

        $.ajax({
            url: '{{ route("dashboard.role.has-permission.edit", ":id") }}'.replace(':id', roleId),
            type: 'GET',
            success: function(response) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(response, 'text/html');
                const formContent = doc.querySelector('.card-body');
                
                if (formContent) {
                    $('#roleEditContent').html(formContent.innerHTML);
                    // Initialize the New Logic
                    initRolePermissionUI('#roleEditContent');
                } else {
                    $('#roleEditContent').html('<div class="alert alert-danger">Failed to load role data</div>');
                }
            },
            error: function() {
                $('#roleEditContent').html('<div class="alert alert-danger">Error loading role data. Please try again.</div>');
            }
        });
    });
    
    // Handle form submission via AJAX (for both create and edit)
    $(document).on('submit', '#roleEditContent form, #roleCreateContent form', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = form.serialize();
        const submitBtn = form.find('button[type="submit"]');
        const isCreate = form.closest('#roleCreateContent').length > 0;
        
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + (isCreate ? 'Creating...' : 'Updating...'));
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: isCreate ? 'Role created successfully' : 'Role updated successfully',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Close offcanvas
                try {
                    const offcanvasId = isCreate ? 'roleCreateOffcanvas' : 'roleEditOffcanvas';
                    const offcanvasEl = document.getElementById(offcanvasId);
                    const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
                    offcanvas.hide();
                } catch(e) { console.error(e); }
                
                // Reload page to show updated data
                setTimeout(function() {
                    location.reload();
                }, 2000);
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> ' + (isCreate ? 'Create Role' : 'Update Role'));
                
                let errorMessage = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage
                });
            }
        });
    });
});
</script>
@endpush