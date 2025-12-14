@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Bank Transfer</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Bank Transfer</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                 <div class="d-flex align-items-center justify-content-between py-1">
                    <div>
                        <h6 class="font-weight-medium mb-0">Disbursement List</h6>
                        <small class="text-muted">{{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-soft-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterCanvas">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
                        </button>
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#generateCanvas" {{ $payrolls->isEmpty() ? 'disabled' : '' }}>
                            <i class="mdi mdi-download me-1"></i> Generate File
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Employee</th>
                                <th>Bank Details</th>
                                <th>Account No.</th>
                                <th>Net Salary</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $payroll)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                       @if($payroll->employee->photo)
                                           <img src="{{ asset($payroll->employee->photo) }}" class="rounded-circle avatar-xs me-2" alt="">
                                       @else
                                           <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12">
                                                    {{ substr($payroll->employee->first_name, 0, 1) }}
                                                </span>
                                           </div>
                                       @endif
                                       <div>
                                           <h6 class="mb-0 font-size-14">{{ $payroll->employee->full_name }}</h6>
                                           <small class="text-muted">{{ $payroll->employee->employee_code }}</small>
                                       </div>
                                   </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">{{ $payroll->employee->bank_name ?? 'N/A' }}</span>
                                        <small class="text-muted">{{ $payroll->employee->bank_branch ?? '' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-monospace">{{ $payroll->employee->bank_account ?? 'N/A' }}</span>
                                </td>
                                <td><strong>${{ number_format($payroll->net_salary, 2) }}</strong></td>
                                <td>
                                    @if($payroll->employee->bank_account)
                                        <span class="badge bg-soft-success text-success">Ready</span>
                                    @else
                                        <span class="badge bg-soft-warning text-warning">Missing Info</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-bank-transfer font-size-24 d-block mb-2"></i>
                                    No paid payroll records found for {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($payrolls->count())
            <div class="card-footer bg-white border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Total Amount: <strong>${{ number_format($payrolls->sum('net_salary'), 2) }}</strong></span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Filter Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterCanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Filter Disbursement</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.bank-transfers.index') }}" method="GET">
            <div class="mb-3">
                <label class="form-label">Month</label>
                <select name="month" class="form-select">
                    @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Year</label>
                <select name="year" class="form-select">
                    @for($i = date('Y'); $i >= 2020; $i--)
                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Apply Filter</button>
            </div>
        </form>
    </div>
</div>

{{-- Generate File Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="generateCanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Generate Transfer File</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.bank-transfers.generate') }}" method="POST">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div class="alert alert-info mb-4">
                <div class="d-flex">
                    <i class="mdi mdi-information-outline font-size-20 me-2"></i>
                    <div>
                        <strong>Selected Period:</strong><br>
                        {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">File Format <span class="text-danger">*</span></label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check card p-3 mb-0 border">
                        <input class="form-check-input" type="radio" name="format" value="npsb" id="formatNPSB" checked>
                        <label class="form-check-label w-100 stretched-link" for="formatNPSB">
                            <strong>NPSB Format</strong>
                            <div class="text-muted small">Bangladesh Standard (CSV)</div>
                        </label>
                    </div>
                    <div class="form-check card p-3 mb-0 border">
                        <input class="form-check-input" type="radio" name="format" value="beftn" id="formatBEFTN">
                        <label class="form-check-label w-100 stretched-link" for="formatBEFTN">
                            <strong>BEFTN Format</strong>
                            <div class="text-muted small">Bangladesh Electronic Funds (TXT)</div>
                        </label>
                    </div>
                    <div class="form-check card p-3 mb-0 border">
                        <input class="form-check-input" type="radio" name="format" value="csv" id="formatCSV">
                        <label class="form-check-label w-100 stretched-link" for="formatCSV">
                            <strong>Generic CSV</strong>
                            <div class="text-muted small">Universal Format</div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="mdi mdi-download me-1"></i> Download File
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
