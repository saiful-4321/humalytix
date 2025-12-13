@extends('Main::layouts.app')

@section('title', 'Performance Improvement Plans')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Performance Improvement Plans (PIP)</h4>
            <p class="text-muted mb-0">Manage employee performance improvement tracking</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#createPipOffcanvas">
            <i class="bx bx-plus"></i> Create PIP
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
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                        <option value="extended">Extended</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search reason..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary me-2"><i class="bx bx-search"></i></button>
                    <a href="{{ route('hrm.pips.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- PIPs List -->
    <div class="card">
        <div class="card-body">
            @if($pips->isEmpty())
                <div class="text-center py-5">
                    <i class="bx bx-line-chart-down display-1 text-muted"></i>
                    <p class="text-muted mt-3">No active PIPs found</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th>Reason</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pips as $pip)
                            <tr>
                                <td>
                                    <strong>{{ $pip->employee->full_name }}</strong>
                                    <br><small class="text-muted">{{ $pip->employee->department->name ?? '' }}</small>
                                </td>
                                <td>{{ Str::limit($pip->reason, 50) }}</td>
                                <td>{{ $pip->start_date->format('M d, Y') }}</td>
                                <td>{{ $pip->end_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $pip->status == 'active' ? 'warning' : ($pip->status == 'completed' ? 'success' : 'danger') }}">
                                        {{ ucfirst($pip->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('hrm.pips.show', $pip) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $pips->links() }}</div>
            @endif
        </div>
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
@endsection
