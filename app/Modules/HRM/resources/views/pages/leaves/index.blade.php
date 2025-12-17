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
                        <button class="btn btn-info btn-sm d-flex align-items-center font-weight-medium" type="button" data-bs-toggle="offcanvas" data-bs-target="#applyLeaveCanvas">
                            <i class="mdi mdi-plus me-1"></i> Apply Leave
                        </button>
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
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('hrm.leaves.show', $leave) }}" class="btn btn-sm btn-soft-primary" title="View">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>

                                        @if($leave->status == 'pending')
                                            @can('hrm.leaves.approve')
                                                    <form action="{{ route('hrm.leaves.approve', $leave) }}" method="POST" class="d-inline confirm-action" data-message="Approve this leave request?" data-confirm-text="Yes, Approve">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-soft-success" title="Approve">
                                                            <i class="mdi mdi-check-circle-outline"></i>
                                                        </button>
                                                    </form>
                                            
                                            <button type="button" class="btn btn-sm btn-soft-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $leave->id }}" title="Reject">
                                                <i class="mdi mdi-close-circle-outline"></i>
                                            </button>
                                            @endcan
                                        @endif
                                    </div>

                                    <!-- Reject Modal (Nested to keep ID unique) -->
                                    <div class="modal fade" id="rejectModal{{ $leave->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="{{ route('hrm.leaves.reject', $leave) }}" method="POST" class="confirm-action" data-message="Reject this leave request? This cannot be undone." data-confirm-text="Yes, Reject">
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

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.confirm-action').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const message = this.getAttribute('data-message') || 'Are you sure?';
                const confirmBtnText = this.getAttribute('data-confirm-text') || 'Yes, proceed!';
                
                Swal.fire({
                    title: 'Confirmation Required',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirmBtnText
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    });
</script>
@endsection

{{-- Apply Leave Offcanvas --}}
<div class="offcanvas offcanvas-end w-50" tabindex="-1" id="applyLeaveCanvas" aria-labelledby="applyLeaveLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="applyLeaveLabel">Apply for Leave</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.leaves.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Employee <span class="text-danger">*</span></label>
                    <select class="form-select select2-offcanvas" name="employee_id" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                    <select class="form-select @error('leave_type_id') is-invalid @enderror" name="leave_type_id" required>
                        <option value="">Select Leave Type</option>
                        @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }} ({{ $type->days_per_year }} days/year)</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="start_date" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="end_date" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Reason <span class="text-danger">*</span></label>
                <textarea class="form-control" name="reason" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Attachment (Optional)</label>
                <input type="file" class="form-control" name="attachment" accept=".pdf,.jpg,.jpeg,.png">
                <small class="text-muted">Max 5MB. Formats: PDF, JPG, PNG</small>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">Submit Application</button>
            </div>
        </form>
    </div>
</div>
