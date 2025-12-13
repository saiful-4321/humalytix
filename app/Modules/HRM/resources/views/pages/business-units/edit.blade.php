@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Edit Business Unit</h2>
            <p class="text-muted">Edit details for {{ $businessUnit->name }}</p>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.business-units.index') }}">Business Units</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix justify-content-center">
    <div class="col-lg-8">
        <div class="card bg-white">
            <div class="card-body">
                <form action="{{ route('hrm.business-units.update', $businessUnit->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $businessUnit->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Code</label>
                        <input type="text" class="form-control" name="code" value="{{ old('code', $businessUnit->code) }}">
                    </div>

                    <div class="mb-3">
                        <label for="head_employee_id" class="form-label">Head of Unit</label>
                        <select class="form-select select2" name="head_employee_id">
                            <option value="">Select Head</option>
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('head_employee_id', $businessUnit->head_employee_id) == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }} ({{ $emp->designation }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3">{{ old('description', $businessUnit->description) }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="isActiveSwitch" name="is_active" value="1" {{ old('is_active', $businessUnit->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActiveSwitch">Active Status</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hrm.business-units.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Business Unit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
