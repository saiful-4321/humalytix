@extends('HRM::layouts.master')

@section('title', 'My Attendance | ESS')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                     <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">My Attendance & Regularization</h4>
                        <div class="page-title-right">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#regularizeOffcanvas">
                                <i class="bx bx-time-five"></i> Request Regularization
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Logs -->
            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Attendance Log</h4>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Check In</th>
                                            <th>Check Out</th>
                                            <th>Status</th>
                                            <th>Hours</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($attendances as $attendance)
                                        <tr>
                                            <td>{{ $attendance->date->format('d M, Y') }}</td>
                                            <td>{{ $attendance->check_in ? $attendance->check_in->format('h:i A') : '-' }}</td>
                                            <td>{{ $attendance->check_out ? $attendance->check_out->format('h:i A') : '-' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'absent' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($attendance->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $attendance->working_hours ? number_format($attendance->working_hours, 2) . ' hrs' : '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No attendance records found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $attendances->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Regularization Requests -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Regularization Requests</h4>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($regularizationRequests as $request)
                                        <tr>
                                            <td>{{ $request->date->format('d M') }} <br> <small class="text-muted">{{ $request->created_at->format('d M Y') }}</small></td>
                                            <td>
                                                @if($request->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($request->status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">No requests.</td>
                                        </tr>
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

<!-- Regularization Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="regularizeOffcanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Req. Attendance Regularization</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.ess.attendance.regularize') }}" method="POST">
            @csrf
            <div class="alert alert-info font-size-13 text-center">
                Submit this request if you missed a punch or have incorrect timings.
            </div>

            <div class="mb-3">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control" required max="{{ date('Y-m-d') }}">
            </div>

            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">Check In</label>
                    <input type="time" name="check_in" class="form-control">
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Check Out</label>
                    <input type="time" name="check_out" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Reason <span class="text-danger">*</span></label>
                <textarea name="reason" class="form-control" rows="3" required placeholder="E.g. Forgot to punch out due to rush..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">Submit Request</button>
        </form>
    </div>
</div>
@endsection
