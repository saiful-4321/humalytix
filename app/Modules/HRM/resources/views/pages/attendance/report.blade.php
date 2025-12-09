@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Attendance Report</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.attendance.index') }}">Attendance</a></li>
                <li class="breadcrumb-item active">Report</li>
            </ul>
        </div>
    </div>
</div>
@include("Main::widgets.message.sweet-alert")
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hrm.attendance.report') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block w-100">Generate Report</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Total Days</th>
                                <th>Present</th>
                                <th>Late</th>
                                <th>Absent</th>
                                <th>Avg Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $emp)
                            @php
                                $empAttendances = $attendances->get($emp->id, collect());
                                $totalDays = $empAttendances->count();
                                $present = $empAttendances->where('status', 'present')->count();
                                $late = $empAttendances->where('status', 'late')->count();
                                $absent = $empAttendances->where('status', 'absent')->count();
                                $avgHours = $empAttendances->avg('working_hours');
                            @endphp
                            @if($totalDays > 0)
                            <tr>
                                <td>{{ $emp->full_name }}</td>
                                <td>{{ $totalDays }}</td>
                                <td>{{ $present }}</td>
                                <td>{{ $late }}</td>
                                <td>{{ $absent }}</td>
                                <td>{{ $avgHours ? round($avgHours, 2) . 'h' : '-' }}</td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
