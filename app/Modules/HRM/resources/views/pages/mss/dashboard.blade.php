@extends('HRM::layouts.master')

@section('title', 'Manager Dashboard | MSS')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Manager Self Service</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item active">MSS Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">My Team</p>
                                    <h4 class="mb-0">{{ $totalTeam }}</h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="bx bx-group font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top">
                            <div class="text-center">
                                <a href="{{ route('hrm.mss.team') }}" class="btn btn-sm btn-link font-size-14 text-decoration-underline">View Team</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Pending Leaves</p>
                                    <h4 class="mb-0">{{ $pendingLeaves }}</h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                        <span class="avatar-title">
                                            <i class="bx bx-calendar-event font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top">
                            <div class="text-center">
                                <a href="{{ route('hrm.mss.approvals') }}" class="btn btn-sm btn-link font-size-14 text-decoration-underline">Manage</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Regularization Req.</p>
                                    <h4 class="mb-0">{{ $pendingRegularizations }}</h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                        <span class="avatar-title">
                                            <i class="bx bx-time font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top">
                             <div class="text-center">
                                <a href="{{ route('hrm.mss.approvals') }}" class="btn btn-sm btn-link font-size-14 text-decoration-underline">Manage</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">Expense Claims</p>
                                    <h4 class="mb-0">{{ $pendingExpenses }}</h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                        <span class="avatar-title">
                                            <i class="bx bx-money font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top">
                             <div class="text-center">
                                <a href="{{ route('hrm.mss.approvals') }}" class="btn btn-sm btn-link font-size-14 text-decoration-underline">Manage</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Manager Actions</h4>
                            <div class="row">
                                <div class="col-md-3">
                                    <a href="{{ route('hrm.mss.roster') }}" class="btn btn-primary w-100 mb-2">
                                        <i class="bx bx-calendar-check me-1"></i> Manage Shifts
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('hrm.mss.team') }}" class="btn btn-info w-100 mb-2">
                                        <i class="bx bx-line-chart me-1"></i> Team Performance
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
