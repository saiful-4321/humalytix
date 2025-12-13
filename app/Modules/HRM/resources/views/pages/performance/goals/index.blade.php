@extends('Main::layouts.app')

@section('title', 'Performance Goals')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Performance Goals</h4>
            <p class="text-muted mb-0">Track and manage employee performance goals</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#createGoalOffcanvas">
            <i class="bx bx-plus"></i> Add Goal
        </button>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
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
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>Not Started</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="">All Priorities</option>
                        <option value="critical">Critical</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search goals..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary me-2"><i class="bx bx-search"></i> Filter</button>
                    <a href="{{ route('hrm.performance-goals.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Goals List -->
    <div class="card">
        <div class="card-body">
            @if($goals->isEmpty())
                <div class="text-center py-5">
                    <i class="bx bx-trophy display-1 text-muted"></i>
                    <p class="text-muted mt-3">No performance goals found</p>
                    <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#createGoalOffcanvas">
                        <i class="bx bx-plus"></i> Create First Goal
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Goal</th>
                                <th>Employee</th>
                                <th>Priority</th>
                                <th>Progress</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($goals as $goal)
                            <tr class="{{ $goal->isOverdue() ? 'table-danger' : '' }}">
                                <td>
                                    <strong>{{ $goal->title }}</strong>
                                    @if($goal->kpi)
                                        <br><small class="text-muted"><i class="bx bx-link"></i> {{ $goal->kpi->name }}</small>
                                    @endif
                                </td>
                                <td>{{ $goal->employee->full_name }}</td>
                                <td>{!! $goal->priority_badge !!}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar" style="width: {{ $goal->progress }}%"></div>
                                        </div>
                                        <small>{{ $goal->progress }}%</small>
                                    </div>
                                </td>
                                <td>
                                    {{ $goal->due_date->format('M d, Y') }}
                                    @if($goal->isOverdue())
                                        <br><small class="text-danger">Overdue</small>
                                    @endif
                                </td>
                                <td>{!! $goal->status_badge !!}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary edit-goal" 
                                            data-id="{{ $goal->id }}"
                                            data-bs-toggle="offcanvas" 
                                            data-bs-target="#editGoalOffcanvas">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-goal" data-id="{{ $goal->id }}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $goals->links() }}</div>
            @endif
        </div>
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
