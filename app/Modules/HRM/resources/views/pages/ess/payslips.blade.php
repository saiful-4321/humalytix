@extends('Main::layouts.app')

@section('title', 'My Payslips | ESS')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">My Payslips</h4>
            <div class="page-title-right">
                 <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('hrm.ess.dashboard') }}">ESS</a></li>
                    <li class="breadcrumb-item active">Payslips</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card bg-white">
            <div class="card-header align-items-center d-flex border-bottom-0 rounded-top p-3 shadow-sm">
                <h4 class="card-title mb-0 flex-grow-1 font-weight-bold text-dark">
                    <i class="mdi mdi-cash-multiple me-2 text-primary"></i> Salary History
                </h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Month</th>
                                <th>Basic Salary</th>
                                <th>Gross Salary</th>
                                <th>Deductions</th>
                                <th>Net Salary</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $payroll)
                            <tr>
                                <td class="fw-bold ps-3">{{ \Carbon\Carbon::parse($payroll->year . '-' . $payroll->month . '-01')->format('F Y') }}</td>
                                <td>{{ number_format($payroll->basic_salary, 2) }}</td>
                                <td>{{ number_format($payroll->gross_salary, 2) }}</td>
                                <td class="text-danger">{{ number_format($payroll->total_deductions, 2) }}</td>
                                <td class="text-success fw-bold">{{ number_format($payroll->net_salary, 2) }}</td>
                                <td>
                                    <span class="badge bg-soft-{{ $payroll->status == 'paid' ? 'success' : 'warning' }} text-{{ $payroll->status == 'paid' ? 'success' : 'warning' }}">
                                        {{ ucfirst($payroll->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('hrm.payroll.download-pdf', $payroll->id) }}" class="btn btn-sm btn-soft-primary" target="_blank">
                                        <i class="bx bx-download"></i> PDF
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-file-document-outline display-4 d-block mb-3"></i>
                                    No payslips found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($payrolls->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $payrolls->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
