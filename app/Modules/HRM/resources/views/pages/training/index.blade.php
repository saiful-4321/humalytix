@extends('Main::layouts.app')

@section('title', 'Training Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Training & Development</h4>
                    <div>
                        <a href="{{ route('hrm.trainings.my-trainings') }}" class="btn btn-outline-primary me-2">
                            <i class="bx bx-user-check me-1"></i> My Trainings
                        </a>
                        @can('hrm.trainings.create')
                        <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddTraining">
                            <i class="bx bx-plus me-1"></i> Add Program
                        </button>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-custom mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#calendar-view" role="tab">
                                <span class="d-none d-sm-block">Calendar</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#programs-view" role="tab">
                                <span class="d-none d-sm-block">Training Programs</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content text-muted">
                        <!-- Calendar Tab -->
                        <div class="tab-pane active" id="calendar-view" role="tabpanel">
                            <div class="row">
                                <div class="col-xl-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-grid">
                                                <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScheduleSession">
                                                    <i class="mdi mdi-plus-circle-outline"></i> Schedule Session
                                                </button>
                                            </div>
                                            <div id="external-events" class="mt-2">
                                                <br>
                                                <p class="text-muted">Upcoming Sessions</p>
                                                @foreach($upcomingSessions as $session)
                                                    <div class="external-event fc-event bg-soft-info text-info" data-class="bg-soft-info">
                                                        <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>
                                                        {{ $session->training->title }}
                                                        <br>
                                                        <small class="text-muted">{{ $session->start_date->format('d M, h:i A') }}</small>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end col-->

                                <div class="col-xl-9">
                                    <div class="card">
                                        <div class="card-body">
                                            <div id="calendar"></div>
                                        </div>
                                    </div>
                                </div> <!-- end col -->
                            </div> 
                        </div>

                        <!-- Programs List Tab -->
                        <div class="tab-pane" id="programs-view" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Code</th>
                                            <th>Title</th>
                                            <th>Trainer</th>
                                            <th>Type</th>
                                            <th>Duration</th>
                                            <th>Sessions</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($trainings as $training)
                                        <tr>
                                            <td>{{ $training->code }}</td>
                                            <td>
                                                <h6 class="mb-0">{{ $training->title }}</h6>
                                                <small class="text-muted">{{ Str::limit($training->description, 50) }}</small>
                                            </td>
                                            <td>{{ $training->trainer }}</td>
                                            <td>
                                                <span class="badge bg-{{ $training->type == 'internal' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($training->type) }}
                                                </span>
                                            </td>
                                            <td>{{ $training->duration_hours }} Hrs</td>
                                            <td>{{ $training->sessions->count() }}</td>
                                            <td>
                                                @can('hrm.trainings.edit')
                                                <button class="btn btn-sm btn-soft-primary" 
                                                        data-bs-toggle="offcanvas" 
                                                        data-bs-target="#editTraining{{ $training->id }}">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                @endcan
                                            </td>
                                        </tr>

                                        <!-- Edit Offcanvas (Loop) -->
                                        <div class="offcanvas offcanvas-end" tabindex="-1" id="editTraining{{ $training->id }}" aria-labelledby="editTrainingLabel{{ $training->id }}">
                                            <div class="offcanvas-header">
                                                <h5 id="editTrainingLabel{{ $training->id }}" class="offcanvas-title">Edit Training Program</h5>
                                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                            </div>
                                            <div class="offcanvas-body">
                                                <form action="{{ route('hrm.trainings.update', $training->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="mb-3">
                                                        <label class="form-label">Training Title</label>
                                                        <input type="text" name="title" class="form-control" value="{{ $training->title }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Trainer Name</label>
                                                        <input type="text" name="trainer" class="form-control" value="{{ $training->trainer }}" required>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Type</label>
                                                            <select name="type" class="form-select">
                                                                <option value="internal" {{ $training->type == 'internal' ? 'selected' : '' }}>Internal</option>
                                                                <option value="external" {{ $training->type == 'external' ? 'selected' : '' }}>External</option>
                                                                <option value="online" {{ $training->type == 'online' ? 'selected' : '' }}>Online</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Duration (Hours)</label>
                                                            <input type="number" name="duration_hours" class="form-control" value="{{ $training->duration_hours }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description</label>
                                                        <textarea name="description" class="form-control" rows="3">{{ $training->description }}</textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary w-100">Update Program</button>
                                                </form>
                                            </div>
                                        </div>
                                        @endforeach
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

<!-- Add Program Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddTraining" aria-labelledby="offcanvasAddTrainingLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasAddTrainingLabel" class="offcanvas-title">Add New Training Program</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.trainings.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Training Code</label>
                <input type="text" name="code" class="form-control" placeholder="e.g. TRN-LDR-001" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Training Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Trainer Name</label>
                <input type="text" name="trainer" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="internal">Internal</option>
                        <option value="external">External</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Duration (Hours)</label>
                    <input type="number" name="duration_hours" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Create Program</button>
        </form>
    </div>
</div>

<!-- Schedule Session Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasScheduleSession" aria-labelledby="offcanvasScheduleSessionLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasScheduleSessionLabel" class="offcanvas-title">Schedule Training Session</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.trainings.sessions.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Select Program</label>
                <select name="training_id" class="form-select" required>
                    <option value="">Select a training...</option>
                    @foreach($trainings as $tr)
                        <option value="{{ $tr->id }}">{{ $tr->title }} ({{ $tr->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Start Date & Time</label>
                <input type="datetime-local" name="start_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">End Date & Time</label>
                <input type="datetime-local" name="end_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Location / Link</label>
                <input type="text" name="location" class="form-control" placeholder="Room 101 or Zoom Link" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Max Participants</label>
                <input type="number" name="max_participants" class="form-control" value="20">
            </div>
            <button type="submit" class="btn btn-primary w-100">Schedule Session</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<!-- FullCalendar logic placeholder - integrating basic list for now, ideally needs full JS setup -->
@endsection
