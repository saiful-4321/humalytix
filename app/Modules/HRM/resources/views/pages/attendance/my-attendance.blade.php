@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>My Attendance</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">My Attendance</li>
            </ul>
        </div>
    </div>
</div>
@include("Main::widgets.message.sweet-alert")
<div class="row clearfix">
    <div class="col-lg-4"><div class="card"><div class="card-body text-center"><h4 class="mb-0">{{ $stats['total_days'] }}</h4><p class="text-muted mb-0">Total Days</p></div></div></div>
    <div class="col-lg-4"><div class="card"><div class="card-body text-center"><h4 class="mb-0">{{ $stats['present_days'] }}</h4><p class="text-muted mb-0">Present</p></div></div></div>
    <div class="col-lg-4"><div class="card"><div class="card-body text-center"><h4 class="mb-0">{{ $stats['late_days'] }}</h4><p class="text-muted mb-0">Late</p></div></div></div>
    @if($todayAttendance)
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Today's Attendance</h5>
                <div class="row">
                    <div class="col-md-3"><p class="text-muted mb-1">Check In</p><h6>{{ $todayAttendance->check_in?->format('h:i A') ?? '-' }}</h6></div>
                    <div class="col-md-3"><p class="text-muted mb-1">Check Out</p><h6>{{ $todayAttendance->check_out?->format('h:i A') ?? '-' }}</h6></div>
                    <div class="col-md-3"><p class="text-muted mb-1">Working Hours</p><h6>{{ $todayAttendance->working_hours ? round($todayAttendance->working_hours, 2) . 'h' : '-' }}</h6></div>
                    <div class="col-md-3"><p class="text-muted mb-1">Status</p><h6><span class="badge bg-success">{{ ucfirst($todayAttendance->status) }}</span></h6></div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Attendance History</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Hours</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($attendances as $att)
                            <tr>
                                <td>{{ $att->date->format('d M, Y') }}</td>
                                <td>{{ $att->check_in?->format('h:i A') ?? '-' }}</td>
                                <td>{{ $att->check_out?->format('h:i A') ?? '-' }}</td>
                                <td>{{ $att->working_hours ? round($att->working_hours, 2) . 'h' : '-' }}</td>
                                <td><span class="badge bg-success">{{ ucfirst($att->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No records found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $attendances->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
