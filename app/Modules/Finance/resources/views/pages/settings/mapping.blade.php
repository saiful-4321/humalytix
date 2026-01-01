@extends('Main::layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Payroll & Expense Mappings</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Finance</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Settings</a></li>
                    <li class="breadcrumb-item active">Mappings</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Integration Settings</h4>
                <p class="text-muted mb-0">Map your payroll components and expense categories to Chart of Accounts</p>
            </div>
            <div class="card-body">
                <form action="{{ route('finance.settings.mapping.update') }}" method="POST">
                    @csrf
                    
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Component Name</th>
                                    <th width="35%">Debit Account (Expense/Asset)</th>
                                    <th width="35%">Credit Account (Liability/Cash)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mappings as $index => $mapping)
                                <tr>
                                    <td>
                                        <input type="hidden" name="mappings[{{ $index }}][id]" value="{{ $mapping->id }}">
                                        <strong>{{ $mapping->component_name }}</strong>
                                    </td>
                                    <td>
                                        <select class="form-select account-select" name="mappings[{{ $index }}][debit_account_id]">
                                            <option value="">Select Account</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}" {{ $mapping->debit_account_id == $acc->id ? 'selected' : '' }}>
                                                    {{ $acc->code }} - {{ $acc->name }} ({{ $acc->type->name ?? '' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select account-select" name="mappings[{{ $index }}][credit_account_id]">
                                            <option value="">Select Account</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}" {{ $mapping->credit_account_id == $acc->id ? 'selected' : '' }}>
                                                    {{ $acc->code }} - {{ $acc->name }} ({{ $acc->type->name ?? '' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-success"><i class="ri-save-line align-middle me-1"></i> Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Initialize select2 if available, though native select is fine for this compact list
</script>
@endsection
