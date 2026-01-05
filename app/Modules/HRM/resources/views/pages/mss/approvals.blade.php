@extends('Main::layouts.app')

@section('title', 'Pending Approvals | MSS')

@section('content')
<div class="container-fluid">

    <!-- Page Title -->
     <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Pending Approvals</h4>
                <div class="page-title-right">
                     <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('hrm.mss.dashboard') }}">MSS</a></li>
                        <li class="breadcrumb-item active">Approvals</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#leaves" role="tab">
                                        <span class="d-none d-sm-block">Leaves ({{ $leaves->count() }})</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#attendance" role="tab">
                                        <span class="d-none d-sm-block">Attendance ({{ $regularizations->count() }})</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#expenses" role="tab">
                                        <span class="d-none d-sm-block">Expenses ({{ $expenses->count() }})</span>
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content p-3 text-muted">
                                <!-- Leaves Tab -->
                                <div class="tab-pane active" id="leaves" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Employee</th>
                                                    <th>Type</th>
                                                    <th>Dates</th>
                                                    <th>Reason</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($leaves as $leave)
                                                <tr>
                                                    <td class="fw-bold">{{ $leave->employee->full_name }}</td>
                                                    <td>{{ $leave->leaveType->name ?? 'Leave' }}</td>
                                                    <td>
                                                        {{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M') }}
                                                        <br><small>({{ $leave->days }} days)</small>
                                                    </td>
                                                    <td>{{ Str::limit($leave->reason, 30) }}</td>
                                                    <td>
                                                        <form action="{{ route('hrm.mss.leaves.approve', $leave->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm"><i class="bx bx-check"></i></button>
                                                        </form>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="rejectItem('{{ route('hrm.mss.leaves.reject', $leave->id) }}')">
                                                            <i class="bx bx-x"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="5" class="text-center">No pending leaves.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Attendance Tab -->
                                <div class="tab-pane" id="attendance" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Employee</th>
                                                    <th>Date</th>
                                                    <th>Check In/Out</th>
                                                    <th>Reason</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($regularizations as $reg)
                                                <tr>
                                                    <td class="fw-bold">{{ $reg->employee->full_name }}</td>
                                                    <td>{{ $reg->date->format('d M, Y') }}</td>
                                                    <td>
                                                        {{ $reg->check_in ? \Carbon\Carbon::parse($reg->check_in)->format('h:i A') : '-' }} - 
                                                        {{ $reg->check_out ? \Carbon\Carbon::parse($reg->check_out)->format('h:i A') : '-' }}
                                                    </td>
                                                    <td>{{ Str::limit($reg->reason, 30) }}</td>
                                                    <td>
                                                        <form action="{{ route('hrm.mss.regularization.approve', $reg->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm"><i class="bx bx-check"></i></button>
                                                        </form>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="rejectItem('{{ route('hrm.mss.regularization.reject', $reg->id) }}')">
                                                            <i class="bx bx-x"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="5" class="text-center">No pending requests.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Expenses Tab -->
                                <div class="tab-pane" id="expenses" role="tabpanel">
                                     <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Employee</th>
                                                    <th>Date</th>
                                                    <th>Title</th>
                                                    <th>Amount</th>
                                                    <th>Ref</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($expenses as $expense)
                                                <tr>
                                                    <td class="fw-bold">{{ $expense->employee->full_name }}</td>
                                                    <td>{{ $expense->date->format('d M') }}</td>
                                                    <td>
                                                        {{ $expense->title }} <br>
                                                        <small class="text-muted">{{ $expense->category }}</small>
                                                    </td>
                                                    <td class="fw-bold">{{ number_format($expense->amount, 2) }}</td>
                                                     <td>
                                                        @if($expense->attachment)
                                                            <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank" class="badge bg-info">View</a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <form action="{{ route('hrm.mss.expenses.approve', $expense->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm"><i class="bx bx-check"></i></button>
                                                        </form>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="rejectItem('{{ route('hrm.mss.expenses.reject', $expense->id) }}')">
                                                            <i class="bx bx-x"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr><td colspan="6" class="text-center">No pending expenses.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="remarks" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function rejectItem(url) {
        document.getElementById('rejectForm').action = url;
        var modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        modal.show();
    }
</script>
@endpush
@endsection
