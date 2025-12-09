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
                    <a href="{{ route('hrm.payroll.create') }}" class="btn btn-primary btn-sm d-flex align-items-center font-weight-medium">
                        <i class="mdi mdi-play-circle-outline me-1"></i> Run Payroll
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="row p-3">
                    <form action="{{ route('hrm.payroll.index') }}" method="GET" class="row g-3 align-items-center">
                        <div class="col-auto">
                           <select name="month" class="form-select">
                               <option value="">Month</option>
                               @for($i=1; $i<=12; $i++)
                               <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                               @endfor
                           </select>
                        </div>
                        <div class="col-auto">
                           <select name="year" class="form-select">
                               <option value="">Year</option>
                               @for($i=date('Y'); $i>=2020; $i--)
                               <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                               @endfor
                           </select>
                        </div>
                        <div class="col-auto">
                            <select name="status" class="form-select">
                                <option value="">Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-dark">Filter</button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive rounded-10 border">
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
                                <td>${{ number_format($payroll->basic_salary) }}</td>
                                <td class="text-success">+ ${{ number_format($payroll->allowances) }}</td>
                                <td class="text-danger">- ${{ number_format($payroll->deductions + $payroll->tax) }}</td>
                                <td><strong>${{ number_format($payroll->net_salary) }}</strong></td>
                                <td>
                                    @if($payroll->status == 'paid')
                                        <span class="badge bg-soft-success text-success">Paid</span>
                                    @else
                                        <span class="badge bg-soft-warning text-warning">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-muted font-size-16 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('hrm.payroll.show', $payroll->id) }}"><i class="bx bx-show me-2"></i> View Slip</a></li>
                                            <li><a class="dropdown-item" href="{{ route('hrm.payroll.download-pdf', $payroll->id) }}"><i class="bx bx-download me-2"></i> Download PDF</a></li>
                                             @if($payroll->status != 'paid')
                                            <li>
                                                <form action="{{ route('hrm.payroll.process', $payroll->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-success" onclick="return confirm('Mark as Paid?')"><i class="bx bx-check-circle me-2"></i> Mark Paid</button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('hrm.payroll.destroy', $payroll->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Delete this payroll entry?')"><i class="bx bx-trash me-2"></i> Delete</button>
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
@endsection
