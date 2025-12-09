@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Payroll Slip</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.payroll.index') }}">Payroll</a></li>
                <li class="breadcrumb-item active">Slip #{{ $payroll->id }}</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix justify-content-center">
    <div class="col-lg-8">
        <div class="card bg-white" id="payslip">
            <div class="card-body">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-6">
                        <h4 class="mb-0 text-primary fw-bold">PAYSLIP</h4>
                        <p class="text-muted mb-0">
                            For {{ date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) }}
                        </p>
                    </div>
                    <div class="col-6 text-end">
                        <h5 class="mb-0">{{ config('app.name') }}</h5>
                        <p class="text-muted font-size-13 mb-0">123 Business Road, Corporate City</p>
                    </div>
                </div>

                <hr>

                <!-- Employee Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="font-size-12 text-uppercase text-muted">Employee Details</h6>
                        <h5 class="mb-1">{{ $payroll->employee->full_name }}</h5>
                        <p class="mb-1 text-muted">{{ $payroll->employee->designation }}</p>
                        <p class="mb-0 text-muted">ID: {{ $payroll->employee->employee_code }}</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h6 class="font-size-12 text-uppercase text-muted">Payment Details</h6>
                        <p class="mb-1"><strong>Status:</strong> <span class="badge {{ $payroll->status == 'paid' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst($payroll->status) }}</span></p>
                        @if($payroll->payment_date)
                            <p class="mb-0 text-muted">Date: {{ $payroll->payment_date->format('d M, Y') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Breakdown Table -->
                <div class="table-responsive border rounded mb-4">
                    <table class="table table-bordered mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="w-50">Earnings</th>
                                <th class="text-end">Amount</th>
                                <th class="w-50 border-start">Deductions</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $earnings = $payroll->items->where('type', 'earning');
                                $deductions = $payroll->items->where('type', 'deduction');
                                $maxRows = max($earnings->count(), $deductions->count());
                                // We also need to account for Basic if it's not in items, but controller adds it to items now.
                            @endphp
                            
                            @for($i=0; $i<$maxRows; $i++)
                                <tr>
                                    <td>{{ $earnings->values()->get($i)->component_name ?? '' }}</td>
                                    <td class="text-end">{{ isset($earnings->values()->get($i)) ? '$'.number_format($earnings->values()->get($i)->amount, 2) : '' }}</td>
                                    
                                    <td class="border-start">{{ $deductions->values()->get($i)->component_name ?? '' }}</td>
                                    <td class="text-end">{{ isset($deductions->values()->get($i)) ? '$'.number_format($deductions->values()->get($i)->amount, 2) : '' }}</td>
                                </tr>
                            @endfor
                            
                            <!-- Totals -->
                            <tr class="fw-bold bg-light">
                                <td>Total Earnings</td>
                                <td class="text-end text-success">${{ number_format($payroll->gross_salary, 2) }}</td>
                                <td class="border-start">Total Deductions</td>
                                <td class="text-end text-danger">${{ number_format($payroll->items->where('type', 'deduction')->sum('amount'), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Net Salary -->
                <div class="row items-center">
                    <div class="col-md-6">
                        <p class="text-muted small mb-0">
                            *This is a computer generated document and does not require signature.
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="border p-3 d-inline-block rounded bg-soft-primary min-w-200">
                            <h6 class="text-uppercase text-muted font-size-12 mb-1">Net Pay</h6>
                            <h3 class="text-primary fw-bold mb-0">${{ number_format($payroll->net_salary, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mt-3 mb-5">
            <button class="btn btn-primary" onclick="window.print()"><i class="mdi mdi-printer me-1"></i> Print</button>
            <a href="{{ route('hrm.payroll.index') }}" class="btn btn-light">Back to List</a>
        </div>
    </div>
</div>
@endsection
