@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Resignation Details</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.resignations.index') }}">Resignations</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ul>
        </div>
    </div>
</div>
@include("Main::widgets.message.sweet-alert")
<div class="row clearfix">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-soft-primary"><h5 class="card-title mb-0">Overview</h5></div>
            <div class="card-body">
                <h5>{{ $resignation->employee->full_name }}</h5>
                <p class="text-muted">{{ $resignation->employee->designation }}</p>
                <hr>
                <p><strong>Resigned:</strong> {{ $resignation->resignation_date->format('d M, Y') }}</p>
                <p><strong>Last Day:</strong> {{ $resignation->last_working_day->format('d M, Y') }}</p>
                <p><strong>Status:</strong> <span class="badge bg-{{ $resignation->status == 'approved' ? 'success' : 'warning' }}">{{ ucfirst($resignation->status) }}</span></p>
                @if($resignation->status == 'pending')
                <div class="d-grid gap-2 mt-3">
                    <form action="{{ route('hrm.resignations.update-status', $resignation) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <div class="mb-2"><label>Confirm Last Working Day</label><input type="date" name="last_working_day" value="{{ $resignation->notice_date->format('Y-m-d') }}" class="form-control"></div>
                        <button class="btn btn-success w-100">Approve Resignation</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Assigned Assets</h5></div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($resignation->employee->assets as $assignment)
                        @if(!$assignment->return_date)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $assignment->asset->name }}
                            <span class="badge bg-warning">Not Returned</span>
                        </li>
                        @endif
                    @endforeach
                    @if($resignation->employee->assets->whereNull('return_date')->count() == 0)
                    <li class="list-item text-center text-muted">No pending assets.</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        @if($resignation->status == 'approved')
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Clearance Checklist</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead><tr><th>Department</th><th>Status</th><th>Remarks</th><th>Action</th></tr></thead>
                        <tbody>
                            @forelse($checklist as $item)
                            <tr>
                                <td>{{ $item->department_name }}</td>
                                <td>
                                    @if($item->status == 'cleared')<span class="badge bg-success">Cleared</span>
                                    @else<span class="badge bg-danger">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $item->remarks }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#clearanceModal{{ $item->id }}">Update</button>
                                    <div class="modal fade" id="clearanceModal{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('hrm.resignations.update-clearance', $item) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header"><h5 class="modal-title">{{ $item->department_name }} Clearance</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                    <div class="modal-body">
                                                        <div class="mb-3"><label>Status</label><select name="status" class="form-select"><option value="cleared">Cleared</option><option value="uncleared">Uncleared</option></select></div>
                                                        <div class="mb-3"><label>Remarks</label><textarea name="remarks" class="form-control">{{ $item->remarks }}</textarea></div>
                                                    </div>
                                                    <div class="modal-footer"><button type="submit" class="btn btn-primary">Save</button></div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4">Checklist will be generated after approval.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title mb-0">Final Settlement</h5>
                @if(!$resignation->finalSettlement)
                <form action="{{ route('hrm.resignations.generate-settlement', $resignation) }}" method="POST">
                    @csrf
                    <button class="btn btn-sm btn-primary">Generate Settlement</button>
                </form>
                @endif
            </div>
            <div class="card-body">
                @if($resignation->finalSettlement)
                <div class="alert alert-info">Settlement Generated. Net Payable: <strong>{{ number_format($resignation->finalSettlement->net_payable, 2) }}</strong></div>
                @else
                <p class="text-muted">Settlement not yet processed.</p>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
