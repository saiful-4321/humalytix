@extends('Main::layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Journal Entries</h4>
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
        <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Journal List</h5>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('finance.journals.index') }}" class="btn btn-{{ !request('type') ? 'primary' : 'soft-primary' }} btn-sm">All</a>
                                <a href="{{ route('finance.journals.index', ['type' => 'payment']) }}" class="btn btn-{{ request('type') == 'payment' ? 'primary' : 'soft-primary' }} btn-sm">Payment</a>
                                <a href="{{ route('finance.journals.index', ['type' => 'receipt']) }}" class="btn btn-{{ request('type') == 'receipt' ? 'primary' : 'soft-primary' }} btn-sm">Receipt</a>
                                <a href="{{ route('finance.journals.index', ['type' => 'contra']) }}" class="btn btn-{{ request('type') == 'contra' ? 'primary' : 'soft-primary' }} btn-sm">Contra</a>
                                <a href="{{ route('finance.journals.index', ['type' => 'journal']) }}" class="btn btn-{{ request('type') == 'journal' ? 'primary' : 'soft-primary' }} btn-sm">Journal</a>
                            </div>
                        </div>
                        <div class="flex-shrink-0 ms-2">
                            @can('finance-journal-create')
                            <button class="btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#createJournalOffcanvas">
                                <i class="fas fa-plus align-bottom me-1"></i> New Journal Entry
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
            
            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                        <thead class="text-muted table-light">
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Journal #</th>
                                <th scope="col">Type</th>
                                <th scope="col">Reference</th>
                                <th scope="col">Description</th>
                                <th scope="col">Total Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($journals as $journal)
                            <tr>
                                <td>{{ $journal->date->format('d M, Y') }}</td>
                                <td><a href="{{ route('finance.journals.show', $journal->id) }}" class="fw-medium link-primary">{{ $journal->journal_number }}</a></td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'payment' => 'warning',
                                            'receipt' => 'success',
                                            'contra' => 'info',
                                            'journal' => 'primary',
                                            'depreciation' => 'secondary',
                                            'payroll' => 'dark'
                                        ];
                                        $color = $typeColors[$journal->type] ?? 'primary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}-subtle text-{{ $color }} text-uppercase">{{ $journal->type }}</span>
                                </td>
                                <td>{{ $journal->reference }}</td>
                                <td>{{ Str::limit($journal->description, 30) }}</td>
                                <td>{{ number_format($journal->entries->sum('debit'), 2) }}</td>
                                <td>
                                    @if($journal->status == 'posted')
                                        <span class="badge bg-success-subtle text-success">Posted</span>
                                    @elseif($journal->status == 'draft')
                                        <span class="badge bg-secondary-subtle text-secondary">Draft</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">{{ ucfirst($journal->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($journal->status == 'draft')
                                    <a href="{{ route('finance.journals.edit', $journal->id) }}" class="btn btn-sm btn-ghost-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('finance.journals.post', $journal->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-ghost-success" title="Post" onclick="return confirm('Are you sure you want to post this journal?')"><i class="fas fa-check-double"></i></button>
                                    </form>
                                    @endif
                                    <a href="{{ route('finance.journals.show', $journal->id) }}" class="btn btn-sm btn-ghost-info" title="View"><i class="fas fa-eye"></i></a>
                                    <button class="btn btn-sm btn-ghost-secondary" title="Print" onclick="window.print()"><i class="fas fa-print"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end mt-3">
                        {{ $journals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Journal Offcanvas -->
<div class="offcanvas offcanvas-end" style="width: 800px;" tabindex="-1" id="createJournalOffcanvas" aria-labelledby="createJournalLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="createJournalLabel">Create Journal Entry</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
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
            
            <div class="mt-4 d-flex justify-content-between">
                <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
                <div>
                    <button type="submit" name="post_journal" value="1" class="btn btn-success me-1">Save & Post</button>
                    <button type="submit" class="btn btn-primary">Save Draft</button>
                </div>
            </div>
        </form>
    </div>
</div>

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
