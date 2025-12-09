@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Salary Assignment</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.employees.index') }}">Employees</a></li>
                <li class="breadcrumb-item active">{{ $employee->full_name }}</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <!-- User Profile Card (Mini) -->
    <div class="col-md-4">
        <div class="card bg-white">
            <div class="card-body text-center">
                <div class="profile-image mb-3">
                     <div class="avatar-xl mx-auto">
                        <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-24">
                            {{ substr($employee->first_name, 0, 1) }}
                        </span>
                    </div>
                </div>
                <h5 class="mb-1">{{ $employee->full_name }}</h5>
                <p class="text-muted mb-2">{{ $employee->designation }}</p>
                <div class="d-flex justify-content-center gap-2">
                    <span class="badge bg-soft-info text-info">{{ $employee->department->name ?? 'No Dept' }}</span>
                    {!! $employee->status_badge !!}
                </div>
            </div>
             <div class="card-footer bg-light">
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <label class="text-muted small mb-0">Joining Date</label>
                        <h6 class="mb-0">{{ $employee->joining_date->format('d M, Y') }}</h6>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small mb-0">Code</label>
                        <h6 class="mb-0">{{ $employee->employee_code }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Salary Info (if exists) -->
        @if($salary)
        <div class="card bg-info text-white border-0 mt-3">
            <div class="card-body">
                <h6 class="text-white-50 text-uppercase font-size-12 mb-2">Current CTC / Salary</h6>
                <h3 class="mb-0 text-white">${{ number_format($salary->basic_salary, 2) }}</h3>
                <small class="text-white-50">Effective from: {{ $salary->effective_date->format('d M, Y') }}</small>
            </div>
        </div>
        @endif
    </div>

    <!-- Salary Configuration -->
    <div class="col-md-8">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <h6 class="font-weight-medium mb-0">Configure Salary Package</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('hrm.employees.salary.update', $employee->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row align-items-end mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Salary Structure <span class="text-danger">*</span></label>
                            <select class="form-select" name="salary_structure_id" id="salaryStructure" required onchange="calculateBreakdown()">
                                <option value="">Select Structure...</option>
                                @foreach($structures as $structure)
                                <option value="{{ $structure->id }}" {{ ($salary && $salary->salary_structure_id == $structure->id) ? 'selected' : '' }}>
                                    {{ $structure->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                             <label class="form-label">Basic Salary (Calculated Base) <span class="text-danger">*</span></label>
                             <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="basic_salary" id="basicSalary" 
                                    value="{{ $salary->basic_salary ?? old('basic_salary') }}" 
                                    step="0.01" required placeholder="Enter amount" oninput="calculateBreakdown()">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Effective Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="effective_date" value="{{ $salary->effective_date?->format('Y-m-d') ?? date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-center mb-2">
                             <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" checked>
                                <label class="form-check-label" for="isActive">Is Active Salary?</label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    
                    <!-- Breakdown Preview -->
                    <div id="salaryBreakdown">
                        <div class="text-center text-muted py-5">
                            <i class="mdi mdi-calculator font-size-24 d-block mb-2"></i>
                            Select a structure and enter basic salary to see the breakdown.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4" id="actionButtons" style="display:none;">
                        <a href="{{ route('hrm.employees.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Salary Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let calculateTimeout;

    function calculateBreakdown() {
        const structureId = document.getElementById('salaryStructure').value;
        const basic = document.getElementById('basicSalary').value;
        const breakdownDiv = document.getElementById('salaryBreakdown');
        const actionButtons = document.getElementById('actionButtons');

        if (!structureId || !basic) {
            breakdownDiv.innerHTML = '<div class="text-center text-muted py-5"><i class="mdi mdi-calculator font-size-24 d-block mb-2"></i>Select structure and basic salary.</div>';
            actionButtons.style.display = 'none';
            return;
        }

        clearTimeout(calculateTimeout);
        calculateTimeout = setTimeout(() => {
            breakdownDiv.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Calculating...</p></div>';
            
            fetch('{{ route("hrm.employees.salary.calculate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    structure_id: structureId,
                    basic_salary: basic
                })
            })
            .then(response => response.json())
            .then(data => {
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success mb-3 text-uppercase font-size-13 fw-bold">Earnings</h6>
                            <table class="table table-sm table-borderless">
                                <tbody>`;
                
                data.preview.earnings.forEach(item => {
                    html += `
                        <tr>
                            <td>${item.name}</td>
                            <td class="text-end fw-medium">$${parseFloat(item.amount).toFixed(2)}</td>
                        </tr>`;
                });

                html += `
                                <tr class="border-top border-success">
                                    <td class="fw-bold">Total Earnings</td>
                                    <td class="text-end fw-bold text-success">$${data.total_earnings.toFixed(2)}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-danger mb-3 text-uppercase font-size-13 fw-bold">Deductions</h6>
                            <table class="table table-sm table-borderless">
                                <tbody>`;
                
                if (data.preview.deductions && data.preview.deductions.length > 0) {
                    data.preview.deductions.forEach(item => {
                        html += `
                            <tr>
                                <td>${item.name}</td>
                                <td class="text-end fw-medium">$${parseFloat(item.amount).toFixed(2)}</td>
                            </tr>`;
                    });
                } else {
                     html += `<tr><td colspan="2" class="text-muted">No deductions</td></tr>`;
                }

                html += `
                                <tr class="border-top border-danger">
                                    <td class="fw-bold">Total Deductions</td>
                                    <td class="text-end fw-bold text-danger">$${data.total_deductions.toFixed(2)}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mt-3 p-3 bg-light rounded d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Net Salary (In Hand)</h5>
                        <h4 class="mb-0 fw-bold text-primary">$${data.net_salary.toFixed(2)}</h4>
                    </div>
                `;
                
                breakdownDiv.innerHTML = html;
                actionButtons.style.display = 'flex';
            });
        }, 500); // Debounce
    }
    
    // Initial call if editing
    document.addEventListener('DOMContentLoaded', function() {
        if(document.getElementById('salaryStructure').value && document.getElementById('basicSalary').value) {
            calculateBreakdown();
        }
    });
</script>
@endsection
