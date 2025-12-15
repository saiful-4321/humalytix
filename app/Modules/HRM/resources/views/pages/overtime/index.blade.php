@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Overtime Management</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Overtime</li>
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
                    <h6 class="font-weight-medium mb-0">Overtime Records</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#createOvertimeOffcanvas">
                            <i class="mdi mdi-plus me-1"></i> Add Overtime
                        </button>
                        <button class="btn btn-secondary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
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
                                <th>Date</th>
                                <th>Employee</th>
                                <th>Hours</th>
                                <th>Type</th>
                                <th>Multiplier</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($overtimes as $overtime)
                            <tr>
                                <td>{{ $overtime->ot_date->format('d M, Y') }}</td>
                                <td>
                                    <div>
                                        <h6 class="mb-0 font-size-14">{{ $overtime->employee->full_name }}</h6>
                                        <small class="text-muted">{{ $overtime->employee->employee_code }}</small>
                                    </div>
                                </td>
                                <td>{{ $overtime->total_hours }} hrs</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $overtime->ot_type)) }}</span>
                                </td>
                                <td>{{ $overtime->multiplier }}x</td>
                                <td class="fw-bold text-success">${{ number_format($overtime->ot_amount, 2) }}</td>
                                <td>
                                    @if($overtime->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($overtime->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($overtime->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @elseif($overtime->status == 'paid')
                                        <span class="badge bg-info">Paid</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        @if($overtime->status == 'pending')
                                        <form action="{{ route('hrm.overtime.approve', $overtime->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-soft-success" title="Approve">
                                                <i class="mdi mdi-check-circle-outline"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('hrm.overtime.reject', $overtime->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-soft-danger" title="Reject">
                                                <i class="mdi mdi-close-circle-outline"></i>
                                            </button>
                                        </form>
                                        @endif
                                        
                                        @if($overtime->status != 'paid')
                                        <form action="{{ route('hrm.overtime.destroy', $overtime->id) }}" method="POST" class="d-inline delete-form">
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
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-clock-outline font-size-24 d-block mb-2"></i>
                                    No overtime records found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($overtimes->count())
            <div class="card-footer bg-transparent border-top">
                {{ $overtimes->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Create Overtime Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="createOvertimeOffcanvas" style="width: 500px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Record Overtime</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.overtime.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Employee <span class="text-danger">*</span></label>
                <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->employee_code }})</option>
                    @endforeach
                </select>
                @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">OT Policy <span class="text-danger">*</span></label>
                <select class="form-select @error('ot_policy_id') is-invalid @enderror" name="ot_policy_id" required>
                    <option value="">Select Policy</option>
                    @foreach($policies as $policy)
                    <option value="{{ $policy->id }}">{{ $policy->name }}</option>
                    @endforeach
                </select>
                @error('ot_policy_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">OT Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('ot_date') is-invalid @enderror" name="ot_date" required value="{{ old('ot_date', date('Y-m-d')) }}">
                @error('ot_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label">Start Time <span class="text-danger">*</span></label>
                    <input type="time" class="form-control @error('start_time') is-invalid @enderror" name="start_time" required>
                    @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-6">
                    <label class="form-label">End Time <span class="text-danger">*</span></label>
                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" name="end_time" required>
                    @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">OT Type <span class="text-danger">*</span></label>
                <select class="form-select @error('ot_type') is-invalid @enderror" name="ot_type" required>
                    <option value="regular">Regular OT</option>
                    <option value="weekend">Weekend OT</option>
                    <option value="holiday">Holiday OT</option>
                    <option value="night_shift">Night Shift</option>
                </select>
                @error('ot_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Overtime</button>
            </div>
        </form>
    </div>
</div>

{{-- Filter Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas">
    <div class="offcanvas-header border-bottom">
        <h5>Filter Overtime</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.overtime.index') }}" method="GET">
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
                <label class="form-label">Month</label>
                <select name="month" class="form-select">
                    <option value="">All Months</option>
                    @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                    @endfor
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
