@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>New Loan Application</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.loans.index') }}">Loans</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix justify-content-center">
    <div class="col-lg-8">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <h6 class="font-weight-medium mb-0">Loan Application Form</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('hrm.loans.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
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
                        <div class="col-md-6">
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
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Loan Amount <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('loan_amount') is-invalid @enderror" 
                                name="loan_amount" step="0.01" min="1" required value="{{ old('loan_amount') }}">
                            @error('loan_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted" id="maxAmountHint"></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tenure (Months) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('tenure_months') is-invalid @enderror" 
                                name="tenure_months" min="1" required value="{{ old('tenure_months') }}">
                            @error('tenure_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted" id="maxTenureHint"></small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Interest Rate (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('interest_rate') is-invalid @enderror" 
                                name="interest_rate" step="0.01" min="0" required value="{{ old('interest_rate') }}">
                            @error('interest_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Interest Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('interest_type') is-invalid @enderror" name="interest_type" required>
                                <option value="flat" {{ old('interest_type') == 'flat' ? 'selected' : '' }}>Flat</option>
                                <option value="reducing" {{ old('interest_type') == 'reducing' ? 'selected' : '' }}>Reducing Balance</option>
                            </select>
                            @error('interest_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
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
                        <a href="{{ route('hrm.loans.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loanType').addEventListener('change', function() {
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
</script>
@endsection
