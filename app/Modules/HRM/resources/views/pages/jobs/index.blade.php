@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Job Postings</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
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
        <div class="card bg-white">
            <div class="card-header border-bottom">
                 <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Job Listings</h6>
                    
                    <div class="d-flex align-items-center gap-2">
                        <a href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#addJobOffcanvas" class="btn btn-info btn-sm d-flex align-items-center font-weight-medium">
                            <i class="mdi mdi-plus me-1"></i> Post New Job
                        </a>
                        
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#jobFilter" aria-controls="jobFilter">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Active Filters Section -->
            @if(request()->hasAny(['department_id', 'status', 'employment_type']))
                <x-Main::active-filters :url="route('hrm.jobs.index')">
                    <x-Main::active-filter-item key="department_id" label="Department" :value="$departments->where('id', request('department_id'))->first()->name ?? request('department_id')" />
                    <x-Main::active-filter-item key="employment_type" label="Type" :value="ucwords(str_replace('_', ' ', request('employment_type')))" />
                    <x-Main::active-filter-item key="status" label="Status" :value="ucfirst(str_replace('_', ' ', request('status')))" />
                </x-Main::active-filters>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Job Title</th>
                                <th>Department</th>
                                <th>Positions</th>
                                <th>Type</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobs as $job)
                            <tr>
                                <td>
                                    <div>
                                        <h6 class="mb-0 font-size-14">{{ $job->title }}</h6>
                                        <small class="text-muted">{{ $job->code }}</small>
                                    </div>
                                </td>
                                <td>{{ $job->department->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-soft-info text-info rounded-pill">{{ $job->positions }} Openings</span></td>
                                <td><span class="badge bg-light text-dark">{{ ucwords(str_replace('_', ' ', $job->employment_type)) }}</span></td>
                                <td>
                                    @if($job->deadline)
                                    {{ $job->deadline->format('d M, Y') }}
                                    @else
                                    <span class="text-muted small">No Deadline</span>
                                    @endif
                                </td>
                                <td>
                                    @if($job->status == 'open')<span class="badge bg-soft-success text-success">Open</span>
                                    @elseif($job->status == 'closed')<span class="badge bg-soft-danger text-danger">Closed</span>
                                    @else<span class="badge bg-soft-warning text-warning">On Hold</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-muted font-size-16 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
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
                            <tr><td colspan="7" class="text-center py-4 text-muted">
                                <i class="mdi mdi-briefcase-outline font-size-24 d-block mb-2"></i>
                                No jobs found.
                            </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($jobs->count())
            <div class="card-footer bg-transparent border-top">
                <div class="d-flex justify-content-end">
                    {{ $jobs->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Add Job Offcanvas --}}
<div class="offcanvas offcanvas-end w-50" tabindex="-1" id="addJobOffcanvas" aria-labelledby="addJobLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="addJobLabel">Post New Job</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.jobs.store') }}" method="POST">
            @csrf
            
            <h5 class="font-size-14 text-uppercase mb-3">Job Details</h5>
            <div class="mb-3">
                <label class="form-label">Job Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" required placeholder="e.g. Senior Software Engineer">
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <select name="department_id" class="form-select select2" required>
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

            <h5 class="font-size-14 text-uppercase mb-3 mt-4">Description & Requirements</h5>
            <div class="mb-3">
                <label class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control" name="description" rows="4" required placeholder="Job description..."></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Requirements</label>
                <textarea class="form-control" name="requirements" rows="4" placeholder="Job requirements..."></textarea>
            </div>
            
            <h5 class="font-size-14 text-uppercase mb-3 mt-4">Additional Info</h5>
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
            </div>
        </form>
    </div>
</div>

{{-- Filter Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="jobFilter" aria-labelledby="jobFilterLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="jobFilterLabel">Filter Jobs</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.jobs.index') }}" method="GET">
            <div class="mb-3">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select select2">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Employment Type</label>
                <select name="employment_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="full_time" {{ request('employment_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                    <option value="part_time" {{ request('employment_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                    <option value="contract" {{ request('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                    <option value="internship" {{ request('employment_type') == 'internship' ? 'selected' : '' }}>Internship</option>
                    <option value="remote" {{ request('employment_type') == 'remote' ? 'selected' : '' }}>Remote</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                </select>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Apply Filter</button>
                <a href="{{ route('hrm.jobs.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>
</div>
@endsection
