@extends('Main::layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ $typeInfo }} Book</h4>
            <div class="d-flex gap-2">
                @if(isset($entries) && count($entries) > 0)
                <button onclick="window.print()" class="btn btn-soft-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </button>
                <a href="{{ route('finance.reports.export', array_merge(request()->all(), ['type' => $typeInfo == 'Cash' ? 'cashbook' : 'bankbook'])) }}" class="btn btn-soft-success btn-sm">
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
                                    <h5 class="mb-0">{{ strtoupper($typeInfo) }} BOOK</h5>
                                    <small class="text-muted">Period: {{ \Carbon\Carbon::parse($startDate)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d-M-Y') }}</small>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotal = 0; @endphp
                            @forelse($entries as $accountId => $accountEntries)
                                @php 
                                    $account = $accounts->firstWhere('id', $accountId);
                                    if (!$account) continue;
                                    $accountTotal = 0;
                                @endphp
                                
                                <!-- Account Header -->
                                <tr class="table-secondary fw-bold">
                                    <td colspan="6" class="ps-2">{{ $account->name }} ({{ $account->code }})</td>
                                </tr>
                                <tr class="fw-semibold" style="font-size: 11px;">
                                    <th width="10%">Date</th>
                                    <th width="15%">Voucher No</th>
                                    <th width="35%">Particulars</th>
                                    <th width="10%" class="text-end">Debit</th>
                                    <th width="10%" class="text-end">Credit</th>
                                    <th width="20%" class="text-end">Balance</th>
                                </tr>
                                
                                @php $runningBalance = 0; @endphp
                                @foreach($accountEntries as $entry)
                                    @php
                                        $runningBalance += ($entry->debit - $entry->credit);
                                        $accountTotal += ($entry->debit - $entry->credit);
                                        $journal = $entry->journal;
                                    @endphp
                                    <tr>
                                        <td>{{ $journal->date->format('d-M-y') }}</td>
                                        <td>{{ $journal->journal_number }}</td>
                                        <td class="ps-2">{{ $entry->description ?: $journal->description }}</td>
                                        <td class="text-end">{{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}</td>
                                        <td class="text-end">{{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}</td>
                                        <td class="text-end fw-semibold">{{ number_format(abs($runningBalance), 2) }} {{ $runningBalance >= 0 ? 'Dr' : 'Cr' }}</td>
                                    </tr>
                                @endforeach
                                
                                <!-- Account Subtotal -->
                                <tr class="fw-bold">
                                    <td colspan="5" class="text-end ps-3">Subtotal: {{ $account->name }}</td>
                                    <td class="text-end">{{ number_format(abs($accountTotal), 2) }} {{ $accountTotal >= 0 ? 'Dr' : 'Cr' }}</td>
                                </tr>
                                <tr><td colspan="6" class="p-1"></td></tr>
                                
                                @php $grandTotal += $accountTotal; @endphp
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No {{ strtolower($typeInfo) }} transactions found</td>
                                </tr>
                            @endforelse
                            
                            @if($entries->count() > 0)
                            <tr class="table-active fw-bold border-top border-dark border-2">
                                <td colspan="5" class="ps-3">GRAND TOTAL</td>
                                <td class="text-end">{{ number_format(abs($grandTotal), 2) }} {{ $grandTotal >= 0 ? 'Dr' : 'Cr' }}</td>
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
<x-offcanvas id="filterOffcanvas" title="<i class='fas fa-filter me-2'></i> {{ $typeInfo }}book Filters">
    <form id="cashbookFilterForm" action="{{ $typeInfo == 'Cash' ? route('finance.reports.cashbook') : route('finance.reports.bankbook') }}" method="GET">
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
            <button type="submit" form="cashbookFilterForm" class="btn btn-primary"><i class="fas fa-search me-1"></i> Apply Filters</button>
            <button type="button" class="btn btn-secondary" onclick="window.print()"><i class="fas fa-print me-1"></i> Print</button>
            <a href="{{ $typeInfo == 'Cash' ? route('finance.reports.cashbook') : route('finance.reports.bankbook') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-1"></i> Reset</a>
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
