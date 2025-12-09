@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Leave Management</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
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
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas"><i class="bx bx-filter"></i> Filter</button>
                    </div>
                    <div>
                        @can('hrm.leaves.create')
                        <a href="{{ route('hrm.leaves.create') }}" class="btn btn-success"><i class="bx bx-plus"></i> Apply Leave</a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr><th>Employee</th><th>Leave Type</th><th>Duration</th><th>Days</th><th>Status</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $leave)
                            <tr>
                                <td>{{ $leave->employee->full_name }}</td>
                                <td>{{ $leave->leaveType->name }}</td>
                                <td>{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M, Y') }}</td>
                                <td>{{ $leave->days }} days</td>
                                <td>
                                    @if($leave->status == 'approved')<span class="badge bg-success">Approved</span>
                                    @elseif($leave->status == 'pending')<span class="badge bg-warning">Pending</span>
                                    @else<span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('hrm.leaves.show', $leave) }}" class="btn btn-sm btn-soft-info"><i class="bx bx-show"></i></a>
                                    @if($leave->status == 'pending')
                                    @can('hrm.leaves.approve')
                                    <form action="{{ route('hrm.leaves.approve', $leave) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-soft-success"><i class="bx bx-check"></i></button>
                                    </form>
                                    <button class="btn btn-sm btn-soft-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $leave->id }}"><i class="bx bx-x"></i></button>
                                    @endcan
                                    @endif
                                </td>
                            </tr>
                            <div class="modal fade" id="rejectModal{{ $leave->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('hrm.leaves.reject', $leave) }}" method="POST">
                                            @csrf
                                            <div class="modal-header"><h5 class="modal-title">Reject Leave</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <div class="modal-body">
                                                <div class="mb-3"><label class="form-label">Rejection Reason</label><textarea class="form-control" name="rejection_reason" rows="3" required></textarea></div>
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Reject</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted"><i class="bx bx-calendar bx-lg d-block mb-2"></i>No leave applications found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $leaves->links() }}</div>
            </div>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas">
    <div class="offcanvas-header"><h5>Filter Leaves</h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button></div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.leaves.index') }}" method="GET">
            <div class="mb-3"><label class="form-label">Employee</label><select class="form-select" name="employee_id"><option value="">All Employees</option>@foreach($employees as $emp)<option value="{{ $emp->id }}">{{ $emp->full_name }}</option>@endforeach</select></div>
            <div class="mb-3"><label class="form-label">Leave Type</label><select class="form-select" name="leave_type_id"><option value="">All Types</option>@foreach($leaveTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach</select></div>
            <div class="mb-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="">All Status</option><option value="pending">Pending</option><option value="approved">Approved</option><option value="rejected">Rejected</option></select></div>
            <div class="d-grid gap-2"><button type="submit" class="btn btn-primary">Apply Filters</button><a href="{{ route('hrm.leaves.index') }}" class="btn btn-secondary">Clear</a></div>
        </form>
    </div>
</div>
@endsection
