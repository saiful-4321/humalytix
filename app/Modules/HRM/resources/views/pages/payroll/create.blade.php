@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Create Payroll</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.payroll.index') }}">Payroll</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ul>
        </div>
    </div>
</div>
@include("Main::widgets.message.sweet-alert")
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hrm.payroll.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employee <span class="text-danger">*</span></label>
                            <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" id="employee_id" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" data-basic="{{ $emp->employeeSalary?->basic_salary ?? 0 }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                                @endforeach
                            </select>
                            @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Month <span class="text-danger">*</span></label>
                            <select class="form-select" name="month" required>
                                @for($i=1;$i<=12;$i++)
                                <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$i,1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Year <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="year" value="{{ date('Y') }}" min="2020" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Basic Salary <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="basic_salary" id="basic_salary" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Allowances</label>
                            <input type="number" class="form-control" name="allowances" step="0.01" min="0" value="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Bonuses</label>
                            <input type="number" class="form-control" name="bonuses" step="0.01" min="0" value="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Deductions</label>
                            <input type="number" class="form-control" name="deductions" step="0.01" min="0" value="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tax</label>
                            <input type="number" class="form-control" name="tax" step="0.01" min="0" value="0">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hrm.payroll.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success">Create Payroll</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('employee_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const basicSalary = selected.getAttribute('data-basic');
    document.getElementById('basic_salary').value = basicSalary || 0;
});
</script>
@endpush
