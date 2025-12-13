@extends('Main::layouts.app')

@section('title', 'Competencies')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Competency Framework</h4>
            <p class="text-muted mb-0">Manage competencies for performance appraisals</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#createCompetencyOffcanvas">
            <i class="bx bx-plus"></i> Add Competency
        </button>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="core" {{ request('type') == 'core' ? 'selected' : '' }}>Core</option>
                        <option value="functional" {{ request('type') == 'functional' ? 'selected' : '' }}>Functional</option>
                        <option value="leadership" {{ request('type') == 'leadership' ? 'selected' : '' }}>Leadership</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search competencies..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary me-2"><i class="bx bx-search"></i> Filter</button>
                    <a href="{{ route('hrm.competencies.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Competencies List -->
    <div class="card">
        <div class="card-body">
            @if($competencies->isEmpty())
                <div class="text-center py-5">
                    <i class="bx bx-medal display-1 text-muted"></i>
                    <p class="text-muted mt-3">No competencies found</p>
                    <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#createCompetencyOffcanvas">
                        <i class="bx bx-plus"></i> Create First Competency
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($competencies as $competency)
                            <tr>
                                <td><strong>{{ $competency->name }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $competency->type == 'core' ? 'primary' : ($competency->type == 'functional' ? 'info' : 'warning') }}">
                                        {{ ucfirst($competency->type) }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($competency->description, 60) }}</td>
                                <td>
                                    <span class="badge bg-{{ $competency->is_active ? 'success' : 'danger' }}">
                                        {{ $competency->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary edit-competency" 
                                            data-id="{{ $competency->id }}"
                                            data-bs-toggle="offcanvas" 
                                            data-bs-target="#editCompetencyOffcanvas">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-competency" data-id="{{ $competency->id }}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $competencies->links() }}</div>
            @endif
        </div>
    </div>
</div>

<!-- Create Competency Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="createCompetencyOffcanvas" style="width: 550px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Create Competency</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="createCompetencyForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="E.g., Communication Skills" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required>
                    <option value="core">Core (Essential for all employees)</option>
                    <option value="functional">Functional (Role-specific)</option>
                    <option value="leadership">Leadership (For managers)</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Describe this competency..."></textarea>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Competency Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="editCompetencyOffcanvas" style="width: 550px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Edit Competency</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="editCompetencyForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="competency_id" id="edit_competency_id">
            
            <div class="mb-3">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Type <span class="text-danger">*</span></label>
                <select name="type" id="edit_type" class="form-select" required>
                    <option value="core">Core</option>
                    <option value="functional">Functional</option>
                    <option value="leadership">Leadership</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" id="edit_description" class="form-control" rows="4"></textarea>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active">
                    <label class="form-check-label" for="edit_is_active">Active</label>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#createCompetencyForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.set('is_active', $('#isActive').is(':checked') ? 1 : 0);
        
        $.ajax({
            url: '{{ route("hrm.competencies.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                location.reload();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'An error occurred', 'error');
            }
        });
    });

    $('.edit-competency').on('click', function() {
        const id = $(this).data('id');
        $.get(`/hrm/competencies/${id}`, function(data) {
            $('#edit_competency_id').val(data.id);
            $('#edit_name').val(data.name);
            $('#edit_type').val(data.type);
            $('#edit_description').val(data.description);
            $('#edit_is_active').prop('checked', data.is_active);
        });
    });

    $('#editCompetencyForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_competency_id').val();
        const formData = new FormData(this);
        formData.set('is_active', $('#edit_is_active').is(':checked') ? 1 : 0);
        
        $.ajax({
            url: `/hrm/competencies/${id}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire('Success!', response.message, 'success');
                location.reload();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'An error occurred', 'error');
            }
        });
    });

    $('.delete-competency').on('click', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This competency will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/hrm/competencies/${id}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        location.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to delete', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
