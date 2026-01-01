@extends('Main::layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Cheque Dishonour Report</h4>
            <div class="d-flex gap-2">
                @if(isset($journals) && count($journals) > 0)
                <button onclick="window.print()" class="btn btn-soft-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </button>
                <a href="{{ route('finance.reports.export', array_merge(request()->all(), ['type' => 'dishonoured'])) }}" class="btn btn-soft-success btn-sm">
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
                                <th colspan="7" class="text-center py-3">
                                    <h5 class="mb-0">CHEQUE DISHONOUR REPORT</h5>
                                    <small class="text-muted">Period: {{ \Carbon\Carbon::parse($startDate)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d-M-Y') }}</small>
                                </th>
                            </tr>
                            <tr class="fw-bold">
                                <th width="10%">Date</th>
                                <th width="15%">Voucher No</th>
                                <th width="10%">Type</th>
                                <th width="20%">Reference</th>
                                <th width="25%">Description</th>
                                <th width="15%" class="text-end">Amount</th>
                                <th width="5%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @forelse($journals as $journal)
                                @php $total += $journal->entries->sum('debit'); @endphp
                                <tr>
                                    <td>{{ $journal->date->format('d-M-y') }}</td>
                                    <td>{{ $journal->journal_number }}</td>
                                    <td><span class="badge bg-danger-subtle text-danger">{{ ucfirst($journal->type) }}</span></td>
                                    <td>{{ $journal->reference }}</td>
                                    <td class="ps-2">{{ $journal->description }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($journal->entries->sum('debit'), 2) }}</td>
                                    <td><span class="badge bg-danger">Dishonoured</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-success py-4">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                                        <p class="mb-0">No dishonoured cheques found</p>
                                    </td>
                                </tr>
                            @endforelse
                            
                            @if($journals->count() > 0)
                            <tr class="table-active fw-bold border-top border-dark border-2">
                                <td colspan="5" class="text-end ps-3">TOTAL DISHONOURED</td>
                                <td class="text-end text-danger">{{ number_format($total, 2) }}</td>
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
        <h5 class="offcanvas-title"><i class="fas fa-filter me-2"></i> Report Filters</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('finance.reports.dishonoured') }}" method="GET">
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
                <a href="{{ route('finance.reports.dishonoured') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-1"></i> Reset</a>
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
