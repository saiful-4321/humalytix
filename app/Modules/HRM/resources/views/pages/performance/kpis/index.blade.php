@extends('Main::layouts.app')

@section('title', 'KPIs / KRAs')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">KPIs / KRAs Management</h4>
            <p class="text-muted mb-0">Manage Key Performance Indicators and Key Result Areas</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#createKpiOffcanvas">
            <i class="bx bx-plus"></i> Add KPI/KRA
        </button>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('hrm.kpis.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="kpi" {{ request('type') == 'kpi' ? 'selected' : '' }}>KPI</option>
                        <option value="kra" {{ request('type') == 'kra' ? 'selected' : '' }}>KRA</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search KPI/KRA..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary me-2"><i class="bx bx-search"></i> Filter</button>
                    <a href="{{ route('hrm.kpis.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- KPIs List -->
    <div class="card">
        <div class="card-body">
            @if($kpis->isEmpty())
                <div class="text-center py-5">
                    <i class="bx bx-target-lock display-1 text-muted"></i>
                    <p class="text-muted mt-3">No KPIs/KRAs found</p>
                    <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#createKpiOffcanvas">
                        <i class="bx bx-plus"></i> Create First KPI/KRA
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Department</th>
                                <th>Target</th>
                                <th>Frequency</th>
                                <th>Weightage</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kpis as $kpi)
                            <tr>
                                <td>
                                    <strong>{{ $kpi->name }}</strong>
                                    @if($kpi->description)
                                        <br><small class="text-muted">{{ Str::limit($kpi->description, 50) }}</small>
                                    @endif
                                </td>
                                <td><span class="badge bg-{{ $kpi->type == 'kpi' ? 'primary' : 'info' }}">{{ strtoupper($kpi->type) }}</span></td>
                                <td>{{ $kpi->department->name ?? 'N/A' }}</td>
                                <td>{{ $kpi->target_value }} {{ $kpi->measurement_unit }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($kpi->frequency) }}</span></td>
                                <td><span class="badge bg-dark">{{ $kpi->weightage }}%</span></td>
                                <td>
                                    <span class="badge bg-{{ $kpi->is_active ? 'success' : 'danger' }}">
                                        {{ $kpi->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary edit-kpi" 
                                            data-id="{{ $kpi->id }}"
                                            data-bs-toggle="offcanvas" 
                                            data-bs-target="#editKpiOffcanvas">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-kpi" data-id="{{ $kpi->id }}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $kpis->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create KPI Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="createKpiOffcanvas" style="width: 600px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Create KPI/KRA</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="createKpiForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required>
                    <option value="kpi">KPI (Key Performance Indicator)</option>
                    <option value="kra">KRA (Key Result Area)</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="E.g., Sales Revenue Growth" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Describe the KPI/KRA..."></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select">
                    <option value="">-- Select Department --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Target Value</label>
                    <input type="number" name="target_value" class="form-control" step="0.01" placeholder="100">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Measurement Unit</label>
                    <input type="text" name="measurement_unit" class="form-control" placeholder="%, Units, $">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Frequency <span class="text-danger">*</span></label>
                    <select name="frequency" class="form-select" required>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="annually">Annually</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Weightage (%) <span class="text-danger">*</span></label>
                    <input type="number" name="weightage" class="form-control" min="0" max="100" value="25" required>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save"></i> Create KPI/KRA
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit KPI Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="editKpiOffcanvas" style="width: 600px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Edit KPI/KRA</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="editKpiForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="kpi_id" id="edit_kpi_id">
            
            <!-- Same fields as create form -->
            <div class="mb-3">
                <label class="form-label">Type <span class="text-danger">*</span></label>
                <select name="type" id="edit_type" class="form-select" required>
                    <option value="kpi">KPI (Key Performance Indicator)</option>
                    <option value="kra">KRA (Key Result Area)</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Department</label>
                <select name="department_id" id="edit_department_id" class="form-select">
                    <option value="">-- Select Department --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Target Value</label>
                    <input type="number" name="target_value" id="edit_target_value" class="form-control" step="0.01">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Measurement Unit</label>
                    <input type="text" name="measurement_unit" id="edit_measurement_unit" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Frequency <span class="text-danger">*</span></label>
                    <select name="frequency" id="edit_frequency" class="form-select" required>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="annually">Annually</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Weightage (%) <span class="text-danger">*</span></label>
                    <input type="number" name="weightage" id="edit_weightage" class="form-control" min="0" max="100" required>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active">
                    <label class="form-check-label" for="edit_is_active">Active</label>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save"></i> Update KPI/KRA
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Create KPI
    $('#createKpiForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.set('is_active', $('#isActive').is(':checked') ? 1 : 0);
        
        $.ajax({
            url: '{{ route("hrm.kpis.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                location.reload();
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                let message = xhr.responseJSON?.message || 'An error occurred';
                if (errors) {
                    message = Object.values(errors).flat().join('<br>');
                }
                Swal.fire('Error!', message, 'error');
            }
        });
    });

    // Load KPI data for editing
    $('.edit-kpi').on('click', function() {
        const kpiId = $(this).data('id');
        
        $.ajax({
            url: `/hrm/kpis/${kpiId}`,
            method: 'GET',
            success: function(kpi) {
                $('#edit_kpi_id').val(kpi.id);
                $('#edit_type').val(kpi.type);
                $('#edit_name').val(kpi.name);
                $('#edit_description').val(kpi.description);
                $('#edit_department_id').val(kpi.department_id);
                $('#edit_target_value').val(kpi.target_value);
                $('#edit_measurement_unit').val(kpi.measurement_unit);
                $('#edit_frequency').val(kpi.frequency);
                $('#edit_weightage').val(kpi.weightage);
                $('#edit_is_active').prop('checked', kpi.is_active);
            },
            error: function() {
                Swal.fire('Error!', 'Failed to load KPI data', 'error');
            }
        });
    });

    // Update KPI
    $('#editKpiForm').on('submit', function(e) {
        e.preventDefault();
        
        const kpiId = $('#edit_kpi_id').val();
        const formData = new FormData(this);
        formData.set('is_active', $('#edit_is_active').is(':checked') ? 1 : 0);
        
        $.ajax({
            url: `/hrm/kpis/${kpiId}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                location.reload();
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                let message = xhr.responseJSON?.message || 'An error occurred';
                if (errors) {
                    message = Object.values(errors).flat().join('<br>');
                }
                Swal.fire('Error!', message, 'error');
            }
        });
    });

    // Delete KPI
    $('.delete-kpi').on('click', function() {
        const kpiId = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This KPI/KRA will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/hrm/kpis/${kpiId}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        location.reload();
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to delete KPI/KRA', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
