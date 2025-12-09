@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Loan Details</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.loans.index') }}">Loans</a></li>
                <li class="breadcrumb-item active">{{ $loan->loan_number }}</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-8">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <h6 class="font-weight-medium mb-0">Loan Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="200">Loan Number:</th>
                        <td>{{ $loan->loan_number }}</td>
                    </tr>
                    <tr>
                        <th>Employee:</th>
                        <td>{{ $loan->employee->full_name }} ({{ $loan->employee->employee_code }})</td>
                    </tr>
                    <tr>
                        <th>Loan Type:</th>
                        <td>{{ $loan->loanType->name }}</td>
                    </tr>
                    <tr>
                        <th>Loan Amount:</th>
                        <td class="fw-bold">${{ number_format($loan->loan_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Interest Rate:</th>
                        <td>{{ $loan->interest_rate }}% ({{ ucfirst($loan->interest_type) }})</td>
                    </tr>
                    <tr>
                        <th>Tenure:</th>
                        <td>{{ $loan->tenure_months }} months</td>
                    </tr>
                    <tr>
                        <th>Monthly EMI:</th>
                        <td class="fw-bold text-primary">${{ number_format($loan->monthly_installment, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Total Payable:</th>
                        <td>${{ number_format($loan->total_payable, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Total Paid:</th>
                        <td class="text-success">${{ number_format($loan->total_paid, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Outstanding:</th>
                        <td class="text-danger fw-bold">${{ number_format($loan->outstanding_balance, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Disbursement Date:</th>
                        <td>{{ $loan->disbursement_date->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <th>First EMI Date:</th>
                        <td>{{ $loan->first_installment_date->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            @if($loan->status == 'pending')
                                <span class="badge bg-warning">Pending Approval</span>
                            @elseif($loan->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($loan->status == 'completed')
                                <span class="badge bg-info">Completed</span>
                            @elseif($loan->status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                    </tr>
                    @if($loan->purpose)
                    <tr>
                        <th>Purpose:</th>
                        <td>{{ $loan->purpose }}</td>
                    </tr>
                    @endif
                </table>

                @if($loan->status == 'pending')
                <div class="mt-4 d-flex gap-2">
                    <form action="{{ route('hrm.loans.approve', $loan->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Approve this loan and generate installment schedule?')">
                            <i class="bx bx-check-circle me-1"></i> Approve Loan
                        </button>
                    </form>
                    <form action="{{ route('hrm.loans.reject', $loan->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this loan application?')">
                            <i class="bx bx-x-circle me-1"></i> Reject
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        @if($loan->installments->count())
        <div class="card bg-white mt-3">
            <div class="card-header border-bottom">
                <h6 class="font-weight-medium mb-0">Installment Schedule</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Paid Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loan->installments as $installment)
                            <tr>
                                <td>{{ $installment->installment_number }}</td>
                                <td>{{ $installment->due_date->format('d M, Y') }}</td>
                                <td>${{ number_format($installment->installment_amount, 2) }}</td>
                                <td>{{ $installment->paid_date?->format('d M, Y') ?? '-' }}</td>
                                <td>
                                    @if($installment->status == 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($installment->status == 'overdue')
                                        <span class="badge bg-danger">Overdue</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card bg-primary text-white border-0">
            <div class="card-body">
                <h6 class="text-white mb-3">Loan Summary</h6>
                <div class="mb-3">
                    <small class="text-white-50 d-block">Progress</small>
                    @php
                        $progress = $loan->total_payable > 0 ? ($loan->total_paid / $loan->total_payable) * 100 : 0;
                    @endphp
                    <div class="progress bg-white bg-opacity-25 mt-2" style="height: 8px;">
                        <div class="progress-bar bg-white" style="width: {{ $progress }}%"></div>
                    </div>
                    <small class="text-white mt-1">{{ number_format($progress, 1) }}% Repaid</small>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total EMIs:</span>
                    <span class="fw-bold">{{ $loan->tenure_months }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Paid EMIs:</span>
                    <span class="fw-bold">{{ $loan->installments->where('status', 'paid')->count() }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Remaining:</span>
                    <span class="fw-bold">{{ $loan->installments->where('status', '!=', 'paid')->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
