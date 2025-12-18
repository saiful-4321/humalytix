@extends('Main::layouts.app')

@section('title', 'Certifications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Employee Certifications</h4>
                    <div class="d-flex gap-2">
                        <form action="{{ route('hrm.certifications.index') }}" method="GET" class="d-flex input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="search" class="form-control" placeholder="Search by Cert No. or Name..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit"><i class="bx bx-search"></i></button>
                        </form>
                        
                        @can('hrm.certifications.create')
                        <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddCert">
                            <i class="bx bx-plus me-1"></i> Record
                        </button>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Employee</th>
                                    <th>Certification</th>
                                    <th>Issuer</th>
                                    <th>Issue Date</th>
                                    <th>Expiry</th>
                                    <th>Credential ID</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($certifications as $cert)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($cert->employee->profile_img)
                                                <img src="{{ $cert->employee->profile_img }}" class="rounded-circle avatar-xs me-2">
                                            @else
                                                <div class="avatar-xs me-2">
                                                    <span class="avatar-title rounded-circle bg-primary text-white">
                                                        {{ substr($cert->employee->first_name, 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0 font-size-14">{{ $cert->employee->full_name }}</h6>
                                                <small class="text-muted">{{ $cert->employee->designation->name ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $cert->name }}</td>
                                    <td>{{ $cert->issuing_organization }}</td>
                                    <td>{{ $cert->issue_date->format('d M y') }}</td>
                                    <td>
                                        @if($cert->does_never_expire)
                                            <span class="badge bg-soft-success text-success">Lifetime</span>
                                        @elseif($cert->expiry_date)
                                            @if($cert->expiry_date->isPast())
                                                <span class="badge bg-danger">Expired</span>
                                            @elseif($cert->expiry_date->diffInDays(now()) < 30)
                                                <span class="badge bg-warning">Expires Soon</span>
                                            @else
                                                {{ $cert->expiry_date->format('d M y') }}
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        {{ $cert->credential_id ?? '-' }}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            {{-- View / Download Actions --}}
                                            @if($cert->credential_url)
                                                <a href="{{ $cert->credential_url }}" target="_blank" class="btn btn-sm btn-soft-primary" data-bs-toggle="tooltip" title="View Link">
                                                    <i class="bx bx-link-external"></i>
                                                </a>
                                                <a href="{{ $cert->credential_url }}" download class="btn btn-sm btn-soft-success" data-bs-toggle="tooltip" title="Download File">
                                                    <i class="bx bx-download"></i>
                                                </a>
                                            @else
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('hrm.certifications.preview', $cert->id) }}" target="_blank" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="View Certificate">
                                                        <i class="bx bx-show"></i>
                                                    </a>
                                                    <a href="{{ route('hrm.certifications.download', $cert->id) }}" class="btn btn-outline-success" data-bs-toggle="tooltip" title="Generate PDF">
                                                        <i class="bx bx-download"></i>
                                                    </a>
                                                </div>
                                            @endif

                                            @can('hrm.certifications.delete')
                                            <form action="{{ route('hrm.certifications.destroy', $cert->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-soft-danger"><i class="bx bx-trash"></i></button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Certification Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddCert" aria-labelledby="offcanvasAddCertLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasAddCertLabel" class="offcanvas-title">Record Certification</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.certifications.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-select select2" required>
                    <option value="">Select Employee...</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Certification Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. AWS Solutions Architect" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Issuing Organization</label>
                <input type="text" name="issuing_organization" class="form-control" placeholder="e.g. Amazon Web Services" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Issue Date</label>
                    <input type="date" name="issue_date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="does_never_expire" id="noExpire">
                        <label class="form-check-label" for="noExpire">Never Expires</label>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Credential ID</label>
                <input type="text" name="credential_id" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Verification URL</label>
                <input type="url" name="credential_url" class="form-control" placeholder="https://...">
            </div>
            <button type="submit" class="btn btn-primary w-100">Save Record</button>
        </form>
    </div>
</div>
@endsection
