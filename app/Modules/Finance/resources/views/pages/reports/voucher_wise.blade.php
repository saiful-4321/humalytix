@extends('Main::layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Voucher-wise Transaction Report</h4>
            <div class="d-flex gap-2">
                @if(isset($journals) && count($journals) > 0)
                <button onclick="window.print()" class="btn btn-soft-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </button>
                <a href="{{ route('finance.reports.export', array_merge(request()->all(), ['type' => 'voucher-wise'])) }}" class="btn btn-soft-success btn-sm">
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
                                    <h5 class="mb-0">VOUCHER-WISE TRANSACTION REGISTER</h5>
                                    <small class="text-muted">Period: {{ \Carbon\Carbon::parse($startDate)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d-M-Y') }}</small>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotal = 0; @endphp
                            @forelse($journals as $journal)
                                @php $journalTotal = $journal->entries->sum('debit'); $grandTotal += $journalTotal; @endphp
                                <!-- Journal Header -->
                                <tr class="table-secondary">
                                    <td colspan="5" class="fw-bold ps-2">
                                        {{ $journal->date->format('d-M-y') }} | {{ $journal->journal_number }} | 
                                        <span class="badge bg-primary-subtle text-primary">{{ ucfirst($journal->type) }}</span> | 
                                        {{ $journal->description }}
                                    </td>
                                </tr>
                                <!-- Journal Entries -->
                                <tr class="fw-semibold" style="font-size: 11px;">
                                    <th width="5%"></th>
                                    <th width="40%">Account</th>
                                    <th width="25%">Narration</th>
                                    <th width="15%" class="text-end">Debit</th>
                                    <th width="15%" class="text-end">Credit</th>
                                </tr>
                                @foreach($journal->entries as $entry)
                                <tr>
                                    <td></td>
                                    <td class="ps-3">{{ $entry->account->name }} ({{ $entry->account->code }})</td>
                                    <td class="ps-2"><small>{{ $entry->description }}</small></td>
                                    <td class="text-end">{{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}</td>
                                    <td class="text-end">{{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}</td>
                                </tr>
                                @endforeach
                                <tr class="fw-bold">
                                    <td colspan="3" class="text-end ps-3">Voucher Total</td>
                                    <td class="text-end">{{ number_format($journalTotal, 2) }}</td>
                                    <td class="text-end">{{ number_format($journalTotal, 2) }}</td>
                                </tr>
                                <tr><td colspan="5" class="p-1"></td></tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No vouchers found</td>
                                </tr>
                            @endforelse
                            
                            @if($journals->count() > 0)
                            <tr class="table-active fw-bold border-top border-dark border-2">
                                <td colspan="3" class="ps-3">GRAND TOTAL ({{ $journals->count() }} Vouchers)</td>
                                <td class="text-end">{{ number_format($grandTotal, 2) }}</td>
                                <td class="text-end">{{ number_format($grandTotal, 2) }}</td>
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
<x-offcanvas id="filterOffcanvas" title="<i class='fas fa-filter me-2'></i> Voucher Filters">
    <form id="vwFilterForm" action="{{ route('finance.reports.voucher-wise') }}" method="GET">
        <div class="mb-3">
            <label class="form-label fw-semibold">From Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">To Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Voucher Type</label>
            <select name="voucher_type" class="form-select">
                <option value="all" {{ $voucherType == 'all' ? 'selected' : '' }}>All Types</option>
                <option value="payment" {{ $voucherType == 'payment' ? 'selected' : '' }}>Payment</option>
                <option value="receipt" {{ $voucherType == 'receipt' ? 'selected' : '' }}>Receipt</option>
                <option value="contra" {{ $voucherType == 'contra' ? 'selected' : '' }}>Contra</option>
                <option value="journal" {{ $voucherType == 'journal' ? 'selected' : '' }}>Journal</option>
            </select>
        </div>
    </form>
    <x-slot:footer>
        <div class="d-grid gap-2">
            <button type="submit" form="vwFilterForm" class="btn btn-primary"><i class="fas fa-search me-1"></i> Apply Filters</button>
            <button type="button" class="btn btn-secondary" onclick="window.print()"><i class="fas fa-print me-1"></i> Print</button>
            <a href="{{ route('finance.reports.voucher-wise') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-1"></i> Reset</a>
        </div>
    </x-slot:footer>
</x-offcanvas>

<style>
@media print {
    .btn, .page-title-box, .offcanvas { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    body { font-size: 11px; }
}
</style>
@endsection
