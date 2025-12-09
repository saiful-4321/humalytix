@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Candidate Profile</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.candidates.index') }}">Candidates</a></li>
                <li class="breadcrumb-item active">{{ $candidate->first_name }}</li>
            </ul>
        </div>
    </div>
</div>
@include("Main::widgets.message.sweet-alert")
<div class="row clearfix">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3"><div class="avatar-lg mx-auto bg-soft-primary rounded-circle d-flex align-items-center justify-content-center"><span class="fs-1">{{ substr($candidate->first_name, 0, 1) }}</span></div></div>
                <h5 class="mb-1">{{ $candidate->first_name }} {{ $candidate->last_name }}</h5>
                <p class="text-muted mb-3">{{ $candidate->job->title }}</p>
                <div class="d-grid gap-2">
                    <form action="{{ route('hrm.candidates.update-status', $candidate) }}" method="POST">
                        @csrf
                        <select class="form-select mb-2" name="status" onchange="this.form.submit()">
                            <option value="applied" {{ $candidate->status == 'applied' ? 'selected' : '' }}>Applied</option>
                            <option value="screening" {{ $candidate->status == 'screening' ? 'selected' : '' }}>Screening</option>
                            <option value="shortlisted" {{ $candidate->status == 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                            <option value="interview" {{ $candidate->status == 'interview' ? 'selected' : '' }}>Interview</option>
                            <option value="offered" {{ $candidate->status == 'offered' ? 'selected' : '' }}>Offered</option>
                            <option value="hired" {{ $candidate->status == 'hired' ? 'selected' : '' }}>Hired (Convert)</option>
                            <option value="rejected" {{ $candidate->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </form>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#scheduleInterviewModal"><i class="bx bx-calendar"></i> Schedule Interview</button>
                    @if($candidate->resume_path)
                    <a href="{{ asset('storage/' . $candidate->resume_path) }}" target="_blank" class="btn btn-outline-secondary"><i class="bx bx-download"></i> Download Resume</a>
                    @endif
                </div>
            </div>
            <div class="card-body border-top">
                <h6 class="mb-3">Contact Details</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="bx bx-envelope me-2"></i> {{ $candidate->email }}</li>
                    <li class="mb-2"><i class="bx bx-phone me-2"></i> {{ $candidate->phone }}</li>
                    <li class="mb-0"><i class="bx bx-calendar me-2"></i> {{ $candidate->created_at->format('d M, Y') }}</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Interviews</h5></div>
            <div class="card-body">
                @forelse($candidate->interviews as $interview)
                <div class="card border mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5>{{ ucfirst($interview->interview_type) }} Interview</h5>
                            <span class="badge bg-{{ $interview->status == 'completed' ? 'success' : 'warning' }}">{{ ucfirst($interview->status) }}</span>
                        </div>
                        <p class="mb-1"><i class="bx bx-calendar"></i> {{ $interview->scheduled_at->format('d M, Y h:i A') }}</p>
                        <p class="mb-2"><i class="bx bx-map"></i> {{ $interview->location ?? 'Online' }}</p>
                        @if($interview->feedback)
                        <div class="bg-light p-2 rounded"><strong>Feedback:</strong> {{ $interview->feedback }}</div>
                        @else
                        <button class="btn btn-sm btn-link p-0">Add Feedback</button>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-center text-muted">No interviews scheduled.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="scheduleInterviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hrm.candidates.schedule-interview', $candidate) }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Schedule Interview</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Type</label><select class="form-select" name="interview_type"><option value="phone">Phone Screen</option><option value="technical">Technical</option><option value="hr">HR Round</option><option value="final">Final Round</option></select></div>
                    <div class="mb-3"><label class="form-label">Date & Time</label><input type="datetime-local" class="form-control" name="scheduled_at" required></div>
                    <div class="mb-3"><label class="form-label">Duration (Minutes)</label><input type="number" class="form-control" name="duration_minutes" value="30" required></div>
                    <div class="mb-3"><label class="form-label">Location/Link</label><input type="text" class="form-control" name="location" placeholder="Meeting Room 1 or Zoom Link"></div>
                    <div class="mb-3"><label class="form-label">Interviewers</label>
                        <select class="form-select" name="interviewer_ids[]" multiple required>
                            @foreach($interviewers as $emp)<option value="{{ $emp->id }}">{{ $emp->full_name }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Schedule</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
