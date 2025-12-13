@extends('HRM::layouts.master')

@section('title', 'Employee Self Service')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Employee Self Service</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-xl-12">
                    <div class="card overflow-hidden">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-3">
                                        <h5 class="text-primary">Welcome Back !</h5>
                                        <p>ESS Dashboard</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{ asset('assets/images/profile-img.png') }}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <img src="{{ $employee->photo_url ?? asset('assets/images/users/avatar-1.png') }}" alt="" class="img-thumbnail rounded-circle">
                                    </div>
                                    <h5 class="font-size-15 text-truncate">{{ $employee->full_name }}</h5>
                                    <p class="text-muted mb-0 text-truncate">{{ $employee->designation }}</p>
                                </div>
            
                                <div class="col-sm-8">
                                    <div class="pt-4">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5 class="font-size-15">{{ $pendingLeaves }}</h5>
                                                <p class="text-muted mb-0">Pending Leaves</p>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="font-size-15">{{ $lastPayroll ? number_format($lastPayroll->net_salary, 2) : 'N/A' }}</h5>
                                                <p class="text-muted mb-0">Last Salary</p>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <a href="{{ route('hrm.ess.profile') }}" class="btn btn-primary waves-effect waves-light btn-sm">View Profile <i class="mdi mdi-arrow-right ms-1"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <h4 class="card-title mb-4">Quick Actions</h4>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card card-h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="text-muted mb-3 lh-1 d-block text-truncate">Apply Leave</h5>
                                    <a href="{{ route('hrm.leaves.my-leaves') }}" class="btn btn-soft-primary btn-sm">Apply Now</a>
                                </div>
                                <div class="flex-shrink-0 text-end dash-widget">
                                    <div id="mini-chart1" data-colors='["#5156be", "#34c38f"]' class="apex-charts"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="text-muted mb-3 lh-1 d-block text-truncate">Regularize Attendance</h5>
                                    <a href="{{ route('hrm.ess.attendance') }}" class="btn btn-soft-warning btn-sm">Request</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="text-muted mb-3 lh-1 d-block text-truncate">View Payslips</h5>
                                    <a href="{{ route('hrm.ess.payslips') }}" class="btn btn-soft-success btn-sm">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="text-muted mb-3 lh-1 d-block text-truncate">Holidays</h5>
                                    <a href="{{ route('hrm.ess.holidays') }}" class="btn btn-soft-info btn-sm">View Calendar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card card-h-100">
                        <div class="card-body">
                             <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="text-muted mb-3 lh-1 d-block text-truncate">Performance Goals</h5>
                                    <a href="{{ route('hrm.performance-goals.my-goals') }}" class="btn btn-soft-danger btn-sm">Track Goals</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="card card-h-100">
                        <div class="card-body">
                             <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="text-muted mb-3 lh-1 d-block text-truncate">Assigned Assets</h5>
                                    <a href="{{ route('hrm.ess.assets') }}" class="btn btn-soft-secondary btn-sm">View Assets</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                     <div class="card card-h-100">
                        <div class="card-body">
                             <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="text-muted mb-3 lh-1 d-block text-truncate">Salary Certificate</h5>
                                    <a href="{{ route('hrm.ess.salary-certificate') }}" class="btn btn-soft-dark btn-sm">Request</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->
</div>
@endsection
