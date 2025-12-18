
@extends('Main::layouts.app')

@section('title', 'Document Expiry Tracker')

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Document Expiry Tracker</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item">Compliance</li>
                <li class="breadcrumb-item active">Expiry Tracker</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                 <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Expiring Documents (Next {{ request('days', 90) }} Days)</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-soft-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
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
                                <th>Document Type</th>
                                <th>Expiry Date</th>
                                <th>Days Remaining</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expiringDocs as $doc)
                            <tr>
                                <td>
                                    <h6 class="mb-0 font-size-14">{{ $doc->employee->full_name }}</h6>
                                    <small class="text-muted">{{ $doc->employee->employee_code }}</small>
                                </td>
                                <td><span class="badge bg-soft-secondary text-dark">{{ $doc->documentType->name ?? $doc->type }}</span></td>
                                <td>{{ $doc->expiry_date->format('d M, Y') }}</td>
                                <td>
                                    <span class="font-weight-bold {{ $doc->expiry_date->diffInDays(now()) < 30 ? 'text-danger' : 'text-warning' }}">
                                        {{ $doc->expiry_date->diffInDays(now()) }} Days
                                    </span>
                                </td>
                                <td>
                                    @if($doc->expiry_date->isPast())
                                        <span class="badge bg-soft-danger text-danger">Expired</span>
                                    @else
                                        <span class="badge bg-soft-warning text-warning">Expiring Soon</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-soft-primary send-reminder-btn" 
                                            data-bs-toggle="offcanvas" 
                                            data-bs-target="#sendReminderOffcanvas" 
                                            data-employee-name="{{ $doc->employee->full_name }}"
                                            data-employee-id="{{ $doc->employee->id }}"
                                            title="Send Reminder">
                                        <i class="mdi mdi-bell-outline"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-check-circle-outline font-size-24 d-block mb-2"></i>
                                    No documents expiring in the selected range.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Filter Options</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form method="GET" action="{{ route('hrm.documents.expiry') }}">
            <div class="mb-3">
                <label class="form-label">Expiry Range</label>
                <select name="days" class="form-select">
                    <option value="30" {{ request('days') == '30' ? 'selected' : '' }}>Next 30 Days</option>
                    <option value="60" {{ request('days') == '60' ? 'selected' : '' }}>Next 60 Days</option>
                    <option value="90" {{ request('days') == '90' || !request('days') ? 'selected' : '' }}>Next 90 Days</option>
                    <option value="180" {{ request('days') == '180' ? 'selected' : '' }}>Next 6 Months</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Document Type</label>
                <select name="document_type_id" class="form-select">
                    <option value="">All Document Types</option>
                    @foreach($documentTypes as $type)
                        <option value="{{ $type->id }}" {{ request('document_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-select">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->first_name }} {{ $emp->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('hrm.documents.expiry') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Send Reminder Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="sendReminderOffcanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Send Expiry Reminder</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <div class="alert alert-info">
            <i class="mdi mdi-information me-2"></i> This will send an email notification to the employee.
        </div>
        <form id="reminderForm">
            <input type="hidden" id="reminder_employee_id" name="employee_id">
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <input type="text" class="form-control" id="reminder_employee_name" readonly disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Note (Optional)</label>
                <textarea class="form-control" rows="3" placeholder="Add a custom message..."></textarea>
            </div>
            <div class="d-grid">
                <button type="button" class="btn btn-primary" onclick="alert('Reminder sent!'); window.bootstrap.Offcanvas.getInstance(document.getElementById('sendReminderOffcanvas')).hide();">Send Reminder</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reminderOffcanvas = document.getElementById('sendReminderOffcanvas');
        if (reminderOffcanvas) {
            reminderOffcanvas.addEventListener('show.bs.offcanvas', function(event) {
                const button = event.relatedTarget;
                const employeeName = button.getAttribute('data-employee-name');
                const employeeId = button.getAttribute('data-employee-id');
                
                document.getElementById('reminder_employee_name').value = employeeName;
                document.getElementById('reminder_employee_id').value = employeeId;
            });
        }
    });
</script>
@endsection
