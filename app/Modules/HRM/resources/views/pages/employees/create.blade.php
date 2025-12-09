@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12">
            <h2>Add New Employee</h2>
        </div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.employees.index') }}">Employees</a></li>
                <li class="breadcrumb-item active">Add New</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hrm.employees.store') }}" method="POST" enctype="multipart/form-data" id="employeeForm">
                    @csrf
                    
                    <!-- Progress Steps -->
                    <div class="mb-4">
                        <ul class="nav nav-pills nav-justified" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="personal-tab" data-bs-toggle="pill" href="#personal" role="tab">
                                    <i class="bx bx-user d-block font-size-20"></i>
                                    <span class="d-none d-sm-block">Personal Info</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="employment-tab" data-bs-toggle="pill" href="#employment" role="tab">
                                    <i class="bx bx-briefcase d-block font-size-20"></i>
                                    <span class="d-none d-sm-block">Employment</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="salary-tab" data-bs-toggle="pill" href="#salary" role="tab">
                                    <i class="bx bx-money d-block font-size-20"></i>
                                    <span class="d-none d-sm-block">Salary & Bank</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="documents-tab" data-bs-toggle="pill" href="#documents" role="tab">
                                    <i class="bx bx-file d-block font-size-20"></i>
                                    <span class="d-none d-sm-block">Documents</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Personal Information -->
                        <div class="tab-pane fade show active" id="personal" role="tabpanel">
                            <h5 class="mb-3">Personal Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                                    @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="blood_group" class="form-label">Blood Group</label>
                                    <select class="form-select" id="blood_group" name="blood_group">
                                        <option value="">Select Blood Group</option>
                                        <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                        <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                        <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                                        <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="marital_status" class="form-label">Marital Status</label>
                                    <select class="form-select" id="marital_status" name="marital_status">
                                        <option value="">Select Status</option>
                                        <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nationality" class="form-label">Nationality</label>
                                    <input type="text" class="form-control" id="nationality" name="nationality" value="{{ old('nationality', 'Bangladeshi') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="present_address" class="form-label">Present Address</label>
                                    <textarea class="form-control" id="present_address" name="present_address" rows="2">{{ old('present_address') }}</textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="photo" class="form-label">Photo</label>
                                    <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                                    @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted">Max size: 5MB. Supported formats: JPG, PNG</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" onclick="nextTab('employment-tab')">
                                    Next <i class="bx bx-chevron-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Employment Details -->
                        <div class="tab-pane fade" id="employment" role="tabpanel">
                            <h5 class="mb-3">Employment Details</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
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
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
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
                                    <input type="text" class="form-control @error('designation') is-invalid @enderror" id="designation" name="designation" value="{{ old('designation') }}" required>
                                    @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="employment_type" class="form-label">Employment Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('employment_type') is-invalid @enderror" id="employment_type" name="employment_type" required>
                                        <option value="">Select Type</option>
                                        <option value="full_time" {{ old('employment_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                        <option value="part_time" {{ old('employment_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                        <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                                        <option value="internship" {{ old('employment_type') == 'internship' ? 'selected' : '' }}>Internship</option>
                                    </select>
                                    @error('employment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="joining_date" class="form-label">Joining Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('joining_date') is-invalid @enderror" id="joining_date" name="joining_date" value="{{ old('joining_date') }}" required>
                                    @error('joining_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="probation_end_date" class="form-label">Probation End Date</label>
                                    <input type="date" class="form-control" id="probation_end_date" name="probation_end_date" value="{{ old('probation_end_date') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="probation" {{ old('status') == 'probation' ? 'selected' : '' }}>Probation</option>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
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
                                        <option value="{{ $manager->id }}" {{ old('reporting_to') == $manager->id ? 'selected' : '' }}>
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
                                        <option value="{{ $bu->id }}" {{ old('business_unit_id') == $bu->id ? 'selected' : '' }}>
                                            {{ $bu->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="prevTab('personal-tab')">
                                    <i class="bx bx-chevron-left"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary" onclick="nextTab('salary-tab')">
                                    Next <i class="bx bx-chevron-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Salary & Bank Details -->
                        <div class="tab-pane fade" id="salary" role="tabpanel">
                            <h5 class="mb-3">Salary & Bank Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="basic_salary" class="form-label">Basic Salary</label>
                                    <input type="number" class="form-control" id="basic_salary" name="basic_salary" value="{{ old('basic_salary') }}" step="0.01">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="bank_name" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="bank_account_number" class="form-label">Account Number</label>
                                    <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="bank_branch" class="form-label">Bank Branch</label>
                                    <input type="text" class="form-control" id="bank_branch" name="bank_branch" value="{{ old('bank_branch') }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="prevTab('employment-tab')">
                                    <i class="bx bx-chevron-left"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary" onclick="nextTab('documents-tab')">
                                    Next <i class="bx bx-chevron-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Documents & KYC -->
                        <div class="tab-pane fade" id="documents" role="tabpanel">
                            <h5 class="mb-3">KYC & Documents</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nid_number" class="form-label">NID Number</label>
                                    <input type="text" class="form-control" id="nid_number" name="nid_number" value="{{ old('nid_number') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="passport_number" class="form-label">Passport Number</label>
                                    <input type="text" class="form-control" id="passport_number" name="passport_number" value="{{ old('passport_number') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tax_id" class="form-label">Tax ID / TIN</label>
                                    <input type="text" class="form-control" id="tax_id" name="tax_id" value="{{ old('tax_id') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="driving_license" class="form-label">Driving License</label>
                                    <input type="text" class="form-control" id="driving_license" name="driving_license" value="{{ old('driving_license') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="remarks" class="form-label">Remarks</label>
                                    <textarea class="form-control" id="remarks" name="remarks" rows="3">{{ old('remarks') }}</textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="prevTab('salary-tab')">
                                    <i class="bx bx-chevron-left"></i> Previous
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bx bx-check-circle"></i> Create Employee
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function nextTab(tabId) {
    var tab = new bootstrap.Tab(document.getElementById(tabId));
    tab.show();
}

function prevTab(tabId) {
    var tab = new bootstrap.Tab(document.getElementById(tabId));
    tab.show();
}
</script>
@endpush
