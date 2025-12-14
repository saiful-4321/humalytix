<h6 class="font-size-14 text-uppercase mb-3 mt-2 text-primary">Earnings</h6>
<div class="table-responsive mb-4 rounded border">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th style="width: 50px;">
                    <div class="form-check">
                        <input class="form-check-input check-all-earnings" type="checkbox" id="{{ $prefix }}_checkAllEarnings">
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
            @php
                $hasComponent = isset($currentStructureComponents) && $currentStructureComponents->has($earning->id);
                $pivot = $hasComponent ? $currentStructureComponents[$earning->id]->pivot : null;
            @endphp
            <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input earning-check" type="checkbox" name="components[{{ $earning->id }}][enabled]" value="1" id="{{ $prefix }}_c_{{ $earning->id }}" {{ $hasComponent ? 'checked' : '' }}>
                    </div>
                </td>
                <td>
                    <label class="form-label mb-0 cursor-pointer" for="{{ $prefix }}_c_{{ $earning->id }}">
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
                            <input type="number" class="form-control" name="components[{{ $earning->id }}][percentage]" step="0.01" value="{{ $pivot->percentage ?? '' }}" placeholder="Override %">
                            <span class="input-group-text">%</span>
                        </div>
                    @else
                        <div class="input-group input-group-sm w-50">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" name="components[{{ $earning->id }}][amount]" step="0.01" value="{{ $pivot->amount ?? '' }}" placeholder="Override amt">
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
                        <input class="form-check-input check-all-deductions" type="checkbox" id="{{ $prefix }}_checkAllDeductions">
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
            @php
                $hasComponent = isset($currentStructureComponents) && $currentStructureComponents->has($deduction->id);
                $pivot = $hasComponent ? $currentStructureComponents[$deduction->id]->pivot : null;
            @endphp
            <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input deduction-check" type="checkbox" name="components[{{ $deduction->id }}][enabled]" value="1" id="{{ $prefix }}_c_{{ $deduction->id }}" {{ $hasComponent ? 'checked' : '' }}>
                    </div>
                </td>
                <td>
                    <label class="form-label mb-0 cursor-pointer" for="{{ $prefix }}_c_{{ $deduction->id }}">
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
                            <input type="number" class="form-control" name="components[{{ $deduction->id }}][percentage]" step="0.01" value="{{ $pivot->percentage ?? '' }}" placeholder="Override %">
                            <span class="input-group-text">%</span>
                        </div>
                    @else
                        <div class="input-group input-group-sm w-50">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" name="components[{{ $deduction->id }}][amount]" step="0.01" value="{{ $pivot->amount ?? '' }}" placeholder="Override amt">
                        </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
