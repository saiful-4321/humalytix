@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Business Unit Details</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.business-units.index') }}">Business Units</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-4 col-md-12">
        <div class="card bg-white">
            <div class="card-body">
                <div class="text-center mt-3 mb-3">
                    <div class="avatar-md mx-auto mb-3">
                        <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-24">
                            {{ substr($businessUnit->name, 0, 1) }}
                        </span>
                    </div>
                    <h5 class="mb-1">{{ $businessUnit->name }}</h5>
                    <p class="text-muted mb-1">{{ $businessUnit->code }}</p>
                    <div>
                        @if($businessUnit->is_active)
                            <span class="badge bg-soft-success text-success">Active</span>
                        @else
                            <span class="badge bg-soft-danger text-danger">Inactive</span>
                        @endif
                    </div>
                </div>
                
                <hr>

                <div class="row text-center">
                    <div class="col-6">
                        <h6 class="mb-1">{{ $stats['total_employees'] }}</h6>
                        <p class="text-muted mb-0">Total Employees</p>
                    </div>
                    <div class="col-6">
                        <h6 class="mb-1">{{ $stats['active_employees'] }}</h6>
                        <p class="text-muted mb-0">Active</p>
                    </div>
                </div>

                <hr>
                
                <div class="mt-3">
                    <h6 class="font-size-14">Head of Unit</h6>
                    @if($businessUnit->head)
                        <div class="d-flex align-items-center mt-3">
                            @if($businessUnit->head->photo)
                                <img src="{{ asset('storage/' . $businessUnit->head->photo) }}" class="rounded-circle avatar-xs me-2" alt="">
                            @else
                                <div class="avatar-xs me-2">
                                    <span class="avatar-title rounded-circle bg-soft-info text-info font-size-12">
                                        {{ substr($businessUnit->head->first_name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-0 font-size-14">{{ $businessUnit->head->full_name }}</h6>
                                <p class="text-muted mb-0 font-size-12">{{ $businessUnit->head->designation }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Not Assigned</p>
                    @endif
                </div>

                <div class="mt-4">
                    <h6 class="font-size-14">Description</h6>
                    <p class="text-muted mb-0">{{ $businessUnit->description ?? 'No description provided.' }}</p>
                </div>

                <div class="mt-4">
                    <a href="{{ route('hrm.business-units.edit', $businessUnit->id) }}" class="btn btn-primary w-100">Edit Business Unit</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8 col-md-12">
        <div class="card bg-white">
            <div class="card-header">
                <h5 class="card-title mb-0">Employees</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Joining Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($businessUnit->employees as $emp)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($emp->photo)
                                            <img src="{{ asset('storage/' . $emp->photo) }}" class="rounded-circle avatar-xs me-2" alt="">
                                        @else
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-soft-secondary text-secondary font-size-10">
                                                    {{ substr($emp->first_name, 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0 font-size-14">{{ $emp->full_name }}</h6>
                                            <p class="text-muted mb-0 font-size-12">{{ $emp->employee_code }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $emp->designation }}</td>
                                <td>{{ $emp->joining_date ? $emp->joining_date->format('d M, Y') : '-' }}</td>
                                <td>{!! $emp->status_badge !!}</td>
                                <td>
                                    <a href="{{ route('hrm.employees.show', $emp->id) }}" class="btn btn-sm btn-light">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No employees in this business unit.</td>
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
