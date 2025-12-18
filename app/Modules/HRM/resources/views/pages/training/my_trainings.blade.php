@extends('Main::layouts.app')

@section('title', 'My Trainings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">My Trainings</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                        <li class="breadcrumb-item active">My Trainings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Upcoming Training -->
        <div class="col-lg-8">
            <h5 class="mb-3">Enrolled / Upcoming</h5>
            @php $hasUpcoming = false; @endphp
            <div class="row">
                @foreach($enrollments as $enrollment)
                    @if($enrollment->status == 'enrolled')
                        @php $hasUpcoming = true; @endphp
                        <div class="col-md-6">
                            <div class="card border-primary border-start border-3">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">{{ $enrollment->session->training->title }}</h5>
                                    <p class="text-muted mb-2"><i class="bx bx-been-here me-1"></i> {{ $enrollment->session->location }}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            <i class="bx bx-calendar me-1"></i> {{ $enrollment->session->start_date->format('d M, Y') }}<br>
                                            <i class="bx bx-time me-1"></i> {{ $enrollment->session->start_date->format('h:i A') }}
                                        </div>
                                        <div class="text-end">
                                            @if($enrollment->status == 'completed' || $enrollment->status == 'attended')
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('hrm.trainings.certificate.preview', $enrollment->id) }}" target="_blank" class="btn btn-outline-primary">
                                                        <i class="bx bx-show me-1"></i> Preview
                                                    </a>
                                                    <a href="{{ route('hrm.trainings.certificate.download', $enrollment->id) }}" class="btn btn-outline-success">
                                                        <i class="bx bx-download me-1"></i> Download
                                                    </a>
                                                </div>
                                            @endif
                                            <span class="badge bg-soft-info text-info mt-2 d-block">Enrolled</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            @if(!$hasUpcoming)
                <div class="alert alert-light" role="alert">No upcoming training sessions scheduled.</div>
            @endif
        </div>

        <!-- History -->
        <div class="col-lg-4">
            <h5 class="mb-3">Training History</h5>
            <div class="card">
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($enrollments as $enrollment)
                            @if($enrollment->status == 'completed' || $enrollment->status == 'attended')
                                <li class="list-group-item py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <span class="avatar-title rounded-circle bg-soft-success text-success font-size-20">
                                                <i class="bx bx-check-double"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="font-size-14 mb-1">{{ $enrollment->session->training->title }}</h6>
                                            <p class="text-muted font-size-13 mb-1">{{ $enrollment->completion_date ? $enrollment->completion_date->format('d M Y') : 'Completed' }}</p>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('hrm.trainings.certificate.preview', $enrollment->id) }}" target="_blank" class="text-primary font-size-12"><i class="bx bx-show"></i> Preview</a>
                                                <a href="{{ route('hrm.trainings.certificate.download', $enrollment->id) }}" class="text-success font-size-12"><i class="bx bx-download"></i> Download</a>
                                            </div>
                                        </div>
                                        @if($enrollment->score)
                                            <div class="text-end">
                                                <h5 class="font-size-14 mb-0">{{ $enrollment->score }}%</h5>
                                                <span class="text-muted font-size-12">Score</span>
                                            </div>
                                        @endif
                                    </div>
                                </li>
                            @endif
                        @endforeach
                        @if($enrollments->whereIn('status', ['completed', 'attended'])->isEmpty())
                            <li class="list-group-item text-center text-muted">No completed trainings yet.</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
