@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Job Details</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.jobs.index') }}">Jobs</a></li>
                <li class="breadcrumb-item active">{{ $job->title }}</li>
            </ul>
        </div>
    </div>
</div>
@include("Main::widgets.message.sweet-alert")
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center mb-4">
                    <div class="col-md-8">
                        <h4 class="mb-1">{{ $job->title }}</h4>
                        <p class="text-muted mb-2">{{ $job->code }} | {{ $job->department->name }}</p>
                        @if($job->status == 'open')<span class="badge bg-success">Open</span>
                        @elseif($job->status == 'closed')<span class="badge bg-danger">Closed</span>
                        @else<span class="badge bg-warning">On Hold</span>
                        @endif
                    </div>
                    <div class="col-md-4 text-end">
                        @can('hrm.jobs.edit')
                        <a href="{{ route('hrm.jobs.edit', $job) }}" class="btn btn-primary"><i class="bx bx-edit"></i> Edit Job</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4"><div class="card"><div class="card-body text-center"><h4 class="mb-0">{{ $stats['total_candidates'] }}</h4><p class="text-muted mb-0">Total Candidates</p></div></div></div>
    <div class="col-lg-4"><div class="card"><div class="card-body text-center"><h4 class="mb-0">{{ $stats['shortlisted'] }}</h4><p class="text-muted mb-0">Shortlisted</p></div></div></div>
    <div class="col-lg-4"><div class="card"><div class="card-body text-center"><h4 class="mb-0">{{ $stats['interviewed'] }}</h4><p class="text-muted mb-0">Interviewed</p></div></div></div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Job Information</h5>
                <table class="table table-sm">
                    <tr><th width="40%">Code:</th><td><strong>{{ $job->code }}</strong></td></tr>
                    <tr><th>Title:</th><td>{{ $job->title }}</td></tr>
                    <tr><th>Department:</th><td>{{ $job->department->name }}</td></tr>
                    <tr><th>Positions:</th><td>{{ $job->positions }}</td></tr>
                    <tr><th>Employment Type:</th><td>{{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}</td></tr>
                    <tr><th>Experience:</th><td>{{ $job->experience_required ?? 'Not specified' }}</td></tr>
                    <tr><th>Salary Range:</th><td>{{ $job->salary_range ?? 'Not specified' }}</td></tr>
                    <tr><th>Deadline:</th><td>{{ $job->deadline?->format('d M, Y') ?? 'Not specified' }}</td></tr>
                    <tr><th>Status:</th><td>
                        @if($job->status == 'open')<span class="badge bg-success">Open</span>
                        @elseif($job->status == 'closed')<span class="badge bg-danger">Closed</span>
                        @else<span class="badge bg-warning">On Hold</span>
                        @endif
                    </td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Job Description</h5>
                <p class="text-muted">{{ $job->description }}</p>
                @if($job->requirements)
                <h6 class="mt-3">Requirements:</h6>
                <p class="text-muted">{{ $job->requirements }}</p>
                @endif
                @if($job->responsibilities)
                <h6 class="mt-3">Responsibilities:</h6>
                <p class="text-muted">{{ $job->responsibilities }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
