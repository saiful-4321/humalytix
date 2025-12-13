@extends('HRM::layouts.master')

@section('title', 'Holiday Calendar | ESS')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                     <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Holiday Calendar</h4>
                        <div class="page-title-right">
                             <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('hrm.ess.dashboard') }}">ESS</a></li>
                                <li class="breadcrumb-item active">Holidays</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6 offset-xl-3">
                     <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Upcoming Holidays</h4>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Day</th>
                                            <th>Occasion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($holidays as $holiday)
                                        <tr>
                                            <td class="fw-bold">{{ \Carbon\Carbon::parse($holiday->date)->format('d M, Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($holiday->date)->format('l') }}</td>
                                            <td>{{ $holiday->name }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No upcoming holidays found.</td>
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
