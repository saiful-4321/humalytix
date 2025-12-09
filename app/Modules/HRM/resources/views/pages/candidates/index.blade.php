@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Candidates</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
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
        <div class="card bg-white">
            <div class="card-header border-bottom">
                 <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Candidates List</h6>
                    
                    <div class="d-flex align-items-center gap-2">
                        <a href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#addCandidateOffcanvas" class="btn btn-info btn-sm d-flex align-items-center font-weight-medium">
                            <i class="mdi mdi-plus me-1"></i> Add Candidate
                        </a>
                        
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#candidateFilter" aria-controls="candidateFilter">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
                        </button>

                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-export me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Excel (XLSX)</a></li>
                                <li><a class="dropdown-item" href="#">CSV</a></li>
                                <li><a class="dropdown-item" href="#">PDF</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Filters Section -->
            @if(request()->hasAny(['job_id', 'status']))
                <x-Main::active-filters :url="route('hrm.candidates.index')">
                    <x-Main::active-filter-item key="job_id" label="Job Position" :value="$jobs->where('id', request('job_id'))->first()->title ?? request('job_id')" />
                    <x-Main::active-filter-item key="status" label="Status" :value="ucfirst(request('status'))" />
                </x-Main::active-filters>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Candidate Name</th>
                                <th>Job Position</th>
                                <th>Applied Date</th>
                                <th>Status</th>
                                <th>Contact</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($candidates as $candidate)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                         <div class="avatar-xs me-2">
                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12">
                                                {{ substr($candidate->first_name, 0, 1) }}{{ substr($candidate->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 font-size-14">{{ $candidate->full_name }}</h6>
                                        </div>
                                    </div>
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
                                    <span class="badge bg-soft-{{ $badgeColor }} text-{{ $badgeColor }}">{{ ucfirst($candidate->status) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="font-size-12 text-muted"><i class="bx bx-envelope me-1"></i> {{ $candidate->email }}</span>
                                        <span class="font-size-12 text-muted"><i class="bx bx-phone me-1"></i> {{ $candidate->phone }}</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-muted font-size-16 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('hrm.candidates.show', $candidate->id) }}"><i class="bx bx-show me-2"></i> View Details</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="#" method="POST" class="d-inline"> {{-- Placeholder delete route --}}
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
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="mdi mdi-account-group-outline font-size-24 d-block mb-2"></i>
                                    No candidates found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($candidates->count())
            <div class="card-footer bg-transparent border-top">
                <div class="d-flex justify-content-end">
                    {{ $candidates->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Add Candidate Offcanvas --}}
<div class="offcanvas offcanvas-end w-50" tabindex="-1" id="addCandidateOffcanvas" aria-labelledby="addCandidateLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="addCandidateLabel">Add New Candidate</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.candidates.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <h5 class="font-size-14 text-uppercase mb-3">Application Details</h5>
            <div class="mb-3">
                <label class="form-label">Job Position <span class="text-danger">*</span></label>
                <select name="job_id" class="form-select select2" required>
                    <option value="">Select Job</option>
                    @foreach($jobs as $job)
                    <option value="{{ $job->id }}">{{ $job->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="first_name" required placeholder="John">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="last_name" required placeholder="Doe">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                     <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" required placeholder="john.doe@example.com">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="phone" required placeholder="+1234567890">
                </div>
            </div>

            <h5 class="font-size-14 text-uppercase mb-3 mt-4">Documents</h5>
            <div class="mb-3">
                <label class="form-label">Resume (PDF/Doc)</label>
                <input type="file" class="form-control" name="resume">
            </div>

            <div class="mb-3">
                <label class="form-label">Cover Letter</label>
                <textarea class="form-control" name="cover_letter" rows="4" placeholder="Write a short cover letter..."></textarea>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Add Candidate</button>
            </div>
        </form>
    </div>
</div>

{{-- Filter Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="candidateFilter" aria-labelledby="candidateFilterLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="candidateFilterLabel">Filter Candidates</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.candidates.index') }}" method="GET">
            <div class="mb-3">
                <label class="form-label">Job Position</label>
                <select name="job_id" class="form-select select2">
                    <option value="">All Jobs</option>
                    @foreach($jobs as $job)
                    <option value="{{ $job->id }}" {{ request('job_id') == $job->id ? 'selected' : '' }}>{{ $job->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="applied" {{ request('status') == 'applied' ? 'selected' : '' }}>Applied</option>
                    <option value="screening" {{ request('status') == 'screening' ? 'selected' : '' }}>Screening</option>
                    <option value="interview" {{ request('status') == 'interview' ? 'selected' : '' }}>Interview</option>
                    <option value="offered" {{ request('status') == 'offered' ? 'selected' : '' }}>Offered</option>
                    <option value="hired" {{ request('status') == 'hired' ? 'selected' : '' }}>Hired</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Apply Filter</button>
                <a href="{{ route('hrm.candidates.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>
</div>
@endsection
