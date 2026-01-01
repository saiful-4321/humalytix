@extends('Main::layouts.app')

@section('content')
<div class="row noprint">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Payment & Receipt Summary (Tally Style)</h4>
            <div class="d-flex gap-2">
                @if($journals->count() > 0)
                <button onclick="window.print()" class="btn btn-soft-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </button>
                <a href="{{ route('finance.reports.export', array_merge(request()->all(), ['type' => 'summary'])) }}" class="btn btn-soft-success btn-sm">
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
        <div class="card overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0" style="font-family: 'Courier New', monospace; font-size: 13px;">
                        <thead class="table-light text-center border-bottom-dark">
                            <tr>
                                <th colspan="4" class="py-3 border-end">
                                    <h5 class="mb-0">RECEIPTS</h5>
                                    <small class="text-muted">Total Vouchers: {{ $receipts->count() }}</small>
                                </th>
                                <th colspan="4" class="py-3">
                                    <h5 class="mb-0">PAYMENTS</h5>
                                    <small class="text-muted">Total Vouchers: {{ $payments->count() }}</small>
                                </th>
                            </tr>
                            <tr class="fw-bold bg-light-subtle">
                                <th width="10%">Date</th>
                                <th width="15%">Vch No</th>
                                <th width="15%">Particulars</th>
                                <th width="10%" class="text-end border-end">Amount</th>
                                <th width="10%">Date</th>
                                <th width="15%">Vch No</th>
                                <th width="15%">Particulars</th>
                                <th width="10%" class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $maxRows = max($receipts->count(), $payments->count());
                                $rTotal = 0;
                                $pTotal = 0;
                                $receiptsArr = $receipts->values();
                                $paymentsArr = $payments->values();
                            @endphp
                            
                            @for($i = 0; $i < $maxRows; $i++)
                                <tr>
                                    <!-- Receipts (Left) -->
                                    @if(isset($receiptsArr[$i]))
                                        @php $amt = $receiptsArr[$i]->entries->sum('debit'); $rTotal += $amt; @endphp
                                        <td class="text-center">{{ $receiptsArr[$i]->date->format('d/m') }}</td>
                                        <td>{{ $receiptsArr[$i]->journal_number }}</td>
                                        <td class="text-truncate" style="max-width: 150px;">{{ $receiptsArr[$i]->description }}</td>
                                        <td class="text-end border-end fw-semibold">{{ number_format($amt, 2) }}</td>
                                    @else
                                        <td></td><td></td><td></td><td class="border-end"></td>
                                    @endif

                                    <!-- Payments (Right) -->
                                    @if(isset($paymentsArr[$i]))
                                        @php $amt = $paymentsArr[$i]->entries->sum('debit'); $pTotal += $amt; @endphp
                                        <td class="text-center">{{ $paymentsArr[$i]->date->format('d/m') }}</td>
                                        <td>{{ $paymentsArr[$i]->journal_number }}</td>
                                        <td class="text-truncate" style="max-width: 150px;">{{ $paymentsArr[$i]->description }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($amt, 2) }}</td>
                                    @else
                                        <td></td><td></td><td></td><td></td>
                                    @endif
                                </tr>
                            @endfor
                            
                            <!-- Totals -->
                            <tr class="fw-bold table-light border-top border-dark">
                                <td colspan="3" class="text-end ps-3">Total Receipts (A)</td>
                                <td class="text-end border-end">{{ number_format($rTotal, 2) }}</td>
                                <td colspan="3" class="text-end ps-3">Total Payments (B)</td>
                                <td class="text-end">{{ number_format($pTotal, 2) }}</td>
                            </tr>
                            
                            <!-- Closing Balance -->
                            <tr class="fw-bold bg-success-subtle">
                                <td colspan="4" class="border-end"></td>
                                <td colspan="3" class="text-end ps-3">CLOSING BALANCE (A - B)</td>
                                <td class="text-end">{{ number_format($rTotal - $pTotal, 2) }}</td>
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
        <form action="{{ route('finance.reports.summary') }}" method="GET">
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
                <button type="button" class="btn btn-soft-danger" onclick="window.print()"><i class="fas fa-file-pdf me-1"></i> Export PDF</button>
                <a href="{{ route('finance.reports.summary') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-1"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<style>
.border-end { border-right: 2px solid #dee2e6 !important; }
.border-bottom-dark { border-bottom: 2px solid #343a40 !important; }
@media print {
    .noprint, .btn, .offcanvas { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    body { font-size: 11px; margin: 0; padding: 0; }
    .table { width: 100% !important; border-collapse: collapse !important; }
    .table th, .table td { border: 1px solid #000 !important; }
    .bg-success-subtle { background-color: #f8f9fa !important; color: #000 !important; }
}
</style>
@endsection
