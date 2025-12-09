@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Shifts & Rosters</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Shifts</li>
            </ul>
        </div>
    </div>
</div>
@include("Main::widgets.message.sweet-alert")
<div class="row clearfix">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Manage Shifts</h5></div>
            <div class="card-body">
                <form action="{{ route('hrm.shifts.store') }}" method="POST">
                    @csrf
                    <div class="mb-3"><label>Shift Name</label><input type="text" name="name" class="form-control" required placeholder="e.g. Morning A"></div>
                    <div class="row">
                        <div class="col-6 mb-3"><label>Start Time</label><input type="time" name="start_time" class="form-control" required></div>
                        <div class="col-6 mb-3"><label>End Time</label><input type="time" name="end_time" class="form-control" required></div>
                    </div>
                    <div class="mb-3"><label>Break (Min)</label><input type="number" name="break_duration" class="form-control" value="60"></div>
                    <button class="btn btn-primary w-100">Create Shift</button>
                </form>
                <hr>
                <div class="list-group">
                    @foreach($shifts as $shift)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $shift->name }}</strong><br>
                            <small>{{ $shift->start_time }} - {{ $shift->end_time }}</small>
                        </div>
                        <span class="badge bg-light text-dark">{{ $shift->break_duration }}m Break</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Weekly Roster</h5>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#assignRosterModal">Assign Roster</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead><tr><th>Date</th><th>Employee</th><th>Shift</th><th>Time</th></tr></thead>
                        <tbody>
                            @forelse($rosters as $roster)
                            <tr>
                                <td>{{ $roster->date->format('D, d M') }}</td>
                                <td>{{ $roster->employee->full_name }}</td>
                                <td><span class="badge bg-info">{{ $roster->shift->name }}</span></td>
                                <td>{{ $roster->shift->start_time }} - {{ $roster->shift->end_time }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center">No upcoming rosters.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $rosters->links() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="assignRosterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hrm.shifts.assign') }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Assign Roster</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Employees</label>
                        <select name="employee_ids[]" class="form-select" multiple required style="height: 150px">
                            @foreach($employees as $emp)<option value="{{ $emp->id }}">{{ $emp->full_name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Shift</label>
                        <select name="shift_id" class="form-select" required>
                            @foreach($shifts as $shift)<option value="{{ $shift->id }}">{{ $shift->name }} ({{ $shift->start_time }}-{{ $shift->end_time }})</option>@endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3"><label>From</label><input type="date" name="start_date" class="form-control" required></div>
                        <div class="col-6 mb-3"><label>To</label><input type="date" name="end_date" class="form-control" required></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-success">Assign</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
