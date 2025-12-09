@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Salary Advances</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Advances</li>
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
                    <h6 class="font-weight-medium mb-0">Salary Advances</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#createAdvanceOffcanvas">
                            <i class="mdi mdi-plus me-1"></i> New Advance Request
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
                                <th>Advance #</th>
                                <th>Employee</th>
                                <th>Amount</th>
                                <th>Deduction Period</th>
                                <th>Monthly Deduction</th>
                                <th>Outstanding</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($advances as $advance)
                            <tr>
                                <td class="fw-medium">{{ $advance->advance_number }}</td>
                                <td>
                                    <div>
                                        <h6 class="mb-0 font-size-14">{{ $advance->employee->full_name }}</h6>
                                        <small class="text-muted">{{ $advance->employee->employee_code }}</small>
                                    </div>
                                </td>
                                <td>${{ number_format($advance->amount, 2) }}</td>
                                <td>{{ $advance->deduction_months }} months</td>
                                <td>${{ number_format($advance->monthly_deduction, 2) }}</td>
                                <td class="text-danger">${{ number_format($advance->outstanding_balance, 2) }}</td>
                                <td>
                                    @if($advance->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($advance->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($advance->status == 'completed')
                                        <span class="badge bg-info">Completed</span>
                                    @elseif($advance->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                            <i class="mdi mdi-dots-vertical font-size-18"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @if($advance->status == 'pending')
                                            <li>
                                                <form action="{{ route('hrm.advances.approve', $advance->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-success"><i class="bx bx-check-circle me-2"></i> Approve</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('hrm.advances.reject', $advance->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger"><i class="bx bx-x-circle me-2"></i> Reject</button>
                                                </form>
                                            </li>
                                            @endif
                                            @if($advance->status != 'active')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('hrm.advances.destroy', $advance->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Delete?')"><i class="bx bx-trash me-2"></i> Delete</button>
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-cash-remove font-size-24 d-block mb-2"></i>
                                    No advance requests found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($advances->count())
            <div class="card-footer bg-transparent border-top">
                {{ $advances->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Create Advance Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="createAdvanceOffcanvas" style="width: 500px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">New Advance Request</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.advances.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Employee <span class="text-danger">*</span></label>
                <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->full_name }} ({{ $employee->employee_code }})
                    </option>
                    @endforeach
                </select>
                @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Advance Amount <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                    name="amount" step="0.01" min="1" required value="{{ old('amount') }}">
                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Deduction Period (Months) <span class="text-danger">*</span></label>
                <select class="form-select @error('deduction_months') is-invalid @enderror" name="deduction_months" required>
                    @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ old('deduction_months') == $i ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'month' : 'months' }}</option>
                    @endfor
                </select>
                @error('deduction_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Disbursement Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('disbursement_date') is-invalid @enderror" 
                    name="disbursement_date" required value="{{ old('disbursement_date', date('Y-m-d')) }}">
                @error('disbursement_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Reason</label>
                <textarea class="form-control" name="reason" rows="3" placeholder="Explain why you need this advance...">{{ old('reason') }}</textarea>
            </div>

            <div class="alert alert-info">
                <i class="mdi mdi-information me-1"></i>
                <strong>Note:</strong> The advance amount will be automatically deducted from salary over the selected period.
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
        </form>
    </div>
</div>

{{-- Filter Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas">
    <div class="offcanvas-header border-bottom">
        <h5>Filter Advances</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.advances.index') }}" method="GET">
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
        </form>
    </div>
</div>
@endsection
