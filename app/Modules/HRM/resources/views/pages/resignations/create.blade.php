@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Submit Resignation</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.resignations.index') }}">Resignations</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ul>
        </div>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hrm.resignations.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <select class="form-select" name="employee_id" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)<option value="{{ $emp->id }}">{{ $emp->full_name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Resignation Date</label><input type="date" class="form-control" name="resignation_date" value="{{ date('Y-m-d') }}" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Notice Date (Last Day)</label><input type="date" class="form-control" name="notice_date" required></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Reason</label><textarea class="form-control" name="reason" rows="4" required></textarea></div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hrm.resignations.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-danger">Submit Resignation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
