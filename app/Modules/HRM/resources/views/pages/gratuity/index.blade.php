@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Gratuity Management</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Gratuity</li>
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
                    <h6 class="font-weight-medium mb-0">Gratuity Records</h6>
                    <a href="{{ route('hrm.gratuity.calculator') }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-calculator me-1"></i> Calculate Gratuity
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Employee</th>
                                <th>Service Years</th>
                                <th>Last Basic</th>
                                <th>Calculated Amount</th>
                                <th>Calculation Date</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gratuities as $gratuity)
                            <tr>
                                <td>
                                    <div>
                                        <h6 class="mb-0 font-size-14">{{ $gratuity->employee->full_name }}</h6>
                                        <small class="text-muted">{{ $gratuity->employee->employee_code }}</small>
                                    </div>
                                </td>
                                <td>{{ $gratuity->service_years }} years</td>
                                <td>${{ number_format($gratuity->last_basic_salary, 2) }}</td>
                                <td class="fw-bold text-success">${{ number_format($gratuity->approved_amount, 2) }}</td>
                                <td>{{ $gratuity->calculation_date->format('d M, Y') }}</td>
                                <td>
                                    @if($gratuity->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($gratuity->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($gratuity->status == 'paid')
                                        <span class="badge bg-info">Paid</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                            <i class="mdi mdi-dots-vertical font-size-18"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @if($gratuity->status == 'pending')
                                            <li>
                                                <form action="{{ route('hrm.gratuity.approve', $gratuity->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-success"><i class="bx bx-check-circle me-2"></i> Approve</button>
                                                </form>
                                            </li>
                                            @endif
                                            @if($gratuity->status == 'approved')
                                            <li>
                                                <button type="button" class="dropdown-item text-primary" data-bs-toggle="modal" data-bs-target="#payModal{{$gratuity->id}}">
                                                    <i class="bx bx-money me-2"></i> Mark as Paid
                                                </button>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>

                                    {{-- Pay Modal --}}
                                    @if($gratuity->status == 'approved')
                                    <div class="modal fade" id="payModal{{$gratuity->id}}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('hrm.gratuity.pay', $gratuity->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Mark as Paid</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Payment Date</label>
                                                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Mark as Paid</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-gift-outline font-size-24 d-block mb-2"></i>
                                    No gratuity records found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($gratuities->count())
            <div class="card-footer bg-transparent border-top">
                {{ $gratuities->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
