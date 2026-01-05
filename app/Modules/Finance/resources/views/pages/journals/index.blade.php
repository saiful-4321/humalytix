@extends('Main::layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Journals</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Finance</a></li>
                    <li class="breadcrumb-item active">Journals</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card bg-white mb-5">
            <div class="card-header align-items-center d-flex border-bottom-0 rounded-top p-3 shadow-sm">
                <h4 class="card-title mb-0 flex-grow-1 font-weight-bold text-dark">
                    <i class="fas fa-sitemap me-2 text-primary"></i> Total Journals: {{ $journals->total() }}
                </h4>
                <div class="flex-shrink-0 d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn btn-soft-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-export me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu bg-light">
                            <li><a class="dropdown-item" href="{{ route('finance.journals.export', array_merge(request()->all(), ['export_type' => 'excel'])) }}">Excel</a></li>
                            <li><a class="dropdown-item" href="{{ route('finance.journals.export', array_merge(request()->all(), ['export_type' => 'csv'])) }}">CSV</a></li>
                            <li><a class="dropdown-item" href="{{ route('finance.journals.export', array_merge(request()->all(), ['export_type' => 'pdf'])) }}">PDF</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-soft-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#filterJournalOffcanvas">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    @can('finance-journal-create')
                    <button class="btn btn-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#createJournalOffcanvas">
                        <i class="fas fa-plus me-1"></i> Create Journal
                    </button>
                    @endcan
                </div>
            </div>

            <!-- Active Filters Bar -->
            <div id="active-filters" class="border-top bg-light-subtle px-3 py-2 d-none card bg-white">
                <div class="d-flex align-items-center">
                    <span class="text-uppercase fs-7 fw-bold text-muted me-2"><small><i class="fas fa-filter me-1"></i> Active Filters:</small></span>
                    <div id="filter-chips" class="d-flex flex-wrap gap-2">
                        <!-- Chips inserted via JS -->
                    </div>
                    <button id="clear-all-filters" class="btn btn-link text-danger btn-sm ms-auto py-0 fs-12 text-decoration-none">Clear All</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Journal No</th>
                                <th>Reference</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-center" width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($journals as $journal)
                            <tr>
                                <td>{{ $journal->date->format('d M, Y') }}</td>
                                <td class="fw-bold text-primary">{{ $journal->journal_number }}</td>
                                <td>{{ $journal->reference ?? '-' }}</td>
                                <td><span class="badge bg-info-subtle text-info">{{ ucfirst($journal->type) }}</span></td>
                                <td class="text-truncate" style="max-width: 200px;">{{ $journal->description }}</td>
                                <td class="fw-bold">{{ number_format($journal->entries->sum('debit'), 2) }}</td>
                                <td>
                                    @if($journal->status == 'posted')
                                        <span class="badge bg-success-subtle text-success">Posted</span>
                                    @elseif($journal->status == 'draft')
                                        <span class="badge bg-warning-subtle text-warning">Draft</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($journal->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- View -->
                                        <a href="{{ route('finance.journals.show', $journal->id) }}" class="btn btn-sm btn-soft-primary" data-bs-toggle="tooltip" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($journal->status == 'draft')
                                        <!-- Edit -->
                                        @can('finance-journal-edit')
                                        <a href="{{ route('finance.journals.edit', $journal->id) }}" class="btn btn-sm btn-soft-info" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        @endcan
                                        @endif

                                        <!-- Print/Download -->
                                        <a href="{{ route('finance.journals.print', $journal->id) }}" target="_blank" class="btn btn-sm btn-soft-success" data-bs-toggle="tooltip" title="Print PDF">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        @if($journal->status == 'draft')
                                        <!-- Post -->
                                        @can('finance-journal-post')
                                        <form action="{{ route('finance.journals.post', $journal->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to post this journal? This cannot be undone.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-soft-warning" data-bs-toggle="tooltip" title="Post Journal">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                        @endcan

                                        <!-- Delete -->
                                        @can('finance-journal-delete')
                                        <form action="{{ route('finance.journals.destroy', $journal->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this draft?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger" data-bs-toggle="tooltip" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                        @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No journal entries found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    {{ $journals->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Offcanvas -->
<x-offcanvas id="filterJournalOffcanvas" title="<i class='fas fa-filter me-2'></i> Filter Journals">
    <form action="{{ route('finance.journals.index') }}" method="GET" id="journalFilterForm">
        <div class="mb-3">
            <label class="form-label">Date Range</label>
            <div class="input-group">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="From">
                <span class="input-group-text">to</span>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="To">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Reference</label>
            <input type="text" name="reference" class="form-control" value="{{ request('reference') }}" placeholder="Search Reference...">
        </div>
        <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
                <option value="all">All Types</option>
                <option value="journal" {{ request('type') == 'journal' ? 'selected' : '' }}>Journal Voucher</option>
                <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>Payment Voucher</option>
                <option value="receipt" {{ request('type') == 'receipt' ? 'selected' : '' }}>Receipt Voucher</option>
                <option value="contra" {{ request('type') == 'contra' ? 'selected' : '' }}>Contra Voucher</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="all">All Statuses</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>Posted</option>
            </select>
        </div>
    </form>
    <x-slot:footer>
        <div class="d-grid gap-2">
            <button type="submit" form="journalFilterForm" class="btn btn-primary">Apply Filters</button>
            <a href="{{ route('finance.journals.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </x-slot:footer>
</x-offcanvas>

<!-- Create Journal Offcanvas -->
<x-offcanvas id="createJournalOffcanvas" title="Create Journal Entry" width="800px">
    <form action="{{ route('finance.journals.store') }}" method="POST" id="journalForm">
        @csrf
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Reference</label>
                <input type="text" class="form-control" name="reference" placeholder="e.g Invoice #123">
            </div>
            <div class="col-md-3">
                 <label class="form-label">Fiscal Year</label>
                 <input type="text" class="form-control" value="{{ $fiscalYear->name ?? 'N/A' }}" readonly disabled>
            </div>
             <div class="col-md-3">
                 <label class="form-label">Voucher Type <span class="text-danger">*</span></label>
                 <select name="type" class="form-select" required>
                     <option value="journal">Journal Voucher</option>
                     <option value="payment">Payment Voucher</option>
                     <option value="receipt">Receipt Voucher</option>
                     <option value="contra">Contra Voucher</option>
                 </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="2"></textarea>
        </div>
        
        <div class="card bg-light border-0">
            <div class="card-body p-2">
                <table class="table table-borderless table-sm mb-0" id="entriesTable">
                    <thead>
                        <tr>
                            <th width="40%">Account</th>
                            <th width="25%">Debit</th>
                            <th width="25%">Credit</th>
                            <th width="10%"></th>
                        </tr>
                    </thead>
                    <tbody id="entriesContainer">
                        <!-- Rows will be added here -->
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold border-top">
                            <td class="text-end">Total:</td>
                            <td id="totalDebit">0.00</td>
                            <td id="totalCredit">0.00</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-center">
                                <span id="balanceMessage" class="text-success small">Balanced</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <div class="mt-2">
                     <button type="button" class="btn btn-sm btn-soft-secondary w-100" id="addRowBtn"><i class="ri-add-line"></i> Add Line</button>
                </div>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="d-flex justify-content-between w-100">
            <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
            <div>
                <button type="submit" form="journalForm" name="post_journal" value="1" class="btn btn-success me-1">Save & Post</button>
                <button type="submit" form="journalForm" class="btn btn-primary">Save Draft</button>
            </div>
        </div>
    </x-slot:footer>
</x-offcanvas>

<template id="entryRowTemplate">
    <tr class="entry-row">
        <td>
            <select name="entries[INDEX][account_id]" class="form-select form-select-sm" required>
                <option value="">Select Account</option>
                @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                @endforeach
            </select>
            <input type="text" name="entries[INDEX][description]" class="form-control form-control-sm mt-1" placeholder="Line description (optional)">
        </td>
        <td>
            <input type="number" step="0.01" name="entries[INDEX][debit]" class="form-control form-control-sm debit-input" value="0.00">
        </td>
        <td>
            <input type="number" step="0.01" name="entries[INDEX][credit]" class="form-control form-control-sm credit-input" value="0.00">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-ghost-danger remove-row"><i class="fas fa-trash-alt"></i></button>
        </td>
    </tr>
</template>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('entriesContainer');
        const template = document.getElementById('entryRowTemplate');
        let rowIndex = 0;

        function addRow() {
            const clone = template.content.cloneNode(true);
            
            // Replace INDEX
            clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
                el.name = el.name.replace('INDEX', rowIndex);
            });
            
            rowIndex++;
            container.appendChild(clone);
            updateTotals();
        }

        // Add 2 initial rows
        addRow();
        addRow();

        document.getElementById('addRowBtn').addEventListener('click', addRow);

        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                if (container.children.length > 2) {
                    e.target.closest('tr').remove();
                    updateTotals();
                } else {
                    alert('Journal must have at least 2 entries');
                }
            }
        });

        container.addEventListener('input', function(e) {
            if (e.target.classList.contains('debit-input')) {
                const row = e.target.closest('tr');
                if (parseFloat(e.target.value) > 0) {
                    row.querySelector('.credit-input').value = 0.00;
                }
            }
            if (e.target.classList.contains('credit-input')) {
                const row = e.target.closest('tr');
                if (parseFloat(e.target.value) > 0) {
                    row.querySelector('.debit-input').value = 0.00;
                }
            }
            updateTotals();
        });

        function updateTotals() {
            let totalDebit = 0;
            let totalCredit = 0;

            document.querySelectorAll('.debit-input').forEach(input => {
                totalDebit += parseFloat(input.value) || 0;
            });
            document.querySelectorAll('.credit-input').forEach(input => {
                totalCredit += parseFloat(input.value) || 0;
            });

            document.getElementById('totalDebit').textContent = totalDebit.toFixed(2);
            document.getElementById('totalCredit').textContent = totalCredit.toFixed(2);
            
            const diff = Math.abs(totalDebit - totalCredit);
            const msg = document.getElementById('balanceMessage');

            if (diff < 0.01 && totalDebit > 0) {
                msg.textContent = 'Balanced';
                msg.className = 'text-success small fw-bold';
            } else {
                msg.textContent = `Unbalanced (Diff: ${diff.toFixed(2)})`;
                msg.className = 'text-danger small fw-bold';
            }
        }
    });
</script>
@endsection
