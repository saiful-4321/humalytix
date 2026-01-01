@extends('Main::layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Profit & Loss Statement</h4>
            <div class="d-flex gap-2">
                @if(isset($data) && count($data) > 0)
                <button onclick="window.print()" class="btn btn-soft-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </button>
                <a href="{{ route('finance.reports.export', array_merge(request()->all(), ['type' => 'profit-loss'])) }}" class="btn btn-soft-success btn-sm">
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
                                <th colspan="2" class="text-center py-3">
                                    <h5 class="mb-0">PROFIT & LOSS STATEMENT</h5>
                                    <small class="text-muted">For the period: {{ \Carbon\Carbon::parse($startDate)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d-M-Y') }}</small>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- REVENUE SECTION -->
                            @if(isset($data['4']) && count($data['4']) > 0)
                            <tr class="table-secondary fw-bold">
                                <td colspan="2" class="ps-2">REVENUE</td>
                            </tr>
                            @php $revenueTotal = 0; @endphp
                            @foreach($data['4'] as $item)
                                @php $revenueTotal += $item['balance']; @endphp
                                <tr>
                                    <td class="ps-4">{{ $item['account']->name }}</td>
                                    <td class="text-end" width="25%">{{ number_format($item['balance'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="fw-bold">
                                <td class="ps-3">Total Revenue</td>
                                <td class="text-end">{{ number_format($revenueTotal, 2) }}</td>
                            </tr>
                            <tr><td colspan="2" class="p-1"></td></tr>
                            @endif

                            <!-- EXPENSES SECTION -->
                            @if(isset($data['5']) && count($data['5']) > 0)
                            <tr class="table-secondary fw-bold">
                                <td colspan="2" class="ps-2">EXPENSES</td>
                            </tr>
                            @php $expenseTotal = 0; @endphp
                            @foreach($data['5'] as $item)
                                @php $expenseTotal += $item['balance']; @endphp
                                <tr>
                                    <td class="ps-4">{{ $item['account']->name }}</td>
                                    <td class="text-end">{{ number_format($item['balance'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="fw-bold">
                                <td class="ps-3">Total Expenses</td>
                                <td class="text-end">{{ number_format($expenseTotal, 2) }}</td>
                            </tr>
                            <tr><td colspan="2" class="p-1"></td></tr>
                            @endif

                            <!-- NET PROFIT/LOSS -->
                            @php
                                $netProfit = ($revenueTotal ?? 0) - ($expenseTotal ?? 0);
                            @endphp
                            <tr class="table-active fw-bold border-top border-dark border-2">
                                <td class="ps-2 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $netProfit >= 0 ? 'NET PROFIT' : 'NET LOSS' }}
                                </td>
                                <td class="text-end {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format(abs($netProfit), 2) }}
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
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title"><i class="fas fa-filter me-2"></i> Report Filters</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('finance.reports.profit-loss') }}" method="GET">
            <div class="mb-3">
                <label class="form-label fw-semibold">From Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">To Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Apply Filters</button>
                <button type="button" class="btn btn-secondary" onclick="window.print()"><i class="fas fa-print me-1"></i> Print</button>
                <a href="{{ route('finance.reports.profit-loss') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-1"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<style>
@media print {
    .btn, .page-title-box, .offcanvas { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    body { font-size: 12px; }
}
</style>
@endsection
