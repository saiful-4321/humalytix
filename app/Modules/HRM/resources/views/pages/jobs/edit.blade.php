@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Edit Job - {{ $job->title }}</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.jobs.index') }}">Jobs</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ul>
        </div>
    </div>
</div>
@include("Main::widgets.message.sweet-alert")
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hrm.jobs.update', $job) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Job Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" value="{{ old('title', $job->title) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Job Code</label>
                            <input type="text" class="form-control" name="code" value="{{ old('code', $job->code) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select" name="department_id" required>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $job->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Positions <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="positions" value="{{ old('positions', $job->positions) }}" min="1" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="employment_type" required>
                                <option value="full_time" {{ $job->employment_type == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                <option value="part_time" {{ $job->employment_type == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                <option value="contract" {{ $job->employment_type == 'contract' ? 'selected' : '' }}>Contract</option>
                                <option value="internship" {{ $job->employment_type == 'internship' ? 'selected' : '' }}>Internship</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Experience Required</label>
                            <input type="text" class="form-control" name="experience_required" value="{{ old('experience_required', $job->experience_required) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Salary Range</label>
                            <input type="text" class="form-control" name="salary_range" value="{{ old('salary_range', $job->salary_range) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Application Deadline</label>
                            <input type="date" class="form-control" name="deadline" value="{{ old('deadline', $job->deadline?->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Job Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="description" rows="4" required>{{ old('description', $job->description) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Requirements</label>
                        <textarea class="form-control" name="requirements" rows="4">{{ old('requirements', $job->requirements) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Responsibilities</label>
                        <textarea class="form-control" name="responsibilities" rows="4">{{ old('responsibilities', $job->responsibilities) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            <option value="open" {{ $job->status == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="closed" {{ $job->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="on_hold" {{ $job->status == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hrm.jobs.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Job</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
