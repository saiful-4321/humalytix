@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Bonus Management</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Bonuses</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Employee Bonuses</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#createBonusOffcanvas">
                            <i class="mdi mdi-plus me-1"></i> New Bonus
                        </button>
                        <button class="btn btn-secondary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#bonusFilter">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Employee</th>
                                <th>Bonus Name</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bonuses as $bonus)
                            <tr>
                                <td>
                                    <div>
                                        <h6 class="mb-0 font-size-14">{{ $bonus->employee->full_name }}</h6>
                                        <small class="text-muted">{{ $bonus->employee->employee_code }}</small>
                                    </div>
                                </td>
                                <td>{{ $bonus->bonus_name }}</td>
                                <td><span class="badge bg-info">{{ $bonus->bonusType->name ?? 'N/A' }}</span></td>
                                <td class="fw-bold text-success">${{ number_format($bonus->amount, 2) }}</td>
                                <td>{{ $bonus->bonus_date->format('d M, Y') }}</td>
                                <td>
                                    @if($bonus->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($bonus->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($bonus->status == 'paid')
                                        <span class="badge bg-info">Paid</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        @if($bonus->status == 'pending')
                                        <form action="{{ route('hrm.bonuses.approve', $bonus->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-soft-success" title="Approve">
                                                <i class="mdi mdi-check-circle-outline"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('hrm.bonuses.reject', $bonus->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-soft-danger" title="Reject">
                                                <i class="mdi mdi-close-circle-outline"></i>
                                            </button>
                                        </form>
                                        @endif
                                        
                                        @if($bonus->status != 'paid')
                                        <form action="{{ route('hrm.bonuses.destroy', $bonus->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-soft-danger delete-btn" title="Delete">
                                                <i class="mdi mdi-delete-outline"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-gift-outline font-size-24 d-block mb-2"></i>
                                    No bonuses found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($bonuses->count())
            <div class="card-footer bg-transparent border-top">
                {{ $bonuses->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Filter Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="bonusFilter">
    <div class="offcanvas-header border-bottom">
        <h5>Filter Bonuses</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.bonuses.index') }}" method="GET">
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Year</label>
                <input type="number" name="year" class="form-control" value="{{ request('year', date('Y')) }}" min="2020" max="2099">
            </div>
            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
        </form>
    </div>
</div>
{{-- Create Bonus Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="createBonusOffcanvas" style="width: 500px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Add New Bonus</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.bonuses.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Employee <span class="text-danger">*</span></label>
                <select class="form-select" name="employee_id" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->employee_code }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Bonus Type</label>
                <select class="form-select" name="bonus_type_id">
                    <option value="">Select Type (Optional)</option>
                    @foreach($bonusTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Bonus Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="bonus_name" placeholder="e.g. Eid Bonus" required>
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label">Amount <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" name="amount" required>
                    </div>
                </div>
                <div class="col-6">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="bonus_date" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">For Year <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="bonus_year" value="{{ date('Y') }}" required>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Create Bonus</button>
            </div>
        </form>
    </div>
</div>
@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteBtns = document.querySelectorAll('.delete-btn');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
@endsection
