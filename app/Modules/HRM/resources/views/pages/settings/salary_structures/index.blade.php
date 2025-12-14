@extends("HRM::layouts.settings")

@section("title", "Salary Structures")
@section("breadcrumb")
    <li class="breadcrumb-item active">Payroll Settings</li>
    <li class="breadcrumb-item active">Structures</li>
@endsection

@section("settings-content")
    <div class="card bg-white">
        <div class="card-header border-bottom">
             <div class="d-flex align-items-center justify-content-between py-1">
                <h6 class="font-weight-medium mb-0">Structures List</h6>
                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center font-weight-medium" data-bs-toggle="offcanvas" data-bs-target="#addStructureOffcanvas">
                    <i class="mdi mdi-plus me-1"></i> Add Structure
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive rounded-10 border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th>Structure Name</th>
                            <th>Earnings</th>
                            <th>Deductions</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($structures as $structure)
                        <tr>
                            <td>
                                <strong>{{ $structure->name }}</strong>
                                @if($structure->description)
                                <small class="text-muted d-block">{{ $structure->description }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($structure->components->where('type', 'earning') as $comp)
                                        <span class="badge bg-soft-success text-success">{{ $comp->name }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($structure->components->where('type', 'deduction') as $comp)
                                        <span class="badge bg-soft-danger text-danger">{{ $comp->name }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                @if($structure->is_active)
                                    <span class="badge bg-soft-primary text-primary">Active</span>
                                @else
                                    <span class="badge bg-soft-secondary text-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="button" class="btn btn-sm btn-soft-info" data-bs-toggle="offcanvas" data-bs-target="#editStructureOffcanvas{{ $structure->id }}">
                                        <i class="mdi mdi-pencil-outline"></i>
                                    </button>
                                    <form action="{{ route('hrm.settings.salary-structures.destroy', $structure->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" onclick="return confirm('Are you sure? This will affect employees assigned to this structure.')">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="mdi mdi-file-tree font-size-24 d-block mb-2"></i>
                                No salary structures found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- Add Structure Offcanvas --}}
    <div class="offcanvas offcanvas-end w-75" tabindex="-1" id="addStructureOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Create Salary Structure</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.settings.salary-structures.store') }}" method="POST">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Structure Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g. Grade A Executive">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" name="description" placeholder="Brief description...">
                    </div>
                    <div class="col-md-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>

                @include('HRM::pages.settings.salary_structures.partials.components_table', ['prefix' => 'create'])
                
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Create Structure</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Offcanvases Loop --}}
    @foreach($structures as $structure)
    <div class="offcanvas offcanvas-end w-75" tabindex="-1" id="editStructureOffcanvas{{ $structure->id }}">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Edit Salary Structure</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.settings.salary-structures.update', $structure->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Structure Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ $structure->name }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" name="description" value="{{ $structure->description }}">
                    </div>
                    <div class="col-md-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $structure->is_active ? 'checked' : '' }}>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>

                @php
                    $structureComponents = $structure->components->keyBy('id');
                @endphp
                @include('HRM::pages.settings.salary_structures.partials.components_table', ['prefix' => 'edit_' . $structure->id, 'currentStructureComponents' => $structureComponents])

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Update Structure</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <script>
        // Use event delegation or simple class selection for toggling
        document.addEventListener('change', function(e) {
            if (e.target.matches('.check-all-earnings')) {
                const form = e.target.closest('form');
                form.querySelectorAll('.earning-check').forEach(c => c.checked = e.target.checked);
            }
            if (e.target.matches('.check-all-deductions')) {
                const form = e.target.closest('form');
                form.querySelectorAll('.deduction-check').forEach(c => c.checked = e.target.checked);
            }
        });
    </script>
@endsection
