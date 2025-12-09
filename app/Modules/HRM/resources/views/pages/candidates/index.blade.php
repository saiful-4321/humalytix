@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12">
            <h2>Candidates</h2>
        </div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Candidates</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <form action="{{ route('hrm.candidates.index') }}" method="GET" class="d-flex gap-2">
                        <select name="job_id" class="form-select">
                            <option value="">All Jobs</option>
                            @foreach($jobs as $job)
                            <option value="{{ $job->id }}" {{ request('job_id') == $job->id ? 'selected' : '' }}>{{ $job->title }}</option>
                            @endforeach
                        </select>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="applied" {{ request('status') == 'applied' ? 'selected' : '' }}>Applied</option>
                            <option value="screening" {{ request('status') == 'screening' ? 'selected' : '' }}>Screening</option>
                            <option value="interview" {{ request('status') == 'interview' ? 'selected' : '' }}>Interview</option>
                            <option value="offered" {{ request('status') == 'offered' ? 'selected' : '' }}>Offered</option>
                            <option value="hired" {{ request('status') == 'hired' ? 'selected' : '' }}>Hired</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        <button type="submit" class="btn btn-secondary">Filter</button>
                    </form>
                    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#addCandidateOffcanvas">
                        <i class="bx bx-plus"></i> Add Candidate
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Candidate Name</th>
                                <th>Job Position</th>
                                <th>Applied Date</th>
                                <th>Status</th>
                                <th>Contact</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($candidates as $candidate)
                            <tr>
                                <td>
                                    <h6 class="mb-0">{{ $candidate->full_name }}</h6>
                                </td>
                                <td>{{ $candidate->job->title ?? 'N/A' }}</td>
                                <td>{{ $candidate->created_at->format('d M, Y') }}</td>
                                <td>
                                    @php
                                        $badges = [
                                            'applied' => 'info', 'screening' => 'secondary', 'shortlisted' => 'primary',
                                            'interview' => 'warning', 'offered' => 'purple', 'hired' => 'success', 'rejected' => 'danger'
                                        ];
                                        $badgeColor = $badges[$candidate->status] ?? 'light';
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }}">{{ ucfirst($candidate->status) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="font-size-12"><i class="bx bx-envelope me-1"></i> {{ $candidate->email }}</span>
                                        <span class="font-size-12"><i class="bx bx-phone me-1"></i> {{ $candidate->phone }}</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('hrm.candidates.show', $candidate->id) }}" class="btn btn-sm btn-soft-primary"><i class="bx bx-show"></i> View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No candidates found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $candidates->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Candidate Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="addCandidateOffcanvas" aria-labelledby="addCandidateLabel" style="width: 500px;">
    <div class="offcanvas-header">
        <h5 id="addCandidateLabel">Add New Candidate</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.candidates.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Job Position <span class="text-danger">*</span></label>
                <select name="job_id" class="form-select" required>
                    <option value="">Select Job</option>
                    @foreach($jobs as $job)
                    <option value="{{ $job->id }}">{{ $job->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="first_name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="last_name" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="phone" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Resume (PDF/Doc)</label>
                <input type="file" class="form-control" name="resume">
            </div>

            <div class="mb-3">
                <label class="form-label">Cover Letter</label>
                <textarea class="form-control" name="cover_letter" rows="4"></textarea>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Add Candidate</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
