@extends('Main::layouts.app')

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12">
            <h2>Workflows</h2>
        </div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            @include('HRM::includes.breadcrumb', ['breadcrumbs' => [
                ['name' => 'HRM', 'url' => route('hrm.dashboard')],
                ['name' => 'Settings', 'url' => route('hrm.settings.index')],
                ['name' => 'Workflows', 'active' => true]
            ]])
            <a href="{{ route('hrm.workflows.create') }}" class="btn btn-primary float-right ml-2">
                <i class="bx bx-plus"></i> Create Workflow
            </a>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Module</th>
                                <th>Trigger</th>
                                <th>Steps</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($workflows as $workflow)
                            <tr>
                                <td>
                                    <strong>{{ $workflow->name }}</strong>
                                    <p class="text-muted mb-0"><small>{{ $workflow->description }}</small></p>
                                </td>
                                <td><span class="badge bg-info">{{ ucfirst($workflow->module_type) }}</span></td>
                                <td>{{ ucfirst($workflow->trigger_event) }}</td>
                                <td>{{ $workflow->steps->count() }} Steps</td>
                                <td>
                                    @if($workflow->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('hrm.workflows.edit', $workflow->id) }}" class="btn btn-sm btn-primary"><i class="bx bx-edit"></i></a>
                                    <form action="{{ route('hrm.workflows.destroy', $workflow->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No workflows found.</td>
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
