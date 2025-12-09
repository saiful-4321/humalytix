@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Create Salary Structure</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item">Settings</li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.settings.salary-structures.index') }}">Salary Structures</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <h6 class="font-weight-medium mb-0">Structure Details</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('hrm.settings.salary-structures.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Structure Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required placeholder="e.g. Grade A Executive">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="description" placeholder="Brief description...">
                        </div>
                        <div class="col-md-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" checked>
                                <label class="form-check-label" for="isActive">Active</label>
                            </div>
                        </div>
                    </div>

                    <h6 class="font-size-14 text-uppercase mb-3 mt-2 text-primary">Earnings</h6>
                    <div class="table-responsive mb-4 rounded border">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAllEarnings">
                                        </div>
                                    </th>
                                    <th>Component</th>
                                    <th>Calculation</th>
                                    <th>Default Value</th>
                                    <th>Override (Optional)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($earnings as $earning)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input earning-check" type="checkbox" name="components[{{ $earning->id }}][enabled]" value="1" id="c_{{ $earning->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <label class="form-label mb-0 cursor-pointer" for="c_{{ $earning->id }}">
                                            <strong>{{ $earning->name }}</strong>
                                        </label>
                                    </td>
                                    <td>{{ ucfirst($earning->calculation_type) }}</td>
                                    <td>
                                        @if($earning->calculation_type == 'percentage')
                                            {{ $earning->default_percentage }}%
                                        @else
                                            {{ $earning->default_amount }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($earning->calculation_type == 'percentage')
                                            <div class="input-group input-group-sm w-50">
                                                <input type="number" class="form-control" name="components[{{ $earning->id }}][percentage]" step="0.01" placeholder="Override %">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        @else
                                            <div class="input-group input-group-sm w-50">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" name="components[{{ $earning->id }}][amount]" step="0.01" placeholder="Override amt">
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h6 class="font-size-14 text-uppercase mb-3 mt-2 text-danger">Deductions</h6>
                    <div class="table-responsive mb-4 rounded border">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAllDeductions">
                                        </div>
                                    </th>
                                    <th>Component</th>
                                    <th>Calculation</th>
                                    <th>Default Value</th>
                                    <th>Override (Optional)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deductions as $deduction)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input deduction-check" type="checkbox" name="components[{{ $deduction->id }}][enabled]" value="1" id="c_{{ $deduction->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <label class="form-label mb-0 cursor-pointer" for="c_{{ $deduction->id }}">
                                            <strong>{{ $deduction->name }}</strong>
                                        </label>
                                    </td>
                                    <td>{{ ucfirst($deduction->calculation_type) }}</td>
                                    <td>
                                        @if($deduction->calculation_type == 'percentage')
                                            {{ $deduction->default_percentage }}%
                                        @else
                                            {{ $deduction->default_amount }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($deduction->calculation_type == 'percentage')
                                            <div class="input-group input-group-sm w-50">
                                                <input type="number" class="form-control" name="components[{{ $deduction->id }}][percentage]" step="0.01" placeholder="Override %">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        @else
                                            <div class="input-group input-group-sm w-50">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" name="components[{{ $deduction->id }}][amount]" step="0.01" placeholder="Override amt">
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hrm.settings.salary-structures.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Structure</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple script to toggle all checkboxes
    document.getElementById('checkAllEarnings').addEventListener('change', function() {
        document.querySelectorAll('.earning-check').forEach(c => c.checked = this.checked);
    });
    document.getElementById('checkAllDeductions').addEventListener('change', function() {
        document.querySelectorAll('.deduction-check').forEach(c => c.checked = this.checked);
    });
</script>
@endsection
