@extends('Main::layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Retained Earnings Statement</h4>
            <div class="d-flex gap-2">
                @if(isset($details) && count($details) > 0)
                <button onclick="window.print()" class="btn btn-soft-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </button>
                <a href="{{ route('finance.reports.export', array_merge(request()->all(), ['type' => 'retained-earnings'])) }}" class="btn btn-soft-success btn-sm">
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
                                <th colspan="5" class="text-center py-3">
                                    <h5 class="mb-0">STATEMENT OF RETAINED EARNINGS</h5>
                                    <small class="text-muted">As on {{ \Carbon\Carbon::parse($asOfDate)->format('d-M-Y') }}</small>
                                </th>
                            </tr>
                            <tr class="fw-bold">
                                <th width="25%">Fiscal Year</th>
                                <th width="18%" class="text-end">Revenue</th>
                                <th width="18%" class="text-end">Expenses</th>
                                <th width="18%" class="text-end">Net Income</th>
                                <th width="21%" class="text-end">Cumulative R/E</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details as $detail)
                                <tr>
                                    <td class="ps-3">{{ $detail['fiscal_year'] }}</td>
                                    <td class="text-end">{{ number_format($detail['revenue'], 2) }}</td>
                                    <td class="text-end">{{ number_format($detail['expense'], 2) }}</td>
                                    <td class="text-end {{ $detail['net_income'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($detail['net_income'], 2) }}
                                    </td>
                                    <td class="text-end fw-semibold">{{ number_format($detail['cumulative_re'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No fiscal year data available</td>
                                </tr>
                            @endforelse
                            
                            @if(count($details) > 0)
                            <tr class="table-active fw-bold border-top border-dark border-2">
                                <td colspan="4" class="ps-3">TOTAL RETAINED EARNINGS</td>
                                <td class="text-end {{ $cumulativeRE >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($cumulativeRE, 2) }}
                                </td>
                            </tr>
                            @endif
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
        <form action="{{ route('finance.reports.retained-earnings') }}" method="GET">
            <div class="mb-3">
                <label class="form-label fw-semibold">As of Date</label>
                <input type="date" name="as_of_date" class="form-control" value="{{ $asOfDate }}">
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Apply Filters</button>
                <button type="button" class="btn btn-secondary" onclick="window.print()"><i class="fas fa-print me-1"></i> Print</button>
                <a href="{{ route('finance.reports.retained-earnings') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-1"></i> Reset</a>
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
