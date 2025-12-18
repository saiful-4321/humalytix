@extends('Main::layouts.app')

@section('title', 'Contract Management')

@section('content')
@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Contract Management</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item">Compliance</li>
                <li class="breadcrumb-item active">Contracts</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                 <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Employee Contracts List</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-soft-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
                        </button>
                        @can('hrm.contracts.create')
                        <button class="btn btn-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#createContractOffcanvas">
                            <i class="mdi mdi-plus me-1"></i> Add Contract
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Employee</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contracts as $contract)
                            <tr>
                                <td>{{ $contract->employee->full_name }}</td>
                                <td>{{ $contract->title }}</td>
                                <td><span class="badge bg-soft-info text-info">{{ $contract->type }}</span></td>
                                <td>
                                    {{ $contract->start_date->format('d M Y') }} 
                                    @if($contract->end_date)
                                     - {{ $contract->end_date->format('d M Y') }}
                                    @else
                                     <span class="text-muted">(Indefinite)</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($contract->status)
                                        @case('signed')
                                            <span class="badge bg-success">Signed</span>
                                            @break
                                        @case('sent')
                                            <span class="badge bg-warning">Pending Signature</span>
                                            @break
                                        @case('expired')
                                            <span class="badge bg-danger">Expired</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $contract->status }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ Storage::url($contract->file_path) }}" target="_blank" class="btn btn-sm btn-soft-primary" title="View"><i class="mdi mdi-eye-outline"></i></a>
                                        @if($contract->status == 'signed')
                                           <a href="{{ Storage::url($contract->file_path) }}" download class="btn btn-sm btn-soft-success" title="Download"><i class="mdi mdi-download"></i></a>
                                        @endif
                                        @can('hrm.contracts.delete')
                                        <form action="{{ route('hrm.contracts.destroy', $contract->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete contract?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger"><i class="mdi mdi-delete-outline"></i></button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

<div class="row">
    <div class="col-12">
        {{ $contracts->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Filter Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="filterOffcanvasLabel">Filter Contracts</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.contracts.index') }}" method="GET">
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="signed" {{ request('status') == 'signed' ? 'selected' : '' }}>Signed</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="Employment" {{ request('type') == 'Employment' ? 'selected' : '' }}>Employment</option>
                    <option value="NDA" {{ request('type') == 'NDA' ? 'selected' : '' }}>NDA</option>
                    <option value="Amendment" {{ request('type') == 'Amendment' ? 'selected' : '' }}>Amendment</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-select">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('hrm.contracts.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>
            </div>
        </div>
    </div>
</div>

<!-- Create Contract Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="createContractOffcanvas" style="width: 500px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Create New Contract</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.contracts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-select" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Contract Title</label>
                <input type="text" class="form-control" name="title" placeholder="e.g. Employment Agreement 2024" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="Employment">Employment Contract</option>
                    <option value="NDA">Non-Disclosure Agreement (NDA)</option>
                    <option value="Amendment">Amendment</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">End Date (Optional)</label>
                    <input type="date" class="form-control" name="end_date">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Contract PDF</label>
                <input type="file" class="form-control" name="file" accept="application/pdf" required>
                <small class="text-muted">Upload the unsigned contract PDF.</small>
            </div>
            <div class="d-flex gap-2 justify-content-end mt-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary">Create & Send</button>
            </div>
        </form>
    </div>
</div>
@endsection
