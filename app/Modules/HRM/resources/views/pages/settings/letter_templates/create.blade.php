@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Create Letter Template</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item">Settings</li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.settings.letter-templates.index') }}">Letter Templates</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <h6 class="font-weight-medium mb-0">New Template Details</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('hrm.settings.letter-templates.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Template Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required placeholder="e.g. Standard Appointment Letter">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="type" required>
                                <option value="appointment">Appointment</option>
                                <option value="termination">Termination</option>
                                <option value="increment">Increment</option>
                                <option value="transfer">Transfer</option>
                                <option value="confirmation">Confirmation</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="subject" required placeholder="e.g. Appointment Letter - {company_name}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content <span class="text-danger">*</span></label>
                        <div class="mb-2">
                             <small class="text-muted d-block mb-1">Click to insert variable:</small>
                             <div class="d-flex flex-wrap gap-1" id="variableButtons">
                                 <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar('{employee_name}')">{employee_name}</button>
                                 <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar('{employee_code}')">{employee_code}</button>
                                 <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar('{designation}')">{designation}</button>
                                 <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar('{department}')">{department}</button>
                                 <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar('{joining_date}')">{joining_date}</button>
                                 <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar('{salary}')">{salary}</button>
                                 <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar('{company_name}')">{company_name}</button>
                                 <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar('{date}')">{date}</button>
                             </div>
                        </div>
                        <textarea class="form-control" name="content" id="templateContent" rows="15" required placeholder="Dear {employee_name}, ..."></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" checked>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('hrm.settings.letter-templates.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function insertVar(text) {
        const textarea = document.getElementById('templateContent');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const before = textarea.value.substring(0, start);
        const after = textarea.value.substring(end, textarea.value.length);
        
        textarea.value = before + text + after;
        textarea.selectionStart = textarea.selectionEnd = start + text.length;
        textarea.focus();
    }
</script>
@endsection
