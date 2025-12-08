@extends("Main::layouts.app")


@section('content')
<div class="row">
    <div class="col-lg-5 col-md-8 col-sm-12">
        <h2>{{ __('Profile') }}</h2>
    </div>
    <div class="col-lg-7 col-md-4 col-sm-12 text-right">
        <ul class="breadcrumb justify-content-end">
            <li class="breadcrumb-item">Dashboard</li>
            <li class="breadcrumb-item">User</li>
            <li class="breadcrumb-item active">Profile</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-md-12">
        <div class="card bg-white p-3">
            <div class="card-body text-center">
                <img src="{{ asset('assets/images/users/avatar-1.png') }}" class="rounded-circle" alt="" width="120" height="120">
                <h4 class="card-title mt-3">{{ auth()->user()->name ?? null }}</h4>
                <span class="badge bg-soft-info text-info">{{ auth()->user()->email ?? null }}</span>
            </div>
        </div>
        
        <div class="card bg-white p-3">

            @php
                $ekcy = \App\Modules\Main\Services\EkycProgressbarService::status(auth()->user()->id);
            @endphp 
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-9">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">BO Account Profile </span>
                        <h4 class="mb-3"></h4>
                    </div>
                    <div class="col-3 mb-3">
                        <div class="float-end">
                            <div class="btn btn-soft-info waves-effect waves-light rounded-circle disabled-custom"> 
                            <i class="bx bx-user"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-nowrap mb-2">
                    <b class="ms-1 text-muted font-size-13 ">Completed Profle</b>
                    <span class="ms-1 text-muted font-size-13 float-end">{{ $ekcy->progress ?? 0 }}%</span>
                </div>
                <div class="progress animated-progess mb-2 bg-light">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $ekcy->progress ?? 0 }}%" aria-valuenow="{{ $ekcy->progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-nowrap mb-2">
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-9 col-md-12">
        <div class="card bg-white">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="font-weight-medium mb-0">Profile Information</h6>

                    @can("user-profile-edit")
                    <a href="javascript:void(0)" route="{{ route('dashboard.user.profile.edit') }}" data-toggle="dynamicModal" class="btn btn-info d-flex align-items-center font-weight-medium">
                        <i class="icon-plus pr-2"></i>
                        Edit Profile
                    </a> 
                    @endcan
                </div>
            </div> 
            <div class="card-body">
                <dl class="row  p-3"> 

                    <dt class="col-sm-3">Name:</dt>
                    <dd class="col-sm-9">{{ auth()->user()->name ?? null }}</dd>

                    <dt class="col-sm-3">Email address:</dt>
                    <dd class="col-sm-9">{{ auth()->user()->email ?? null }}</dd>

                    <dt class="col-sm-3">Mobile Number:</dt>
                    <dd class="col-sm-9">{{ auth()->user()->mobile ?? null }}</dd>

                    <dt class="col-sm-3">NID:</dt>
                    <dd class="col-sm-9">{{ auth()->user()->nid ?? null }}</dd>


                    <dt class="col-sm-3">Date Of Birth:</dt>
                    <dd class="col-sm-9">{{ dbToDate(auth()->user()->date_of_birth) }}</dd>

                    <dt class="col-sm-3">Status:</dt>
                    <dd class="col-sm-9">{{ auth()->user()->status ?? null }}</dd>

                    <dt class="col-sm-3">Payment Status:</dt>
                    <dd class="col-sm-9">{{ auth()->user()->payment_status ?? null }}</dd>

                    <dt class="col-sm-3">Roles:</dt>
                    <dd class="col-sm-9">
                        @foreach(auth()->user()->getRoleNames() as $role) 
                            <span class="badge bg-soft-primary text-primary">{{ $role ?? null }}</span>
                        @endforeach    
                    </dd>

                    <dt class="col-sm-3">API Token:</dt>
                    <dd class="col-sm-9">
                        <i class="badge bg-soft-warning text-warning copy">{{ auth()->user()->api_token ?? null }}</i>
                    </dd>

                    <dt class="col-sm-3">Created By</dt>
                    <dd class="col-sm-9">{{ auth()->user()->createdBy->name ?? null }}</dd>

                    <dt class="col-sm-3">Updated By</dt>
                    <dd class="col-sm-9">{{ auth()->user()->updatedBy->name ?? null }}</dd>

                    <dt class="col-sm-3">Created Date:</dt>
                    <dd class="col-sm-9">{{ dbToDateTime(auth()->user()->created_at ?? null) }}</dd>

                    <dt class="col-sm-3">Updated Date:</dt>
                    <dd class="col-sm-9">{{ dbToDateTime(auth()->user()->updated_at ?? null) }}</dd>

                    <dt class="col-sm-3">Verified Date:</dt>
                    <dd class="col-sm-9">{{ dbToDateTime(auth()->user()->email_verified_at ?? null) }}</dd> 
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
