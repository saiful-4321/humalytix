@extends("HRM::layouts.settings")

@section("title", "Letter Templates")
@section("breadcrumb")
    <li class="breadcrumb-item active">Recruitment Settings</li>
    <li class="breadcrumb-item active">Templates</li>
@endsection

@section("settings-content")
    <div class="card bg-white">
        <div class="card-header border-bottom">
             <div class="d-flex align-items-center justify-content-between py-1">
                <h6 class="font-weight-medium mb-0">Templates List</h6>
                <a href="{{ route('hrm.settings.letter-templates.create') }}" class="btn btn-primary btn-sm d-flex align-items-center font-weight-medium">
                    <i class="mdi mdi-plus me-1"></i> Add New Template
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive rounded-10 border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th>Template Name</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                        <tr>
                            <td>
                                <strong>{{ $template->name }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-soft-info text-info">{{ ucfirst($template->type) }}</span>
                            </td>
                            <td>{{ Str::limit($template->subject, 30) }}</td>
                            <td>
                                @if($template->is_active)
                                    <span class="badge bg-soft-success text-success">Active</span>
                                @else
                                    <span class="badge bg-soft-secondary text-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted font-size-16 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('hrm.settings.letter-templates.edit', $template->id) }}"><i class="bx bx-edit me-2"></i> Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('hrm.settings.letter-templates.destroy', $template->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')"><i class="bx bx-trash me-2"></i> Delete</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="mdi mdi-text-box-outline font-size-24 d-block mb-2"></i>
                                No templates found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($templates->count())
        <div class="card-footer bg-transparent border-top">
            <div class="d-flex justify-content-end">
                {{ $templates->links() }}
            </div>
        </div>
        @endif
    </div>
@endsection
