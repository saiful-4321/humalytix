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

<div class="row clearfix mb-4">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <i class="bx bx-info-circle me-1"></i> Please fill in the basic personal information to create the employee profile. employment and other details can be added in the next steps.
                </div>

                <!-- Progress Steps (Visual Only) -->
                <div class="mb-4">
                    <ul class="nav nav-pills nav-justified">
                        <li class="nav-item">
                            <a class="nav-link active" href="javascript:void(0)">
                                <i class="bx bx-user d-block font-size-20"></i>
                                <span class="d-none d-sm-block">Personal Info</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" href="javascript:void(0)">
                                <i class="bx bx-briefcase d-block font-size-20"></i>
                                <span class="d-none d-sm-block">Employment</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" href="javascript:void(0)">
                                <i class="bx bx-money d-block font-size-20"></i>
                                <span class="d-none d-sm-block">Salary & Bank</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" href="javascript:void(0)">
                                <i class="bx bx-file d-block font-size-20"></i>
                                <span class="d-none d-sm-block">Documents</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <form class="p-3" action="{{ route('hrm.employees.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="form_step" value="personal">
                    
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
                        <button type="submit" class="btn btn-primary">
                            Save & Continue <i class="bx bx-chevron-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

