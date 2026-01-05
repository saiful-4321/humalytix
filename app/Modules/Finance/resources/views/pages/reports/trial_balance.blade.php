@extends('Main::layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Trial Balance</h4>
            <div class="d-flex gap-2">
                @if(isset($balances) && count($balances) > 0)
                <button onclick="window.print()" class="btn btn-soft-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </button>
                <a href="{{ route('finance.reports.export', array_merge(request()->all(), ['type' => 'trial-balance'])) }}" class="btn btn-soft-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i> Excel
                </a>
                @endif
                <button class="btn btn-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                    <i class="fas fa-filter me-1"></i> Filters
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0" style="font-family: 'Courier New', monospace; font-size: 13px;">
                        <thead class="table-light">
                            <tr>
                                <th colspan="4" class="text-center py-3">
                                    <h5 class="mb-0">TRIAL BALANCE</h5>
                                    <small class="text-muted">Period: {{ \Carbon\Carbon::parse($startDate)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d-M-Y') }}</small>
                                </th>
                            </tr>
                            <tr class="fw-bold">
                                <th width="50%">Particulars</th>
                                <th width="16%" class="text-end">Debit (Dr)</th>
                                <th width="16%" class="text-end">Credit (Cr)</th>
                                <th width="18%" class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalDebit = 0;
                                $totalCredit = 0;
                            @endphp
                            
                            @foreach($balances as $balance)
                                @php
                                    $debit = $balance->total_debit;
                                    $credit = $balance->total_credit;
                                    $netBalance = $debit - $credit;
                                    $totalDebit += $debit;
                                    $totalCredit += $credit;
                                @endphp
                                <tr>
                                    <td class="ps-3">{{ $balance->account->name }} ({{ $balance->account->code }})</td>
                                    <td class="text-end">{{ number_format($debit, 2) }}</td>
                                    <td class="text-end">{{ number_format($credit, 2) }}</td>
                                    <td class="text-end {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }}">
                                        {{ number_format(abs($netBalance), 2) }} {{ $netBalance >= 0 ? 'Dr' : 'Cr' }}
                                    </td>
                                </tr>
                            @endforeach
                            
                            <tr class="table-active fw-bold border-top border-dark border-2">
                                <td class="ps-3">TOTAL</td>
                                <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                                <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
                                <td class="text-end">
                                    @if(abs($totalDebit - $totalCredit) < 0.01)
                                        <span class="text-success">Balanced ✓</span>
                                    @else
                                        <span class="text-danger">Diff: {{ number_format(abs($totalDebit - $totalCredit), 2) }}</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Offcanvas -->
<x-offcanvas id="filterOffcanvas" title="<i class='fas fa-filter me-2'></i> Report Filters">
    <form id="tbFilterForm" action="{{ route('finance.reports.trial-balance') }}" method="GET">
        <div class="mb-3">
            <label class="form-label fw-semibold">From Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">To Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>
    </form>
    <x-slot:footer>
        <div class="d-grid gap-2">
            <button type="submit" form="tbFilterForm" class="btn btn-primary"><i class="fas fa-search me-1"></i> Apply Filters</button>
            <button type="button" class="btn btn-secondary" onclick="window.print()"><i class="fas fa-print me-1"></i> Print</button>
            <a href="{{ route('finance.reports.trial-balance') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-1"></i> Reset</a>
        </div>
    </x-slot:footer>
</x-offcanvas>

<style>
@media print {
    .btn, .page-title-box, .offcanvas { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    body { font-size: 12px; }
}
</style>
@endsection
