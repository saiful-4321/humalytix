@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Roster Management</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Roster</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between py-1 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('hrm.rosters.index', ['date' => $startOfWeek->copy()->subWeek()->format('Y-m-d')]) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="mdi mdi-chevron-left"></i>
                        </a>
                        <form action="{{ route('hrm.rosters.index') }}" method="GET" class="d-inline-flex align-items-center">
                            <input type="date" name="date" value="{{ $startOfWeek->format('Y-m-d') }}" class="form-control form-control-sm" onchange="this.form.submit()">
                            
                            <select name="department_id" class="form-select form-select-sm ms-2" style="width: 150px;" onchange="this.form.submit()">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </form>
                        <a href="{{ route('hrm.rosters.index', ['date' => $startOfWeek->copy()->addWeek()->format('Y-m-d')]) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="mdi mdi-chevron-right"></i>
                        </a>
                        <span class="ms-2 fw-medium">
                            {{ $dates[0]->format('d M') }} - {{ $dates[6]->format('d M, Y') }}
                        </span>
                    </div>
                    
                    <button type="submit" form="rosterForm" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-content-save me-1"></i> Save Roster
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <form action="{{ route('hrm.rosters.store') }}" method="POST" id="rosterForm">
                    @csrf
                    <input type="hidden" name="week_date" value="{{ $startOfWeek->format('Y-m-d') }}">
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th class="text-start" style="width: 200px;">Employee</th>
                                    @foreach($dates as $date)
                                    <th style="min-width: 120px;">
                                        <div>{{ $date->format('D') }}</div>
                                        <small class="text-muted">{{ $date->format('d M') }}</small>
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $employee->full_name }}</div>
                                        <div class="small text-muted">{{ $employee->employee_code }}</div>
                                        @if($employee->department)
                                        <div class="small text-muted">{{ $employee->department->name }}</div>
                                        @endif
                                    </td>
                                    @foreach($dates as $date)
                                    @php
                                        $dateStr = $date->format('Y-m-d');
                                        $roster = $employee->rosters->firstWhere('date', $date);
                                        $selectedShift = $roster ? $roster->shift_id : null;
                                        $isOff = $roster ? $roster->is_off_day : false;
                                    @endphp
                                    <td class="p-1">
                                        <select name="roster[{{ $employee->id }}][{{ $dateStr }}]" class="form-select form-select-sm {{ $isOff ? 'bg-secondary-subtle' : '' }}" style="font-size: 11px;">
                                            <option value="">-- Select --</option>
                                            <option value="off" {{ $isOff ? 'selected' : '' }}>OFF DAY</option>
                                            @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ ($selectedShift == $shift->id) ? 'selected' : '' }}>
                                                {{ $shift->name }} ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    @endforeach
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">No employees found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
                <div class="p-3">
                    {{ $employees->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
