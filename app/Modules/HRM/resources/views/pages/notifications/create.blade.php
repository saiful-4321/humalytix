@extends('Main::layouts.app')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Send Custom Notification</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('hrm.notifications.index') }}">Notifications</a></li>
                            <li class="breadcrumb-item active">Send Custom</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card">
                    <div class="card-header bg-primary bg-gradient">
                        <h5 class="card-title text-white mb-0">
                            <i class="bx bx-send me-2"></i>Create and Send Notification
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('hrm.notifications.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <!-- Notification Type -->
                                <div class="col-md-6 mb-3">
                                    <label for="type" class="form-label">Notification Type <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="">Select Type</option>
                                        <option value="default">General</option>
                                        <option value="leave">Leave Related</option>
                                        <option value="expense">Expense Related</option>
                                        <option value="training">Training Related</option>
                                        <option value="document">Document Related</option>
                                        <option value="approval">Approval Required</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Title -->
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label">Notification Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           placeholder="Enter notification title" 
                                           value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Message -->
                                <div class="col-12 mb-3">
                                    <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                    <textarea name="message" id="message" rows="4" 
                                              class="form-control @error('message') is-invalid @enderror" 
                                              placeholder="Enter notification message" required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Action URL (Optional) -->
                                <div class="col-12 mb-3">
                                    <label for="action_url" class="form-label">Action URL (Optional)</label>
                                    <input type="url" name="action_url" id="action_url" 
                                           class="form-control @error('action_url') is-invalid @enderror" 
                                           placeholder="https://example.com/action" 
                                           value="{{ old('action_url') }}">
                                    <small class="text-muted">Leave blank if no action is required</small>
                                    @error('action_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3">
                                <i class="bx bx-group me-2"></i>Select Recipients
                            </h5>

                            <div class="row">
                                <!-- Recipient Type -->
                                <div class="col-12 mb-3">
                                    <label class="form-label">Send To <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="recipient_type" 
                                                   id="recipient_all" value="all" 
                                                   {{ old('recipient_type') === 'all' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="recipient_all">
                                                All Users
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="recipient_type" 
                                                   id="recipient_department" value="department"
                                                   {{ old('recipient_type') === 'department' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="recipient_department">
                                                By Department
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="recipient_type" 
                                                   id="recipient_individual" value="individual"
                                                   {{ old('recipient_type') === 'individual' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="recipient_individual">
                                                Individual Users
                                            </label>
                                        </div>
                                    </div>
                                    @error('recipient_type')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Department Selection -->
                                <div class="col-12 mb-3 department-select" style="display: none;">
                                    <label for="department_id" class="form-label">Select Department</label>
                                    <select name="department_id" id="department_id" class="form-select">
                                        <option value="">Choose Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Individual User Selection -->
                                <div class="col-12 mb-3 user-select" style="display: none;">
                                    <label for="user_ids" class="form-label">Select Users</label>
                                    <select name="user_ids[]" id="user_ids" class="form-select select2" multiple>
                                        @foreach($employees as $employee)
                                            @if($employee->user)
                                                <option value="{{ $employee->user->id }}" 
                                                    {{ in_array($employee->user->id, old('user_ids', [])) ? 'selected' : '' }}>
                                                    {{ $employee->full_name }} ({{ $employee->employee_code }})
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('user_ids')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <a href="{{ route('hrm.notifications.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-send me-1"></i> Send Notification
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-show me-2"></i>Preview
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="notification-preview">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-md">
                                        <span class="avatar-title rounded-circle font-size-20 notification-icon-0" id="preview-icon">
                                            <i class="bx bx-bell"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="font-size-15 mb-1" id="preview-title">Notification Title</h5>
                                    <p class="text-muted mb-2" id="preview-message">Your notification message will appear here...</p>
                                    <p class="text-muted font-size-13 mb-0">
                                        <i class="mdi mdi-clock-outline"></i>
                                        Just now
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide recipient fields based on selection
    const recipientRadios = document.querySelectorAll('input[name="recipient_type"]');
    const departmentSelect = document.querySelector('.department-select');
    const userSelect = document.querySelector('.user-select');

    recipientRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            departmentSelect.style.display = 'none';
            userSelect.style.display = 'none';

            if (this.value === 'department') {
                departmentSelect.style.display = 'block';
            } else if (this.value === 'individual') {
                userSelect.style.display = 'block';
            }
        });
    });

    // Trigger on page load if old value exists
    const checkedRadio = document.querySelector('input[name="recipient_type"]:checked');
    if (checkedRadio) {
        checkedRadio.dispatchEvent(new Event('change'));
    }

    // Live preview
    const typeSelect = document.getElementById('type');
    const titleInput = document.getElementById('title');
    const messageInput = document.getElementById('message');
    const previewIcon = document.getElementById('preview-icon');
    const previewTitle = document.getElementById('preview-title');
    const previewMessage = document.getElementById('preview-message');

    const iconMap = {
        'leave': 'bx-calendar',
        'expense': 'bx-wallet',
        'training': 'bx-book-reader',
        'document': 'bx-file',
        'approval': 'bx-check-circle',
        'default': 'bx-bell'
    };

    typeSelect.addEventListener('change', function() {
        const icon = iconMap[this.value] || 'bx-bell';
        previewIcon.innerHTML = `<i class="bx ${icon}"></i>`;
    });

    titleInput.addEventListener('input', function() {
        previewTitle.textContent = this.value || 'Notification Title';
    });

    messageInput.addEventListener('input', function() {
        previewMessage.textContent = this.value || 'Your notification message will appear here...';
    });
});
</script>

<style>
.notification-preview {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.5rem;
    border-left: 4px solid #556ee6;
}
</style>
@endsection
