@extends("HRM::layouts.settings")

@section("title", "Salary Components")
@section("breadcrumb")
    <li class="breadcrumb-item active">Payroll Settings</li>
    <li class="breadcrumb-item active">Components</li>
@endsection

@section("settings-content")
    <div class="card bg-white">
        <div class="card-header border-bottom">
             <div class="d-flex align-items-center justify-content-between py-1">
                <h6 class="font-weight-medium mb-0">Components List</h6>
                <button class="btn btn-primary btn-sm d-flex align-items-center font-weight-medium" data-bs-toggle="offcanvas" data-bs-target="#addComponentOffcanvas">
                    <i class="mdi mdi-plus me-1"></i> Add Component
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#earnings" role="tab">
                        <span class="d-block d-sm-none"><i class="mdi mdi-cash-plus"></i></span>
                        <span class="d-none d-sm-block">Earnings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#deductions" role="tab">
                        <span class="d-block d-sm-none"><i class="mdi mdi-cash-minus"></i></span>
                        <span class="d-none d-sm-block">Deductions</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content p-3 text-muted">
                {{-- Earnings Tab --}}
                <div class="tab-pane active" id="earnings" role="tabpanel">
                    <div class="table-responsive rounded-10 border">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th>Name</th>
                                    <th>Calculation</th>
                                    <th>Value</th>
                                    <th>Taxable</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($components->where('type', 'earning') as $component)
                                <tr>
                                    <td><strong>{{ $component->name }}</strong></td>
                                    <td>{{ ucfirst($component->calculation_type) }}</td>
                                    <td>
                                        @if($component->calculation_type == 'percentage')
                                            {{ $component->default_percentage }}% of {{ $component->percentageBasis->name ?? 'Basic' }}
                                        @else
                                            Fixed: {{ $component->default_amount }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($component->is_taxable)
                                            <span class="badge bg-soft-warning text-warning">Taxable</span>
                                        @else
                                            <span class="badge bg-soft-success text-success">Non-Taxable</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($component->is_active)
                                            <span class="badge bg-soft-success text-success">Active</span>
                                        @else
                                            <span class="badge bg-soft-secondary text-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                         <button class="btn btn-sm btn-link text-primary" onclick="editComponent({{ $component }})">
                                            <i class="bx bx-edit font-size-16"></i>
                                        </button>
                                        <form action="{{ route('hrm.settings.salary-components.destroy', $component->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger" onclick="return confirm('Delete this component?')">
                                                <i class="bx bx-trash font-size-16"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4">No earnings configured.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Deductions Tab --}}
                <div class="tab-pane" id="deductions" role="tabpanel">
                    <div class="table-responsive rounded-10 border">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th>Name</th>
                                    <th>Calculation</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($components->where('type', 'deduction') as $component)
                                <tr>
                                    <td><strong>{{ $component->name }}</strong></td>
                                    <td>{{ ucfirst($component->calculation_type) }}</td>
                                    <td>
                                        @if($component->calculation_type == 'percentage')
                                            {{ $component->default_percentage }}% of {{ $component->percentageBasis->name ?? 'Basic' }}
                                        @else
                                            Fixed: {{ $component->default_amount }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($component->is_active)
                                            <span class="badge bg-soft-success text-success">Active</span>
                                        @else
                                            <span class="badge bg-soft-secondary text-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-link text-primary" onclick="editComponent({{ $component }})">
                                            <i class="bx bx-edit font-size-16"></i>
                                        </button>
                                         <form action="{{ route('hrm.settings.salary-components.destroy', $component->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger" onclick="return confirm('Delete this component?')">
                                                <i class="bx bx-trash font-size-16"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4">No deductions configured.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add/Edit Offcanvas --}}
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="addComponentOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasTitle">Add Salary Component</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="componentForm" action="{{ route('hrm.settings.salary-components.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="POST" id="formMethod">
                
                <div class="mb-3">
                    <label class="form-label">Component Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="name" required placeholder="e.g. House Rent Allowance">
                </div>

                <div class="mb-3">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="type" id="type" required onchange="toggleTaxable()">
                        <option value="earning">Earning</option>
                        <option value="deduction">Deduction</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Calculation Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="calculation_type" id="calculation_type" required onchange="toggleCalculationFields()">
                        <option value="fixed">Fixed Amount</option>
                        <option value="percentage">Percentage</option>
                    </select>
                </div>

                <div class="row" id="fixedAmountField">
                    <div class="col-12 mb-3">
                        <label class="form-label">Default Amount</label>
                         <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" name="default_amount" id="default_amount" step="0.01">
                        </div>
                    </div>
                </div>

                <div class="row d-none" id="percentageFields">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Percentage (%)</label>
                        <input type="number" class="form-control" name="default_percentage" id="default_percentage" step="0.01">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Of Component</label>
                        <select class="form-select" name="percentage_basis_id" id="percentage_basis_id">
                            <option value="">Of Basic Salary</option>
                            @foreach($components->where('type', 'earning') as $comp)
                            <option value="{{ $comp->id }}">{{ $comp->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Leave empty for Basic Salary</small>
                    </div>
                </div>

                <div class="mb-3" id="taxableField">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_taxable" name="is_taxable" value="1" checked>
                        <label class="form-check-label" for="is_taxable">Is Taxable?</label>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Component</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleCalculationFields() {
            const type = document.getElementById('calculation_type').value;
            const fixedDiv = document.getElementById('fixedAmountField');
            const percentDiv = document.getElementById('percentageFields');
            
            if (type === 'percentage') {
                fixedDiv.classList.add('d-none');
                percentDiv.classList.remove('d-none');
                document.getElementById('default_amount').removeAttribute('required');
                document.getElementById('default_percentage').setAttribute('required', 'required');
            } else {
                fixedDiv.classList.remove('d-none');
                percentDiv.classList.add('d-none');
                document.getElementById('default_amount').setAttribute('required', 'required');
                document.getElementById('default_percentage').removeAttribute('required');
            }
        }

        function toggleTaxable() {
            const type = document.getElementById('type').value;
            const taxableDiv = document.getElementById('taxableField');
            if (type === 'deduction') {
                taxableDiv.classList.add('d-none');
            } else {
                taxableDiv.classList.remove('d-none');
            }
        }

        function editComponent(data) {
            document.getElementById('offcanvasTitle').textContent = 'Edit Salary Component';
            document.getElementById('submitBtn').textContent = 'Update Component';
            
            const form = document.getElementById('componentForm');
            form.action = "{{ route('hrm.settings.salary-components.index') }}/" + data.id;
            document.getElementById('formMethod').value = 'PUT';

            document.getElementById('name').value = data.name;
            document.getElementById('type').value = data.type;
            document.getElementById('calculation_type').value = data.calculation_type;
            document.getElementById('default_amount').value = data.default_amount;
            document.getElementById('default_percentage').value = data.default_percentage;
            document.getElementById('percentage_basis_id').value = data.percentage_basis_id;
            
            document.getElementById('is_taxable').checked = data.is_taxable;
            document.getElementById('is_active').checked = data.is_active;

            toggleCalculationFields();
            toggleTaxable();

            const bsOffcanvas = new bootstrap.Offcanvas(document.getElementById('addComponentOffcanvas'));
            bsOffcanvas.show();
        }
        
        // Reset form on open if creating
        document.getElementById('addComponentOffcanvas').addEventListener('hidden.bs.offcanvas', function () {
            document.getElementById('offcanvasTitle').textContent = 'Add Salary Component';
            document.getElementById('submitBtn').textContent = 'Save Component';
            document.getElementById('componentForm').reset();
            document.getElementById('componentForm').action = "{{ route('hrm.settings.salary-components.store') }}";
            document.getElementById('formMethod').value = 'POST';
            toggleCalculationFields();
            toggleTaxable();
        });
    </script>
@endsection
