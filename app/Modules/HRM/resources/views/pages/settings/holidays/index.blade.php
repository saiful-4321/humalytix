@extends("HRM::layouts.settings")

@section("title", "Holiday Calendar")
@section("breadcrumb")
    <li class="breadcrumb-item active">Attendance Settings</li>
    <li class="breadcrumb-item active">Holiday Calendar</li>
@endsection

@section("settings-content")
    <div class="card bg-white">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between py-1">
                <h6 class="font-weight-medium mb-0">Holidays List</h6>
                <div class="d-flex gap-2">
                    <form action="{{ route('hrm.settings.holidays.index') }}" method="GET" class="d-flex align-items-center">
                        <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                            @for($i = date('Y')-1; $i <= date('Y')+3; $i++)
                            <option value="{{ $i }}" {{ request('year', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </form>
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#createHolidayOffcanvas">
                        <i class="mdi mdi-plus me-1"></i> Add Holiday
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Holiday Name</th>
                            <th>Date(s)</th>
                            <th>Type</th>
                            <th>Recurring</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($holidays as $holiday)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $holiday->name }}</div>
                                @if($holiday->description)
                                <small class="text-muted">{{ Str::limit($holiday->description, 30) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($holiday->start_date->eq($holiday->end_date))
                                    {{ $holiday->start_date->format('d M, Y') }}
                                    <div class="text-muted small">{{ $holiday->start_date->format('l') }}</div>
                                @else
                                    {{ $holiday->start_date->format('d M') }} - {{ $holiday->end_date->format('d M, Y') }}
                                    <div class="text-muted small">{{ $holiday->start_date->diffInDays($holiday->end_date) + 1 }} Days</div>
                                @endif
                            </td>
                            <td>
                                @if($holiday->type == 'public')
                                    <span class="badge bg-primary">Public</span>
                                @elseif($holiday->type == 'company')
                                    <span class="badge bg-info">Company</span>
                                @else
                                    <span class="badge bg-warning text-dark">Weekend</span>
                                @endif
                            </td>
                            <td>
                                @if($holiday->is_recurring)
                                    <i class="mdi mdi-check-circle text-success font-size-18" title="Recurring Annually"></i>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($holiday->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button class="btn btn-sm btn-soft-info" onclick="editHoliday({{ json_encode($holiday) }})">
                                        <i class="mdi mdi-pencil-outline"></i>
                                    </button>
                                    <form action="{{ route('hrm.settings.holidays.destroy', $holiday->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" onclick="return confirm('Delete this holiday?')">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="mdi mdi-calendar-blank font-size-24 d-block mb-2"></i>
                                No holidays found for {{ request('year', date('Y')) }}.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create Holiday Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="createHolidayOffcanvas" style="width: 500px;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title">Add Holiday</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.settings.holidays.store') }}" method="POST" id="createHolidayForm">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label">Holiday Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="end_date" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="type" required>
                        <option value="public">Public Holiday</option>
                        <option value="company">Company Holiday</option>
                        <option value="weekend">Weekend</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_recurring" value="1" id="recurringCheck" checked>
                        <label class="form-check-label" for="recurringCheck">Recurring Annually</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck" checked>
                        <label class="form-check-label" for="activeCheck">Active</label>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Holiday</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Holiday Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="editHolidayOffcanvas" style="width: 500px;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title">Edit Holiday</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="editHolidayForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Holiday Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="edit_name" required>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="start_date" id="edit_start_date" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="end_date" id="edit_end_date" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="type" id="edit_type" required>
                        <option value="public">Public Holiday</option>
                        <option value="company">Company Holiday</option>
                        <option value="weekend">Weekend</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_recurring" value="1" id="edit_recurring">
                        <label class="form-check-label" for="edit_recurring">Recurring Annually</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_active">
                        <label class="form-check-label" for="edit_active">Active</label>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Holiday</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function editHoliday(holiday) {
        document.getElementById('edit_name').value = holiday.name;
        document.getElementById('edit_start_date').value = holiday.start_date.substring(0, 10);
        document.getElementById('edit_end_date').value = holiday.end_date.substring(0, 10);
        document.getElementById('edit_type').value = holiday.type;
        document.getElementById('edit_description').value = holiday.description;
        
        document.getElementById('edit_recurring').checked = holiday.is_recurring ? true : false;
        document.getElementById('edit_active').checked = holiday.is_active ? true : false;
        
        let url = "{{ route('hrm.settings.holidays.update', ':id') }}";
        url = url.replace(':id', holiday.id);
        document.getElementById('editHolidayForm').action = url;
        
        var bsOffcanvas = new bootstrap.Offcanvas(document.getElementById('editHolidayOffcanvas'));
        bsOffcanvas.show();
    }
    </script>
@endsection
