@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Assign Asset</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.assets.index') }}">Assets</a></li>
                <li class="breadcrumb-item active">Assign</li>
            </ul>
        </div>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-4">Assigning: {{ $asset->name }} ({{ $asset->code }})</h5>
                <form action="{{ route('hrm.assets.store-assignment', $asset) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Assign To <span class="text-danger">*</span></label>
                        <select class="form-select" name="employee_id" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Assigned Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="assigned_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Condition <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="assigned_condition" value="{{ $asset->condition }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" placeholder="Any additional notes..."></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hrm.assets.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Assign Asset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
