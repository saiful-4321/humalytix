@extends('Main::layouts.app')

@section('title', '360° Appraisals')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">360° Performance Appraisals</h4>
            <p class="text-muted mb-0">Multi-source feedback and appraisals</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#createAppraisalOffcanvas">
            <i class="bx bx-plus"></i> New Appraisal
        </button>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
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
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search appraisals..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary me-2"><i class="bx bx-search"></i></button>
                    <a href="{{ route('hrm.appraisals-360.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Appraisals List -->
    <div class="card">
        <div class="card-body">
            @if($appraisals->isEmpty())
                <div class="text-center py-5">
                    <i class="bx bx-user-check display-1 text-muted"></i>
                    <p class="text-muted mt-3">No appraisals found</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Appraisal</th>
                                <th>Employee</th>
                                <th>Period</th>
                                <th>Reviewers</th>
                                <th>Progress</th>
                                <th>Score</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appraisals as $appraisal)
                            <tr>
                                <td><strong>{{ $appraisal->appraisal_name }}</strong></td>
                                <td>{{ $appraisal->employee->full_name }}</td>
                                <td>{{ $appraisal->review_period }}</td>
                                <td>{{ $appraisal->reviewers->count() }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 6px; width: 80px;">
                                            <div class="progress-bar" style="width: {{ $appraisal->getCompletionPercentage() }}%"></div>
                                        </div>
                                        <small>{{ $appraisal->getCompletionPercentage() }}%</small>
                                    </div>
                                </td>
                                <td>
                                    @if($appraisal->overall_score)
                                        <span class="badge bg-primary">{{ $appraisal->overall_score }}/10</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $appraisal->status == 'completed' ? 'success' : ($appraisal->status == 'in_progress' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $appraisal->status)) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('hrm.appraisals-360.show', $appraisal) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger delete-appraisal" data-id="{{ $appraisal->id }}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $appraisals->links() }}</div>
            @endif
        </div>
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
