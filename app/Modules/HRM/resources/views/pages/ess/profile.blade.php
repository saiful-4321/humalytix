@extends('HRM::layouts.master')

@section('title', 'My Profile | ESS')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">My Profile</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('hrm.ess.dashboard') }}">ESS</a></li>
                                <li class="breadcrumb-item active">Profile</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-4">
                    <div class="card overflow-hidden">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-3">
                                        <h5 class="text-primary">{{ $employee->full_name }}</h5>
                                        <p class="mb-0">{{ $employee->designation }}</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{ asset('assets/images/profile-img.png') }}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <img src="{{ $employee->photo_url ?? asset('assets/images/users/avatar-1.png') }}" alt="" class="img-thumbnail rounded-circle">
                                    </div>
                                    <div class="p-2">
                                        <h5 class="font-size-15 text-truncate">Employee ID: {{ $employee->employee_code }}</h5>
                                        <p class="text-muted mb-0 text-truncate">Department: {{ $employee->department->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Personal Info</h4>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Mobile :</th>
                                            <td>{{ $employee->mobile }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Email :</th>
                                            <td>{{ $employee->email }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Location :</th>
                                            <td>{{ $employee->permanent_address }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Detailed Information</h4>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#editProfileOffcanvas">
                                    <i class="bx bx-edit"></i> Edit Info
                                </button>
                            </div>
                            
                            <hr>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">Phone</label>
                                    <p>{{ $employee->phone ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">Mobile</label>
                                    <p>{{ $employee->mobile ?? '-' }}</p>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="fw-bold">Address</label>
                                    <p>{{ $employee->address ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">Emergency Contact Name</label>
                                    <p>{{ $employee->emergency_contact_name ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">Emergency Contact Phone</label>
                                    <p>{{ $employee->emergency_contact_phone ?? '-' }}</p>
                                </div>
                            </div>
                            
                            <!-- Add more fields as needed (Joining date, etc are usually read only) -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="editProfileOffcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Edit Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.ess.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ $employee->phone }}">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Mobile</label>
                <input type="text" name="mobile" class="form-control" value="{{ $employee->mobile }}">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3">{{ $employee->address }}</textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Emergency Contact Name</label>
                <input type="text" name="emergency_contact_name" class="form-control" value="{{ $employee->emergency_contact_name }}">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Emergency Contact Phone</label>
                <input type="text" name="emergency_contact_phone" class="form-control" value="{{ $employee->emergency_contact_phone }}">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Profile Photo</label>
                <input type="file" name="photo" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary w-100">Update Profile</button>
        </form>
    </div>
</div>
@endsection
