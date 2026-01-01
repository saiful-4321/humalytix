@extends('Main::layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Edit Journal Entry</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('finance.dashboard') }}">Finance</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('finance.journals.index') }}">Journals</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Edit Journal {{ $journal->journal_number }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('finance.journals.update', $journal->id) }}" method="POST" id="journalFormPage">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date" value="{{ $journal->date->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Reference</label>
                            <input type="text" class="form-control" name="reference" value="{{ $journal->reference }}" placeholder="e.g Invoice #123">
                        </div>
                        <div class="col-md-3">
                             <label class="form-label">Fiscal Year</label>
                             <input type="text" class="form-control" value="{{ $fiscalYear->name ?? 'N/A' }}" readonly disabled>
                        </div>
                        <div class="col-md-3">
                             <label class="form-label">Voucher Type <span class="text-danger">*</span></label>
                             <select name="type" class="form-select" required>
                                 <option value="journal" {{ $journal->type == 'journal' ? 'selected' : '' }}>Journal Voucher</option>
                                 <option value="payment" {{ $journal->type == 'payment' ? 'selected' : '' }}>Payment Voucher</option>
                                 <option value="receipt" {{ $journal->type == 'receipt' ? 'selected' : '' }}>Receipt Voucher</option>
                                 <option value="contra" {{ $journal->type == 'contra' ? 'selected' : '' }}>Contra Voucher</option>
                             </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2">{{ $journal->description }}</textarea>
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
                                    @foreach($journal->entries as $index => $entry)
                                    <tr class="entry-row">
                                        <td>
                                            <select name="entries[{{ $index }}][account_id]" class="form-select form-select-sm" required>
                                                <option value="">Select Account</option>
                                                @foreach($accounts as $acc)
                                                    <option value="{{ $acc->id }}" {{ $entry->account_id == $acc->id ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="entries[{{ $index }}][description]" class="form-control form-control-sm mt-1" value="{{ $entry->description }}" placeholder="Line description (optional)">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="entries[{{ $index }}][debit]" class="form-control form-control-sm debit-input" value="{{ $entry->debit }}">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="entries[{{ $index }}][credit]" class="form-control form-control-sm credit-input" value="{{ $entry->credit }}">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-ghost-danger remove-row"><i class="fas fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
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
                                 <button type="button" class="btn btn-sm btn-soft-secondary w-100" id="addRowBtn"><i class="fas fa-plus"></i> Add Line</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('finance.journals.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Journal</button>
                    </div>
                </form>
            </div>
        </div>
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
        let rowIndex = {{ $journal->entries->count() }}; // Start from existing count

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

        document.getElementById('addRowBtn').addEventListener('click', addRow);

        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                const tr = e.target.closest('tr');
                if (container.querySelectorAll('tr').length > 2) {
                    tr.remove();
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
        
        // Initial Calculation
        updateTotals();
    });
</script>
@endsection
