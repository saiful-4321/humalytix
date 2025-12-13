@extends('Main::layouts.app')

@section('title', 'OKRs')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">OKR Management</h4>
            <p class="text-muted mb-0">Manage Objectives and Key Results</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#createOkrOffcanvas">
            <i class="bx bx-plus"></i> Add OKR
        </button>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Level</label>
                    <select name="level" class="form-select">
                        <option value="">All Levels</option>
                        <option value="company" {{ request('level') == 'company' ? 'selected' : '' }}>Company</option>
                        <option value="department" {{ request('level') == 'department' ? 'selected' : '' }}>Department</option>
                        <option value="individual" {{ request('level') == 'individual' ? 'selected' : '' }}>Individual</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quarter</label>
                    <select name="quarter" class="form-select">
                        <option value="">All Quarters</option>
                        <option value="Q1">Q1</option>
                        <option value="Q2">Q2</option>
                        <option value="Q3">Q3</option>
                        <option value="Q4">Q4</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Year</label>
                    <input type="number" name="year" class="form-control" value="{{ request('year', $currentYear) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary me-2"><i class="bx bx-search"></i></button>
                    <a href="{{ route('hrm.okrs.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- OKRs List -->
    <div class="card">
        <div class="card-body">
            @if($okrs->isEmpty())
                <div class="text-center py-5">
                    <i class="bx bx-bullseye display-1 text-muted"></i>
                    <p class="text-muted mt-3">No OKRs found</p>
                </div>
            @else
                <div class="row">
                    @foreach($okrs as $okr)
                    <div class="col-md-6 mb-3">
                        <div class="card border">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">{{ $okr->title }}</h6>
                                        <small class="text-muted">
                                            <span class="badge bg-{{ $okr->level == 'company' ? 'primary' : ($okr->level == 'department' ? 'info' : 'secondary') }}">
                                                {{ ucfirst($okr->level) }}
                                            </span>
                                            @if($okr->quarter)
                                                <span class="badge bg-dark">{{ $okr->quarter }} {{ $okr->year }}</span>
                                            @endif
                                        </small>
                                    </div>
                                    <span class="badge bg-{{ $okr->status == 'active' ? 'success' : ($okr->status == 'completed' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst($okr->status) }}
                                    </span>
                                </div>
                                
                                @if($okr->description)
                                    <p class="text-muted small mb-2">{{ Str::limit($okr->description, 80) }}</p>
                                @endif

                                <div class="mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">Overall Progress</small>
                                        <small class="fw-bold">{{ $okr->progress }}%</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar" style="width: {{ $okr->progress }}%"></div>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted d-block mb-1">Key Results ({{ $okr->keyResults->count() }})</small>
                                    @foreach($okr->keyResults->take(2) as $kr)
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="bx bx-target-lock text-primary me-1"></i>
                                            <small>{{ Str::limit($kr->description, 50) }} - {{ $kr->progress }}%</small>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('hrm.okrs.show', $okr) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-show"></i> View
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger delete-okr" data-id="{{ $okr->id }}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-3">{{ $okrs->links() }}</div>
            @endif
        </div>
    </div>
</div>

<!-- Create OKR Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="createOkrOffcanvas" style="width: 650px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Create OKR</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="createOkrForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Objective Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" placeholder="E.g., Increase Market Share" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Describe the objective..."></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Level <span class="text-danger">*</span></label>
                    <select name="level" id="okr_level" class="form-select" required>
                        <option value="company">Company-wide</option>
                        <option value="department">Department</option>
                        <option value="individual">Individual</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="draft">Draft</option>
                        <option value="active" selected>Active</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>

            <div class="mb-3" id="department_field" style="display:none;">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select">
                    <option value="">-- Select Department --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3" id="employee_field" style="display:none;">
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-select">
                    <option value="">-- Select Employee --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Quarter</label>
                    <select name="quarter" class="form-select">
                        <option value="">None</option>
                        <option value="Q1">Q1</option>
                        <option value="Q2">Q2</option>
                        <option value="Q3">Q3</option>
                        <option value="Q4">Q4</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Year <span class="text-danger">*</span></label>
                    <input type="number" name="year" class="form-control" value="{{ $currentYear }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">End Date <span class="text-danger">*</span></label>
                <input type="date" name="end_date" class="form-control" required>
            </div>

            <hr>
            <h6>Key Results</h6>
            <div id="keyResultsList">
                <div class="key-result-item mb-3 border p-3 rounded">
                    <div class="mb-2">
                        <input type="text" name="key_results[0][description]" class="form-control" placeholder="Key Result Description *" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="key_results[0][measurement_unit]" class="form-control" placeholder="Unit (%, $, etc)">
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="key_results[0][target_value]" class="form-control" placeholder="Target *" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="key_results[0][weightage]" class="form-control" placeholder="Weight %" value="25" min="0" max="100" required>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-secondary" id="addKeyResult">
                <i class="bx bx-plus"></i> Add Key Result
            </button>

            <div class="d-flex gap-2 justify-content-end mt-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Create OKR</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let krIndex = 1;

$(document).ready(function() {
    // Show/hide fields based on level
    $('#okr_level').on('change', function() {
        const level = $(this).val();
        $('#department_field').toggle(level === 'department');
        $('#employee_field').toggle(level === 'individual');
    });

    // Add key result
    $('#addKeyResult').on('click', function() {
        const html = `
            <div class="key-result-item mb-3 border p-3 rounded">
                <div class="mb-2">
                    <input type="text" name="key_results[${krIndex}][description]" class="form-control" placeholder="Key Result Description *" required>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="key_results[${krIndex}][measurement_unit]" class="form-control" placeholder="Unit">
                    </div>
                    <div class="col-md-4">
                        <input type="number" name="key_results[${krIndex}][target_value]" class="form-control" placeholder="Target *" step="0.01" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="key_results[${krIndex}][weightage]" class="form-control" placeholder="Weight %" value="25" min="0" max="100" required>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-kr"><i class="bx bx-trash"></i></button>
                    </div>
                </div>
            </div>
        `;
        $('#keyResultsList').append(html);
        krIndex++;
    });

    $(document).on('click', '.remove-kr', function() {
        $(this).closest('.key-result-item').remove();
    });

    $('#createOkrForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("hrm.okrs.store") }}',
            method: 'POST',
            data: new FormData(this),
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

    $('.delete-okr').on('click', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This OKR will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/hrm/okrs/${id}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        location.reload();
                    }
                });
            }
        });
    });
});
</script>
@endpush
