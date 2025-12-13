@extends('HRM::layouts.master')

@section('title', 'My Assets | ESS')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">My Assigned Assets</h4>
                        <div class="page-title-right">
                             <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('hrm.ess.dashboard') }}">ESS</a></li>
                                <li class="breadcrumb-item active">Assets</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                     <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Assets</h4>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Asset Name</th>
                                            <th>Asset Code</th>
                                            <th>Serial Number</th>
                                            <th>Type</th>
                                            <th>Assigned Date</th>
                                            <th>Condition</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($assets as $asset)
                                        <tr>
                                            <td class="fw-bold">{{ $asset->name }}</td>
                                            <td>{{ $asset->code }}</td>
                                            <td>{{ $asset->serial_number ?? '-' }}</td>
                                            <td>{{ $asset->type ?? '-' }}</td>
                                            <td>{{ $asset->assigned_date ? \Carbon\Carbon::parse($asset->assigned_date)->format('d M, Y') : '-' }}</td>
                                            <td>{{ ucfirst($asset->condition ?? 'Good') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No assets assigned to you.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
