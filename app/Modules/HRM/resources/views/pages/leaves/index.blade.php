@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Leaves</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Leaves</li>
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
                    <h6 class="font-weight-medium mb-0">Leave Applications</h6>
                    
                    <div class="d-flex align-items-center gap-2">
                        @can('hrm.leaves.create')
                        <a href="{{ route('hrm.leaves.create') }}" class="btn btn-info btn-sm d-flex align-items-center font-weight-medium">
                            <i class="mdi mdi-plus me-1"></i> Apply Leave
                        </a>
                        @endcan
                        
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#leaveFilter" aria-controls="leaveFilter">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
                        </button>

                        @can('hrm.leaves.export')
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-export me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Excel (XLSX)</a></li>
                                <li><a class="dropdown-item" href="#">PDF</a></li>
                            </ul>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Active Filters Section -->
            @if(request()->hasAny(['employee_id', 'leave_type_id', 'status']))
                <x-Main::active-filters :url="route('hrm.leaves.index')">
                    <x-Main::active-filter-item key="employee_id" label="Employee" :value="$employees->where('id', request('employee_id'))->first()->full_name ?? request('employee_id')" />
                    <x-Main::active-filter-item key="leave_type_id" label="Leave Type" :value="$leaveTypes->where('id', request('leave_type_id'))->first()->name ?? request('leave_type_id')" />
                    <x-Main::active-filter-item key="status" label="Status" :value="ucfirst(request('status'))" />
                </x-Main::active-filters>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Employee</th>
                                <th>Leave Type</th>
                                <th>Duration</th>
                                <th>Days</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $leave)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                         <div class="avatar-xs me-2">
                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12">
                                                {{ substr($leave->employee->first_name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 font-size-14">{{ $leave->employee->full_name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark">{{ $leave->leaveType->name }}</span></td>
                                <td>{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M, Y') }}</td>
                                <td><span class="badge bg-soft-info text-info">{{ $leave->days }} Days</span></td>
                                <td>
                                    @if($leave->status == 'approved')<span class="badge bg-soft-success text-success">Approved</span>
                                    @elseif($leave->status == 'pending')<span class="badge bg-soft-warning text-warning">Pending</span>
                                    @else<span class="badge bg-soft-danger text-danger">Rejected</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-muted font-size-16 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('hrm.leaves.show', $leave) }}"><i class="bx bx-show me-2"></i> View Details</a></li>
                                            
                                            @if($leave->status == 'pending')
                                                @can('hrm.leaves.approve')
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('hrm.leaves.approve', $leave) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-success"><i class="bx bx-check me-2"></i> Approve</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $leave->id }}">
                                                        <i class="bx bx-x me-2"></i> Reject
                                                    </a>
                                                </li>
                                                @endcan
                                            @endif
                                        </ul>
                                    </div>

                                    <!-- Reject Modal (Nested to keep ID unique) -->
                                    <div class="modal fade" id="rejectModal{{ $leave->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="{{ route('hrm.leaves.reject', $leave) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject Leave Application</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" name="rejection_reason" rows="3" required placeholder="Please provide a reason..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="mdi mdi-calendar-remove-outline font-size-24 d-block mb-2"></i>
                                    No leave applications found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($leaves->count())
            <div class="card-footer bg-transparent border-top">
                <div class="d-flex justify-content-end">
                    {{ $leaves->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Filter Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="leaveFilter" aria-labelledby="leaveFilterLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="leaveFilterLabel">Filter Leaves</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.leaves.index') }}" method="GET">
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
                <label class="form-label">Leave Type</label>
                <select class="form-select select2" name="leave_type_id">
                    <option value="">All Types</option>
                    @foreach($leaveTypes as $type)
                    <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('hrm.leaves.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>
</div>
@endsection
