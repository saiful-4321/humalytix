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
    // Handle role create button click
    $('.role-create-btn').on('click', function() {
        const offcanvas = new bootstrap.Offcanvas(document.getElementById('roleCreateOffcanvas'));
        offcanvas.show();
        
        // Load role create form via AJAX
        $.ajax({
            url: '{{ route("dashboard.role.has-permission.create") }}',
            type: 'GET',
            success: function(response) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(response, 'text/html');
                const formContent = doc.querySelector('.card-body');
                
                if (formContent) {
                    $('#roleCreateContent').html(formContent.innerHTML);
                    initializeCheckboxes('#roleCreateContent');
                } else {
                    $('#roleCreateContent').html('<div class="alert alert-danger">Failed to load form</div>');
                }
            },
            error: function() {
                $('#roleCreateContent').html('<div class="alert alert-danger">Error loading form. Please try again.</div>');
            }
        });
    });
    
    // Handle role edit button click
    $('.role-edit-btn').on('click', function() {
        const roleId = $(this).data('role-id');
        const roleName = $(this).data('role-name');
        
        const offcanvas = new bootstrap.Offcanvas(document.getElementById('roleEditOffcanvas'));
        offcanvas.show();
        
        // Load role edit form via AJAX
        $.ajax({
            url: '{{ route("dashboard.role.has-permission.edit", ":id") }}'.replace(':id', roleId),
            type: 'GET',
            success: function(response) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(response, 'text/html');
                const formContent = doc.querySelector('.card-body');
                
                if (formContent) {
                    $('#roleEditContent').html(formContent.innerHTML);
                    initializeCheckboxes('#roleEditContent');
                } else {
                    $('#roleEditContent').html('<div class="alert alert-danger">Failed to load role data</div>');
                }
            },
            error: function() {
                $('#roleEditContent').html('<div class="alert alert-danger">Error loading role data. Please try again.</div>');
            }
        });
    });
    
    function initializeCheckboxes(container) {
        $(container + " #check_all").off('change').on('change', function(){ 
            $(container + " input:checkbox").prop('checked', $(this).prop("checked"));
        });

        $(container + ' input:checkbox').off('change').on('change', function() {
            if($(this).prop("checked") == false) { 
                $(container + " #check_all").prop('checked', false);
            } 

            if ($(this).hasClass('checkboxHeader') && $(this).prop("checked") == true) {
                $(this).closest('.checkboxGroup').find("input:checkbox").prop('checked', true);
            } else if ($(this).hasClass('checkboxHeader') && $(this).prop("checked") == false) {
                $(this).closest('.checkboxGroup').find("input:checkbox").prop('checked', false);
            }   

            $(container + ' .checkboxGroup').each(function(){
                if ($(this).find('input:checkbox:checked').length == $(this).find('input:checkbox').length-1) {
                    $(this).find('input.checkboxHeader:checkbox').prop('checked', !$(this).find('input.checkboxHeader:checkbox').prop('checked'));
                } 
            });

            if ($(container + ' input:checkbox:checked').length == $(container + ' input:checkbox').length-1) {
                $(container + " #check_all").prop('checked', true);
            }
        });

        // Initial selection
        $(container + ' .checkboxGroup').each(function(){
            if ($(this).find('input:checkbox:checked').length == $(this).find('input:checkbox').length-1) {
                $(this).find('input:checkbox').prop('checked', true);
            }
        });

        if ($(container + ' input:checkbox:checked').length == $(container + ' input:checkbox').length-1) {
            $(container + " #check_all").prop('checked', true);
        }
    }
    
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
                const offcanvasId = isCreate ? 'roleCreateOffcanvas' : 'roleEditOffcanvas';
                const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById(offcanvasId));
                offcanvas.hide();
                
                // Reload page to show updated data
                setTimeout(function() {
                    location.reload();
                }, 2000);
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> ' + (isCreate ? 'Create' : 'Update'));
                
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