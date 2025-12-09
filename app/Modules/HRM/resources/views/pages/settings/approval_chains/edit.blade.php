@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <h2>Edit Approval Chain</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item">Settings</li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.settings.approval-chains.index') }}">Approval Chains</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hrm.settings.approval-chains.update', $approvalChain->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label">Chain Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ $approvalChain->name }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2">{{ $approvalChain->description }}</textarea>
                    </div>

                    <h5 class="mb-3">Approval Levels</h5>
                    <div id="levels-container">
                        @foreach($approvalChain->levels as $index => $level)
                        <div class="level-item card card-body bg-light border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Level <span class="level-number">{{ $index + 1 }}</span></h6>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLevel(this)"><i class="bx bx-trash"></i></button>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Approver Type</label>
                                    <select class="form-select" name="levels[{{ $index }}][type]" onchange="toggleValueInput(this)" required>
                                        <option value="reporting_manager" {{ $level->approver_type == 'reporting_manager' ? 'selected' : '' }}>Reporting Manager</option>
                                        <option value="designation" {{ $level->approver_type == 'designation' ? 'selected' : '' }}>Designation</option>
                                        <option value="specific_user" {{ $level->approver_type == 'specific_user' ? 'selected' : '' }}>Specific Employee</option>
                                    </select>
                                </div>
                                <div class="col-md-6 value-container value-designation" style="{{ $level->approver_type != 'designation' ? 'display:none;' : '' }}">
                                    <label class="form-label">Designation Name</label>
                                    <input type="text" class="form-control" name="levels[{{ $index }}][value]" value="{{ $level->approver_type == 'designation' ? $level->approver_value : '' }}" placeholder="e.g. HR Manager">
                                </div>
                                <div class="col-md-6 value-container value-specific_user" style="{{ $level->approver_type != 'specific_user' ? 'display:none;' : '' }}">
                                    <label class="form-label">Select Employee</label>
                                    <select class="form-select" name="levels[{{ $index }}][value]">
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $emp)
                                        <option value="{{ $emp->user_id }}" {{ ($level->approver_type == 'specific_user' && $level->approver_value == $emp->user_id) ? 'selected' : '' }}>{{ $emp->full_name }} ({{ $emp->designation }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-soft-primary mb-4" onclick="addLevel()">
                        <i class="bx bx-plus"></i> Add Level
                    </button>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update Chain</button>
                        <a href="{{ route('hrm.settings.approval-chains.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<template id="level-template">
    <div class="level-item card card-body bg-light border mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Level <span class="level-number"></span></h6>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLevel(this)"><i class="bx bx-trash"></i></button>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Approver Type</label>
                <select class="form-select" name="levels[INDEX][type]" onchange="toggleValueInput(this)" required>
                    <option value="reporting_manager">Reporting Manager</option>
                    <option value="designation">Designation</option>
                    <option value="specific_user">Specific Employee</option>
                </select>
            </div>
            <div class="col-md-6 value-container value-designation" style="display:none;">
                <label class="form-label">Designation Name</label>
                <input type="text" class="form-control" name="levels[INDEX][value]" placeholder="e.g. HR Manager">
            </div>
            <div class="col-md-6 value-container value-specific_user" style="display:none;">
                <label class="form-label">Select Employee</label>
                <select class="form-select" name="levels[INDEX][value]">
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->user_id }}">{{ $emp->full_name }} ({{ $emp->designation }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</template>

<script>
    let levelCount = {{ $approvalChain->levels->count() }};

    function addLevel() {
        levelCount++;
        const template = document.getElementById('level-template');
        const container = document.getElementById('levels-container');
        const clone = template.content.cloneNode(true);

        clone.querySelector('.level-number').textContent = levelCount;
        
        // Update names
        clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
            el.name = el.name.replace('INDEX', levelCount - 1);
        });

        container.appendChild(clone);
    }

    function removeLevel(btn) {
        btn.closest('.level-item').remove();
        // Update level numbers and internal index logic if feasible
        // Here we rely on Laravel accepting non-sequential or re-indexing in Controller (which I handled in Controller by just looping request->levels)
        // Ideally visual update:
        let i = 1;
        document.querySelectorAll('.level-item').forEach(el => {
            el.querySelector('.level-number').textContent = i++;
        });
        levelCount = document.querySelectorAll('.level-item').length;
    }

    function toggleValueInput(select) {
        const row = select.closest('.row');
        row.querySelectorAll('.value-container').forEach(el => el.style.display = 'none');
        
        if (select.value === 'designation') {
            row.querySelector('.value-designation').style.display = 'block';
        } else if (select.value === 'specific_user') {
            row.querySelector('.value-specific_user').style.display = 'block';
        }
    }
</script>
@endsection
