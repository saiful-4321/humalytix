@extends('Main::layouts.app')

@section('title', 'My Assets | ESS')

@section('content')
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
        <div class="card bg-white">
            <div class="card-header align-items-center d-flex border-bottom-0 rounded-top p-3 shadow-sm">
                <h4 class="card-title mb-0 flex-grow-1 font-weight-bold text-dark">
                    <i class="mdi mdi-laptop-mac me-2 text-primary"></i> Assigned Assets: {{ $assets->count() }}
                </h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="ps-3">Asset Name</th>
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
                                <td class="fw-bold ps-3">{{ $asset->name }}</td>
                                <td>{{ $asset->code }}</td>
                                <td>{{ $asset->serial_number ?? '-' }}</td>
                                <td>{{ $asset->type ?? '-' }}</td>
                                <td>{{ $asset->assigned_date ? \Carbon\Carbon::parse($asset->assigned_date)->format('d M, Y') : '-' }}</td>
                                <td>
                                    <span class="badge bg-soft-{{ strtolower($asset->condition) == 'damaged' ? 'danger' : 'success' }} text-{{ strtolower($asset->condition) == 'damaged' ? 'danger' : 'success' }}">
                                        {{ ucfirst($asset->condition ?? 'Good') }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-laptop-off display-4 d-block mb-3"></i>
                                    No assets assigned to you.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
