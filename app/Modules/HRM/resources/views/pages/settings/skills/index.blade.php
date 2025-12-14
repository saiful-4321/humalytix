@extends("HRM::layouts.settings")

@section("title", "Skills")
@section("breadcrumb")
    <li class="breadcrumb-item active">Organization Settings</li>
    <li class="breadcrumb-item active">Skills</li>
@endsection

@section("settings-content")
    <div class="card bg-white">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between py-1">
                <h6 class="font-weight-medium mb-0">Skills List</h6>
                
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-info btn-sm d-flex align-items-center font-weight-medium" data-bs-toggle="offcanvas" data-bs-target="#addSkillOffcanvas">
                        <i class="mdi mdi-plus me-1"></i> Add New Skill
                    </button>
                    
                    {{-- Optional Filter Button if we had filters --}}
                    {{-- <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#skillFilter">
                        <i class="mdi mdi-filter-variant me-1"></i> Filter
                    </button> --}}
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive rounded-10 border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($skills as $skill)
                        <tr>
                            <td>
                                <h6 class="mb-0 font-size-14">{{ $skill->name }}</h6>
                                @if($skill->description)
                                <small class="text-muted">{{ Str::limit($skill->description, 50) }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-soft-primary text-primary">{{ $skill->category ?? 'N/A' }}</span></td>
                            <td>
                                @if($skill->is_active)
                                <span class="badge bg-soft-success text-success">Active</span>
                                @else
                                <span class="badge bg-soft-danger text-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form action="{{ route('hrm.skills.destroy', $skill->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button type="button" class="btn btn-sm btn-soft-info" data-bs-toggle="offcanvas" data-bs-target="#editSkillOffcanvas{{ $skill->id }}">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </button>
                                        <button type="submit" class="btn btn-sm btn-soft-danger" onclick="return confirm('Are you sure?')">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="mdi mdi-school font-size-24 d-block mb-2"></i>
                                    No skills found.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination if available --}}
        {{-- <div class="card-footer bg-transparent border-top">
            <div class="d-flex justify-content-end">
                {{ $skills->links() }}
            </div>
        </div> --}}
    </div>

    {{-- Add Skill Offcanvas --}}
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="addSkillOffcanvas" aria-labelledby="addSkillLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="addSkillLabel">Add New Skill</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.skills.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required placeholder="e.g. Project Management">
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" class="form-control" name="category" placeholder="e.g. Technical, Soft Skill">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Save Skill</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Offcanvases Loop --}}
    @foreach($skills as $skill)
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="editSkillOffcanvas{{ $skill->id }}" aria-labelledby="editSkillLabel{{ $skill->id }}">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="editSkillLabel{{ $skill->id }}">Edit Skill</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.skills.update', $skill->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ $skill->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" class="form-control" name="category" value="{{ $skill->category }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3">{{ $skill->description }}</textarea>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $skill->is_active ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Update Skill</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endsection
