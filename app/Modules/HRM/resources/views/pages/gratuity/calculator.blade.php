@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Gratuity Calculator</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Gratuity Calculator</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix justify-content-center">
    <div class="col-lg-8">
        @if($config)
        <div class="alert alert-info mb-3">
            <h6 class="alert-heading"><i class="mdi mdi-information me-1"></i> Gratuity Policy</h6>
            <p class="mb-1"><strong>Formula:</strong> {{ $config->formula_description ?? 'Last Basic × Years of Service × ' . $config->multiplier }}</p>
            <p class="mb-1"><strong>Minimum Service:</strong> {{ $config->min_service_years }} years</p>
            @if($config->max_gratuity_amount)
            <p class="mb-0"><strong>Maximum Amount:</strong> ${{ number_format($config->max_gratuity_amount, 2) }}</p>
            @endif
        </div>
        @else
        <div class="alert alert-warning mb-3">
            <i class="mdi mdi-alert me-1"></i> No active gratuity configuration found. Please contact admin.
        </div>
        @endif

        <div class="card bg-white">
            <div class="card-header border-bottom">
                <h6 class="font-weight-medium mb-0">Calculate Gratuity</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('hrm.gratuity.calculate') }}" method="POST" id="gratuityForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Select Employee <span class="text-danger">*</span></label>
                        <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" id="employeeSelect" required>
                            <option value="">Choose employee...</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" 
                                data-joining="{{ $employee->joining_date?->format('Y-m-d') }}"
                                data-basic="{{ $employee->salary?->basic_salary ?? $employee->basic_salary ?? 0 }}">
                                {{ $employee->full_name }} ({{ $employee->employee_code }})
                            </option>
                            @endforeach
                        </select>
                        @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Calculation Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('calculation_date') is-invalid @enderror" 
                            name="calculation_date" id="calculationDate" value="{{ date('Y-m-d') }}" required>
                        @error('calculation_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Usually the exit/retirement date</small>
                    </div>

                    <div id="previewSection" class="d-none">
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body">
                                <h6 class="mb-3">Preview Calculation</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Joining Date:</strong> <span id="previewJoining">-</span></p>
                                        <p class="mb-2"><strong>Service Years:</strong> <span id="previewServiceYears">-</span></p>
                                        <p class="mb-0"><strong>Last Basic Salary:</strong> $<span id="previewBasicSalary">0.00</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Multiplier:</strong> {{ $config->multiplier ?? 0 }}</p>
                                        <p class="mb-0"><strong>Estimated Gratuity:</strong> 
                                            <span class="fw-bold text-success font-size-18">$<span id="previewGratuity">0.00</span></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('hrm.gratuity.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Calculate & Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const employeeSelect = document.getElementById('employeeSelect');
    const calculationDate = document.getElementById('calculationDate');
    const previewSection = document.getElementById('previewSection');
    const multiplier = {{ $config->multiplier ?? 0 }};
    const minServiceYears = {{ $config->min_service_years ?? 5 }};
    
    function updatePreview() {
        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
        if (!selectedOption.value) {
            previewSection.classList.add('d-none');
            return;
        }
        
        const joiningDate = new Date(selectedOption.dataset.joining);
        const calcDate = new Date(calculationDate.value);
        const basicSalary = parseFloat(selectedOption.dataset.basic);
        
        if (!joiningDate || !calcDate) return;
        
        // Calculate service years
        const diffTime = Math.abs(calcDate - joiningDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        const serviceYears = (diffDays / 365).toFixed(2);
        
        // Calculate gratuity
        let gratuity = basicSalary * serviceYears * multiplier;
        
        @if($config && $config->max_gratuity_amount)
        if (gratuity > {{ $config->max_gratuity_amount }}) {
            gratuity = {{ $config->max_gratuity_amount }};
        }
        @endif
        
        // Update preview
        document.getElementById('previewJoining').textContent = joiningDate.toLocaleDateString();
        document.getElementById('previewServiceYears').textContent = serviceYears + ' years';
        document.getElementById('previewBasicSalary').textContent = basicSalary.toFixed(2);
        document.getElementById('previewGratuity').textContent = gratuity.toFixed(2);
        
        previewSection.classList.remove('d-none');
        
        // Check eligibility
        if (parseFloat(serviceYears) < minServiceYears) {
            previewSection.querySelector('.card').classList.add('border-danger');
            document.getElementById('previewServiceYears').innerHTML = serviceYears + ' years <span class="badge bg-danger">Not Eligible</span>';
        } else {
            previewSection.querySelector('.card').classList.remove('border-danger');
        }
    }
    
    employeeSelect.addEventListener('change', updatePreview);
    calculationDate.addEventListener('change', updatePreview);
});
</script>
@endsection
