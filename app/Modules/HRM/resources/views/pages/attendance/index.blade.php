@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Attendance Management</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Attendance</li>
            </ul>
        </div>
    </div>
</div>
@include("Main::widgets.message.sweet-alert")
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                            <i class="bx bx-filter"></i> Filter
                        </button>
                        <a href="{{ route('hrm.attendance.report') }}" class="btn btn-info">
                            <i class="bx bx-bar-chart"></i> Report
                        </a>
                    </div>
                    <div>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#checkInModal">
                            <i class="bx bx-log-in"></i> Check In
                        </button>
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#checkOutModal">
                            <i class="bx bx-log-out"></i> Check Out
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Hours</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $att)
                            <tr>
                                <td>{{ $att->date->format('d M, Y') }}</td>
                                <td>{{ $att->employee->full_name }}</td>
                                <td>{{ $att->employee->department->name ?? 'N/A' }}</td>
                                <td>{{ $att->check_in?->format('h:i A') ?? '-' }}</td>
                                <td>{{ $att->check_out?->format('h:i A') ?? '-' }}</td>
                                <td>{{ $att->working_hours ? round($att->working_hours, 2) . 'h' : '-' }}</td>
                                <td>
                                    @if($att->status == 'present')
                                    <span class="badge bg-success">Present</span>
                                    @elseif($att->status == 'late')
                                    <span class="badge bg-warning">Late</span>
                                    @elseif($att->status == 'absent')
                                    <span class="badge bg-danger">Absent</span>
                                    @else
                                    <span class="badge bg-secondary">{{ ucfirst($att->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bx bx-calendar bx-lg d-block mb-2"></i>
                                    No attendance records found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $attendances->links() }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Check In Modal -->
<div class="modal fade" id="checkInModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hrm.attendance.check-in') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Check In</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Employee <span class="text-danger">*</span></label>
                        <select class="form-select" name="employee_id" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Check In</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Check Out Modal -->
<div class="modal fade" id="checkOutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hrm.attendance.check-out') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Check Out</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Employee <span class="text-danger">*</span></label>
                        <select class="form-select" name="employee_id" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Check Out</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Filter Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas">
    <div class="offcanvas-header">
        <h5>Filter Attendance</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.attendance.index') }}" method="GET">
            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" class="form-control" name="date" value="{{ request('date', today()->format('Y-m-d')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <select class="form-select" name="employee_id">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Department</label>
                <select class="form-select" name="department_id">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="present">Present</option>
                    <option value="late">Late</option>
                    <option value="absent">Absent</option>
                </select>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('hrm.attendance.index') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>
@endsection
