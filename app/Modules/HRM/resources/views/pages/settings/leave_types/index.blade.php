@extends("HRM::layouts.settings")

@section("title", "Leave Policies")
@section("breadcrumb")
    <li class="breadcrumb-item active">Leave Settings</li>
    <li class="breadcrumb-item active">Policies</li>
@endsection

@section("settings-content")
    <div class="card bg-white">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between py-1">
                <h6 class="font-weight-medium mb-0">Leave Policies</h6>
                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#createPolicyCanvas" aria-controls="createPolicyCanvas">
                    <i class="bx bx-plus me-1"></i> Create Policy
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
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
                                <i class="bx bx-check-circle text-success font-size-18" title="Active"></i>
                            </td>
                            <td>
                                <a href="{{ route('hrm.settings.leave-types.edit', $type->id) }}" class="btn btn-sm btn-link text-primary"><i class="bx bx-edit font-size-18"></i></a>
                                <form action="{{ route('hrm.settings.leave-types.destroy', $type->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger" onclick="return confirm('Are you sure?')"><i class="bx bx-trash font-size-18"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No leave policies defined.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($leaveTypes->count())
        <div class="card-footer bg-transparent border-top">
            <div class="d-flex justify-content-end">
                {{ $leaveTypes->links() }}
            </div>
        </div>
        @endif
    </div>

    {{-- Create Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="createPolicyCanvas" aria-labelledby="createPolicyCanvasLabel" style="width: 500px;">
        <div class="offcanvas-header">
            <h5 id="createPolicyCanvasLabel">Create Leave Policy</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.settings.leave-types.store') }}" method="POST" id="createPolicyForm">
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
