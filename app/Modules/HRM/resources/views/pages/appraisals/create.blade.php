@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Create Appraisal</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.appraisals.index') }}">Appraisals</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ul>
        </div>
    </div>
</div>
@include("Main::widgets.message.sweet-alert")
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hrm.appraisals.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employee <span class="text-danger">*</span></label>
                            <select class="form-select" name="employee_id" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reviewer <span class="text-danger">*</span></label>
                            <select class="form-select" name="reviewer_id" required>
                                <option value="">Select Reviewer</option>
                                @foreach($reviewers as $rev)
                                <option value="{{ $rev->id }}">{{ $rev->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Review Period <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="review_period" placeholder="e.g., Q1 2024" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Review Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="review_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Performance Score (1-10) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="performance_score" min="1" max="10" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Strengths</label>
                        <textarea class="form-control" name="strengths" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Areas for Improvement</label>
                        <textarea class="form-control" name="weaknesses" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Goals for Next Period</label>
                        <textarea class="form-control" name="goals" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Additional Comments</label>
                        <textarea class="form-control" name="comments" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            <option value="draft">Draft</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hrm.appraisals.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success">Create Appraisal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
