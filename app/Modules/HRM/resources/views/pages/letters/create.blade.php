@extends("Main::layouts.app")
@push('css_before')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Issue Letter</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.letters.index') }}">Letters</a></li>
                <li class="breadcrumb-item active">Issue</li>
            </ul>
        </div>
    </div>
</div>
@include("Main::widgets.message.sweet-alert")
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hrm.letters.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employee <span class="text-danger">*</span></label>
                            <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                                @endforeach
                            </select>
                            @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Template <span class="text-danger">*</span></label>
                            <select class="form-select @error('template_id') is-invalid @enderror" name="template_id" id="template_id" required>
                                <option value="">Select Template</option>
                                @foreach($templates as $tpl)
                                <option value="{{ $tpl->id }}" data-type="{{ $tpl->type }}">{{ $tpl->name }} ({{ ucfirst($tpl->type) }})</option>
                                @endforeach
                            </select>
                            @error('template_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Issued Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="issued_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Signatory <span class="text-danger">*</span></label>
                            <select class="form-select" name="signatory_id" required>
                                <option value="">Select Signatory</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $emp->designation == 'CEO' || $emp->designation == 'HR Manager' ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <strong>Available Variables:</strong>
                        <p class="mb-0">{employee_name}, {employee_code}, {designation}, {department}, {date}, {signatory_name}, {company_name}</p>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hrm.letters.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success">Issue Letter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    // Preview template logic could be added here
</script>
@endpush
