@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Leave Details</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.leaves.index') }}">Leaves</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ul>
        </div>
    </div>
</div>
@include("Main::widgets.message.sweet-alert")
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Leave Information</h5>
                        <table class="table table-sm">
                            <tr><th width="40%">Employee:</th><td>{{ $leave->employee->full_name }}</td></tr>
                            <tr><th>Leave Type:</th><td>{{ $leave->leaveType->name }}</td></tr>
                            <tr><th>Start Date:</th><td>{{ $leave->start_date->format('d M, Y') }}</td></tr>
                            <tr><th>End Date:</th><td>{{ $leave->end_date->format('d M, Y') }}</td></tr>
                            <tr><th>Total Days:</th><td><strong>{{ $leave->days }} days</strong></td></tr>
                            <tr><th>Status:</th><td>
                                @if($leave->status == 'approved')<span class="badge bg-success">Approved</span>
                                @elseif($leave->status == 'pending')<span class="badge bg-warning">Pending</span>
                                @else<span class="badge bg-danger">Rejected</span>
                                @endif
                            </td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3">Additional Details</h5>
                        <table class="table table-sm">
                            <tr><th width="40%">Applied On:</th><td>{{ $leave->created_at->format('d M, Y h:i A') }}</td></tr>
                            @if($leave->approved_by)
                            <tr><th>Approved By:</th><td>{{ $leave->approvedBy->name ?? 'N/A' }}</td></tr>
                            <tr><th>Approved At:</th><td>{{ $leave->approved_at?->format('d M, Y h:i A') }}</td></tr>
                            @endif
                            @if($leave->rejection_reason)
                            <tr><th>Rejection Reason:</th><td>{{ $leave->rejection_reason }}</td></tr>
                            @endif
                            @if($leave->attachment)
                            <tr><th>Attachment:</th><td><a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="btn btn-sm btn-soft-info"><i class="bx bx-download"></i> Download</a></td></tr>
                            @endif
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Reason:</h6>
                        <p class="text-muted">{{ $leave->reason }}</p>
                    </div>
                </div>

                @if($leave->approval_chain_id)
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3">Approval Timeline</h5>
                        <ul class="list-group list-group-flush">
                            {{-- 1. Show existing logs --}}
                            @foreach($leave->approvalLogs as $log)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge {{ $log->status == 'approved' ? 'bg-success' : 'bg-danger' }} me-2">Level {{ $log->level }}</span>
                                    <strong>{{ $log->approver->name ?? 'System' }}</strong> 
                                    <span class="text-muted small">({{ $log->created_at->format('d M h:i A') }})</span>
                                    <p class="mb-0 text-muted small ms-1">{{ $log->comments }}</p>
                                </div>
                                <span><i class="bx {{ $log->status == 'approved' ? 'bx-check text-success' : 'bx-x text-danger' }} font-size-18"></i></span>
                            </li>
                            @endforeach

                            {{-- 2. Show Pending Steps if not completed --}}
                            @if(!$leave->is_completed)
                                @foreach($leave->approvalChain->levels->where('level', '>=', $leave->current_level) as $level)
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                    <div>
                                        <span class="badge bg-secondary me-2">Level {{ $level->level }}</span>
                                        <span class="text-muted">
                                            @if($level->level == $leave->current_level)
                                                <strong class="text-primary">Waiting for:</strong>
                                            @else
                                                Next:
                                            @endif
                                            @if($level->approver_type == 'reporting_manager') Reporting Manager
                                            @elseif($level->approver_type == 'designation') {{ $level->approver_value }}
                                            @elseif($level->approver_type == 'specific_user') User #{{ $level->approver_value }}
                                            @endif
                                        </span>
                                    </div>
                                    @if($level->level == $leave->current_level)
                                    <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                                    @else
                                    <i class="bx bx-time-five text-muted"></i>
                                    @endif
                                </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
                @endif

                @if($leave->status == 'pending')
                <div class="d-flex justify-content-end gap-2 mt-3">
                    @can('hrm.leaves.approve')
                    <form action="{{ route('hrm.leaves.approve', $leave) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Approve</button>
                    </form>
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="bx bx-x"></i> Reject</button>
                    @endcan
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hrm.leaves.reject', $leave) }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Reject Leave</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Rejection Reason <span class="text-danger">*</span></label><textarea class="form-control" name="rejection_reason" rows="3" required></textarea></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Reject Leave</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
