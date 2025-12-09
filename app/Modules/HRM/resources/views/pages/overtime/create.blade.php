@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Overtime Management</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.overtime.index') }}">Overtime</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix justify-content-center">
    <div class="col-lg-8">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <h6 class="font-weight-medium mb-0">Record Overtime</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('hrm.overtime.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Employee <span class="text-danger">*</span></label>
                            <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->employee_code }})</option>
                                @endforeach
                            </select>
                            @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">OT Policy <span class="text-danger">*</span></label>
                            <select class="form-select @error('ot_policy_id') is-invalid @enderror" name="ot_policy_id" required>
                                <option value="">Select Policy</option>
                                @foreach($policies as $policy)
                                <option value="{{ $policy->id }}">{{ $policy->name }}</option>
                                @endforeach
                            </select>
                            @error('ot_policy_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">OT Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('ot_date') is-invalid @enderror" name="ot_date" required value="{{ old('ot_date', date('Y-m-d')) }}">
                            @error('ot_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('start_time') is-invalid @enderror" name="start_time" required>
                            @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('end_time') is-invalid @enderror" name="end_time" required>
                            @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">OT Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('ot_type') is-invalid @enderror" name="ot_type" required>
                            <option value="regular">Regular OT</option>
                            <option value="weekend">Weekend OT</option>
                            <option value="holiday">Holiday OT</option>
                            <option value="night_shift">Night Shift</option>
                        </select>
                        @error('ot_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('hrm.overtime.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Overtime</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
