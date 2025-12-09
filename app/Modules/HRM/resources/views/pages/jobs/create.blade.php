@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Post New Job</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.jobs.index') }}">Jobs</a></li>
                <li class="breadcrumb-item active">Post Job</li>
            </ul>
        </div>
    </div>
</div>
@include("Main::widgets.message.sweet-alert")
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hrm.jobs.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Job Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Job Code</label>
                            <input type="text" class="form-control" name="code" value="{{ old('code') }}" placeholder="Auto-generated if empty">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select @error('department_id') is-invalid @enderror" name="department_id" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Positions <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('positions') is-invalid @enderror" name="positions" value="{{ old('positions', 1) }}" min="1" required>
                            @error('positions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('employment_type') is-invalid @enderror" name="employment_type" required>
                                <option value="full_time">Full Time</option>
                                <option value="part_time">Part Time</option>
                                <option value="contract">Contract</option>
                                <option value="internship">Internship</option>
                            </select>
                            @error('employment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Experience Required</label>
                            <input type="text" class="form-control" name="experience_required" value="{{ old('experience_required') }}" placeholder="e.g., 2-3 years">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Salary Range</label>
                            <input type="text" class="form-control" name="salary_range" value="{{ old('salary_range') }}" placeholder="e.g., 30,000 - 50,000">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Application Deadline</label>
                            <input type="date" class="form-control" name="deadline" value="{{ old('deadline') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Job Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4" required>{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Requirements</label>
                        <textarea class="form-control" name="requirements" rows="4">{{ old('requirements') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Responsibilities</label>
                        <textarea class="form-control" name="responsibilities" rows="4">{{ old('responsibilities') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                            <option value="on_hold">On Hold</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hrm.jobs.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success">Post Job</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
