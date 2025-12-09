@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Payroll Management</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Payroll</li>
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
                    <div>
                        @can('hrm.payroll.create')
                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#bulkGenerateModal"><i class="bx bx-cog"></i> Bulk Generate</button>
                        <a href="{{ route('hrm.payroll.create') }}" class="btn btn-success"><i class="bx bx-plus"></i> Create Payroll</a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Employee</th><th>Period</th><th>Basic</th><th>Gross</th><th>Deductions</th><th>Net</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            @forelse($payrolls as $payroll)
                            <tr>
                                <td>{{ $payroll->employee->full_name }}</td>
                                <td>{{ date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) }}</td>
                                <td>৳{{ number_format($payroll->basic_salary, 2) }}</td>
                                <td>৳{{ number_format($payroll->gross_salary, 2) }}</td>
                                <td>৳{{ number_format(($payroll->deductions + $payroll->tax), 2) }}</td>
                                <td><strong>৳{{ number_format($payroll->net_salary, 2) }}</strong></td>
                                <td>
                                    @if($payroll->status == 'paid')<span class="badge bg-success">Paid</span>
                                    @elseif($payroll->status == 'pending')<span class="badge bg-warning">Pending</span>
                                    @else<span class="badge bg-danger">{{ ucfirst($payroll->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @can('hrm.payroll.view')<a href="{{ route('hrm.payroll.show', $payroll) }}" class="btn btn-sm btn-soft-info"><i class="bx bx-show"></i></a>@endcan
                                    @if($payroll->status == 'pending')
                                    @can('hrm.payroll.process')
                                    <form action="{{ route('hrm.payroll.process', $payroll) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-soft-success" onclick="return confirm('Process this payroll?')"><i class="bx bx-check"></i></button>
                                    </form>
                                    @endcan
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center py-4 text-muted"><i class="bx bx-money bx-lg d-block mb-2"></i>No payroll records found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $payrolls->links() }}</div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="bulkGenerateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hrm.payroll.bulk-generate') }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Bulk Generate Payroll</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Month <span class="text-danger">*</span></label><select class="form-select" name="month" required>@for($i=1;$i<=12;$i++)<option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$i,1)) }}</option>@endfor</select></div>
                    <div class="mb-3"><label class="form-label">Year <span class="text-danger">*</span></label><input type="number" class="form-control" name="year" value="{{ date('Y') }}" min="2020" required></div>
                    <p class="text-muted small">This will generate payroll for all active employees with salary information.</p>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Generate Payroll</button></div>
            </form>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas">
    <div class="offcanvas-header"><h5>Filter Payroll</h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button></div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.payroll.index') }}" method="GET">
            <div class="mb-3"><label class="form-label">Month</label><select class="form-select" name="month"><option value="">All Months</option>@for($i=1;$i<=12;$i++)<option value="{{ $i }}">{{ date('F', mktime(0,0,0,$i,1)) }}</option>@endfor</select></div>
            <div class="mb-3"><label class="form-label">Year</label><input type="number" class="form-control" name="year" placeholder="e.g., 2024"></div>
            <div class="mb-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="">All Status</option><option value="pending">Pending</option><option value="paid">Paid</option></select></div>
            <div class="d-grid gap-2"><button type="submit" class="btn btn-primary">Apply Filters</button><a href="{{ route('hrm.payroll.index') }}" class="btn btn-secondary">Clear</a></div>
        </form>
    </div>
</div>
@endsection
