@extends('Main::layouts.app')

@section('title', $appraisal360->appraisal_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">{{ $appraisal360->appraisal_name }}</h4>
            <div class="text-muted">
                <i class="bx bx-user"></i> {{ $appraisal360->employee->full_name }} | 
                <i class="bx bx-calendar"></i> {{ $appraisal360->review_period }}
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('hrm.appraisals-360.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> Back
            </a>
            <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#addReviewerOffcanvas">
                <i class="bx bx-user-plus"></i> Add Reviewers
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Summary Card -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-3">Overall Progress</h6>
                    <div class="text-center mb-4">
                        <div class="display-4 fw-bold mb-2">
                            @if($appraisal360->overall_score)
                                {{ $appraisal360->overall_score }}<span class="fs-4 text-muted">/10</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $appraisal360->getCompletionPercentage() }}%"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">{{ $appraisal360->getCompletionPercentage() }}% Completed</small>
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Status</span>
                            <span class="badge bg-{{ $appraisal360->status == 'completed' ? 'success' : 'primary' }}">
                                {{ ucfirst($appraisal360->status) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Start Date</span>
                            <span>{{ $appraisal360->start_date->format('M d, Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Due Date</span>
                            <span>{{ $appraisal360->end_date->format('M d, Y') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Reviewers List -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Reviewers & Feedback</h5>
                </div>
                <div class="card-body">
                    @if($appraisal360->reviewers->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted">No reviewers assigned yet.</p>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="offcanvas" data-bs-target="#addReviewerOffcanvas">
                                Assign Reviewers
                            </button>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Reviewer</th>
                                        <th>Relation</th>
                                        <th>Status</th>
                                        <th>Score</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appraisal360->reviewers as $reviewer)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ substr($reviewer->reviewer->first_name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $reviewer->reviewer->full_name }}</h6>
                                                    <small class="text-muted">{{ $reviewer->reviewer->designation->name ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-label-info">{{ ucfirst($reviewer->reviewer_type) }}</span></td>
                                        <td>
                                            @if($reviewer->status == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($reviewer->rating)
                                                <span class="fw-bold">{{ $reviewer->rating }}/10</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            {{ $reviewer->completed_at ? $reviewer->completed_at->format('M d') : '-' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Reviewer Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="addReviewerOffcanvas" style="width: 500px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Add Reviewers</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.appraisals-360.add-reviewers', $appraisal360) }}" method="POST">
            @csrf
            
            <div id="reviewerFields">
                <div class="reviewer-row border-bottom pb-3 mb-3">
                    <div class="mb-3">
                        <label class="form-label">Reviewer</label>
                        <select name="reviewers[0][reviewer_id]" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach(\App\Modules\HRM\Models\Employee::active()->where('id', '!=', $appraisal360->employee_id)->orderBy('first_name')->get() as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Relationship</label>
                        <select name="reviewers[0][reviewer_type]" class="form-select" required>
                            <option value="peer">Peer</option>
                            <option value="manager">Manager</option>
                            <option value="subordinate">Subordinate</option>
                            <option value="self">Self</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-sm btn-outline-secondary mb-3" id="addMoreReviewer">
                <i class="bx bx-plus"></i> Add Another Reviewer
            </button>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Save Reviewers</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let reviewerIndex = 1;
$('#addMoreReviewer').click(function() {
    const html = `
        <div class="reviewer-row border-bottom pb-3 mb-3">
            <div class="d-flex justify-content-between mb-2">
                <strong>Reviewer #${reviewerIndex + 1}</strong>
                <button type="button" class="btn btn-xs btn-outline-danger remove-reviewer"><i class="bx bx-trash"></i></button>
            </div>
            <div class="mb-3">
                <label class="form-label">Reviewer</label>
                <select name="reviewers[${reviewerIndex}][reviewer_id]" class="form-select" required>
                    <option value="">Select Employee</option>
                    @foreach(\App\Modules\HRM\Models\Employee::active()->where('id', '!=', $appraisal360->employee_id)->orderBy('first_name')->get() as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Relationship</label>
                <select name="reviewers[${reviewerIndex}][reviewer_type]" class="form-select" required>
                    <option value="peer">Peer</option>
                    <option value="manager">Manager</option>
                    <option value="subordinate">Subordinate</option>
                </select>
            </div>
        </div>
    `;
    $('#reviewerFields').append(html);
    reviewerIndex++;
});

$(document).on('click', '.remove-reviewer', function() {
    $(this).closest('.reviewer-row').remove();
});
</script>
@endpush
@endsection
