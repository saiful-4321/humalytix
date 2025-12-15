@extends('Main::layouts.app')

@section('title', 'OKRs')

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>OKRs</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Performance</li>
                <li class="breadcrumb-item active">OKRs</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                 <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">OKRs List</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-soft-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
                        </button>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#createOkrOffcanvas">
                            <i class="mdi mdi-plus me-1"></i> Add OKR
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Objective</th>
                                <th>Owner</th>
                                <th>Period</th>
                                <th>Level</th>
                                <th>Progress</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($okrs as $okr)
                            <tr>
                                <td>
                                    <h6 class="mb-0 font-size-14">{{ $okr->title }}</h6>
                                    <small class="text-muted">{{ Str::limit($okr->description, 50) }}</small>
                                </td>
                                <td>
                                    @if($okr->level == 'company')
                                        <span class="badge bg-soft-primary text-primary">Company</span>
                                    @elseif($okr->level == 'department')
                                        <span class="badge bg-soft-info text-info">{{ $okr->department->name ?? 'Dept' }}</span>
                                    @else
                                        <span class="badge bg-soft-secondary text-secondary">Individual</span>
                                        <div class="small mt-1">{{ $okr->employee->full_name ?? '' }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $okr->year }} {{ $okr->quarter }}</div>
                                    <small class="text-muted">{{ $okr->start_date->format('M d') }} - {{ $okr->end_date->format('M d') }}</small>
                                </td>
                                <td>{{ ucfirst($okr->level) }}</td>
                                <td style="width: 200px;">
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $okr->progress }}%"></div>
                                        </div>
                                        <span class="fw-bold">{{ $okr->progress }}%</span>
                                    </div>
                                    <small class="text-muted">{{ $okr->status }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('hrm.okrs.show', $okr->id) }}" class="btn btn-sm btn-soft-primary" title="Details">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                        <button class="btn btn-sm btn-soft-primary edit-okr" 
                                            data-id="{{ $okr->id }}"
                                            data-bs-toggle="offcanvas" 
                                            data-bs-target="#editOkrOffcanvas"
                                            title="Edit">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </button>
                                        <button class="btn btn-sm btn-soft-danger delete-okr" data-id="{{ $okr->id }}" title="Delete">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-target font-size-24 d-block mb-2"></i>
                                    No OKRs found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($okrs->count())
            <div class="card-footer bg-transparent border-top">
                <div class="d-flex justify-content-end">
                    {{ $okrs->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Filter Options</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form method="GET" action="{{ route('hrm.okrs.index') }}">
            <div class="mb-3">
                <label class="form-label">Level</label>
                <select name="level" class="form-select">
                    <option value="">All Levels</option>
                    <option value="company" {{ request('level') == 'company' ? 'selected' : '' }}>Company</option>
                    <option value="department" {{ request('level') == 'department' ? 'selected' : '' }}>Department</option>
                    <option value="individual" {{ request('level') == 'individual' ? 'selected' : '' }}>Individual</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Period</label>
                <div class="row g-2">
                    <div class="col-6">
                        <select name="year" class="form-select">
                            <option value="">Year</option>
                            @for($y = date('Y'); $y >= date('Y')-2; $y--)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-6">
                        <select name="quarter" class="form-select">
                            <option value="">Quarter</option>
                            <option value="Q1" {{ request('quarter') == 'Q1' ? 'selected' : '' }}>Q1</option>
                            <option value="Q2" {{ request('quarter') == 'Q2' ? 'selected' : '' }}>Q2</option>
                            <option value="Q3" {{ request('quarter') == 'Q3' ? 'selected' : '' }}>Q3</option>
                            <option value="Q4" {{ request('quarter') == 'Q4' ? 'selected' : '' }}>Q4</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search objectives..." value="{{ request('search') }}">
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('hrm.okrs.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
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

<!-- Edit OKR Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="editOkrOffcanvas" style="width: 650px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Edit OKR</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="editOkrForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Objective Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Level <span class="text-danger">*</span></label>
                    <select name="level" id="edit_okr_level" class="form-select" required>
                        <option value="company">Company-wide</option>
                        <option value="department">Department</option>
                        <option value="individual">Individual</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="mb-3" id="edit_department_field" style="display:none;">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select">
                    <option value="">-- Select Department --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3" id="edit_employee_field" style="display:none;">
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
                    <input type="number" name="year" class="form-control" required>
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

            <div class="d-flex gap-2 justify-content-end mt-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Update OKR</button>
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

    // Show/hide fields based on level (Edit Form)
    $('#edit_okr_level').on('change', function() {
        const level = $(this).val();
        $('#edit_department_field').toggle(level === 'department');
        $('#edit_employee_field').toggle(level === 'individual');
    });

    // Edit OKR
    $('.edit-okr').on('click', function() {
        const id = $(this).data('id');
        $.get(`/hrm/okrs/${id}`, function(response) {
            if(response.success) {
                const okr = response.data;
                const form = $('#editOkrForm');
                
                form.find('[name="title"]').val(okr.title);
                form.find('[name="description"]').val(okr.description);
                form.find('[name="level"]').val(okr.level).trigger('change');
                form.find('[name="status"]').val(okr.status);
                form.find('[name="year"]').val(okr.year);
                form.find('[name="quarter"]').val(okr.quarter);
                
                // Format dates to YYYY-MM-DD
                const startDate = new Date(okr.start_date).toISOString().split('T')[0];
                const endDate = new Date(okr.end_date).toISOString().split('T')[0];
                
                form.find('[name="start_date"]').val(startDate);
                form.find('[name="end_date"]').val(endDate);

                if(okr.department_id) {
                    form.find('[name="department_id"]').val(okr.department_id);
                }
                if(okr.employee_id) {
                    form.find('[name="employee_id"]').val(okr.employee_id);
                }

                // Set form action
                form.attr('action', `/hrm/okrs/${id}`);
            }
        });
    });

    $('#editOkrForm').on('submit', function(e) {
        e.preventDefault();
        const url = $(this).attr('action');
        
        $.ajax({
            url: url,
            method: 'PUT',
            data: $(this).serialize(),
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
