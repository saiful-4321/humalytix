@extends('HRM::layouts.master')

@section('title', 'Manage Team Shifts | MSS')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
             <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Manage Roster</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('hrm.mss.dashboard') }}">MSS</a></li>
                                <li class="breadcrumb-item active">Roster</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Assign Shift Form -->
                <div class="col-xl-4">
                    <div class="card">
                         <div class="card-header">
                            <h4 class="card-title">Assign Shift</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('hrm.mss.roster.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Employee</label>
                                    <select name="employee_id" class="form-select" required>
                                        <option value="">Select Employee</option>
                                        @foreach($subordinates as $sub)
                                            <option value="{{ $sub->id }}">{{ $sub->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Shift</label>
                                    <select name="shift_id" class="form-select" required>
                                        <option value="">Select Shift</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}">{{ $shift->name }} ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">From Date</label>
                                        <input type="date" name="start_date" class="form-control" required min="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">To Date</label>
                                        <input type="date" name="end_date" class="form-control" required min="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Assign Shift</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Shift Overview (Future enhancements: Full Calendar) -->
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Tips</h4>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="mdi mdi-circle-medium text-primary me-1"></i> Use this form to assign shifts to your direct reports.</li>
                                <li class="mb-2"><i class="mdi mdi-circle-medium text-primary me-1"></i> Ensure you select valid future dates.</li>
                                <li class="mb-2"><i class="mdi mdi-circle-medium text-primary me-1"></i> Updates will overwrite existing assignments for the selected dates.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
