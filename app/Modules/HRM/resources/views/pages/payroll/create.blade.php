@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Run Payroll</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.payroll.index') }}">Payroll</a></li>
                <li class="breadcrumb-item active">Wizard</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card bg-white mt-5">
            <div class="card-header border-bottom text-center">
                <div class="mb-3">
                     <i class="mdi mdi-cash-multiple font-size-24 text-primary bg-soft-primary p-2 rounded-circle"></i>
                </div>
                <h5 class="mb-0">Payroll Generator Wizard</h5>
                <p class="text-muted small">Generate salaries for all active eligible employees</p>
            </div>
            <div class="card-body">
                <form action="{{ route('hrm.payroll.bulk-generate') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Month <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" name="month" required>
                                @for($i=1; $i<=12; $i++)
                                    <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Year <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" name="year" required>
                                @for($i=date('Y'); $i>=2020; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <div class="d-flex">
                            <i class="mdi mdi-information-outline font-size-20 me-2"></i>
                            <div>
                                <h6 class="alert-heading font-size-14">How it works:</h6>
                                <p class="mb-0 font-size-13">
                                    This process will scan all active employees with a valid Salary Structure assigned.
                                    It will calculate their Basic, Allowances, and Deductions based on the structure rules 
                                    and generate a "Pending" payroll entry for review.
                                    <br><br>
                                    Existing payrolls for the selected period will be skipped.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="mdi mdi-rocket-launch-outline me-1"></i> Start Generation Process
                        </button>
                        <a href="{{ route('hrm.payroll.index') }}" class="btn btn-light mt-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
