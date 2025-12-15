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
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#calculatorCanvas">
                            <i class="mdi mdi-calculator me-1"></i> Calculate Gratuity
                        </button>
                         <button class="btn btn-soft-secondary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#settingsCanvas">
                            <i class="mdi mdi-cog-outline me-1"></i> Settings
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
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
                                        <span class="badge bg-soft-warning text-warning">Pending</span>
                                    @elseif($gratuity->status == 'approved')
                                        <span class="badge bg-soft-success text-success">Approved</span>
                                    @elseif($gratuity->status == 'paid')
                                        <span class="badge bg-soft-info text-info">Paid</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        @if($gratuity->status == 'pending')
                                        <form action="{{ route('hrm.gratuity.approve', $gratuity->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-soft-success" title="Approve">
                                                <i class="mdi mdi-check-circle-outline"></i>
                                            </button>
                                        </form>
                                        @endif
                                        
                                        @if($gratuity->status == 'approved')
                                        <button type="button" class="btn btn-sm btn-soft-primary" data-bs-toggle="offcanvas" data-bs-target="#payCanvas{{$gratuity->id}}" title="Mark Paid">
                                            <i class="mdi mdi-cash-multiple"></i>
                                        </button>
                                        @endif
                                    </div>
                                    
                                    {{-- Pay Offcanvas (moved outside loop in a better implementation, but here inside for logic simplicity per row) --}}
                                    @if($gratuity->status == 'approved')
                                    <div class="offcanvas offcanvas-end" tabindex="-1" id="payCanvas{{$gratuity->id}}">
                                        <div class="offcanvas-header border-bottom">
                                            <h5 class="offcanvas-title">Mark Gratuity as Paid</h5>
                                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                        </div>
                                        <div class="offcanvas-body">
                                            <form action="{{ route('hrm.gratuity.pay', $gratuity->id) }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label class="form-label">Employee</label>
                                                    <input type="text" class="form-control" value="{{ $gratuity->employee->full_name }}" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Amount</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="text" class="form-control" value="{{ number_format($gratuity->approved_amount, 2) }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                </div>
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary">Confirm Payment</button>
                                                </div>
                                            </form>
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
                <div class="d-flex justify-content-end">
                    {{ $gratuities->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Calculator Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="calculatorCanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Gratuity Calculator</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.gratuity.calculate') }}" method="POST">
            @csrf
            
            <div class="alert alert-soft-primary mb-3">
                <i class="mdi mdi-information-outline me-1"></i>
                Formula: {{ $config->formula_description }}
            </div>

            <div class="mb-3">
                <label class="form-label">Employee</label>
                <select class="form-select" name="employee_id" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->full_name }} (Joined: {{ $employee->joining_date?->format('d M, Y') }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Calculation Date</label>
                <input type="date" class="form-control" name="calculation_date" value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Calculate and Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Settings Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="settingsCanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Gratuity Configuration</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.gratuity.config.store') }}" method="POST">
            @csrf
            
            <div class="alert alert-soft-info mb-4">
                <small>Configure the global rules for gratuity calculation.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Min. Service Years <span class="text-danger">*</span></label>
                <input type="number" step="0.1" name="min_service_years" class="form-control" value="{{ $config->min_service_years }}" required>
                 <div class="form-text">Minimum years of service required to be eligible.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Multiplier <span class="text-danger">*</span></label>
                <input type="number" step="0.1" name="multiplier" class="form-control" value="{{ $config->multiplier }}" required>
                <div class="form-text">Number of basic salaries paid per year of service.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Max Limit (Optional)</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" name="max_gratuity_amount" class="form-control" value="{{ $config->max_gratuity_amount }}">
                </div>
                <div class="form-text">Leave empty for no upper limit.</div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Description Payload</label>
                <textarea name="formula_description" class="form-control" rows="2">{{ $config->formula_description }}</textarea>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Save Configuration</button>
            </div>
        </form>
    </div>
</div>
@endsection
