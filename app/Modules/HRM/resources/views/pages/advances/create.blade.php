@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>New Advance Request</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.advances.index') }}">Advances</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix justify-content-center">
    <div class="col-lg-8">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <h6 class="font-weight-medium mb-0">Salary Advance Request Form</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('hrm.advances.store') }}" method="POST">
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

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Advance Amount <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                name="amount" step="0.01" min="1" required value="{{ old('amount') }}">
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Deduction Period (Months) <span class="text-danger">*</span></label>
                            <select class="form-select @error('deduction_months') is-invalid @enderror" name="deduction_months" required>
                                @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('deduction_months') == $i ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'month' : 'months' }}</option>
                                @endfor
                            </select>
                            @error('deduction_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Disbursement Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('disbursement_date') is-invalid @enderror" 
                            name="disbursement_date" required value="{{ old('disbursement_date', date('Y-m-d')) }}">
                        @error('disbursement_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea class="form-control" name="reason" rows="3" placeholder="Explain why you need this advance...">{{ old('reason') }}</textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="mdi mdi-information me-1"></i>
                        <strong>Note:</strong> The advance amount will be automatically deducted from salary over the selected period.
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('hrm.advances.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
