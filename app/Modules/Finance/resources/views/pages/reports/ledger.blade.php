@extends('Main::layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">General Ledger</h4>
            <div class="d-flex gap-2">
                @if(isset($entries) && count($entries) > 0)
                <button onclick="window.print()" class="btn btn-soft-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </button>
                <a href="{{ route('finance.reports.export', array_merge(request()->all(), ['type' => 'ledger'])) }}" class="btn btn-soft-success btn-sm">
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
            @if(isset($selectedAccount) && $selectedAccount)
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0" style="font-family: 'Courier New', monospace; font-size: 13px;">
                        <thead class="table-light">
                            <tr>
                                <th colspan="6" class="text-center py-3">
                                    <h5 class="mb-0">{{ strtoupper($selectedAccount->name) }}</h5>
                                    <small class="text-muted">{{ $selectedAccount->code }} | Period: {{ \Carbon\Carbon::parse($startDate)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d-M-Y') }}</small>
                                </th>
                            </tr>
                            <tr class="fw-bold">
                                <th width="10%">Date</th>
                                <th width="35%">Particulars</th>
                                <th width="15%">Vch Type</th>
                                <th width="10%" class="text-end">Debit</th>
                                <th width="10%" class="text-end">Credit</th>
                                <th width="20%" class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $runningBalance = $openingBalance ?? 0;
                                $totalDebit = 0;
                                $totalCredit = 0;
                            @endphp
                            
                            <!-- Opening Balance -->
                            <tr class="table-secondary fw-bold">
                                <td colspan="5" class="ps-3">Opening Balance</td>
                                <td class="text-end">{{ number_format(abs($runningBalance), 2) }} {{ $runningBalance >= 0 ? 'Dr' : 'Cr' }}</td>
                            </tr>

                            @foreach($entries as $entry)
                                @php
                                    $totalDebit += $entry->debit;
                                    $totalCredit += $entry->credit;
                                    
                                    if ($selectedAccount->type->normal_balance == 'debit') {
                                        $runningBalance += ($entry->debit - $entry->credit);
                                    } else {
                                        $runningBalance += ($entry->credit - $entry->debit);
                                    }
                                    
                                    $journal = $entry->journal;
                                @endphp
                                <tr>
                                    <td>{{ $journal->date->format('d-M-y') }}</td>
                                    <td class="ps-2">
                                        {{ $entry->description ?: $journal->description }}
                                        <br><small class="text-muted">{{ $journal->journal_number }}</small>
                                    </td>
                                    <td><small>{{ ucfirst($journal->type) }}</small></td>
                                    <td class="text-end">{{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}</td>
                                    <td class="text-end">{{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}</td>
                                    <td class="text-end fw-semibold">{{ number_format(abs($runningBalance), 2) }} {{ $runningBalance >= 0 ? 'Dr' : 'Cr' }}</td>
                                </tr>
                            @endforeach
                            
                            <!-- Closing Balance -->
                            <tr class="table-active fw-bold border-top border-dark border-2">
                                <td colspan="3" class="ps-3">Closing Balance</td>
                                <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                                <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
                                <td class="text-end">{{ number_format(abs($runningBalance), 2) }} {{ $runningBalance >= 0 ? 'Dr' : 'Cr' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="card-body text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <p class="text-muted">Please select an account from filters to view ledger</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title"><i class="fas fa-filter me-2"></i> Ledger Filters</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('finance.reports.ledger') }}" method="GET">
            <div class="mb-3">
                <label class="form-label fw-semibold">Account <span class="text-danger">*</span></label>
                <select name="account_id" class="form-select select2" required>
                    <option value="">Select Account</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ (isset($selectedAccount) && $selectedAccount->id == $acc->id) ? 'selected' : '' }}>
                            {{ $acc->code }} - {{ $acc->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">From Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">To Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Show Ledger</button>
                @if(isset($selectedAccount))
                <button type="button" class="btn btn-secondary" onclick="window.print()"><i class="fas fa-print me-1"></i> Print</button>
                @endif
                <a href="{{ route('finance.reports.ledger') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-1"></i> Reset</a>
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
