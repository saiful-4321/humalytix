@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <h2>Create Approval Chain</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item">Settings</li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.settings.approval-chains.index') }}">Approval Chains</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hrm.settings.approval-chains.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label">Chain Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g. Standard Leave, Executive Leave">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>

                    <h5 class="mb-3">Approval Levels</h5>
                    <div id="levels-container">
                        {{-- Levels will be added here --}}
                    </div>

                    <button type="button" class="btn btn-soft-primary mb-4" onclick="addLevel()">
                        <i class="bx bx-plus"></i> Add Level
                    </button>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save Chain</button>
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
    let levelCount = 0;

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
        // Re-index logic could be added here if needed, but array index works if backend handles list
        // Update level visuals
        document.querySelectorAll('.level-item').forEach((el, index) => {
            el.querySelector('.level-number').textContent = index + 1;
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

    // Add first level by default
    document.addEventListener('DOMContentLoaded', () => {
        addLevel();
    });
</script>
@endsection
