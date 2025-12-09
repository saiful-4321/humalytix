@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Salary Register</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item">Reports</li>
                <li class="breadcrumb-item active">Salary Register</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Salary Register - {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</h6>
                    <div class="d-flex gap 2">
                        <button class="btn btn-secondary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#periodFilter">
                            <i class="mdi mdi-calendar me-1"></i> Change Period
                        </button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="mdi mdi-download me-1"></i> Download
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('hrm.reports.salary-register', ['month' => $month, 'year' => $year, 'download' => 'pdf']) }}">PDF</a></li>
                                <li><a class="dropdown-item" href="{{ route('hrm.reports.export-excel', ['month' => $month, 'year' => $year]) }}">Excel</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                {{-- Summary Cards --}}
                <div class="row g-3 p-3">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white mb-0">
                            <div class="card-body p-3">
                                <h6 class="text-white">Total Employees</h6>
                                <h3 class="mb-0">{{ $summary['total_employees'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white mb-0">
                            <div class="card-body p-3">
                                <h6 class="text-white">Total Gross</h6>
                                <h3 class="mb-0">${{ number_format($summary['total_gross'], 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white mb-0">
                            <div class="card-body p-3">
                                <h6 class="text-white">Total Deductions</h6>
                                <h3 class="mb-0">${{ number_format($summary['total_deductions'] + $summary['total_tax'], 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white mb-0">
                            <div class="card-body p-3">
                                <h6 class="text-white">Total Net</h6>
                                <h3 class="mb-0">${{ number_format($summary['total_net'], 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Employee Code</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th class="text-end">Basic</th>
                                <th class="text-end">Allowances</th>
                                <th class="text-end">Bonuses</th>
                                <th class="text-end">Gross</th>
                                <th class="text-end">Deductions</th>
                                <th class="text-end">Tax</th>
                                <th class="text-end">Net</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $payroll)
                            <tr>
                                <td><strong>{{ $payroll->employee->employee_code }}</strong></td>
                                <td>{{ $payroll->employee->full_name }}</td>
                                <td>{{ $payroll->employee->department->name ?? 'N/A' }}</td>
                                <td class="text-end">${{ number_format($payroll->basic_salary, 2) }}</td>
                                <td class="text-end">${{ number_format($payroll->allowances, 2) }}</td>
                                <td class="text-end">${{ number_format($payroll->bonuses, 2) }}</td>
                                <td class="text-end fw-bold">${{ number_format($payroll->gross_salary, 2) }}</td>
                                <td class="text-end text-danger">${{ number_format($payroll->deductions, 2) }}</td>
                                <td class="text-end text-warning">${{ number_format($payroll->tax, 2) }}</td>
                                <td class="text-end fw-bold text-success">${{ number_format($payroll->net_salary, 2) }}</td>
                                <td>
                                    @if($payroll->status == 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">No payroll data for selected period</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($payrolls->count())
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="3" class="text-end">TOTAL:</td>
                                <td class="text-end">${{ number_format($summary['total_basic'], 2) }}</td>
                                <td class="text-end">${{ number_format($summary['total_allowances'], 2) }}</td>
                                <td class="text-end">${{ number_format($summary['total_bonuses'], 2) }}</td>
                                <td class="text-end">${{ number_format($summary['total_gross'], 2) }}</td>
                                <td class="text-end text-danger">${{ number_format($summary['total_deductions'], 2) }}</td>
                                <td class="text-end text-warning">${{ number_format($summary['total_tax'], 2) }}</td>
                                <td class="text-end text-success">${{ number_format($summary['total_net'], 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Period Filter Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="periodFilter">
    <div class="offcanvas-header border-bottom">
        <h5>Select Period</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.reports.salary-register') }}" method="GET">
            <div class="mb-3">
                <label class="form-label">Month</label>
                <select name="month" class="form-select">
                    @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Year</label>
                <input type="number" name="year" class="form-control" value="{{ $year }}" min="2020" max="2099">
            </div>
            <button type="submit" class="btn btn-primary w-100">Apply</button>
        </form>
    </div>
</div>
@endsection
