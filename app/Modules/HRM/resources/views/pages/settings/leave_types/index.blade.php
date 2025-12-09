@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <h2>Leave Policies</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item">Leaves</li>
                <li class="breadcrumb-item active">Policies</li>
            </ul>
        </div>
        <div class="col-lg-6 col-md-4 col-sm-12 text-right">
            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#createPolicyCanvas" aria-controls="createPolicyCanvas">
                <i class="bx bx-plus"></i> Create Policy
            </button>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Name / Code</th>
                                <th>Entitlement</th>
                                <th>Approvals</th>
                                <th>Carry Forward</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaveTypes as $type)
                            <tr>
                                <td>
                                    <strong>{{ $type->name }}</strong><br>
                                    <small class="text-muted">{{ $type->code }}</small>
                                </td>
                                <td>
                                    @if($type->is_unlimited)
                                        <span class="badge bg-soft-info text-info">Unlimited</span>
                                    @else
                                        {{ $type->days_per_year }} days/year
                                    @endif
                                    <div class="font-size-11 text-muted">
                                        {{ $type->is_paid ? 'Paid' : 'Unpaid' }}
                                    </div>
                                </td>
                                <td>
                                    @if($type->approvalChain)
                                        <a href="{{ route('hrm.settings.approval-chains.edit', $type->approvalChain->id) }}" class="text-primary">
                                            <i class="bx bx-link"></i> {{ $type->approvalChain->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Standard Approval</span>
                                    @endif
                                </td>
                                <td>
                                    @if($type->carry_forward_limit > 0)
                                        Max {{ $type->carry_forward_limit }} days
                                    @else
                                        <span class="text-muted">None</span>
                                    @endif
                                </td>
                                <td>
                                    <i class="bx bx-check-circle text-success" title="Active"></i>
                                </td>
                                <td>
                                    <a href="{{ route('hrm.settings.leave-types.edit', $type->id) }}" class="btn btn-sm btn-soft-primary"><i class="bx bx-edit"></i></a>
                                    <form action="{{ route('hrm.settings.leave-types.destroy', $type->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" onclick="return confirm('Are you sure?')"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No leave policies defined.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $leaveTypes->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Create Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="createPolicyCanvas" aria-labelledby="createPolicyCanvasLabel" style="width: 500px;">
    <div class="offcanvas-header">
        <h5 id="createPolicyCanvasLabel">Create Leave Policy</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.settings.leave-types.store') }}" method="POST">
            @csrf
            
            <h6 class="text-primary mb-3">Basic Info</h6>
            <div class="mb-3">
                <label class="form-label">Policy Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" required placeholder="e.g. Sick Leave">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="code" required placeholder="e.g. SL">
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="2"></textarea>
            </div>

            <h6 class="text-primary mb-3 mt-4">Rules & Entitlement</h6>
            <div class="row align-items-center mb-3">
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_unlimited" name="is_unlimited" onchange="toggleLimit(this)">
                        <label class="form-check-label" for="is_unlimited">Unlimited Leave</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Days Per Year</label>
                    <input type="number" class="form-control" name="days_per_year" value="0" min="0">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_paid" name="is_paid" checked>
                        <label class="form-check-label" for="is_paid">Pxid Leave</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="requires_attachment" name="requires_attachment">
                        <label class="form-check-label" for="requires_attachment">Attach Req?</label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Carry Forward Limit</label>
                <input type="number" class="form-control" name="carry_forward_limit" value="0" min="0">
            </div>

            <h6 class="text-primary mb-3 mt-4">Workflow</h6>
            <div class="mb-3">
                <label class="form-label">Approval Chain</label>
                <select class="form-select" name="approval_chain_id">
                    <option value="">Standard (Direct Manager)</option>
                    @foreach($approvalChains as $chain)
                    <option value="{{ $chain->id }}">{{ $chain->name }} ({{ $chain->levels_count ?? $chain->levels->count() }} Levels)</option>
                    @endforeach
                </select>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Create Policy</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleLimit(checkbox) {
        const input = document.querySelector('input[name="days_per_year"]');
        if (checkbox.checked) {
            input.readOnly = true;
            input.value = 0;
        } else {
            input.readOnly = false;
        }
    }
</script>
@endsection
