@extends('Main::layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Journal Details</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('finance.dashboard') }}">Finance</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('finance.journals.index') }}">Journals</a></li>
                    <li class="breadcrumb-item active">{{ $journal->journal_number }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Journal Entry: {{ $journal->journal_number }}</h5>
                <div>
                    <span class="badge bg-{{ $journal->status === 'POSTED' ? 'success' : 'warning' }} fs-12">
                        {{ $journal->status }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-sm-3">
                        <p class="text-muted mb-1">Date</p>
                        <h6 class="fs-14">{{ \Carbon\Carbon::parse($journal->date)->format('d M, Y') }}</h6>
                    </div>
                    <div class="col-sm-3">
                        <p class="text-muted mb-1">Reference</p>
                        <h6 class="fs-14">{{ $journal->reference ?? '-' }}</h6>
                    </div>
                    <div class="col-sm-3">
                        <p class="text-muted mb-1">Created By</p>
                        <h6 class="fs-14">{{ $journal->creator->name ?? 'System' }}</h6>
                    </div>
                    @if($journal->posted_at)
                    <div class="col-sm-3">
                        <p class="text-muted mb-1">Posted At</p>
                        <h6 class="fs-14">{{ \Carbon\Carbon::parse($journal->posted_at)->format('d M, Y H:i') }}</h6>
                    </div>
                    @endif
                </div>

                @if($journal->description)
                <div class="mb-4">
                    <p class="text-muted mb-1">Description</p>
                    <p class="fs-14 mb-0">{{ $journal->description }}</p>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Account Code</th>
                                <th scope="col">Account Name</th>
                                <th scope="col" class="text-end">Debit</th>
                                <th scope="col" class="text-end">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalDebit = 0;
                                $totalCredit = 0;
                            @endphp
                            @foreach($journal->entries as $entry)
                                @php
                                    $totalDebit += $entry->debit;
                                    $totalCredit += $entry->credit;
                                @endphp
                                <tr>
                                    <td>{{ $entry->account->code ?? 'N/A' }}</td>
                                    <td>
                                        <span class="fw-medium">{{ $entry->account->name ?? 'Unknown Account' }}</span>
                                    </td>
                                    <td class="text-end">
                                        {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}
                                    </td>
                                    <td class="text-end">
                                        {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="2" class="text-end">Total</td>
                                <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                                <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('finance.journals.index') }}" class="btn btn-light">Back to List</a>
                    
                    @if($journal->status === 'DRAFT')
                        <form action="{{ route('finance.journals.post', $journal->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to post this journal? This action cannot be undone.');">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="ri-check-double-line align-bottom me-1"></i> Post Journal
                            </button>
                        </form>
                    @endif
                    
                    <button type="button" class="btn btn-soft-primary" onclick="window.print()">
                        <i class="ri-printer-line align-bottom me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
