@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Payroll Management</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Payroll</li>
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
                    <h6 class="font-weight-medium mb-0">Payroll List</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-soft-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterCanvas">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
                        </button>
                        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center font-weight-medium" data-bs-toggle="offcanvas" data-bs-target="#runPayrollOffcanvas">
                            <i class="mdi mdi-play-circle-outline me-1"></i> Run Payroll
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Period</th>
                                <th>Employee</th>
                                <th>Basic</th>
                                <th>Allowances</th>
                                <th>Deductions</th>
                                <th>Net Salary</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $payroll)
                            <tr>
                                <td>
                                    <span class="fw-medium">{{ date('F', mktime(0, 0, 0, $payroll->month, 1)) }} {{ $payroll->year }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                       @if($payroll->employee->photo)
                                           <img src="{{ asset($payroll->employee->photo) }}" class="rounded-circle avatar-xs me-2" alt="">
                                       @else
                                           <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12">
                                                    {{ substr($payroll->employee->first_name, 0, 1) }}
                                                </span>
                                           </div>
                                       @endif
                                       <div>
                                           <h6 class="mb-0 font-size-14">{{ $payroll->employee->full_name }}</h6>
                                           <small class="text-muted">{{ $payroll->employee->employee_code }}</small>
                                       </div>
                                   </div>
                                </td>
                                <td>${{ number_format($payroll->basic_salary, 2) }}</td>
                                <td class="text-success">+ ${{ number_format($payroll->allowances + $payroll->bonuses, 2) }}</td>
                                <td class="text-danger">- ${{ number_format($payroll->deductions + $payroll->tax, 2) }}</td>
                                <td><strong>${{ number_format($payroll->net_salary, 2) }}</strong></td>
                                <td>
                                    @if($payroll->status == 'paid')
                                        <span class="badge bg-soft-success text-success">Paid</span>
                                    @else
                                        <span class="badge bg-soft-warning text-warning">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('hrm.payroll.show', $payroll->id) }}" class="btn btn-sm btn-soft-primary" title="View Slip">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                        <a href="{{ route('hrm.payroll.download-pdf', $payroll->id) }}" class="btn btn-sm btn-soft-secondary" title="Download PDF">
                                            <i class="mdi mdi-download"></i>
                                        </a>
                                        @if($payroll->status != 'paid')
                                        <form action="{{ route('hrm.payroll.process', $payroll->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-soft-success" title="Mark Paid" onclick="return confirm('Mark as Paid?')">
                                                <i class="mdi mdi-check-circle-outline"></i>
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('hrm.payroll.destroy', $payroll->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger" title="Delete" onclick="return confirm('Delete this payroll entry?')">
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
                                    <i class="mdi mdi-cash-register font-size-24 d-block mb-2"></i>
                                    No payroll records found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
             @if($payrolls->count())
            <div class="card-footer bg-transparent border-top">
                <div class="d-flex justify-content-end">
                    {{ $payrolls->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Filter Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterCanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Filter Payrolls</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.payroll.index') }}" method="GET">
            <div class="mb-3">
               <label class="form-label">Month</label>
               <select name="month" class="form-select">
                   <option value="">All Months</option>
                   @for($i=1; $i<=12; $i++)
                   <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                   @endfor
               </select>
            </div>
            <div class="mb-3">
               <label class="form-label">Year</label>
               <select name="year" class="form-select">
                   <option value="">All Years</option>
                   @for($i=date('Y'); $i>=2020; $i--)
                   <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                   @endfor
               </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </div>
        </form>
    </div>
</div>

{{-- Run Payroll Offcanvas --}}
<div class="offcanvas offcanvas-end w-50" tabindex="-1" id="runPayrollOffcanvas" aria-labelledby="runPayrollLabel">
    <div class="offcanvas-header border-bottom">
        <h5 id="runPayrollLabel">Run Payroll</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.payroll.bulk-generate') }}" method="POST">
            @csrf
            
            <div class="alert alert-soft-primary mb-4">
                <div class="d-flex mb-2">
                    <i class="mdi mdi-clipboard-check-outline font-size-20 me-2"></i>
                    <h6 class="font-weight-bold mb-0 pt-1">Pre-Payroll Checklist</h6>
                </div>
                <p class="mb-3">Please ensure the following actions are completed for the selected month before generating payroll:</p>
                
                <ul class="list-group list-group-flush bg-transparent">
                    <li class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between align-items-center">
                        <div>
                            <i class="mdi mdi-clock-outline me-2 text-primary"></i>
                            <a href="{{ route('hrm.overtime.index') }}" target="_blank" class="text-decoration-none">Review Overtime</a>
                        </div>
                        <i class="mdi mdi-open-in-new text-muted"></i>
                    </li>
                    <li class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between align-items-center">
                        <div>
                            <i class="mdi mdi-star-outline me-2 text-primary"></i>
                            <a href="{{ route('hrm.bonuses.index') }}" target="_blank" class="text-decoration-none">Approve Bonuses</a>
                        </div>
                        <i class="mdi mdi-open-in-new text-muted"></i>
                    </li>
                    <li class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between align-items-center">
                        <div>
                            <i class="mdi mdi-calendar-check-outline me-2 text-primary"></i>
                            <a href="{{ route('hrm.leaves.index') }}" target="_blank" class="text-decoration-none">Process Leave Requests</a>
                        </div>
                        <i class="mdi mdi-open-in-new text-muted"></i>
                    </li>
                </ul>
            </div>

            <div class="card border mb-4">
                <div class="card-body bg-light">
                    <div class="mb-3">
                        <label class="form-label">Select Month <span class="text-danger">*</span></label>
                        <select name="month" class="form-select form-select-lg" required>
                            <option value="">Choose Month...</option>
                            @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Year <span class="text-danger">*</span></label>
                        <select name="year" class="form-select form-select-lg" required>
                            @for($i=date('Y'); $i>=2020; $i--)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="mdi mdi-cog-cogs me-1"></i> Generate Payroll
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
