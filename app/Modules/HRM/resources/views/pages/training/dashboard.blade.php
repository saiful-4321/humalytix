@extends('Main::layouts.app')

@section('title', 'L&D Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">L&D Dashboard</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                        <li class="breadcrumb-item active">L&D Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Widgets -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stat bg-primary text-white">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-start mini-stat-img me-4">
                            <i class="bx bx-book-open font-size-24"></i>
                        </div>
                        <h5 class="font-size-16 text-uppercase text-white-50">Programs</h5>
                        <h4 class="fw-medium font-size-24">{{ $stats['total_trainings'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stat bg-primary text-white">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-start mini-stat-img me-4">
                            <i class="bx bx-calendar-event font-size-24"></i>
                        </div>
                        <h5 class="font-size-16 text-uppercase text-white-50">Upcoming</h5>
                        <h4 class="fw-medium font-size-24">{{ $stats['active_sessions'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stat bg-primary text-white">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-start mini-stat-img me-4">
                            <i class="bx bx-user-check font-size-24"></i>
                        </div>
                        <h5 class="font-size-16 text-uppercase text-white-50">Trained</h5>
                        <h4 class="fw-medium font-size-24">{{ $stats['participants_trained'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stat bg-primary text-white">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-start mini-stat-img me-4">
                            <i class="bx bx-certification font-size-24"></i>
                        </div>
                        <h5 class="font-size-16 text-uppercase text-white-50">Certified</h5>
                        <h4 class="fw-medium font-size-24">{{ $stats['certified_employees'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Upcoming Sessions -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Upcoming Training Sessions</h4>
                    <div class="flex-shrink-0">
                        <a href="{{ route('hrm.trainings.index') }}" class="btn btn-sm btn-light">View All</a>
                    </div>
                </div>
                <div class="card-body px-0">
                    <div class="table-responsive px-3">
                        <table class="table table-borderless table-nowrap align-middle mb-0">
                            <tbody>
                                @forelse($upcomingSessions as $session)
                                <tr>
                                    <td style="width: 50px;">
                                        <div class="avatar-sm">
                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-18">
                                                {{ substr($session->training->title, 0, 1) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <h5 class="font-size-14 mb-1">{{ $session->training->title }}</h5>
                                        <p class="text-muted mb-0"><i class="bx bx-calendar me-1"></i> {{ $session->start_date->format('d M, h:i A') }}</p>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-warning text-warning">{{ ucfirst($session->status) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No upcoming sessions scheduled.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Skill Matrix Preview -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Skill Matrix Snapshot</h4>
                    <div class="flex-shrink-0">
                        <a href="{{ route('hrm.skills.matrix') }}" class="btn btn-sm btn-light">Full Matrix</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Emp / Skill</th>
                                    @foreach($topSkills as $skill)
                                        <th>{{ Str::limit($skill->name, 10) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sampleEmployees as $emp)
                                <tr>
                                    <td class="text-start fw-bold">{{ $emp->first_name }}</td>
                                    @foreach($topSkills as $skill)
                                        @php
                                            $empSkill = $emp->skills->firstWhere('id', $skill->id);
                                            $level = $empSkill->pivot->proficiency_level ?? null;
                                            $badgeClass = match($level) {
                                                'beginner' => 'bg-soft-secondary',
                                                'intermediate' => 'bg-soft-info',
                                                'advanced' => 'bg-soft-primary',
                                                'expert' => 'bg-success text-white',
                                                default => '',
                                            };
                                        @endphp
                                        <td class="{{ $badgeClass }}"></td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2 d-flex justify-content-end gap-2 text-muted small">
                        <span><span class="badge bg-soft-secondary p-1"> </span> Beginner</span>
                        <span><span class="badge bg-soft-info p-1"> </span> Intermediate</span>
                        <span><span class="badge bg-soft-primary p-1"> </span> Advanced</span>
                        <span><span class="badge bg-success p-1"> </span> Expert</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
