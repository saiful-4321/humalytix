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
                    <a href="{{ route('hrm.resignations.create') }}" class="btn btn-danger"><i class="bx bx-plus"></i> Submit Resignation</a>
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
@endsection
