@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Performance Appraisals</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Appraisals</li>
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
                    <div><button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas"><i class="bx bx-filter"></i> Filter</button></div>
                    <div>@can('hrm.appraisals.create')<a href="{{ route('hrm.appraisals.create') }}" class="btn btn-success"><i class="bx bx-plus"></i> New Appraisal</a>@endcan</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Employee</th><th>Reviewer</th><th>Period</th><th>Review Date</th><th>Score</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            @forelse($appraisals as $appraisal)
                            <tr>
                                <td>{{ $appraisal->employee->full_name }}</td>
                                <td>{{ $appraisal->reviewer->full_name }}</td>
                                <td>{{ $appraisal->review_period }}</td>
                                <td>{{ $appraisal->review_date->format('d M, Y') }}</td>
                                <td><span class="badge bg-{{ $appraisal->performance_score >= 7 ? 'success' : ($appraisal->performance_score >= 5 ? 'warning' : 'danger') }}">{{ $appraisal->performance_score }}/10</span></td>
                                <td>
                                    @if($appraisal->status == 'completed')<span class="badge bg-success">Completed</span>
                                    @else<span class="badge bg-secondary">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    @can('hrm.appraisals.view')<a href="{{ route('hrm.appraisals.show', $appraisal) }}" class="btn btn-sm btn-soft-info"><i class="bx bx-show"></i></a>@endcan
                                    @can('hrm.appraisals.edit')<a href="{{ route('hrm.appraisals.edit', $appraisal) }}" class="btn btn-sm btn-soft-primary"><i class="bx bx-edit"></i></a>@endcan
                                    @can('hrm.appraisals.delete')
                                    <form action="{{ route('hrm.appraisals.destroy', $appraisal) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted"><i class="bx bx-star bx-lg d-block mb-2"></i>No appraisals found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $appraisals->links() }}</div>
            </div>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas">
    <div class="offcanvas-header"><h5>Filter Appraisals</h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button></div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.appraisals.index') }}" method="GET">
            <div class="mb-3"><label class="form-label">Employee</label><select class="form-select" name="employee_id"><option value="">All Employees</option>@foreach($employees as $emp)<option value="{{ $emp->id }}">{{ $emp->full_name }}</option>@endforeach</select></div>
            <div class="mb-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="">All Status</option><option value="draft">Draft</option><option value="completed">Completed</option></select></div>
            <div class="d-grid gap-2"><button type="submit" class="btn btn-primary">Apply Filters</button><a href="{{ route('hrm.appraisals.index') }}" class="btn btn-secondary">Clear</a></div>
        </form>
    </div>
</div>
@endsection
