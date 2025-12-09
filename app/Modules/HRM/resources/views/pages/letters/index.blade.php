@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Digital Letters</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Letters</li>
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
                    <h6 class="font-weight-medium mb-0">Issued Letters</h6>
                    
                    <div class="d-flex align-items-center gap-2">
                        <a href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#issueLetterOffcanvas" class="btn btn-info btn-sm d-flex align-items-center font-weight-medium">
                            <i class="mdi mdi-plus me-1"></i> Issue New Letter
                        </a>
                        
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#letterFilter" aria-controls="letterFilter">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Active Filters Section -->
            @if(request()->hasAny(['type', 'employee_id']))
                <x-Main::active-filters :url="route('hrm.letters.index')">
                    <x-Main::active-filter-item key="employee_id" label="Employee" :value="$employees->where('id', request('employee_id'))->first()->full_name ?? request('employee_id')" />
                    <x-Main::active-filter-item key="type" label="Type" :value="ucfirst(request('type'))" />
                </x-Main::active-filters>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Employee</th>
                                <th>Type</th>
                                <th>Template</th>
                                <th>Issued Date</th>
                                <th>Signatory</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($letters as $letter)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                         <div class="avatar-xs me-2">
                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12">
                                                {{ substr($letter->employee->first_name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 font-size-14">{{ $letter->employee->full_name }}</h6>
                                            <small class="text-muted">{{ $letter->employee->employee_code }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $colors = ['appointment' => 'success', 'termination' => 'danger', 'increment' => 'info', 'transfer' => 'warning'];
                                        $color = $colors[$letter->type] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-soft-{{ $color }} text-{{ $color }}">{{ ucfirst($letter->type) }}</span>
                                </td>
                                <td>{{ $letter->template->name }}</td>
                                <td>{{ $letter->issued_date->format('d M, Y') }}</td>
                                <td>{{ $letter->signatory->full_name ?? 'N/A' }}</td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-muted font-size-16 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('hrm.letters.show', $letter) }}"><i class="bx bx-show me-2"></i> View</a></li>
                                            <li><a class="dropdown-item" href="{{ route('hrm.letters.download', $letter) }}"><i class="bx bx-download me-2"></i> Download</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="mdi mdi-file-document-outline font-size-24 d-block mb-2"></i>
                                    No letters issued.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($letters->count())
            <div class="card-footer bg-transparent border-top">
                <div class="d-flex justify-content-end">
                    {{ $letters->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Issue Letter Offcanvas --}}
<div class="offcanvas offcanvas-end w-50" tabindex="-1" id="issueLetterOffcanvas" aria-labelledby="issueLetterLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="issueLetterLabel">Issue New Letter</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.letters.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Employee <span class="text-danger">*</span></label>
                <select class="form-select select2" name="employee_id" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Template <span class="text-danger">*</span></label>
                <select class="form-select select2" name="template_id" required>
                    <option value="">Select Template</option>
                    @foreach($templates as $tpl)
                    <option value="{{ $tpl->id }}">{{ $tpl->name }} ({{ ucfirst($tpl->type) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Issued Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="issued_date" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Signatory <span class="text-danger">*</span></label>
                    <select class="form-select select2" name="signatory_id" required>
                        <option value="">Select Signatory</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ $emp->designation == 'CEO' || $emp->designation == 'HR Manager' ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="alert alert-info bg-soft-info border-info">
                <strong>Available Variables:</strong>
                <p class="mb-0 font-size-12">{employee_name}, {employee_code}, {designation}, {department}, {date}, {signatory_name}, {company_name}</p>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Issue Letter</button>
            </div>
        </form>
    </div>
</div>

{{-- Filter Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="letterFilter">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Filter Letters</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.letters.index') }}" method="GET">
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <select class="form-select select2" name="employee_id">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>
             <div class="mb-3">
                <label class="form-label">Type</label>
                <select class="form-select" name="type">
                    <option value="">All Types</option>
                    <option value="appointment">Appointment</option>
                    <option value="termination">Termination</option>
                    <option value="increment">Increment</option>
                    <option value="transfer">Transfer</option>
                </select>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('hrm.letters.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>
@endsection
