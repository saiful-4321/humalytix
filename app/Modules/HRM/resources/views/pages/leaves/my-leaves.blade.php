@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>My Leaves</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">My Leaves</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix mb-4">
    <div class="col-12">
        <h6 class="mb-3">Leave Balances ({{ date('Y') }})</h6>
    </div>
    @forelse($allocations as $allocation)
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title mb-0">{{ $allocation->leaveType->name }}</h6>
                    @if($allocation->leaveType->is_paid)
                        <span class="badge bg-success-subtle text-success">Paid</span>
                    @else
                        <span class="badge bg-warning-subtle text-warning">Unpaid</span>
                    @endif
                </div>
                
                <h3 class="mb-1">{{ $allocation->balance + 0 }} <small class="fs-6 text-muted">/ {{ $allocation->allocated_days + $allocation->carried_over_days + 0 }} Days</small></h3>
                <div class="progress mt-3" style="height: 6px;">
                    @php
                        $total = $allocation->allocated_days + $allocation->carried_over_days;
                        $percent = $total > 0 ? ($allocation->used_days / $total) * 100 : 0;
                        $color = 'bg-success';
                        if($percent > 50) $color = 'bg-warning';
                        if($percent > 80) $color = 'bg-danger';
                    @endphp
                    <div class="progress-bar {{ $color }}" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="mt-2 text-muted small">
                    Used: {{ $allocation->used_days + 0 }} days
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info">
            No leave allocations found for this year. Please contact HR.
        </div>
    </div>
    @endforelse
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">My Leave Applications</h6>
                    <a href="{{ route('hrm.leaves.create') }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-plus me-1"></i> Apply Leave
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Type</th>
                                <th>Duration</th>
                                <th>Days</th>
                                <th>Apply Date</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $leave)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{ $leave->leaveType->name ?? 'N/A' }}</span>
                                    @if($leave->attachment)
                                    <i class="bx bx-paperclip ms-1 text-muted"></i>
                                    @endif
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} 
                                    - 
                                    {{ \Carbon\Carbon::parse($leave->end_date)->format('d M, Y') }}
                                </td>
                                <td>{{ $leave->days }}</td>
                                <td>{{ $leave->created_at->format('d M, Y') }}</td>
                                <td>
                                    @if($leave->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                        @if($leave->approvedBy)
                                            <div class="small text-muted mt-1">by {{ $leave->approvedBy->name }}</div>
                                        @endif
                                    @elseif($leave->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                        @if($leave->current_level > 1)
                                            <div class="small text-muted mt-1">Level {{ $leave->current_level }}</div>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('hrm.leaves.show', $leave->id) }}" class="btn btn-sm btn-link text-primary">
                                        <i class="bx bx-show font-size-18"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-file-document-outline font-size-24 d-block mb-2"></i>
                                    No leave applications found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $leaves->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
