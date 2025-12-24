@extends('Main::layouts.app')

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12">
            <h2>Create Workflow</h2>
        </div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            @include('HRM::includes.breadcrumb', ['breadcrumbs' => [
                ['name' => 'HRM', 'url' => route('hrm.dashboard')],
                ['name' => 'Settings', 'url' => route('hrm.settings.index')],
                ['name' => 'Workflows', 'url' => route('hrm.workflows.index')],
                ['name' => 'Create', 'active' => true]
            ]])
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hrm.workflows.store') }}" method="POST">
                    @csrf
                    
                    <h5 class="card-title">Basic Information</h5>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Workflow Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. Leave Approval > 3 Days">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Module <span class="text-danger">*</span></label>
                            <select name="module_type" class="form-select" required>
                                <option value="">Select Module</option>
                                <option value="leave">Leave Request</option>
                                <option value="expense">Expense Claim</option>
                                <option value="appraisal">Appraisal</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Trigger Event</label>
                            <select name="trigger_event" class="form-select">
                                <option value="created">On Creation</option>
                                <option value="updated">On Update</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <h5 class="card-title d-flex justify-content-between align-items-center">
                        Workflow Steps
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-step">
                            <i class="bx bx-plus"></i> Add Step
                        </button>
                    </h5>
                    
                    <div id="steps-container">
                        <!-- Steps will be added here dynamically -->
                        <div class="alert alert-info" id="no-steps-msg">No steps defined. Click "Add Step" to begin.</div>
                    </div>

                    <div class="mt-4 text-right">
                        <a href="{{ route('hrm.workflows.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Workflow</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Step Template (Hidden) -->
<div id="step-template" style="display:none;">
    <div class="card mb-3 border step-item">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
            <h6 class="mb-0 step-title">Step #</h6>
            <button type="button" class="btn btn-sm btn-danger btn-remove-step"><i class="bx bx-trash"></i></button>
        </div>
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Step Name</label>
                    <input type="text" class="form-control step-name-input" placeholder="e.g. Manager Approval" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Approver Type</label>
                    <select class="form-select step-approver-input" required>
                        <option value="">Select Approver</option>
                        <option value="reporting_manager">Reporting Manager</option>
                        <option value="department_head">Department Head</option>
                        <option value="hr_manager">HR Manager (Role)</option>
                        <option value="specific_user">Specific User (TODO)</option>
                    </select>
                </div>
            </div>
            <!-- Future: Conditions and Auto-Actions -->
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('steps-container');
        const addButton = document.getElementById('btn-add-step');
        const template = document.getElementById('step-template').firstElementChild;
        const noStepsMsg = document.getElementById('no-steps-msg');
        let stepCount = 0;

        addButton.addEventListener('click', function() {
            stepCount++;
            const clone = template.cloneNode(true);
            
            // Update Title
            clone.querySelector('.step-title').textContent = `Step ${stepCount}`;
            
            // Update Input Names
            // steps[0][step_name], etc.
            const index = stepCount - 1;
            clone.querySelector('.step-name-input').name = `steps[${index}][step_name]`;
            clone.querySelector('.step-approver-input').name = `steps[${index}][approver_type]`;
            
            // Add Remove Logic
            clone.querySelector('.btn-remove-step').addEventListener('click', function() {
                clone.remove();
                // Ideally update indices here, but for simple append it's okay if backend handles it or we re-index on submit.
                // For a robust builder, we should re-index.
            });

            container.appendChild(clone);
            noStepsMsg.style.display = 'none';
        });
    });
</script>
@endpush
