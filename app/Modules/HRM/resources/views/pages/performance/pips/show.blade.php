@extends('Main::layouts.app')

@section('title', 'PIP Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">PIP Details: {{ $pip->employee->full_name }}</h4>
            <div class="text-muted">
                <span class="badge bg-{{ $pip->status == 'active' ? 'warning' : ($pip->status == 'completed' ? 'success' : 'danger') }}">
                    {{ ucfirst($pip->status) }}
                </span>
                <span class="mx-2">|</span>
                <i class="bx bx-calendar"></i> {{ $pip->start_date->format('M d') }} - {{ $pip->end_date->format('M d, Y') }}
            </div>
        </div>
        <a href="{{ route('hrm.pips.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back"></i> Back
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="fw-bold">Reason for PIP</h6>
                    <p class="text-muted mb-4">{{ $pip->reason }}</p>
                    
                    <h6 class="fw-bold">Expected Goals</h6>
                    <p class="text-muted">{{ $pip->goals }}</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">PIP Management</h6>
                    <form action="{{ route('hrm.pips.update-status', $pip) }}" method="POST" class="mb-3">
                        @csrf
                        <label class="form-label small text-muted">Update Status</label>
                        <div class="input-group">
                            <select name="status" class="form-select">
                                <option value="active" {{ $pip->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ $pip->status == 'completed' ? 'selected' : '' }}>Completed (Success)</option>
                                <option value="failed" {{ $pip->status == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="extended" {{ $pip->status == 'extended' ? 'selected' : '' }}>Extended</option>
                            </select>
                            <button class="btn btn-outline-primary" type="submit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h6 class="mb-0">Action Items</h6>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#addActionItemOffcanvas">
                        <i class="bx bx-plus"></i> Add Item
                    </button>
                </div>
                <div class="card-body">
                    @if($pip->actionItems->isEmpty())
                        <div class="text-center py-3 text-muted">No action items defined yet.</div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($pip->actionItems as $item)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="form-check">
                                        <input class="form-check-input update-action-status" type="checkbox" 
                                            data-id="{{ $item->id }}" 
                                            {{ $item->status == 'completed' ? 'checked' : '' }}>
                                        <label class="form-check-label {{ $item->status == 'completed' ? 'text-decoration-line-through text-muted' : '' }}">
                                            {{ $item->action }}
                                        </label>
                                        <div class="small text-muted mt-1">Due: {{ $item->due_date->format('M d') }}</div>
                                    </div>
                                    <span class="badge bg-label-secondary">{{ ucfirst($item->status) }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Action Item Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="addActionItemOffcanvas" style="width: 500px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Add Action Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.pips.add-action-item', $pip) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Action Description <span class="text-danger">*</span></label>
                <textarea name="action" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Due Date <span class="text-danger">*</span></label>
                <input type="date" name="due_date" class="form-control" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Add Action Item</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.update-action-status').change(function() {
        const id = $(this).data('id');
        const status = $(this).is(':checked') ? 'completed' : 'pending';
        
        $.ajax({
            url: `/hrm/pips/action-items/${id}`,
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function() {
                location.reload();
            }
        });
    });
});
</script>
@endpush
@endsection
