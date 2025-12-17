@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <h2>Leave Allocations</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.leaves.index') }}">Leaves</a></li>
                <li class="breadcrumb-item active">Allocations</li>
            </ul>
        </div>
        <div class="col-lg-6 col-md-4 col-sm-12 text-right">
            <form action="{{ route('hrm.leaves.allocations.generate') }}" method="POST" class="d-inline confirm-action" data-message="This will generate allocations for all employees based on policies. Existing allocations will be skipped.">
                @csrf
                <input type="hidden" name="year" value="{{ $year }}">
                <button type="submit" class="btn btn-primary">
                    <i class="mdi mdi-cog-refresh me-1"></i> Auto-Generate for {{ $year }}
                </button>
            </form>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h6 class="card-title mb-0 pt-2">Allocation List</h6>
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#allocationFilter">
                        <i class="mdi mdi-filter-variant me-1"></i> Filter
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Leave Type</th>
                                <th>Allocated</th>
                                <th>Used</th>
                                <th>Balance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allocations as $allocation)
                            <tr>
                                <td>
                                    <strong>{{ $allocation->employee->full_name }}</strong><br>
                                    <small class="text-muted">{{ $allocation->employee->designation }}</small>
                                </td>
                                <td>{{ $allocation->leaveType->name }}</td>
                                <td>{{ $allocation->allocated_days }}</td>
                                <td>{{ $allocation->used_days }}</td>
                                <td>
                                    @if($allocation->balance < 0)
                                    <span class="text-danger">{{ $allocation->balance }}</span>
                                    @else
                                    <span class="text-success">{{ $allocation->balance }}</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-soft-primary" onclick="editAllocation({{ $allocation->id }}, '{{ $allocation->allocated_days }}', '{{ $allocation->employee->full_name }}', '{{ $allocation->leaveType->name }}')">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No allocations found for {{ $year }}.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $allocations->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editAllocationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" method="POST" id="editAllocationForm">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Allocation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Employee:</strong> <span id="modalEmployee"></span></p>
                    <p><strong>Type:</strong> <span id="modalType"></span></p>
                    <div class="mb-3">
                        <label class="form-label">Allocated Days</label>
                        <input type="number" step="0.5" class="form-control" name="allocated_days" id="modalAllocated" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function editAllocation(id, allocated, employee, type) {
        document.getElementById('editAllocationForm').action = "{{ route('hrm.leaves.allocations.index') }}/" + id;
        document.getElementById('modalAllocated').value = allocated;
        document.getElementById('modalEmployee').innerText = employee;
        document.getElementById('modalType').innerText = type;
        
        var myModal = new bootstrap.Modal(document.getElementById('editAllocationModal'));
        myModal.show();
    }

    // SweetAlert Confirmation
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.confirm-action').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const message = this.getAttribute('data-message') || 'Are you sure?';
                const confirmBtnText = this.getAttribute('data-confirm-text') || 'Yes, proceed!';
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirmBtnText
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    });
</script>

{{-- Filter Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="allocationFilter" aria-labelledby="allocationFilterLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="allocationFilterLabel">Filter Allocations</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="" method="GET">
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-select select2">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Year</label>
                <input type="number" name="year" class="form-control" value="{{ $year }}" placeholder="Year">
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('hrm.leaves.allocations.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>
</div>
@endsection
