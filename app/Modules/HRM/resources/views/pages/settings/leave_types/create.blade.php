@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <h2>Create Leave Policy</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item">Settings</li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.settings.leave-types.index') }}">Leave Policies</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hrm.settings.leave-types.store') }}" method="POST">
                    @csrf
                    
                    <h5 class="mb-3 text-primary">Basic Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Policy Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required placeholder="e.g. Sick Leave, Annual Leave">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" required placeholder="e.g. SL, AL">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>

                    <h5 class="mb-3 mt-4 text-primary">Entitlement & Rules</h5>
                    <div class="row align-items-center mb-3">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_unlimited" name="is_unlimited" onchange="toggleLimit(this)">
                                <label class="form-check-label" for="is_unlimited">Unlimited Leave</label>
                            </div>
                        </div>
                        <div class="col-md-4" id="days-container">
                            <label class="form-label">Days Per Year <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="days_per_year" value="0" min="0">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_paid" name="is_paid" checked>
                                <label class="form-check-label" for="is_paid">Is Paid Leave?</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="requires_attachment" name="requires_attachment">
                                <label class="form-check-label" for="requires_attachment">Unleash Attachment Requirement?</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Carry Forward Limit (Days)</label>
                            <input type="number" class="form-control" name="carry_forward_limit" value="0" min="0" placeholder="0 for none">
                            <small class="text-muted">Max days carried to next year</small>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4 text-primary">Workflow</h5>
                    <div class="mb-3">
                        <label class="form-label">Approval Chain</label>
                        <select class="form-select" name="approval_chain_id">
                            <option value="">Standard Approval (Direct Reporting Manager/Admin)</option>
                            @foreach($approvalChains as $chain)
                            <option value="{{ $chain->id }}">{{ $chain->name }} ({{ $chain->levels->count() }} Levels)</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Select a custom approval workflow. If empty, uses default.</small>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Create Policy</button>
                        <a href="{{ route('hrm.settings.leave-types.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleLimit(checkbox) {
        const input = document.querySelector('input[name="days_per_year"]');
        if (checkbox.checked) {
            input.disabled = true;
            input.value = 0;
        } else {
            input.disabled = false;
        }
    }
</script>
@endsection
