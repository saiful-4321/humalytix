@extends("HRM::layouts.settings")

@section("title", "Shift Setup")
@section("breadcrumb")
    <li class="breadcrumb-item active">Attendance Settings</li>
    <li class="breadcrumb-item active">Shift Setup</li>
@endsection

@section("settings-content")
    <div class="card bg-white">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between py-1">
                <h6 class="font-weight-medium mb-0">Work Shifts</h6>
                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#createShiftOffcanvas">
                    <i class="mdi mdi-plus me-1"></i> Add Shift
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Shift Name</th>
                            <th>Schedule</th>
                            <th>Duration</th>
                            <th>Grace Period</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $shift)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $shift->name }}</div>
                                <small class="text-muted">{{ $shift->code }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}
                                </span>
                            </td>
                            <td>
                                <div class="small">Work: {{ $shift->full_day_hours }}h</div>
                                <div class="small text-muted">Break: {{ $shift->break_duration_minutes }}m</div>
                            </td>
                            <td>{{ $shift->grace_period_minutes }} mins</td>
                            <td>
                                @if($shift->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="mdi mdi-dots-vertical font-size-18"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button type="button" class="dropdown-item" 
                                                onclick="editShift({{ json_encode($shift) }})">
                                                <i class="bx bx-edit me-2"></i> Edit
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('hrm.shifts.destroy', $shift->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Delete this shift?')">
                                                    <i class="bx bx-trash me-2"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="mdi mdi-clock-time-four-outline font-size-24 d-block mb-2"></i>
                                No shifts defined yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create Shift Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="createShiftOffcanvas" style="width: 500px;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title">Create New Shift</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.shifts.store') }}" method="POST" id="createShiftForm">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label">Shift Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required placeholder="e.g. Regular Shift">
                </div>

                <div class="mb-3">
                    <label class="form-label">Shift Code</label>
                    <input type="text" class="form-control" name="code" placeholder="e.g. REG-01">
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Start Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" name="start_time" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">End Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" name="end_time" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Grace Period (Min)</label>
                        <input type="number" class="form-control" name="grace_period_minutes" value="15" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Break Duration (Min)</label>
                        <input type="number" class="form-control" name="break_duration_minutes" value="60" min="0">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Full Day (Hours)</label>
                        <input type="number" class="form-control" name="full_day_hours" value="8" step="0.5" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Half Day (Hours)</label>
                        <input type="number" class="form-control" name="half_day_hours" value="4" step="0.5" min="0">
                    </div>
                </div>

                <div class="mb-3 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeCheck" checked>
                    <label class="form-check-label" for="activeCheck">Active Shift</label>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Shift</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Shift Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="editShiftOffcanvas" style="width: 500px;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title">Edit Shift</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="editShiftForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Shift Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="edit_name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Shift Code</label>
                    <input type="text" class="form-control" name="code" id="edit_code">
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Start Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" name="start_time" id="edit_start_time" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">End Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" name="end_time" id="edit_end_time" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Grace Period (Min)</label>
                        <input type="number" class="form-control" name="grace_period_minutes" id="edit_grace_period" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Break Duration (Min)</label>
                        <input type="number" class="form-control" name="break_duration_minutes" id="edit_break_duration" min="0">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Full Day (Hours)</label>
                        <input type="number" class="form-control" name="full_day_hours" id="edit_full_day" step="0.5" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Half Day (Hours)</label>
                        <input type="number" class="form-control" name="half_day_hours" id="edit_half_day" step="0.5" min="0">
                    </div>
                </div>

                <div class="mb-3 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active">
                    <label class="form-check-label" for="edit_is_active">Active Shift</label>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Shift</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function editShift(shift) {
        document.getElementById('edit_name').value = shift.name;
        document.getElementById('edit_code').value = shift.code;
        
        // Extract time properly (HH:mm)
        document.getElementById('edit_start_time').value = shift.start_time.substring(0, 5);
        document.getElementById('edit_end_time').value = shift.end_time.substring(0, 5);
        
        document.getElementById('edit_grace_period').value = shift.grace_period_minutes;
        document.getElementById('edit_break_duration').value = shift.break_duration_minutes;
        document.getElementById('edit_full_day').value = shift.full_day_hours;
        document.getElementById('edit_half_day').value = shift.half_day_hours;
        document.getElementById('edit_is_active').checked = shift.is_active ? true : false;
        
        // Set Action URL
        let url = "{{ route('hrm.shifts.update', ':id') }}";
        url = url.replace(':id', shift.id);
        document.getElementById('editShiftForm').action = url;
        
        // Show Offcanvas
        var bsOffcanvas = new bootstrap.Offcanvas(document.getElementById('editShiftOffcanvas'));
        bsOffcanvas.show();
    }
    </script>
@endsection
