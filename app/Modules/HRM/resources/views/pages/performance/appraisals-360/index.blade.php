@extends('Main::layouts.app')

@section('title', '360° Appraisals')

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>360° Appraisals</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Performance</li>
                <li class="breadcrumb-item active">360° Appraisals</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                 <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Appraisals List</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-soft-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
                        </button>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#createAppraisalOffcanvas">
                            <i class="mdi mdi-plus me-1"></i> New Appraisal
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Appraisal Name</th>
                                <th>Employee</th>
                                <th>Review Period</th>
                                <th>Timeline</th>
                                <th>Status</th>
                                <th>Score</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appraisals as $appraisal)
                            <tr>
                                <td>
                                    <h6 class="mb-0 font-size-14">{{ $appraisal->appraisal_name }}</h6>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-2">
                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12">
                                                {{ substr($appraisal->employee->first_name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 font-size-14">{{ $appraisal->employee->full_name }}</h6>
                                            <small class="text-muted">{{ $appraisal->employee->department->name ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $appraisal->review_period }}</td>
                                <td>
                                    <small>{{ $appraisal->start_date->format('M d') }} - {{ $appraisal->end_date->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-soft-{{ $appraisal->status == 'completed' ? 'success' : ($appraisal->status == 'in_progress' ? 'primary' : 'secondary') }} text-{{ $appraisal->status == 'completed' ? 'success' : ($appraisal->status == 'in_progress' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $appraisal->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($appraisal->overall_score)
                                        <span class="badge bg-dark">{{ $appraisal->overall_score }} / 5</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('hrm.appraisals-360.show', $appraisal->id) }}" class="btn btn-sm btn-soft-primary" title="Details">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                        <button class="btn btn-sm btn-soft-primary edit-appraisal" 
                                            data-id="{{ $appraisal->id }}"
                                            data-bs-toggle="offcanvas" 
                                            data-bs-target="#editAppraisalOffcanvas"
                                            title="Edit">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </button>
                                        <button class="btn btn-sm btn-soft-danger delete-appraisal" data-id="{{ $appraisal->id }}" title="Delete">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-briefcase-check-outline font-size-24 d-block mb-2"></i>
                                    No appraisal cycles found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($appraisals->count())
            <div class="card-footer bg-transparent border-top">
                <div class="d-flex justify-content-end">
                    {{ $appraisals->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Filter Options</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form method="GET" action="{{ route('hrm.appraisals-360.index') }}">
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-select">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Review Period</label>
                <input type="text" name="review_period" class="form-control" placeholder="e.g. Q1 2025" value="{{ request('review_period') }}">
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('hrm.appraisals-360.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Create Appraisal Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="createAppraisalOffcanvas" style="width: 600px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Create 360° Appraisal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="createAppraisalForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Employee <span class="text-danger">*</span></label>
                <select name="employee_id" class="form-select" required>
                    <option value="">-- Select Employee --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Appraisal Name <span class="text-danger">*</span></label>
                <input type="text" name="appraisal_name" class="form-control" placeholder="E.g., Annual Review 2024" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Review Period <span class="text-danger">*</span></label>
                <input type="text" name="review_period" class="form-control" placeholder="E.g., Q4 2024" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="draft" selected>Draft</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <div class="alert alert-info">
                <small><i class="bx bx-info-circle"></i> After creating, you can add reviewers and manage feedback</small>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Appraisal Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="editAppraisalOffcanvas" style="width: 600px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Edit Appraisal Cycle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="editAppraisalForm">
            @csrf
            
            <!-- Employee (Read-only as changing subject mid-cycle is rare/problematic) -->
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <input type="text" id="edit_employee_name" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Appraisal Name <span class="text-danger">*</span></label>
                <input type="text" name="appraisal_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Review Period <span class="text-danger">*</span></label>
                <input type="text" name="review_period" class="form-control" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="draft">Draft</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Summary / Notes</label>
                <textarea name="summary" class="form-control" rows="3" placeholder="Optional summary or notes..."></textarea>
            </div>

            <div class="d-flex gap-2 justify-content-end mt-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Update Appraisal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#createAppraisalForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("hrm.appraisals-360.store") }}',
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                location.reload();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'An error occurred', 'error');
            }
        });
    });

    // Edit Appraisal
    $('.edit-appraisal').on('click', function() {
        const id = $(this).data('id');
        $.get(`/hrm/appraisals-360/${id}`, function(response) {
            if(response.success) {
                const appraisal = response.data;
                const form = $('#editAppraisalForm');
                
                form.find('#edit_employee_name').val(appraisal.employee ? appraisal.employee.full_name : 'Unknown');
                form.find('[name="appraisal_name"]').val(appraisal.appraisal_name);
                form.find('[name="review_period"]').val(appraisal.review_period);
                form.find('[name="status"]').val(appraisal.status);
                form.find('[name="summary"]').val(appraisal.summary);
                
                // Format dates
                const startDate = new Date(appraisal.start_date).toISOString().split('T')[0];
                const endDate = new Date(appraisal.end_date).toISOString().split('T')[0];
                
                form.find('[name="start_date"]').val(startDate);
                form.find('[name="end_date"]').val(endDate);

                // Set form action
                form.attr('action', `/hrm/appraisals-360/${id}`);
            }
        });
    });

    $('#editAppraisalForm').on('submit', function(e) {
        e.preventDefault();
        const url = $(this).attr('action');
        
        $.ajax({
            url: url,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                location.reload();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'An error occurred', 'error');
            }
        });
    });


    $('.delete-appraisal').on('click', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This appraisal will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/hrm/appraisals-360/${id}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        location.reload();
                    }
                });
            }
        });
    });
});
</script>
@endpush
