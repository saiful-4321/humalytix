@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Bank Transfer Files</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Bank Transfers</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix justify-content-center">
    <div class="col-lg-8">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <h6 class="font-weight-medium mb-0">Generate Bank Transfer File</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('hrm.bank-transfers.generate') }}" method="POST">
                    @csrf
                    
                    <div class="alert alert-info">
                        <i class="mdi mdi-information me-1"></i>
                        <strong>Note:</strong> This will generate a bank-ready file for all paid payrolls in the selected period.
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Month <span class="text-danger">*</span></label>
                            <select name="month" class="form-select" required>
                                @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Year <span class="text-danger">*</span></label>
                            <input type="number" name="year" class="form-control" value="{{ $year }}" min="2020" max="2099" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">File Format <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="form-check card p-3 mb-0">
                                    <input class="form-check-input" type="radio" name="format" value="npsb" id="formatNPSB" checked>
                                    <label class="form-check-label" for="formatNPSB">
                                        <strong>NPSB Format</strong>
                                        <small class="d-block text-muted">Bangladesh (CSV)</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check card p-3 mb-0">
                                    <input class="form-check-input" type="radio" name="format" value="beftn" id="formatBEFTN">
                                    <label class="form-check-label" for="formatBEFTN">
                                        <strong>BEFTN Format</strong>
                                        <small class="d-block text-muted">Bangladesh (TXT)</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check card p-3 mb-0">
                                    <input class="form-check-input" type="radio" name="format" value="csv" id="formatCSV">
                                    <label class="form-check-label" for="formatCSV">
                                        <strong>Generic CSV</strong>
                                        <small class="d-block text-muted">Universal</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($payrolls->count())
                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body">
                            <h6 class="mb-2">Preview</h6>
                            <p class="mb-1"><strong>Total Employees:</strong> {{ $payrolls->count() }}</p>
                            <p class="mb-0"><strong>Total Amount:</strong> ${{ number_format($payrolls->sum('net_salary'), 2) }}</p>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        No paid payrolls found for the selected period.
                    </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-primary" {{ $payrolls->count() == 0 ? 'disabled' : '' }}>
                            <i class="mdi mdi-download me-1"></i> Generate File
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
