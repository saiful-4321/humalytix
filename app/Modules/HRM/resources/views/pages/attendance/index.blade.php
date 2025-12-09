@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Attendance</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
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
        <div class="card bg-white">
            <div class="card-header border-bottom">
                 <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Daily Logs</h6>
                    
                    <div class="d-flex align-items-center gap-2">
                        @can('hrm.attendance.check-in')
                        <button class="btn btn-success btn-sm d-flex align-items-center font-weight-medium" data-bs-toggle="offcanvas" data-bs-target="#checkInOffcanvas">
                            <i class="bx bx-log-in-circle me-1"></i> Check In
                        </button>
                        @endcan
                        
                        @can('hrm.attendance.check-out')
                        <button class="btn btn-warning btn-sm d-flex align-items-center font-weight-medium" data-bs-toggle="offcanvas" data-bs-target="#checkOutOffcanvas">
                            <i class="bx bx-log-out-circle me-1"></i> Check Out
                        </button>
                        @endcan
                        
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#attendanceFilter" aria-controls="attendanceFilter">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
                        </button>

                        <a href="{{ route('hrm.attendance.report') }}" class="btn btn-info btn-sm">
                            <i class="bx bx-bar-chart-alt-2 me-1"></i> Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Active Filters Section -->
            @if(request()->hasAny(['date', 'employee_id', 'department_id', 'status']))
                <x-Main::active-filters :url="route('hrm.attendance.index')">
                    <x-Main::active-filter-item key="date" label="Date" :value="request('date')" />
                    <x-Main::active-filter-item key="employee_id" label="Employee" :value="$employees->where('id', request('employee_id'))->first()->full_name ?? request('employee_id')" />
                    <x-Main::active-filter-item key="department_id" label="Department" :value="$departments->where('id', request('department_id'))->first()->name ?? request('department_id')" />
                    <x-Main::active-filter-item key="status" label="Status" :value="ucfirst(request('status'))" />
                </x-Main::active-filters>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Date</th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Hours</th>
                                <th class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $att)
                            <tr>
                                <td>{{ $att->date->format('d M, Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                         <div class="avatar-xs me-2">
                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12">
                                                {{ substr($att->employee->first_name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 font-size-14">{{ $att->employee->full_name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $att->employee->department->name ?? '-' }}</td>
                                <td>
                                    @if($att->check_in)
                                        <span class="text-success"><i class="bx bx-time-five me-1"></i>{{ $att->check_in->format('h:i A') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($att->check_out)
                                        <span class="text-danger"><i class="bx bx-time-five me-1"></i>{{ $att->check_out->format('h:i A') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $att->working_hours ? round($att->working_hours, 2) . 'h' : '-' }}</td>
                                <td class="text-end">
                                    @if($att->status == 'present')
                                    <span class="badge bg-soft-success text-success">Present</span>
                                    @elseif($att->status == 'late')
                                    <span class="badge bg-soft-warning text-warning">Late</span>
                                    @elseif($att->status == 'absent')
                                    <span class="badge bg-soft-danger text-danger">Absent</span>
                                    @else
                                    <span class="badge bg-soft-secondary text-secondary">{{ ucfirst($att->status) }}</span>
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
            </div>
            
            @if($attendances->count())
            <div class="card-footer bg-transparent border-top">
                <div class="d-flex justify-content-end">
                    {{ $attendances->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Check In Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="checkInOffcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Check In</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
         <form action="{{ route('hrm.attendance.check-in') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Employee <span class="text-danger">*</span></label>
                <select class="form-select select2" name="employee_id" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea class="form-control" name="notes" rows="3" placeholder="Optional notes..."></textarea>
            </div>
            
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-success">Check In</button>
            </div>
        </form>
    </div>
</div>

<!-- Check Out Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="checkOutOffcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Check Out</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
         <form action="{{ route('hrm.attendance.check-out') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Employee <span class="text-danger">*</span></label>
                <select class="form-select select2" name="employee_id" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                    @endforeach
                </select>
            </div>
             <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-warning">Check Out</button>
            </div>
        </form>
    </div>
</div>

<!-- Filter Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="attendanceFilter" aria-labelledby="attendanceFilterLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="attendanceFilterLabel">Filter Attendance</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.attendance.index') }}" method="GET">
            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" class="form-control" name="date" value="{{ request('date', today()->format('Y-m-d')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <select class="form-select select2" name="employee_id">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Department</label>
                <select class="form-select select2" name="department_id">
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
                <a href="{{ route('hrm.attendance.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>
@endsection
