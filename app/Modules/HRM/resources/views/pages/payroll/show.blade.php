@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Payroll Details</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.payroll.index') }}">Payroll</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ul>
        </div>
    </div>
</div>
@include("Main::widgets.message.sweet-alert")
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center mb-4">
                    <div class="col-md-8">
                        <h4 class="mb-1">{{ $payroll->employee->full_name }}</h4>
                        <p class="text-muted mb-2">{{ date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) }}</p>
                        @if($payroll->status == 'paid')<span class="badge bg-success">Paid</span>
                        @else<span class="badge bg-warning">Pending</span>
                        @endif
                    </div>
                    <div class="col-md-4 text-end">
                        @if($payroll->status == 'pending')
                        @can('hrm.payroll.process')
                        <form action="{{ route('hrm.payroll.process', $payroll) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Process this payroll?')"><i class="bx bx-check"></i> Process Payment</button>
                        </form>
                        @endcan
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Employee Information</h5>
                <table class="table table-sm">
                    <tr><th width="40%">Name:</th><td>{{ $payroll->employee->full_name }}</td></tr>
                    <tr><th>Code:</th><td>{{ $payroll->employee->employee_code }}</td></tr>
                    <tr><th>Department:</th><td>{{ $payroll->employee->department->name ?? 'N/A' }}</td></tr>
                    <tr><th>Branch:</th><td>{{ $payroll->employee->branch->name ?? 'N/A' }}</td></tr>
                    <tr><th>Designation:</th><td>{{ $payroll->employee->designation }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Salary Breakdown</h5>
                <table class="table table-sm">
                    <tr><th width="50%">Basic Salary:</th><td class="text-end">৳{{ number_format($payroll->basic_salary, 2) }}</td></tr>
                    <tr><th>Allowances:</th><td class="text-end">৳{{ number_format($payroll->allowances, 2) }}</td></tr>
                    <tr><th>Bonuses:</th><td class="text-end">৳{{ number_format($payroll->bonuses, 2) }}</td></tr>
                    <tr class="table-active"><th><strong>Gross Salary:</strong></th><td class="text-end"><strong>৳{{ number_format($payroll->gross_salary, 2) }}</strong></td></tr>
                    <tr><th>Deductions:</th><td class="text-end text-danger">-৳{{ number_format($payroll->deductions, 2) }}</td></tr>
                    <tr><th>Tax:</th><td class="text-end text-danger">-৳{{ number_format($payroll->tax, 2) }}</td></tr>
                    <tr class="table-success"><th><strong>Net Salary:</strong></th><td class="text-end"><strong>৳{{ number_format($payroll->net_salary, 2) }}</strong></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Payment Information</h5>
                <div class="row">
                    <div class="col-md-3"><p class="text-muted mb-1">Period</p><h6>{{ date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) }}</h6></div>
                    <div class="col-md-3"><p class="text-muted mb-1">Status</p><h6>
                        @if($payroll->status == 'paid')<span class="badge bg-success">Paid</span>
                        @else<span class="badge bg-warning">Pending</span>
                        @endif
                    </h6></div>
                    <div class="col-md-3"><p class="text-muted mb-1">Payment Date</p><h6>{{ $payroll->payment_date?->format('d M, Y') ?? 'Not Paid' }}</h6></div>
                    <div class="col-md-3"><p class="text-muted mb-1">Created At</p><h6>{{ $payroll->created_at->format('d M, Y') }}</h6></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
