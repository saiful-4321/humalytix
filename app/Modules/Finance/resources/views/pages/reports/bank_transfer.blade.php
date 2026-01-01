@extends('Main::layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Bank Transfer Report</h4>
            <div class="d-flex gap-2">
                @if(isset($journals) && count($journals) > 0)
                <button onclick="window.print()" class="btn btn-soft-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </button>
                <a href="{{ route('finance.reports.export', array_merge(request()->all(), ['type' => 'bank-transfer'])) }}" class="btn btn-soft-success btn-sm">
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
                                <th colspan="6" class="text-center py-3">
                                    <h5 class="mb-0">BANK TRANSFER REGISTER (CONTRA)</h5>
                                    <small class="text-muted">Period: {{ \Carbon\Carbon::parse($startDate)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d-M-Y') }}</small>
                                </th>
                            </tr>
                            <tr class="fw-bold">
                                <th width="10%">Date</th>
                                <th width="15%">Voucher No</th>
                                <th width="25%">From Account</th>
                                <th width="25%">To Account</th>
                                <th width="15%" class="text-end">Amount</th>
                                <th width="10%">Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @forelse($journals as $journal)
                                @php 
                                    $debitEntry = $journal->entries->where('debit', '>', 0)->first();
                                    $creditEntry = $journal->entries->where('credit', '>', 0)->first();
                                    $amount = $debitEntry ? $debitEntry->debit : 0;
                                    $total += $amount;
                                @endphp
                                <tr>
                                    <td>{{ $journal->date->format('d-M-y') }}</td>
                                    <td>{{ $journal->journal_number }}</td>
                                    <td class="ps-2">{{ $creditEntry ? $creditEntry->account->name : '-' }}</td>
                                    <td class="ps-2">{{ $debitEntry ? $debitEntry->account->name : '-' }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($amount, 2) }}</td>
                                    <td>{{ $journal->reference }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No bank transfers found</td>
                                </tr>
                            @endforelse
                            
                            @if($journals->count() > 0)
                            <tr class="table-active fw-bold border-top border-dark border-2">
                                <td colspan="4" class="text-end ps-3">TOTAL</td>
                                <td class="text-end">{{ number_format($total, 2) }}</td>
                                <td></td>
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
        <h5 class="offcanvas-title"><i class="fas fa-filter me-2"></i> Transfer Filters</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('finance.reports.bank-transfer') }}" method="GET">
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
                <a href="{{ route('finance.reports.bank-transfer') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-1"></i> Reset</a>
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
