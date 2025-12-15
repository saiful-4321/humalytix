@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Loan Management</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Loans</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Employee Loans</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#createLoanOffcanvas">
                            <i class="mdi mdi-plus me-1"></i> New Loan
                        </button>
                        <button class="btn btn-secondary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Loan #</th>
                                <th>Employee</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Tenure</th>
                                <th>Monthly EMI</th>
                                <th>Outstanding</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                            <tr>
                                <td><a href="{{ route('hrm.loans.show', $loan->id) }}" class="fw-medium">{{ $loan->loan_number }}</a></td>
                                <td>
                                    <div>
                                        <h6 class="mb-0 font-size-14">{{ $loan->employee->full_name }}</h6>
                                        <small class="text-muted">{{ $loan->employee->employee_code }}</small>
                                    </div>
                                </td>
                                <td>{{ $loan->loanType->name }}</td>
                                <td>${{ number_format($loan->loan_amount, 2) }}</td>
                                <td>{{ $loan->tenure_months }} months</td>
                                <td>${{ number_format($loan->monthly_installment, 2) }}</td>
                                <td class="text-danger">${{ number_format($loan->outstanding_balance, 2) }}</td>
                                <td>
                                    @if($loan->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($loan->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($loan->status == 'completed')
                                        <span class="badge bg-info">Completed</span>
                                    @elseif($loan->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('hrm.loans.show', $loan->id) }}" class="btn btn-sm btn-soft-primary" title="Details">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                        
                                        @if($loan->status == 'pending')
                                        <form action="{{ route('hrm.loans.approve', $loan->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-soft-success" title="Approve">
                                                <i class="mdi mdi-check-circle-outline"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('hrm.loans.reject', $loan->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-soft-danger" title="Reject">
                                                <i class="mdi mdi-close-circle-outline"></i>
                                            </button>
                                        </form>
                                        @endif
                                        
                                        @if($loan->status != 'active' && $loan->status != 'completed')
                                        <form action="{{ route('hrm.loans.destroy', $loan->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-soft-danger delete-btn" title="Delete">
                                                <i class="mdi mdi-delete-outline"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-cash-remove font-size-24 d-block mb-2"></i>
                                    No loans found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($loans->count())
            <div class="card-footer bg-transparent border-top">
                {{ $loans->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Create Loan Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="createLoanOffcanvas" style="width: 500px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">New Loan Application</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.loans.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Employee <span class="text-danger">*</span></label>
                <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->full_name }} ({{ $employee->employee_code }})
                    </option>
                    @endforeach
                </select>
                @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Loan Type <span class="text-danger">*</span></label>
                <select class="form-select @error('loan_type_id') is-invalid @enderror" name="loan_type_id" id="loanType" required>
                    <option value="">Select Loan Type</option>
                    @foreach($loanTypes as $type)
                    <option value="{{ $type->id }}" 
                        data-max-amount="{{ $type->max_amount }}"
                        data-interest-rate="{{ $type->interest_rate }}"
                        data-interest-type="{{ $type->interest_type }}"
                        data-max-tenure="{{ $type->max_tenure_months }}"
                        {{ old('loan_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                    @endforeach
                </select>
                @error('loan_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Loan Amount <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('loan_amount') is-invalid @enderror" 
                    name="loan_amount" step="0.01" min="1" required value="{{ old('loan_amount') }}">
                @error('loan_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted" id="maxAmountHint"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Tenure (Months) <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('tenure_months') is-invalid @enderror" 
                    name="tenure_months" min="1" required value="{{ old('tenure_months') }}">
                @error('tenure_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted" id="maxTenureHint"></small>
            </div>

            <div class="mb-3">
                <label class="form-label">Interest Rate (%) <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('interest_rate') is-invalid @enderror" 
                    name="interest_rate" step="0.01" min="0" required value="{{ old('interest_rate') }}">
                @error('interest_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Interest Type <span class="text-danger">*</span></label>
                <select class="form-select @error('interest_type') is-invalid @enderror" name="interest_type" required>
                    <option value="flat" {{ old('interest_type') == 'flat' ? 'selected' : '' }}>Flat</option>
                    <option value="reducing" {{ old('interest_type') == 'reducing' ? 'selected' : '' }}>Reducing Balance</option>
                </select>
                @error('interest_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Disbursement Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('disbursement_date') is-invalid @enderror" 
                    name="disbursement_date" required value="{{ old('disbursement_date', date('Y-m-d')) }}">
                @error('disbursement_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Purpose</label>
                <textarea class="form-control" name="purpose" rows="3">{{ old('purpose') }}</textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Application</button>
            </div>
        </form>
    </div>
</div>

{{-- Filter Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas">
    <div class="offcanvas-header border-bottom">
        <h5>Filter Loans</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.loans.index') }}" method="GET">
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Search Employee</label>
                <input type="text" name="search" class="form-control" placeholder="Name or code..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete Confirmation
    const deleteBtns = document.querySelectorAll('.delete-btn');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    const loanTypeSelect = document.getElementById('loanType');
    if (loanTypeSelect) {
        loanTypeSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const maxAmount = selected.dataset.maxAmount;
            const maxTenure = selected.dataset.maxTenure;
            const interestRate = selected.dataset.interestRate;
            const interestType = selected.dataset.interestType;
            
            if (maxAmount) {
                document.getElementById('maxAmountHint').textContent = `Max: $${parseFloat(maxAmount).toLocaleString()}`;
            }
            if (maxTenure) {
                document.getElementById('maxTenureHint').textContent = `Max: ${maxTenure} months`;
            }
            if (interestRate) {
                document.querySelector('[name="interest_rate"]').value = interestRate;
            }
            if (interestType) {
                document.querySelector('[name="interest_type"]').value = interestType;
            }
        });
    }
});
</script>
@endsection
