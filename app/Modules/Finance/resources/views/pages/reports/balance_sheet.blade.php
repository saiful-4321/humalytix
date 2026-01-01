@extends('Main::layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Balance Sheet</h4>
            <div class="d-flex gap-2">
                @if(isset($data) && count($data) > 0)
                <button onclick="window.print()" class="btn btn-soft-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </button>
                <a href="{{ route('finance.reports.export', array_merge(request()->all(), ['type' => 'balance-sheet'])) }}" class="btn btn-soft-success btn-sm">
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
                                    <h5 class="mb-0">BALANCE SHEET</h5>
                                    <small class="text-muted">As on {{ \Carbon\Carbon::parse($asOfDate)->format('d-M-Y') }}</small>
                                </th>
                            </tr>
                            <tr class="fw-bold">
                                <th width="40%">LIABILITIES</th>
                                <th width="10%" class="text-end">Amount</th>
                                <th width="40%">ASSETS</th>
                                <th width="10%" class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $liabilityTotal = 0;
                                $equityTotal = 0;
                                $assetTotal = 0;
                                
                                // Calculate totals
                                foreach(($data['2'] ?? []) as $item) $liabilityTotal += $item['balance'];
                                foreach(($data['3'] ?? []) as $item) $equityTotal += $item['balance'];
                                foreach(($data['1'] ?? []) as $item) $assetTotal += $item['balance'];
                                
                                $liabilities = array_values($data['2'] ?? []);
                                $equity = array_values($data['3'] ?? []);
                                $assets = array_values($data['1'] ?? []);
                            @endphp
                            
                            <!-- LIABILITIES HEADER -->
                            <tr class="table-secondary">
                                <td class="fw-bold ps-2">LIABILITIES</td>
                                <td></td>
                                <td class="fw-bold ps-2">ASSETS</td>
                                <td></td>
                            </tr>
                            
                            <!-- LIABILITIES ITEMS vs ASSETS ITEMS -->
                            @for($i = 0; $i < max(count($liabilities), count($assets)); $i++)
                                <tr>
                                    <!-- Left: Liabilities -->
                                    @if($i < count($liabilities))
                                        <td class="ps-4">{{ $liabilities[$i]['account']->name ?? 'N/A' }}</td>
                                        <td class="text-end">{{ number_format($liabilities[$i]['balance'] ?? 0, 2) }}</td>
                                    @else
                                        <td></td>
                                        <td></td>
                                    @endif
                                    
                                    <!-- Right: Assets -->
                                    @if($i < count($assets))
                                        <td class="ps-4">{{ $assets[$i]['account']->name ?? 'N/A' }}</td>
                                        <td class="text-end">{{ number_format($assets[$i]['balance'] ?? 0, 2) }}</td>
                                    @else
                                        <td></td>
                                        <td></td>
                                    @endif
                                </tr>
                            @endfor
                            
                            <!-- LIABILITIES SUBTOTAL -->
                            <tr class="fw-bold">
                                <td class="ps-3">Total Liabilities</td>
                                <td class="text-end">{{ number_format($liabilityTotal, 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                            
                            <tr><td colspan="4" class="p-1"></td></tr>
                            
                            <!-- EQUITY HEADER -->
                            <tr class="table-secondary">
                                <td class="fw-bold ps-2">EQUITY</td>
                                <td></td>
                                <td colspan="2"></td>
                            </tr>
                            
                            @foreach($equity as $item)
                                <tr>
                                    <td class="ps-4">{{ $item['account']->name ?? 'N/A' }}</td>
                                    <td class="text-end">{{ number_format($item['balance'] ?? 0, 2) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            @endforeach
                            
                            <tr>
                                <td class="ps-4">Retained Earnings</td>
                                <td class="text-end {{ $retainedEarnings >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format(abs($retainedEarnings), 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                            
                            <tr class="fw-bold">
                                <td class="ps-3">Total Equity</td>
                                <td class="text-end">{{ number_format($equityTotal + $retainedEarnings, 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                            
                            <!-- GRAND TOTALS -->
                            <tr class="table-active fw-bold border-top border-dark border-2">
                                <td class="ps-2">TOTAL</td>
                                <td class="text-end">{{ number_format($liabilityTotal + $equityTotal + $retainedEarnings, 2) }}</td>
                                <td class="ps-2">TOTAL</td>
                                <td class="text-end">{{ number_format($assetTotal, 2) }}</td>
                            </tr>
                            
                            <!-- Balance Check -->
                            @php
                                $leftTotal = $liabilityTotal + $equityTotal + $retainedEarnings;
                                $difference = abs($leftTotal - $assetTotal);
                            @endphp
                            @if($difference > 0.01)
                            <tr class="table-warning">
                                <td colspan="4" class="text-center text-danger fw-bold">
                                    <i class="fas fa-exclamation-triangle"></i> Out of Balance: Difference = {{ number_format($difference, 2) }}
                                </td>
                            </tr>
                            @else
                            <tr class="table-success">
                                <td colspan="4" class="text-center text-success fw-bold">
                                    <i class="fas fa-check-circle"></i> Balance Sheet is Balanced
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
        <form action="{{ route('finance.reports.balance-sheet') }}" method="GET">
            <div class="mb-3">
                <label class="form-label fw-semibold">As of Date</label>
                <input type="date" name="as_of_date" class="form-control" value="{{ $asOfDate }}">
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Apply Filters</button>
                <button type="button" class="btn btn-secondary" onclick="window.print()"><i class="fas fa-print me-1"></i> Print</button>
            </div>
        </form>
    </div>
</div>

<style>
@media print {
    .btn, .page-title-box, .offcanvas { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    body { font-size: 11px; }
}
</style>
@endsection
