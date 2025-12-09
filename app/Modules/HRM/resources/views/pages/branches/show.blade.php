@extends("Main::layouts.app")
@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12"><h2>Branch Details</h2></div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.branches.index') }}">Branches</a></li>
                <li class="breadcrumb-item active">{{ $branch->name }}</li>
            </ul>
        </div>
    </div>
</div>
@include("Main::widgets.message.sweet-alert")
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1">{{ $branch->name }}</h4>
                        <p class="text-muted mb-2">{{ $branch->code }}</p>
                        @if($branch->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-danger">Inactive</span>
                        @endif
                    </div>
                    <div class="col-md-4 text-end">
                        @can('hrm.branches.edit')
                        <a href="{{ route('hrm.branches.edit', $branch) }}" class="btn btn-primary">
                            <i class="bx bx-edit"></i> Edit Branch
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $stats['total_employees'] }}</h4>
                <p class="text-muted mb-0">Total Employees</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $stats['active_employees'] }}</h4>
                <p class="text-muted mb-0">Active Employees</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="mb-0">{{ $stats['departments'] }}</h4>
                <p class="text-muted mb-0">Departments</p>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Branch Information</h5>
                <table class="table table-sm">
                    <tr><th width="40%">Code:</th><td><strong>{{ $branch->code }}</strong></td></tr>
                    <tr><th>Name:</th><td>{{ $branch->name }}</td></tr>
                    <tr><th>Manager:</th><td>{{ $branch->manager->full_name ?? 'Not Assigned' }}</td></tr>
                    <tr><th>Phone:</th><td>{{ $branch->phone ?? 'N/A' }}</td></tr>
                    <tr><th>Email:</th><td>{{ $branch->email ?? 'N/A' }}</td></tr>
                    <tr><th>Address:</th><td>{{ $branch->address ?? 'N/A' }}</td></tr>
                    <tr><th>City:</th><td>{{ $branch->city ?? 'N/A' }}</td></tr>
                    <tr><th>Country:</th><td>{{ $branch->country ?? 'N/A' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Departments ({{ $branch->departments->count() }})</h5>
                @if($branch->departments->count() > 0)
                <div class="list-group">
                    @foreach($branch->departments as $dept)
                    <a href="{{ route('hrm.departments.show', $dept) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between">
                            <div><h6 class="mb-0">{{ $dept->name }}</h6></div>
                            <span class="badge bg-soft-primary text-primary">{{ $dept->employees_count ?? 0 }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center py-3">No departments</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
