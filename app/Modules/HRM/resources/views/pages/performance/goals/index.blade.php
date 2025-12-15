@extends('Main::layouts.app')

@section('title', 'Performance Goals')

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Performance Goals</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Performance</li>
                <li class="breadcrumb-item active">Goals</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                 <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Goals List</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-soft-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
                        </button>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#createGoalOffcanvas">
                            <i class="mdi mdi-plus me-1"></i> Add Goal
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Goal</th>
                                <th>Employee</th>
                                <th>Timeline</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($goals as $goal)
                            <tr>
                                <td>
                                    <h6 class="mb-0 font-size-14">{{ $goal->title }}</h6>
                                    @if($goal->kpi)
                                        <small class="text-muted"><i class="bx bx-target-lock"></i> {{ $goal->kpi->name }}</small>
                                    @endif
                                </td>
                                <td>{{ $goal->employee->full_name }}</td>
                                <td>
                                    <small>
                                        {{ $goal->start_date->format('M d') }} - {{ $goal->due_date->format('M d, Y') }}
                                        @if($goal->isOverdue())
                                            <span class="text-danger fw-bold ms-1">Overdue</span>
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-soft-{{ $goal->priority == 'high' || $goal->priority == 'critical' ? 'danger' : ($goal->priority == 'medium' ? 'warning' : 'info') }} text-{{ $goal->priority == 'high' || $goal->priority == 'critical' ? 'danger' : ($goal->priority == 'medium' ? 'warning' : 'info') }}">
                                        {{ ucfirst($goal->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-soft-{{ $goal->status == 'completed' ? 'success' : ($goal->status == 'in_progress' ? 'primary' : 'secondary') }} text-{{ $goal->status == 'completed' ? 'success' : ($goal->status == 'in_progress' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $goal->status)) }}
                                    </span>
                                </td>
                                <td style="width: 150px;">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $goal->progress }}%"></div>
                                    </div>
                                    <small>{{ $goal->progress }}%</small>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('hrm.performance-goals.show', $goal) }}" class="btn btn-sm btn-soft-primary" title="Details">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                        <button class="btn btn-sm btn-soft-primary edit-goal" 
                                            data-id="{{ $goal->id }}"
                                            data-bs-toggle="offcanvas" 
                                            data-bs-target="#editGoalOffcanvas"
                                            title="Edit">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </button>
                                        <button class="btn btn-sm btn-soft-danger delete-goal" data-id="{{ $goal->id }}" title="Delete">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-trophy-variant-outline font-size-24 d-block mb-2"></i>
                                    No active goals found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($goals->count())
            <div class="card-footer bg-transparent border-top">
                <div class="d-flex justify-content-end">
                    {{ $goals->links() }}
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
        <form method="GET" action="{{ route('hrm.performance-goals.index') }}">
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
                    <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>Not Started</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-select">
                    <option value="">All Priorities</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('hrm.performance-goals.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Create Goal Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="createGoalOffcanvas" style="width: 600px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Create Performance Goal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="createGoalForm">
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
                <label class="form-label">Link to KPI (Optional)</label>
                <select name="kpi_id" class="form-select">
                    <option value="">-- Select KPI --</option>
                    @foreach($kpis as $kpi)
                        <option value="{{ $kpi->id }}">{{ $kpi->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Goal Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" placeholder="E.g., Increase sales by 20%" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Describe the goal..."></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Due Date <span class="text-danger">*</span></label>
                    <input type="date" name="due_date" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Priority <span class="text-danger">*</span></label>
                    <select name="priority" class="form-select" required>
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="not_started" selected>Not Started</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Initial Progress (%)</label>
                <input type="number" name="progress" class="form-control" min="0" max="100" value="0">
            </div>

            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Create Goal</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Goal Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="editGoalOffcanvas" style="width: 600px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Edit Performance Goal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="editGoalForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="goal_id" id="edit_goal_id">
            
            <div class="mb-3">
                <label class="form-label">Employee <span class="text-danger">*</span></label>
                <select name="employee_id" id="edit_employee_id" class="form-select" required>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Link to KPI</label>
                <select name="kpi_id" id="edit_kpi_id" class="form-select">
                    <option value="">-- Select KPI --</option>
                    @foreach($kpis as $kpi)
                        <option value="{{ $kpi->id }}">{{ $kpi->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Goal Title <span class="text-danger">*</span></label>
                <input type="text" name="title" id="edit_title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" id="edit_start_date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Due Date <span class="text-danger">*</span></label>
                    <input type="date" name="due_date" id="edit_due_date" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Priority <span class="text-danger">*</span></label>
                    <select name="priority" id="edit_priority" class="form-select" required>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" id="edit_status" class="form-select" required>
                        <option value="not_started">Not Started</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Progress (%)</label>
                <input type="number" name="progress" id="edit_progress" class="form-control" min="0" max="100">
            </div>

            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Update Goal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#createGoalForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("hrm.performance-goals.store") }}',
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

    $('.edit-goal').on('click', function() {
        const id = $(this).data('id');
        $.get(`/hrm/performance-goals/${id}`, function(data) {
            $('#edit_goal_id').val(data.id);
            $('#edit_employee_id').val(data.employee_id);
            $('#edit_kpi_id').val(data.kpi_id);
            $('#edit_title').val(data.title);
            $('#edit_description').val(data.description);
            $('#edit_start_date').val(data.start_date);
            $('#edit_due_date').val(data.due_date);
            $('#edit_priority').val(data.priority);
            $('#edit_status').val(data.status);
            $('#edit_progress').val(data.progress);
            $('#edit_notes').val(data.notes);
        });
    });

    $('#editGoalForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_goal_id').val();
        $.ajax({
            url: `/hrm/performance-goals/${id}`,
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

    $('.delete-goal').on('click', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This goal will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/hrm/performance-goals/${id}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        location.reload();
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to delete goal', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
