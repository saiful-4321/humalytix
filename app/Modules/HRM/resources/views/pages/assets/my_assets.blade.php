@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>My Assets</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">My Assets</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    @forelse($assets as $asset)
    <div class="col-lg-4 col-md-6">
        <div class="card bg-white">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm me-3">
                        <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-20">
                            <i class="bx bx-cube"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="font-size-14 mb-1">{{ $asset->name }}</h5>
                        <p class="text-muted mb-0">{{ $asset->code }}</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-nowrap mb-0">
                        <tbody>
                            <tr>
                                <th scope="row">Category :</th>
                                <td>{{ $asset->category->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Serial No :</th>
                                <td>{{ $asset->serial_number ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Assigned Date :</th>
                                <td>{{ $asset->currentAssignment->assigned_date->format('d M, Y') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Condition :</th>
                                <td>{{ ucfirst($asset->condition) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="mdi mdi-cube-off-outline text-muted display-4"></i>
                </div>
                <h4 class="mb-0">No Assets Assigned</h4>
                <p class="text-muted mt-2">You currently do not have any assets assigned to you.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
