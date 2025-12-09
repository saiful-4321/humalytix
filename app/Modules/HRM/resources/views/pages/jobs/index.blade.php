@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12">
            <h2>Job Postings</h2>
        </div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Jobs</li>
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
                    <form action="{{ route('hrm.jobs.index') }}" method="GET" class="d-flex gap-2">
                        <select name="department_id" class="form-select">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        </select>
                        <button type="submit" class="btn btn-secondary">Filter</button>
                    </form>
                    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#addJobOffcanvas">
                        <i class="bx bx-plus"></i> Post New Job
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Department</th>
                                <th>Positions</th>
                                <th>Type</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobs as $job)
                            <tr>
                                <td>
                                    <strong>{{ $job->title }}</strong><br>
                                    <small class="text-muted">{{ $job->code }}</small>
                                </td>
                                <td>{{ $job->department->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-soft-info text-info">{{ $job->positions }}</span></td>
                                <td>{{ ucwords(str_replace('_', ' ', $job->employment_type)) }}</td>
                                <td>
                                    @if($job->deadline)
                                    {{ $job->deadline->format('d M, Y') }}
                                    @else
                                    <span class="text-muted">No Deadline</span>
                                    @endif
                                </td>
                                <td>
                                    @if($job->status == 'open')<span class="badge bg-success">Open</span>
                                    @elseif($job->status == 'closed')<span class="badge bg-danger">Closed</span>
                                    @else<span class="badge bg-warning">On Hold</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-horizontal"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('hrm.jobs.show', $job->id) }}"><i class="bx bx-show me-2"></i> View Details</a></li>
                                            <li><a class="dropdown-item" href="{{ route('hrm.jobs.edit', $job->id) }}"><i class="bx bx-edit me-2"></i> Edit</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('hrm.jobs.destroy', $job->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')"><i class="bx bx-trash me-2"></i> Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">No jobs found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $jobs->links() }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Add Job Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="addJobOffcanvas" aria-labelledby="addJobLabel" style="width: 600px;">
    <div class="offcanvas-header">
        <h5 id="addJobLabel">Post New Job</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.jobs.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Job Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <select name="department_id" class="form-select" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Vacancies <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="positions" value="1" min="1" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select name="employment_type" class="form-select" required>
                        <option value="full_time">Full Time</option>
                        <option value="part_time">Part Time</option>
                        <option value="contract">Contract</option>
                        <option value="internship">Internship</option>
                        <option value="remote">Remote</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="open">Open</option>
                        <option value="closed">Closed</option>
                        <option value="on_hold">On Hold</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control" name="description" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Requirements</label>
                <textarea class="form-control" name="requirements" rows="3"></textarea>
            </div>
            
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">Salary Range</label>
                    <input type="text" class="form-control" name="salary_range" placeholder="e.g. 50k - 80k">
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Application Deadline</label>
                    <input type="date" class="form-control" name="deadline">
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Post Job</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
