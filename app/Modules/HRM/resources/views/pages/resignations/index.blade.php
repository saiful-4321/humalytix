@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Resignations</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Resignations</li>
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
                    <h5 class="card-title">Resignation Requests</h5>
                    <button type="button" class="btn btn-danger" data-bs-toggle="offcanvas" data-bs-target="#addResignationOffcanvas"><i class="bx bx-plus"></i> Submit Resignation</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Employee</th><th>Resignation Date</th><th>Notice Date</th><th>Last Working Day</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            @forelse($resignations as $resignation)
                            <tr>
                                <td>{{ $resignation->employee->full_name }}</td>
                                <td>{{ $resignation->resignation_date->format('d M, Y') }}</td>
                                <td>{{ $resignation->notice_date->format('d M, Y') }}</td>
                                <td>{{ $resignation->last_working_day ? $resignation->last_working_day->format('d M, Y') : '-' }}</td>
                                <td>
                                    @if($resignation->status == 'approved')<span class="badge bg-success">Approved</span>
                                    @elseif($resignation->status == 'rejected')<span class="badge bg-danger">Rejected</span>
                                    @else<span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('hrm.resignations.show', $resignation) }}" class="btn btn-sm btn-soft-primary">Manage</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $resignations->links() }}</div>
            </div>
        </div>
    </div>
</div>
                <div class="mt-3">{{ $resignations->links() }}</div>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="addResignationOffcanvas" aria-labelledby="addResignationLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="addResignationLabel">Submit Resignation</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.resignations.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Employee <span class="text-danger">*</span></label>
                <select name="employee_id" class="form-select" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->employee_code }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Resignation Date <span class="text-danger">*</span></label>
                <input type="date" name="resignation_date" class="form-control" required value="{{ date('Y-m-d') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Notice Date <span class="text-danger">*</span></label>
                <input type="date" name="notice_date" class="form-control" required>
                <small class="text-muted">The date when notice period ends</small>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Reason <span class="text-danger">*</span></label>
                <textarea name="reason" class="form-control" rows="3" required placeholder="Reason for resignation..."></textarea>
            </div>
            
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
        </form>
    </div>
</div>
@endsection
