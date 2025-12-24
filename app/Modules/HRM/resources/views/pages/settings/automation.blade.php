@extends('Main::layouts.app')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Automation Settings</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('hrm.settings.index') }}">Settings</a></li>
                            <li class="breadcrumb-item active">Automation</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <!-- Manual Run Section -->
                <div class="card mb-3">
                    <div class="card-header bg-success bg-gradient">
                        <h5 class="card-title text-white mb-0">
                            <i class="bx bx-play-circle me-2"></i>Manual Run Commands
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Run automation tasks manually without waiting for scheduled execution.</p>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bx bx-calendar-plus text-success me-2"></i>Leave Accrual
                                        </h6>
                                        <p class="card-text text-muted small">Generate leave allocations for all employees based on policies.</p>
                                        <button type="button" class="btn btn-sm btn-success" onclick="runAutomation('accrual', '{{ route('hrm.settings.automation.run-accrual') }}')">
                                            <i class="bx bx-play me-1"></i>Run Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bx bx-file text-warning me-2"></i>Document Expiry Check
                                        </h6>
                                        <p class="card-text text-muted small">Check for expiring documents and send notifications.</p>
                                        <button type="button" class="btn btn-sm btn-warning" onclick="runAutomation('document-check', '{{ route('hrm.settings.automation.run-document-check') }}')">
                                            <i class="bx bx-play me-1"></i>Run Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bx bx-bell text-danger me-2"></i>Approval Escalation
                                        </h6>
                                        <p class="card-text text-muted small">Send reminders and escalate pending approvals.</p>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="runAutomation('escalation', '{{ route('hrm.settings.automation.run-escalation') }}')">
                                            <i class="bx bx-play me-1"></i>Run Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Form -->
                <div class="card">
                    <div class="card-header bg-primary bg-gradient">
                        <h5 class="card-title text-white mb-0">
                            <i class="bx bx-cog me-2"></i>HR Workflow Automation Configuration
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('hrm.settings.automation.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Workflow Auto-Trigger Settings -->
                            <div class="mb-4">
                                <h5 class="font-size-15 mb-3">
                                    <i class="bx bx-git-branch text-primary me-2"></i>Workflow Auto-Triggers
                                </h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="auto_trigger_workflows" 
                                                   name="auto_trigger_workflows" {{ $settings->auto_trigger_workflows ? 'checked' : '' }}>
                                            <label class="form-check-label" for="auto_trigger_workflows">
                                                Enable Workflow Auto-Triggers
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="auto_trigger_leave_workflows" 
                                                   name="auto_trigger_leave_workflows" {{ $settings->auto_trigger_leave_workflows ? 'checked' : '' }}>
                                            <label class="form-check-label" for="auto_trigger_leave_workflows">
                                                Auto-trigger Leave Workflows
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="auto_trigger_expense_workflows" 
                                                   name="auto_trigger_expense_workflows" {{ $settings->auto_trigger_expense_workflows ? 'checked' : '' }}>
                                            <label class="form-check-label" for="auto_trigger_expense_workflows">
                                                Auto-trigger Expense Workflows
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-info">
                                    <i class="bx bx-info-circle me-2"></i>
                                    When enabled, workflows will automatically start when leaves or expenses are created.
                                </div>
                            </div>

                            <hr>

                            <!-- Leave Accrual Settings -->
                            <div class="mb-4">
                                <h5 class="font-size-15 mb-3">
                                    <i class="bx bx-calendar-plus text-success me-2"></i>Leave Accrual Automation
                                </h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="auto_accrue_leaves" 
                                                   name="auto_accrue_leaves" {{ $settings->auto_accrue_leaves ? 'checked' : '' }}>
                                            <label class="form-check-label" for="auto_accrue_leaves">
                                                Enable Auto Leave Accrual
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="accrual_day_of_month" class="form-label">Accrual Day of Month</label>
                                            <input type="number" class="form-control" id="accrual_day_of_month" 
                                                   name="accrual_day_of_month" value="{{ $settings->accrual_day_of_month }}" 
                                                   min="1" max="28">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="accrual_time" class="form-label">Accrual Time</label>
                                            <input type="time" class="form-control" id="accrual_time" 
                                                   name="accrual_time" value="{{ $settings->accrual_time }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-info">
                                    <i class="bx bx-info-circle me-2"></i>
                                    Leaves will be automatically accrued on the specified day and time each month.
                                </div>
                            </div>

                            <hr>

                            <!-- Document Expiry Settings -->
                            <div class="mb-4">
                                <h5 class="font-size-15 mb-3">
                                    <i class="bx bx-file text-warning me-2"></i>Document Expiry Checks
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="check_expiring_documents" 
                                                   name="check_expiring_documents" {{ $settings->check_expiring_documents ? 'checked' : '' }}>
                                            <label class="form-check-label" for="check_expiring_documents">
                                                Enable Document Expiry Checks
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="expiry_check_time" class="form-label">Check Time (Daily)</label>
                                            <input type="time" class="form-control" id="expiry_check_time" 
                                                   name="expiry_check_time" value="{{ $settings->expiry_check_time }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-info">
                                    <i class="bx bx-info-circle me-2"></i>
                                    System will check for expiring documents daily and notify employees at 30, 15, and 7 days before expiry.
                                </div>
                            </div>

                            <hr>

                            <!-- Approval Escalation Settings -->
                            <div class="mb-4">
                                <h5 class="font-size-15 mb-3">
                                    <i class="bx bx-bell text-danger me-2"></i>Approval Escalation
                                </h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="escalate_approvals" 
                                                   name="escalate_approvals" {{ $settings->escalate_approvals ? 'checked' : '' }}>
                                            <label class="form-check-label" for="escalate_approvals">
                                                Enable Approval Escalation
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="reminder_after_days" class="form-label">Reminder After (Days)</label>
                                            <input type="number" class="form-control" id="reminder_after_days" 
                                                   name="reminder_after_days" value="{{ $settings->reminder_after_days }}" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="escalate_after_days" class="form-label">Escalate After (Days)</label>
                                            <input type="number" class="form-control" id="escalate_after_days" 
                                                   name="escalate_after_days" value="{{ $settings->escalate_after_days }}" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label for="escalation_check_time" class="form-label">Check Time</label>
                                            <input type="time" class="form-control" id="escalation_check_time" 
                                                   name="escalation_check_time" value="{{ $settings->escalation_check_time }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-info">
                                    <i class="bx bx-info-circle me-2"></i>
                                    Pending approvals will receive reminders and escalate to next level if not acted upon.
                                </div>
                            </div>

                            <hr>

                            <!-- Notification Settings -->
                            <div class="mb-4">
                                <h5 class="font-size-15 mb-3">
                                    <i class="bx bx-message text-info me-2"></i>Notification Settings
                                </h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="use_queue_for_notifications" 
                                                   name="use_queue_for_notifications" {{ $settings->use_queue_for_notifications ? 'checked' : '' }}>
                                            <label class="form-check-label" for="use_queue_for_notifications">
                                                Use Queue for Notifications
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="send_email_notifications" 
                                                   name="send_email_notifications" {{ $settings->send_email_notifications ? 'checked' : '' }}>
                                            <label class="form-check-label" for="send_email_notifications">
                                                Send Email Notifications
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="send_sms_notifications" 
                                                   name="send_sms_notifications" {{ $settings->send_sms_notifications ? 'checked' : '' }}>
                                            <label class="form-check-label" for="send_sms_notifications">
                                                Send SMS Notifications
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-info">
                                    <i class="bx bx-info-circle me-2"></i>
                                    Configure how notifications are sent. Queue processing recommended for large user bases.
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

<script>
// Manual Run Automation with SweetAlert
function runAutomation(type, url) {
    let title, text, icon;
    
    switch(type) {
        case 'accrual':
            title = 'Run Leave Accrual?';
            text = 'This will generate leave allocations for all employees based on leave types.';
            icon = 'question';
            break;
        case 'document-check':
            title = 'Run Document Expiry Check?';
            text = 'This will check for expiring documents and send notifications.';
            icon = 'question';
            break;
        case 'escalation':
            title = 'Run Approval Escalation?';
            text = 'This will send reminders and escalate pending approvals.';
            icon = 'question';
            break;
    }
    
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, run it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Running...',
                text: 'Please wait while the automation runs.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send AJAX request
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    title: 'Success!',
                    text: data.message || 'Automation completed successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            })
            .catch(error => {
                console.error('Automation error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'An error occurred while running the automation.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}
</script>
