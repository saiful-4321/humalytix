@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12">
            <h2>Digital Letters</h2>
        </div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
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
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title">Issued Letters</h5>
                    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#issueLetterOffcanvas">
                        <i class="bx bx-plus"></i> Issue New Letter
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Type</th>
                                <th>Template</th>
                                <th>Issued Date</th>
                                <th>Signatory</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($letters as $letter)
                            <tr>
                                <td>
                                    <strong>{{ $letter->employee->full_name }}</strong><br>
                                    <small class="text-muted">{{ $letter->employee->employee_code }}</small>
                                </td>
                                <td>
                                    @php
                                        $colors = ['appointment' => 'success', 'termination' => 'danger', 'increment' => 'info', 'transfer' => 'warning'];
                                        $color = $colors[$letter->type] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ ucfirst($letter->type) }}</span>
                                </td>
                                <td>{{ $letter->template->name }}</td>
                                <td>{{ $letter->issued_date->format('d M, Y') }}</td>
                                <td>{{ $letter->signatory->full_name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('hrm.letters.show', $letter) }}" class="btn btn-sm btn-soft-primary"><i class="bx bx-show"></i></a>
                                    <a href="{{ route('hrm.letters.download', $letter) }}" class="btn btn-sm btn-soft-secondary"><i class="bx bx-download"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No letters issued.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $letters->links() }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Issue Letter Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="issueLetterOffcanvas" aria-labelledby="issueLetterLabel" style="width: 500px;">
    <div class="offcanvas-header">
        <h5 id="issueLetterLabel">Issue New Letter</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.letters.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Employee <span class="text-danger">*</span></label>
                <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                    @endforeach
                </select>
                @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Template <span class="text-danger">*</span></label>
                <select class="form-select @error('template_id') is-invalid @enderror" name="template_id" required>
                    <option value="">Select Template</option>
                    @foreach($templates as $tpl)
                    <option value="{{ $tpl->id }}">{{ $tpl->name }} ({{ ucfirst($tpl->type) }})</option>
                    @endforeach
                </select>
                @error('template_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Issued Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="issued_date" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Signatory <span class="text-danger">*</span></label>
                    <select class="form-select" name="signatory_id" required>
                        <option value="">Select Signatory</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ $emp->designation == 'CEO' || $emp->designation == 'HR Manager' ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Available Variables:</strong>
                <p class="mb-0 font-size-12">{employee_name}, {employee_code}, {designation}, {department}, {date}, {signatory_name}, {company_name}</p>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-success">Issue Letter</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
