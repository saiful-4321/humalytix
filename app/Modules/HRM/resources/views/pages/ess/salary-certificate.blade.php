@extends('HRM::layouts.master')

@section('title', 'Salary Certificate | ESS')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                     <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Salary Certificate Requests</h4>
                        <div class="page-title-right">
                             <button class="btn btn-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#requestCertificateOffcanvas">
                                <i class="bx bx-plus"></i> New Request
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                     <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Request History</h4>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date Requested</th>
                                            <th>Type</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($requests as $request)
                                        <tr>
                                            <td>{{ $request->created_at->format('d M, Y') }}</td>
                                            <td class="fw-bold">{{ ucfirst(str_replace('_', ' ', $request->type)) }}</td>
                                            <td>{{ $request->reason }}</td>
                                            <td>
                                                <span class="badge bg-{{ $request->status == 'pending' ? 'warning' : ($request->status == 'approved' ? 'success' : 'danger') }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($request->status == 'approved' && $request->letter_id)
                                                    <a href="{{ route('hrm.letters.download', $request->letter_id) }}" class="btn btn-sm btn-primary" target="_blank">
                                                        <i class="bx bx-download"></i> Download
                                                    </a>
                                                @elseif($request->status == 'rejected')
                                                     <i class="mdi mdi-information-outline" title="{{ $request->remarks }}"></i> {{ $request->remarks }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No requests found.</td>
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

<!-- Request Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="requestCertificateOffcanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Request Letter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.ess.salary-certificate.request') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Letter Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required>
                    <option value="salary_certificate">Salary Certificate</option>
                    <option value="noc">No Objection Certificate (NOC)</option>
                    <option value="experience_letter">Experience Letter</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Reason <span class="text-danger">*</span></label>
                <textarea name="reason" class="form-control" rows="3" required placeholder="E.g. For visa purpose..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">Submit Request</button>
        </form>
    </div>
</div>
@endsection
