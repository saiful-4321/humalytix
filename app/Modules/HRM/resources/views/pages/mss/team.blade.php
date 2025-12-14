@extends('HRM::layouts.master')

@section('title', 'My Team | MSS')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
             <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">My Team</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('hrm.mss.dashboard') }}">MSS</a></li>
                                <li class="breadcrumb-item active">Team</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Employee</th>
                                            <th>Designation</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($subordinates as $sub)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <img src="{{ $sub->photo ? asset('storage/' . $sub->photo) : asset('assets/images/users/avatar-1.png') }}" class="img-fluid rounded-circle">
                                                    </div>
                                                    <div>
                                                        <h5 class="font-size-14 mb-0">{{ $sub->full_name }}</h5>
                                                        <p class="text-muted mb-0 font-size-12">{{ $sub->employee_code }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $sub->designation }}</td>
                                            <td>{{ $sub->department->name ?? '-' }}</td>
                                            <td>{!! $sub->status_badge !!}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a href="#" class="dropdown-toggle card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="mdi mdi-dots-horizontal font-size-18"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a href="{{ route('hrm.okrs.index', ['employee_id' => $sub->id]) }}" class="dropdown-item"><i class="bx bx-target-lock font-size-16 align-middle me-1"></i> View OKRs</a></li>
                                                        <li><a href="{{ route('hrm.appraisals-360.index', ['employee_id' => $sub->id]) }}" class="dropdown-item"><i class="bx bx-star font-size-16 align-middle me-1"></i> View Appraisals</a></li>
                                                        <!-- Add Shift Management Link ? -->
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No team members found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $subordinates->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
