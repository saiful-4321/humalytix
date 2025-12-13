@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12">
            <h2>Edit Employee - {{ $employee->full_name }}</h2>
        </div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.employees.index') }}">Employees</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix mb-4">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-body">
                <!-- Progress Steps -->
                <div class="mb-4">
                    <ul class="nav nav-pills nav-justified" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $currentStep == 'personal' ? 'active' : '' }}" href="{{ route('hrm.employees.edit', ['employee' => $employee->id, 'step' => 'personal']) }}">
                                <i class="bx bx-user d-block font-size-20"></i>
                                <span class="d-none d-sm-block">Personal Info</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $currentStep == 'employment' ? 'active' : '' }}" href="{{ route('hrm.employees.edit', ['employee' => $employee->id, 'step' => 'employment']) }}">
                                <i class="bx bx-briefcase d-block font-size-20"></i>
                                <span class="d-none d-sm-block">Employment</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $currentStep == 'salary' ? 'active' : '' }}" href="{{ route('hrm.employees.edit', ['employee' => $employee->id, 'step' => 'salary']) }}">
                                <i class="bx bx-money d-block font-size-20"></i>
                                <span class="d-none d-sm-block">Salary & Bank</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $currentStep == 'kyc' ? 'active' : '' }}" href="{{ route('hrm.employees.edit', ['employee' => $employee->id, 'step' => 'kyc']) }}">
                                <i class="bx bx-id-card d-block font-size-20"></i>
                                <span class="d-none d-sm-block">KYC Info</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $currentStep == 'documents' ? 'active' : '' }}" href="{{ route('hrm.employees.edit', ['employee' => $employee->id, 'step' => 'documents']) }}">
                                <i class="bx bx-file d-block font-size-20"></i>
                                <span class="d-none d-sm-block">Documents</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Personal Information -->
                    <div class="tab-pane fade {{ $currentStep == 'personal' ? 'show active' : '' }}" id="personal" role="tabpanel">
                        <form class="p-3" action="{{ route('hrm.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="form_step" value="personal">

                            <h5 class="mb-3">Personal Information</h5>
                            
                            @if($employee->photo)
                            <div class="mb-3">
                                <label class="form-label">Current Photo</label>
                                <div>
                                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}" class="rounded" width="100">
                                </div>
                            </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required>
                                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required>
                                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $employee->email) }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth?->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $employee->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="blood_group" class="form-label">Blood Group</label>
                                    <select class="form-select" id="blood_group" name="blood_group">
                                        <option value="">Select Blood Group</option>
                                        @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                        <option value="{{ $bg }}" {{ old('blood_group', $employee->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="marital_status" class="form-label">Marital Status</label>
                                    <select class="form-select" id="marital_status" name="marital_status">
                                        <option value="">Select Status</option>
                                        <option value="single" {{ old('marital_status', $employee->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ old('marital_status', $employee->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="divorced" {{ old('marital_status', $employee->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="widowed" {{ old('marital_status', $employee->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nationality" class="form-label">Nationality</label>
                                    <input type="text" class="form-control" id="nationality" name="nationality" value="{{ old('nationality', $employee->nationality) }}">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="present_address" class="form-label">Present Address</label>
                                    <textarea class="form-control" id="present_address" name="present_address" rows="2">{{ old('present_address', $employee->present_address) }}</textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="photo" class="form-label">Update Photo</label>
                                    <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                                    @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted">Leave empty to keep current photo</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="submit" name="action" value="save_exit" class="btn btn-secondary">
                                    <i class="bx bx-save"></i> Save & Exit
                                </button>
                                <button type="submit" name="action" value="save_continue" class="btn btn-primary">
                                    Save & Continue <i class="bx bx-chevron-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Employment Details -->
                    <div class="tab-pane fade {{ $currentStep == 'employment' ? 'show active' : '' }}" id="employment" role="tabpanel">
                        <form class="p-3" action="{{ route('hrm.employees.update', $employee) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="form_step" value="employment">

                            <h5 class="mb-3">Employment Details</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                                    <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id" required>
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id', $employee->branch_id) == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="designation" class="form-label">Designation <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('designation') is-invalid @enderror" id="designation" name="designation" value="{{ old('designation', $employee->designation) }}" required>
                                    @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="employment_type" class="form-label">Employment Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('employment_type') is-invalid @enderror" id="employment_type" name="employment_type" required>
                                        <option value="">Select Type</option>
                                        <option value="full_time" {{ old('employment_type', $employee->employment_type) == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                        <option value="part_time" {{ old('employment_type', $employee->employment_type) == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                        <option value="contract" {{ old('employment_type', $employee->employment_type) == 'contract' ? 'selected' : '' }}>Contract</option>
                                        <option value="internship" {{ old('employment_type', $employee->employment_type) == 'internship' ? 'selected' : '' }}>Internship</option>
                                    </select>
                                    @error('employment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="joining_date" class="form-label">Joining Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('joining_date') is-invalid @enderror" id="joining_date" name="joining_date" value="{{ old('joining_date', $employee->joining_date?->format('Y-m-d')) }}" required>
                                    @error('joining_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="confirmation_date" class="form-label">Confirmation Date</label>
                                    <input type="date" class="form-control" id="confirmation_date" name="confirmation_date" value="{{ old('confirmation_date', $employee->confirmation_date?->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="employeeStatus" name="status" required>
                                        <option value="probation" {{ old('status', $employee->status) == 'probation' ? 'selected' : '' }}>Probation</option>
                                        <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="confirmed" {{ old('status', $employee->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="notice_period" {{ old('status', $employee->status) == 'notice_period' ? 'selected' : '' }}>Notice Period</option>
                                        <option value="resigned" {{ old('status', $employee->status) == 'resigned' ? 'selected' : '' }}>Resigned</option>
                                    </select>
                                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="reporting_to" class="form-label">Reporting Manager</label>
                                    <select class="form-select" id="reporting_to" name="reporting_to">
                                        <option value="">Select Manager</option>
                                        @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('reporting_to', $employee->reporting_to) == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->full_name }} ({{ $manager->designation }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="business_unit_id" class="form-label">Business Unit</label>
                                    <select class="form-select" id="business_unit_id" name="business_unit_id">
                                        <option value="">Select Business Unit</option>
                                        @foreach($businessUnits as $bu)
                                        <option value="{{ $bu->id }}" {{ old('business_unit_id', $employee->business_unit_id) == $bu->id ? 'selected' : '' }}>
                                            {{ $bu->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="submit" name="action" value="save_exit" class="btn btn-secondary">
                                    <i class="bx bx-save"></i> Save & Exit
                                </button>
                                <button type="submit" name="action" value="save_continue" class="btn btn-primary">
                                    Save & Continue <i class="bx bx-chevron-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Salary & Bank Details -->
                    <div class="tab-pane fade {{ $currentStep == 'salary' ? 'show active' : '' }}" id="salary" role="tabpanel">
                        <form class="p-3" action="{{ route('hrm.employees.update', $employee) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="form_step" value="salary">

                            <h5 class="mb-3">Salary & Bank Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="basic_salary" class="form-label">Basic Salary</label>
                                    <input type="number" class="form-control" id="basic_salary" name="basic_salary" value="{{ old('basic_salary', $employee->basic_salary) }}" step="0.01">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method">
                                        <option value="bank" {{ old('payment_method', $employee->payment_method) == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="cash" {{ old('payment_method', $employee->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="cheque" {{ old('payment_method', $employee->payment_method) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                        <option value="mfs" {{ old('payment_method', $employee->payment_method) == 'mfs' ? 'selected' : '' }}>MFS (Mobile Banking)</option>
                                    </select>
                                    @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row" id="mfs_details" style="{{ old('payment_method', $employee->payment_method) == 'mfs' ? '' : 'display:none;' }}">
                                <div class="col-md-6 mb-3">
                                    <label for="mfs_provider" class="form-label">MFS Provider</label>
                                    <select class="form-select" id="mfs_provider" name="mfs_provider">
                                        <option value="">Select Provider</option>
                                        <option value="bkash" {{ old('mfs_provider', $employee->mfs_provider) == 'bkash' ? 'selected' : '' }}>bKash</option>
                                        <option value="nagad" {{ old('mfs_provider', $employee->mfs_provider) == 'nagad' ? 'selected' : '' }}>Nagad</option>
                                        <option value="rocket" {{ old('mfs_provider', $employee->mfs_provider) == 'rocket' ? 'selected' : '' }}>Rocket</option>
                                        <option value="upay" {{ old('mfs_provider', $employee->mfs_provider) == 'upay' ? 'selected' : '' }}>Upay</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mfs_account_number" class="form-label">MFS Account Number</label>
                                    <input type="text" class="form-control" id="mfs_account_number" name="mfs_account_number" value="{{ old('mfs_account_number', $employee->mfs_account_number) }}">
                                </div>
                            </div>

                            <script>
                                document.getElementById('payment_method').addEventListener('change', function() {
                                    const mfsDetails = document.getElementById('mfs_details');
                                    if (this.value === 'mfs') {
                                        mfsDetails.style.display = 'flex';
                                    } else {
                                        mfsDetails.style.display = 'none';
                                    }
                                });
                            </script>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="bank_name" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name', $employee->bank_name) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="bank_account_number" class="form-label">Account Number</label>
                                    <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $employee->bank_account_number) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="bank_branch" class="form-label">Bank Branch</label>
                                    <input type="text" class="form-control" id="bank_branch" name="bank_branch" value="{{ old('bank_branch', $employee->bank_branch) }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="submit" name="action" value="save_exit" class="btn btn-secondary">
                                    <i class="bx bx-save"></i> Save & Exit
                                </button>
                                <button type="submit" name="action" value="save_continue" class="btn btn-primary">
                                    Save & Continue <i class="bx bx-chevron-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- KYC Details (New Step) -->
                    <div class="tab-pane fade {{ $currentStep == 'kyc' ? 'show active' : '' }}" id="kyc" role="tabpanel">
                        <form class="p-3" action="{{ route('hrm.employees.update', $employee) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="form_step" value="kyc">

                            <h5 class="mb-3">KYC Information</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="father_name" class="form-label">Father's Name</label>
                                    <input type="text" class="form-control" id="father_name" name="father_name" value="{{ old('father_name', $employee->father_name) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mother_name" class="form-label">Mother's Name</label>
                                    <input type="text" class="form-control" id="mother_name" name="mother_name" value="{{ old('mother_name', $employee->mother_name) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="spouse_name" class="form-label">Spouse Name</label>
                                    <input type="text" class="form-control" id="spouse_name" name="spouse_name" value="{{ old('spouse_name', $employee->spouse_name) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="personal_email" class="form-label">Personal Email</label>
                                    <input type="email" class="form-control" id="personal_email" name="personal_email" value="{{ old('personal_email', $employee->personal_email) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nid_number" class="form-label">NID Number</label>
                                    <input type="text" class="form-control" id="nid_number" name="nid_number" value="{{ old('nid_number', $employee->nid_number) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="passport_number" class="form-label">Passport Number</label>
                                    <input type="text" class="form-control" id="passport_number" name="passport_number" value="{{ old('passport_number', $employee->passport_number) }}">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tax_id" class="form-label">Tax ID / TIN</label>
                                    <input type="text" class="form-control" id="tax_id" name="tax_id" value="{{ old('tax_id', $employee->tax_id) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="driving_license" class="form-label">Driving License</label>
                                    <input type="text" class="form-control" id="driving_license" name="driving_license" value="{{ old('driving_license', $employee->driving_license) }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="submit" name="action" value="save_exit" class="btn btn-secondary">
                                    <i class="bx bx-save"></i> Save & Exit
                                </button>
                                <button type="submit" name="action" value="save_continue" class="btn btn-primary">
                                    Save & Continue <i class="bx bx-chevron-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Documents & KYC -->
                    <div class="tab-pane fade {{ $currentStep == 'documents' ? 'show active' : '' }}" id="documents" role="tabpanel">
                        <form class="p-3" action="{{ route('hrm.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="form_step" value="documents">

                            <h5 class="mb-3">Documents Upload</h5>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                            <!-- Documents Upload Section -->
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="text-primary mb-0">Required Documents</h6>
                                        @can('hrm.settings.view')
                                        <a href="{{ route('hrm.document-types.index') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="bx bx-cog"></i> Manage Document Types
                                        </a>
                                        @endcan
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 40%">Document Type</th>
                                                    <th>Upload/Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($documentTypes->where('urgency', 'required') as $type)
                                                @php
                                                    $existingDoc = $employee->documents->where('document_type_id', $type->id)->first();
                                                @endphp
                                                <tr>
                                                    <td>
                                                        {{ $type->name }} <span class="text-danger">*</span>
                                                        @if($type->description)
                                                        <br><small class="text-muted">{{ $type->description }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($existingDoc)
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <span class="badge bg-success"><i class="bx bx-check"></i> Uploaded</span>
                                                                <div class="btn-group">
                                                                    <a href="{{ route('hrm.employees.documents.download', ['employee' => $employee->id, 'document' => $existingDoc->id]) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="Download">
                                                                        <i class="bx bx-download"></i>
                                                                    </a>
                                                                    <form action="{{ route('hrm.employees.documents.destroy', ['employee' => $employee->id, 'document' => $existingDoc->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this document?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bx bx-trash"></i></button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <input type="file" name="doc_{{ $type->id }}" class="form-control form-control-sm" data-doc-type="{{ $type->id }}" data-doc-title="{{ $type->name }}">
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <h6 class="text-info mb-3 mt-4">Nice To Have Documents</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 40%">Document Type</th>
                                                    <th>Upload/Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($documentTypes->where('urgency', 'nice_to_have') as $type)
                                                @php
                                                    $existingDoc = $employee->documents->where('document_type_id', $type->id)->first();
                                                    $index = 100 + $loop->index; // Offset index to avoid collision
                                                @endphp
                                                <tr>
                                                    <td>
                                                        {{ $type->name }}
                                                        @if($type->description)
                                                        <br><small class="text-muted">{{ $type->description }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($existingDoc)
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <span class="badge bg-success"><i class="bx bx-check"></i> Uploaded</span>
                                                                <div class="btn-group">
                                                                    <a href="{{ route('hrm.employees.documents.download', ['employee' => $employee->id, 'document' => $existingDoc->id]) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="Download">
                                                                        <i class="bx bx-download"></i>
                                                                    </a>
                                                                    <form action="{{ route('hrm.employees.documents.destroy', ['employee' => $employee->id, 'document' => $existingDoc->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this document?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bx bx-trash"></i></button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <input type="file" name="doc_{{ $type->id }}" class="form-control form-control-sm" data-doc-type="{{ $type->id }}" data-doc-title="{{ $type->name }}">
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <h6 class="text-secondary mb-3 mt-4">Additional / Custom Documents</h6>
                                    <div id="document-list">
                                        <!-- Other Documents -->
                                        @foreach($employee->documents->whereNotIn('document_type_id', $documentTypes->pluck('id')) as $doc)
                                        <div class="row mb-2">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" value="{{ $doc->document_number }}" readonly>
                                            </div>
                                            <div class="col-md-5">
                                                <a href="{{ route('hrm.employees.documents.download', ['employee' => $employee->id, 'document' => $doc->id]) }}" class="btn btn-sm btn-info" target="_blank">Download</a>
                                            </div>
                                            <div class="col-md-2">
                                                 <form action="{{ route('hrm.employees.documents.destroy', ['employee' => $employee->id, 'document' => $doc->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this document?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="row mb-2 document-row-template" style="display:none;">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control doc-title" placeholder="Document Title (e.g. Reference Letter)">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="file" class="form-control doc-file">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-remove-doc"><i class="bx bx-trash"></i></button>
                                        </div>
                                    </div>
                            <button type="button" class="btn btn-sm btn-dark mt-2" id="btn-add-doc"><i class="bx bx-plus"></i> Add Custom Document</button>
                                </div>
                            </div>
                            
                            <script>
                                // Handle predefined document type uploads
                                document.addEventListener('DOMContentLoaded', function() {
                                    const documentForm = document.querySelector('#documents form');
                                    if (!documentForm) {
                                        console.error('Document form not found!');
                                        return;
                                    }
                                    
                                    console.log('Document upload form found and initialized');
                                    
                                    documentForm.addEventListener('submit', function(e) {
                                        console.log('Form submitted, processing documents...');
                                        
                                        // Find all doc_* file inputs
                                        const docInputs = documentForm.querySelectorAll('input[type="file"][name^="doc_"]');
                                        let docIndex = 0;
                                        
                                        console.log('Found ' + docInputs.length + ' document input fields');
                                        
                                        docInputs.forEach(function(input) {
                                            if (input.files && input.files.length > 0) {
                                                const docType = input.getAttribute('data-doc-type');
                                                const docTitle = input.getAttribute('data-doc-title');
                                                
                                                console.log('Processing document:', {
                                                    type: docType,
                                                    title: docTitle,
                                                    fileName: input.files[0].name
                                                });
                                                
                                                // Create proper name format
                                                input.setAttribute('name', `documents[${docIndex}][file]`);
                                                
                                                // Create hidden fields for title and type
                                                const titleInput = document.createElement('input');
                                                titleInput.type = 'hidden';
                                                titleInput.name = `documents[${docIndex}][title]`;
                                                titleInput.value = docTitle;
                                                documentForm.appendChild(titleInput);
                                                
                                                const typeInput = document.createElement('input');
                                                typeInput.type = 'hidden';
                                                typeInput.name = `documents[${docIndex}][document_type_id]`;
                                                typeInput.value = docType;
                                                documentForm.appendChild(typeInput);
                                                
                                                console.log('Created hidden inputs for document index:', docIndex);
                                                docIndex++;
                                            } else {
                                                // Remove empty file inputs
                                                input.removeAttribute('name');
                                            }
                                        });
                                        
                                        console.log('Total documents to upload:', docIndex);
                                    });
                                });
                                
                                // Handle custom document uploads
                                document.addEventListener('DOMContentLoaded', function() {
                                    let docIndex = 200; // Start high to avoid collision
                                    const container = document.getElementById('document-list');
                                    const template = document.querySelector('.document-row-template');
                                    const addButton = document.getElementById('btn-add-doc');
                                    
                                    if (!addButton || !template || !container) {
                                        console.error('Custom document elements not found:', {
                                            addButton: !!addButton,
                                            template: !!template,
                                            container: !!container
                                        });
                                        return;
                                    }
                                    
                                    console.log('Custom document upload initialized');
                                    
                                    addButton.addEventListener('click', function() {
                                        console.log('Adding custom document row');
                                        
                                        const newRow = template.cloneNode(true);
                                        newRow.style.display = 'flex'; // Make it visible
                                        newRow.classList.remove('document-row-template');
                                        newRow.classList.add('document-row');
                                        
                                        // Update names
                                        const titleInput = newRow.querySelector('.doc-title');
                                        titleInput.name = `documents[${docIndex}][title]`;
                                        titleInput.required = true;
                                        
                                        const fileInput = newRow.querySelector('.doc-file');
                                        fileInput.name = `documents[${docIndex}][file]`;
                                        fileInput.required = true;
                                        
                                        container.appendChild(newRow);
                                        console.log('Custom document row added with index:', docIndex);
                                        docIndex++;
                                        
                                        newRow.querySelector('.btn-remove-doc').addEventListener('click', function() {
                                            console.log('Removing custom document row');
                                            newRow.remove();
                                        });
                                    });
                                });
                            </script>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="remarks" class="form-label">Remarks</label>
                                    <textarea class="form-control" id="remarks" name="remarks" rows="3">{{ old('remarks', $employee->remarks) }}</textarea>
                                </div>
                            </div>



                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                    <i class="bx bx-arrow-back"></i> Back
                                </button>
                                <button type="submit" name="action" value="save_continue" class="btn btn-success">
                                    <i class="bx bx-check-circle"></i> Finish & View Profile
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

