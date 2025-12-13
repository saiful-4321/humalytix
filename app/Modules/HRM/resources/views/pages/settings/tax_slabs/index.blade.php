@extends("HRM::layouts.settings")

@section("title", "Tax Slabs")
@section("breadcrumb")
    <li class="breadcrumb-item active">Payroll Settings</li>
    <li class="breadcrumb-item active">Tax Slabs</li>
@endsection

@section("settings-content")
    <div class="card bg-white">
        <div class="card-header border-bottom">
             <div class="d-flex align-items-center justify-content-between py-1">
                <h6 class="font-weight-medium mb-0">Tax Slabs</h6>
                <button class="btn btn-primary btn-sm d-flex align-items-center font-weight-medium" data-bs-toggle="offcanvas" data-bs-target="#addTaxSlabOffcanvas">
                    <i class="mdi mdi-plus me-1"></i> Add Tax Slab
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#male" role="tab">Male</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#female" role="tab">Female</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#other" role="tab">Other / General</a>
                </li>
            </ul>

            <div class="tab-content p-3 text-muted">
                @foreach(['male', 'female', 'other'] as $gender)
                <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{ $gender }}" role="tabpanel">
                    <div class="table-responsive rounded-10 border">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th>Name/Label</th>
                                    <th>Income Range</th>
                                    <th>Tax Rate</th>
                                    <th>Min. Deduction</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $filter = $gender == 'other' ? ['other', 'all'] : [$gender];
                                    $slabs = $taxSlabs->whereIn('gender', $filter);
                                @endphp

                                @forelse($slabs as $slab)
                                <tr>
                                    <td>{{ $slab->name ?? '-' }}</td>
                                    <td>
                                        ${{ number_format($slab->min_income) }} - 
                                        @if($slab->max_income)
                                            ${{ number_format($slab->max_income) }}
                                        @else
                                            <span class="text-muted">Unlimited</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-soft-danger text-danger">{{ $slab->tax_rate }}%</span></td>
                                    <td>{{ $slab->deduction_amount > 0 ? '$'.number_format($slab->deduction_amount) : '-' }}</td>
                                    <td>
                                        @if($slab->is_active)
                                            <span class="badge bg-soft-success text-success">Active</span>
                                        @else
                                            <span class="badge bg-soft-secondary text-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-link text-primary" onclick="editSlab({{ $slab }})">
                                            <i class="bx bx-edit font-size-16"></i>
                                        </button>
                                        <form action="{{ route('hrm.settings.tax-slabs.destroy', $slab->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger" onclick="return confirm('Delete this tax slab?')">
                                                <i class="bx bx-trash font-size-16"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4">No tax slabs configured for {{ ucfirst($gender) }}.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Add/Edit Offcanvas --}}
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="addTaxSlabOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasTitle">Add Tax Slab</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="slabForm" action="{{ route('hrm.settings.tax-slabs.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="POST" id="formMethod">
                
                <div class="mb-3">
                    <label class="form-label">Slab Name (Optional)</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="e.g. First Slab">
                </div>

                 <div class="mb-3">
                    <label class="form-label">Applicable For <span class="text-danger">*</span></label>
                    <select class="form-select" name="gender" id="gender" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                        <option value="all">All Genders</option>
                    </select>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Min Income <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="min_income" id="min_income" required step="0.01">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Max Income</label>
                        <input type="number" class="form-control" name="max_income" id="max_income" step="0.01">
                        <small class="text-muted">Leave empty for unlimited</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tax Rate (%) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="tax_rate" id="tax_rate" required step="0.01" min="0" max="100">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fixed Deduction Amt</label>
                        <input type="number" class="form-control" name="deduction_amount" id="deduction_amount" step="0.01">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Tax Slab</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editSlab(data) {
            document.getElementById('offcanvasTitle').textContent = 'Edit Tax Slab';
            document.getElementById('submitBtn').textContent = 'Update Slab';
            
            const form = document.getElementById('slabForm');
            form.action = "{{ route('hrm.settings.tax-slabs.index') }}/" + data.id;
            document.getElementById('formMethod').value = 'PUT';

            document.getElementById('name').value = data.name;
            document.getElementById('gender').value = data.gender;
            document.getElementById('min_income').value = data.min_income;
            document.getElementById('max_income').value = data.max_income;
            document.getElementById('tax_rate').value = data.tax_rate;
            document.getElementById('deduction_amount').value = data.deduction_amount;
            
            document.getElementById('is_active').checked = data.is_active;

            const bsOffcanvas = new bootstrap.Offcanvas(document.getElementById('addTaxSlabOffcanvas'));
            bsOffcanvas.show();
        }
        
        document.getElementById('addTaxSlabOffcanvas').addEventListener('hidden.bs.offcanvas', function () {
            document.getElementById('offcanvasTitle').textContent = 'Add Tax Slab';
            document.getElementById('submitBtn').textContent = 'Save Tax Slab';
            document.getElementById('slabForm').reset();
            document.getElementById('slabForm').action = "{{ route('hrm.settings.tax-slabs.store') }}";
            document.getElementById('formMethod').value = 'POST';
        });
    </script>
@endsection
