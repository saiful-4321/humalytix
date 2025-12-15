@extends('Main::layouts.app')

@section('title', 'Performance Improvement Plans')

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Performance Improvement Plans</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Performance</li>
                <li class="breadcrumb-item active">PIPs</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                 <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">PIPs List</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-soft-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
                        </button>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#createPipOffcanvas">
                            <i class="mdi mdi-plus me-1"></i> Initiate PIP
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Reason</th>
                                <th>Employee</th>
                                <th>Manager</th>
                                <th>Timeline</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pips as $pip)
                            <tr>
                                <td>
                                    <h6 class="mb-0 font-size-14">{{ $pip->title ?? 'Improvement Plan' }}</h6>
                                    <small class="text-muted">{{ Str::limit($pip->reason, 40) }}</small>
                                </td>
                                <td>{{ $pip->employee->full_name }}</td>
                                <td>{{ $pip->manager->full_name ?? 'N/A' }}</td>
                                <td>
                                    {{ $pip->start_date->format('M d') }} - {{ $pip->end_date->format('M d, Y') }}
                                    @if($pip->isOverdue())
                                        <span class="text-danger fw-bold ms-1">Overdue</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-soft-{{ $pip->status == 'active' ? 'primary' : ($pip->status == 'successful' ? 'success' : 'danger') }} text-{{ $pip->status == 'active' ? 'primary' : ($pip->status == 'successful' ? 'success' : 'danger') }}">
                                        {{ ucfirst($pip->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('hrm.pips.show', $pip->id) }}" class="btn btn-sm btn-soft-primary" title="Details">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                        <button class="btn btn-sm btn-soft-primary edit-pip" 
                                            data-id="{{ $pip->id }}"
                                            data-bs-toggle="offcanvas" 
                                            data-bs-target="#editPipOffcanvas"
                                            title="Edit">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </button>
                                        <button class="btn btn-sm btn-soft-danger delete-pip" data-id="{{ $pip->id }}" title="Delete">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-trending-up font-size-24 d-block mb-2"></i>
                                    No active PIPs found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($pips->count())
            <div class="card-footer bg-transparent border-top">
                <div class="d-flex justify-content-end">
                    {{ $pips->links() }}
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
        <form method="GET" action="{{ route('hrm.pips.index') }}">
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
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="successful" {{ request('status') == 'successful' ? 'selected' : '' }}>Successful</option>
                    <option value="unsuccessful" {{ request('status') == 'unsuccessful' ? 'selected' : '' }}>Unsuccessful</option>
                    <option value="extended" {{ request('status') == 'extended' ? 'selected' : '' }}>Extended</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search PIPs..." value="{{ request('search') }}">
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('hrm.pips.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Create PIP Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="createPipOffcanvas" style="width: 550px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Initiate PIP</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.pips.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Employee <span class="text-danger">*</span></label>
                <select name="employee_id" class="form-select" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Primary Reason <span class="text-danger">*</span></label>
                <textarea name="reason" class="form-control" rows="3" required placeholder="Why is this PIP being initiated?"></textarea>
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
                <label class="form-label">Goals / Expected Outcomes</label>
                <textarea name="goals" class="form-control" rows="4" placeholder="What needs to be achieved..."></textarea>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Create Plan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit PIP Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="editPipOffcanvas" style="width: 550px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Edit PIP</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="editPipForm">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <input type="text" id="edit_employee_name" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Primary Reason <span class="text-danger">*</span></label>
                <textarea name="reason" class="form-control" rows="3" required></textarea>
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
                <label class="form-label">Review Date</label>
                <input type="date" name="review_date" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="active">Active</option>
                    <option value="successful">Successful</option>
                    <option value="unsuccessful">Unsuccessful</option>
                    <option value="extended">Extended</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Goals / Success Criteria</label>
                <textarea name="success_criteria" class="form-control" rows="4"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Outcomes (for closing)</label>
                <textarea name="outcomes" class="form-control" rows="2"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Final Review Notes</label>
                <textarea name="final_review" class="form-control" rows="2"></textarea>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Update Plan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
.ready(function() {
    .on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("hrm.pips.store") }}',
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

    // Edit PIP
    .on('click', function() {
        const id = .data('id');
        $.get(`/hrm/pips/${id}`, function(response) {
            if(response.success) {
                const pip = response.data;
                const form = ;
                
                form.find('#edit_employee_name').val(pip.employee ? pip.employee.full_name : 'Unknown');
                form.find('[name="title"]').val(pip.title);
                form.find('[name="reason"]').val(pip.reason);
                form.find('[name="status"]').val(pip.status);
                form.find('[name="success_criteria"]').val(pip.success_criteria);
                form.find('[name="outcomes"]').val(pip.outcomes);
                form.find('[name="final_review"]').val(pip.final_review);
                
                // Format dates
                const startDate = new Date(pip.start_date).toISOString().split('T')[0];
                const endDate = new Date(pip.end_date).toISOString().split('T')[0];
                
                form.find('[name="start_date"]').val(startDate);
                form.find('[name="end_date"]').val(endDate);

                if(pip.review_date) {
                    const reviewDate = new Date(pip.review_date).toISOString().split('T')[0];
                    form.find('[name="review_date"]').val(reviewDate);
                }

                // Set form action
                form.attr('action', `/hrm/pips/${id}`);
            }
        });
    });

    .on('submit', function(e) {
        e.preventDefault();
        const url = .attr('action');
        
        $.ajax({
            url: url,
            method: 'PUT',
            data: .serialize(),
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                location.reload();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'An error occurred', 'error');
            }
        });
    });

    .on('click', function() {
        const id = .data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This PIP will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/hrm/pips/${id}`,
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

@push('scripts')
<script>
$(document).ready(function() {
    $('#createPipForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("hrm.pips.store") }}',
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

    // Edit PIP
    $('.edit-pip').on('click', function() {
        const id = $(this).data('id');
        $.get(`/hrm/pips/${id}`, function(response) {
            if(response.success) {
                const pip = response.data;
                const form = $('#editPipForm');
                
                form.find('#edit_employee_name').val(pip.employee ? pip.employee.full_name : 'Unknown');
                form.find('[name="title"]').val(pip.title);
                form.find('[name="reason"]').val(pip.reason);
                form.find('[name="status"]').val(pip.status);
                form.find('[name="success_criteria"]').val(pip.success_criteria);
                form.find('[name="outcomes"]').val(pip.outcomes);
                form.find('[name="final_review"]').val(pip.final_review);
                
                // Format dates
                const startDate = new Date(pip.start_date).toISOString().split('T')[0];
                const endDate = new Date(pip.end_date).toISOString().split('T')[0];
                
                form.find('[name="start_date"]').val(startDate);
                form.find('[name="end_date"]').val(endDate);

                if(pip.review_date) {
                    const reviewDate = new Date(pip.review_date).toISOString().split('T')[0];
                    form.find('[name="review_date"]').val(reviewDate);
                }

                // Set form action
                form.attr('action', `/hrm/pips/${id}`);
            }
        });
    });

    $('#editPipForm').on('submit', function(e) {
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

    $('.delete-pip').on('click', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This PIP will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/hrm/pips/${id}`,
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
