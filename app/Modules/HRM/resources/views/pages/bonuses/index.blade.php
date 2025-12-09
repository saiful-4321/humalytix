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
                        <a href="{{ route('hrm.bonuses.create') }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-plus me-1"></i> New Bonus
                        </a>
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
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                            <i class="mdi mdi-dots-vertical font-size-18"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @if($bonus->status == 'pending')
                                            <li>
                                                <form action="{{ route('hrm.bonuses.approve', $bonus->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-success"><i class="bx bx-check-circle me-2"></i> Approve</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('hrm.bonuses.reject', $bonus->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger"><i class="bx bx-x-circle me-2"></i> Reject</button>
                                                </form>
                                            </li>
                                            @endif
                                            @if($bonus->status != 'paid')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('hrm.bonuses.destroy', $bonus->id) }}" method="POST">
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
@endsection
