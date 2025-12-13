@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Document Types</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.settings.index') }}">Settings</a></li>
                <li class="breadcrumb-item active">Document Types</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12 col-md-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Document Types List</h6>
                    @can('hrm.settings.create')
                    <a href="{{ route('hrm.document-types.create') }}" class="btn btn-info btn-sm">
                        <i class="mdi mdi-plus me-1"></i> Add New Type
                    </a>
                    @endcan
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Required</th>
                                <th>Urgency</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documentTypes as $type)
                            <tr>
                                <td>
                                    <h6 class="mb-0">{{ $type->name }}</h6>
                                    @if($type->description)
                                    <small class="text-muted">{{ Str::limit($type->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-soft-primary text-primary">{{ ucfirst($type->category) }}</span>
                                </td>
                                <td>
                                    @if($type->is_required)
                                    <span class="badge bg-danger">Yes</span>
                                    @else
                                    <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($type->urgency == 'required')
                                    <span class="badge bg-danger">Required</span>
                                    @elseif($type->urgency == 'nice_to_have')
                                    <span class="badge bg-info">Nice to Have</span>
                                    @else
                                    <span class="badge bg-secondary">Not Required</span>
                                    @endif
                                </td>
                                <td>{{ $type->order }}</td>
                                <td>
                                    @if($type->is_active)
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @can('hrm.settings.edit')
                                    <a href="{{ route('hrm.document-types.edit', $type) }}" class="btn btn-sm btn-soft-primary" title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('hrm.settings.delete')
                                    <form action="{{ route('hrm.document-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" title="Delete">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No document types found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($documentTypes->hasPages())
            <div class="card-footer">
                {{ $documentTypes->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
