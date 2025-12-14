@extends("HRM::layouts.settings")

@section("title", "Letter Templates")
@section("breadcrumb")
    <li class="breadcrumb-item active">Recruitment Settings</li>
    <li class="breadcrumb-item active">Templates</li>
@endsection

@section("settings-content")
    <div class="card bg-white">
        <div class="card-header border-bottom">
             <div class="d-flex align-items-center justify-content-between py-1">
                <h6 class="font-weight-medium mb-0">Templates List</h6>
                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center font-weight-medium" data-bs-toggle="offcanvas" data-bs-target="#createTemplateCanvas">
                    <i class="mdi mdi-plus me-1"></i> Add New Template
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive rounded-10 border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th>Template Name</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                        <tr>
                            <td>
                                <strong>{{ $template->name }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-soft-info text-info">{{ ucfirst($template->type) }}</span>
                            </td>
                            <td>{{ Str::limit($template->subject, 30) }}</td>
                            <td>
                                @if($template->is_active)
                                    <span class="badge bg-soft-success text-success">Active</span>
                                @else
                                    <span class="badge bg-soft-secondary text-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="button" class="btn btn-sm btn-soft-info" data-bs-toggle="offcanvas" data-bs-target="#editTemplateCanvas{{ $template->id }}">
                                        <i class="mdi mdi-pencil-outline"></i>
                                    </button>
                                    <form action="{{ route('hrm.settings.letter-templates.destroy', $template->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" onclick="return confirm('Are you sure?')">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="mdi mdi-text-box-outline font-size-24 d-block mb-2"></i>
                                No templates found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($templates->count())
        <div class="card-footer bg-transparent border-top">
            <div class="d-flex justify-content-end">
                {{ $templates->links() }}
            </div>
        </div>
        @endif
    </div>
    {{-- Create Offcanvas --}}
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="createTemplateCanvas" aria-labelledby="createTemplateCanvasLabel">
        <div class="offcanvas-header">
            <h5 id="createTemplateCanvasLabel">Create Letter Template</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
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
                         <div class="d-flex flex-wrap gap-1">
                             <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar(this, '{employee_name}')">{employee_name}</button>
                             <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar(this, '{employee_code}')">{employee_code}</button>
                             <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar(this, '{designation}')">{designation}</button>
                             <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar(this, '{department}')">{department}</button>
                             <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar(this, '{joining_date}')">{joining_date}</button>
                             <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar(this, '{salary}')">{salary}</button>
                             <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar(this, '{company_name}')">{company_name}</button>
                             <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar(this, '{date}')">{date}</button>
                         </div>
                    </div>
                    <textarea class="form-control template-content" name="content" rows="15" required placeholder="Dear {employee_name}, ..."></textarea>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Create Template</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Offcanvases Loop --}}
    @foreach($templates as $template)
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="editTemplateCanvas{{ $template->id }}" aria-labelledby="editTemplateCanvasLabel{{ $template->id }}">
        <div class="offcanvas-header">
            <h5 id="editTemplateCanvasLabel{{ $template->id }}">Edit Letter Template</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.settings.letter-templates.update', $template->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Template Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ $template->name }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" required>
                            <option value="appointment" {{ $template->type == 'appointment' ? 'selected' : '' }}>Appointment</option>
                            <option value="termination" {{ $template->type == 'termination' ? 'selected' : '' }}>Termination</option>
                            <option value="increment" {{ $template->type == 'increment' ? 'selected' : '' }}>Increment</option>
                            <option value="transfer" {{ $template->type == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="confirmation" {{ $template->type == 'confirmation' ? 'selected' : '' }}>Confirmation</option>
                            <option value="other" {{ $template->type == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Subject <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="subject" value="{{ $template->subject }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Content <span class="text-danger">*</span></label>
                    <div class="mb-2">
                         <small class="text-muted d-block mb-1">Click to insert variable:</small>
                         <div class="d-flex flex-wrap gap-1">
                             <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar(this, '{employee_name}')">{employee_name}</button>
                             <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar(this, '{employee_code}')">{employee_code}</button>
                             <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar(this, '{designation}')">{designation}</button>
                             <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar(this, '{department}')">{department}</button>
                             <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar(this, '{joining_date}')">{joining_date}</button>
                             <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar(this, '{salary}')">{salary}</button>
                             <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar(this, '{company_name}')">{company_name}</button>
                             <button type="button" class="btn btn-xs btn-outline-secondary" onclick="insertVar(this, '{date}')">{date}</button>
                         </div>
                    </div>
                    <textarea class="form-control template-content" name="content" rows="15" required>{{ $template->content }}</textarea>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Update Template</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <script>
        function insertVar(btn, text) {
            // Find the textarea within the same form
            const form = btn.closest('form');
            const textarea = form.querySelector('.template-content');
            
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
