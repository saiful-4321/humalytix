@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12">
            <h2>Employee Profile</h2>
        </div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.employees.index') }}">Employees</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <!-- Employee Header Card -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        @if($employee->photo)
                        <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}" class="rounded-circle" width="120" height="120">
                        @else
                        <div class="avatar-lg mx-auto">
                            <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-24">
                                {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                            </span>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-7">
                        <h4 class="mb-1">{{ $employee->full_name }}</h4>
                        <p class="text-muted mb-2">{{ $employee->designation }}</p>
                        <div class="mb-2">
                            <span class="badge bg-soft-info text-info me-1"><i class="bx bx-id-card"></i> {{ $employee->employee_code }}</span>
                            {!! $employee->status_badge !!}
                        </div>
                        <div class="text-muted">
                            <i class="bx bx-building me-1"></i> {{ $employee->department->name ?? 'N/A' }} | {{ $employee->branch->name ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="col-md-3 text-end">
                        @can('hrm.employees.edit')
                        <a href="{{ route('hrm.employees.edit', $employee) }}" class="btn btn-primary mb-2">
                            <i class="bx bx-edit"></i> Edit Profile
                        </a>
                        @endcan
                        <div class="text-muted small">
                            <div><strong>Joined:</strong> {{ $employee->joining_date?->format('d M, Y') }}</div>
                            <div><strong>Experience:</strong> {{ $employee->total_experience }} years</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#overview" role="tab">
                            <i class="bx bx-user-circle font-size-20"></i>
                            <span class="d-none d-sm-block">Overview</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#family" role="tab">
                            <i class="bx bx-group font-size-20"></i>
                            <span class="d-none d-sm-block">Family</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#documents" role="tab">
                            <i class="bx bx-file font-size-20"></i>
                            <span class="d-none d-sm-block">Documents</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#history" role="tab">
                            <i class="bx bx-history font-size-20"></i>
                            <span class="d-none d-sm-block">History</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#skills" role="tab">
                            <i class="bx bx-badge-check font-size-20"></i>
                            <span class="d-none d-sm-block">Skills</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content p-3">
                    <!-- Overview Tab -->
                    <div class="tab-pane active" id="overview" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">Personal Information</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <th width="40%">Full Name:</th>
                                        <td>{{ $employee->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>{{ $employee->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone:</th>
                                        <td>{{ $employee->phone ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date of Birth:</th>
                                        <td>{{ $employee->date_of_birth?->format('d M, Y') ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Gender:</th>
                                        <td>{{ ucfirst($employee->gender ?? 'N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Blood Group:</th>
                                        <td>{{ $employee->blood_group ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Marital Status:</th>
                                        <td>{{ ucfirst($employee->marital_status ?? 'N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nationality:</th>
                                        <td>{{ $employee->nationality ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h5 class="mb-3">Employment Information</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <th width="40%">Employee Code:</th>
                                        <td><strong>{{ $employee->employee_code }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Department:</th>
                                        <td>{{ $employee->department->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Branch:</th>
                                        <td>{{ $employee->branch->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Designation:</th>
                                        <td>{{ $employee->designation }}</td>
                                    </tr>
                                    <tr>
                                        <th>Employment Type:</th>
                                        <td>{{ ucfirst(str_replace('_', ' ', $employee->employment_type)) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Joining Date:</th>
                                        <td>{{ $employee->joining_date?->format('d M, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>{!! $employee->status_badge !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Reporting To:</th>
                                        <td>{{ $employee->reportingManager->full_name ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h5 class="mb-3">KYC Information</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <th width="20%">NID Number:</th>
                                        <td>{{ $employee->nid_number ?? 'N/A' }}</td>
                                        <th width="20%">Passport:</th>
                                        <td>{{ $employee->passport_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tax ID:</th>
                                        <td>{{ $employee->tax_id ?? 'N/A' }}</td>
                                        <th>Driving License:</th>
                                        <td>{{ $employee->driving_license ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($employee->subordinates->count() > 0)
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h5 class="mb-3">Team Members ({{ $employee->subordinates->count() }})</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Designation</th>
                                                <th>Department</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($employee->subordinates as $subordinate)
                                            <tr>
                                                <td><a href="{{ route('hrm.employees.show', $subordinate) }}">{{ $subordinate->full_name }}</a></td>
                                                <td>{{ $subordinate->designation }}</td>
                                                <td>{{ $subordinate->department->name ?? 'N/A' }}</td>
                                                <td>{!! $subordinate->status_badge !!}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Family Tab -->
                    <div class="tab-pane" id="family" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Family Members</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addFamilyModal"><i class="bx bx-plus"></i> Add Family Member</button>
                        </div>

                        @if($employee->familyMembers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Relationship</th>
                                        <th>Date of Birth</th>
                                        <th>Contact</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employee->familyMembers as $member)
                                    <tr>
                                        <td>{{ $member->name }}</td>
                                        <td>{{ ucfirst($member->relationship) }}</td>
                                        <td>{{ $member->date_of_birth?->format('d M, Y') ?? 'N/A' }}</td>
                                        <td>{{ $member->contact_number ?? 'N/A' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-soft-primary"><i class="bx bx-edit"></i></button>
                                            <button class="btn btn-sm btn-soft-danger"><i class="bx bx-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4 text-muted">
                            <i class="bx bx-group bx-lg d-block mb-2"></i>
                            No family members added yet
                        </div>
                        @endif
                    </div>

                    <!-- Documents Tab -->
                    <div class="tab-pane" id="documents" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Documents</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal"><i class="bx bx-upload"></i> Upload Document</button>
                        </div>

                        @if($employee->documents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Document Type</th>
                                        <th>Document Number</th>
                                        <th>Issue Date</th>
                                        <th>Expiry Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employee->documents as $doc)
                                    <tr>
                                        <td>{{ $doc->documentType->name ?? 'N/A' }}</td>
                                        <td>{{ $doc->document_number ?? 'N/A' }}</td>
                                        <td>{{ $doc->issue_date?->format('d M, Y') ?? 'N/A' }}</td>
                                        <td>
                                            @if($doc->expiry_date)
                                            {{ $doc->expiry_date->format('d M, Y') }}
                                            @if($doc->is_expired)
                                            <span class="badge bg-danger">Expired</span>
                                            @endif
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($doc->is_verified)
                                            <span class="badge bg-success">Verified</span>
                                            @else
                                            <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($doc->file_path)
                                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-soft-info">
                                                <i class="bx bx-download"></i>
                                            </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4 text-muted">
                            <i class="bx bx-file bx-lg d-block mb-2"></i>
                            No documents uploaded yet
                        </div>
                        @endif
                    </div>

                    <!-- Employment History Tab -->
                    <div class="tab-pane" id="history" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Employment History</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addHistoryModal"><i class="bx bx-plus"></i> Add History</button>
                        </div>

                        @if($employee->employmentHistories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Designation</th>
                                        <th>Period</th>
                                        <th>Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employee->employmentHistories as $history)
                                    <tr>
                                        <td>{{ $history->company_name }}</td>
                                        <td>{{ $history->designation }}</td>
                                        <td>
                                            {{ $history->start_date?->format('M Y') }} - 
                                            {{ $history->end_date?->format('M Y') ?? 'Present' }}
                                        </td>
                                        <td>{{ $history->duration }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4 text-muted">
                            <i class="bx bx-history bx-lg d-block mb-2"></i>
                            No employment history added yet
                        </div>
                        @endif
                    </div>

                    <!-- Skills Tab -->
                    <div class="tab-pane" id="skills" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Skills & Competencies</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSkillModal"><i class="bx bx-plus"></i> Add Skill</button>
                        </div>

                        @if($employee->skills->count() > 0)
                        <div class="row">
                            @foreach($employee->skills as $employeeSkill)
                            <div class="col-md-6 mb-3">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0">{{ $employeeSkill->skill->name ?? 'N/A' }}</h6>
                                            <span class="badge bg-soft-primary text-primary">{{ ucfirst($employeeSkill->proficiency_level) }}</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            @php
                                            $percentage = match($employeeSkill->proficiency_level) {
                                                'beginner' => 25,
                                                'intermediate' => 50,
                                                'advanced' => 75,
                                                'expert' => 100,
                                                default => 0
                                            };
                                            @endphp
                                            <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        @if($employeeSkill->years_of_experience)
                                        <small class="text-muted mt-1 d-block">{{ $employeeSkill->years_of_experience }} years experience</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4 text-muted">
                            <i class="bx bx-badge-check bx-lg d-block mb-2"></i>
                            No skills added yet
                        </div>
                        @endif
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Family Member Modal -->
<div class="modal fade" id="addFamilyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hrm.employees.family.store', $employee) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Family Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Relationship <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="relationship" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" name="date_of_birth">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gender</label>
                            <select class="form-select" name="gender">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="text" class="form-control" name="contact_number">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_dependent" value="1" id="isDependent">
                            <label class="form-check-label" for="isDependent">Is Dependent</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Family Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hrm.employees.documents.store', $employee) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Document Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="document_type_id" required>
                            <option value="">Select Type</option>
                            @foreach($documentTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Document Number</label>
                        <input type="text" class="form-control" name="document_number">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Issue Date</label>
                            <input type="date" class="form-control" name="issue_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" name="expiry_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="file" required accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Max 10MB. Formats: PDF, JPG, PNG</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload Document</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Employment History Modal -->
<div class="modal fade" id="addHistoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hrm.employees.history.store', $employee) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Employment History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="company_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Designation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="designation" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Responsibilities</label>
                        <textarea class="form-control" name="responsibilities" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add History</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Skill Modal -->
<div class="modal fade" id="addSkillModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hrm.employees.skills.store', $employee) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Skill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Skill <span class="text-danger">*</span></label>
                        <select class="form-select" name="skill_id" required>
                            <option value="">Select Skill</option>
                            @foreach($allSkills as $skill)
                            <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Proficiency Level <span class="text-danger">*</span></label>
                        <select class="form-select" name="proficiency_level" required>
                            <option value="">Select Level</option>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                            <option value="expert">Expert</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Years of Experience</label>
                        <input type="number" class="form-control" name="years_of_experience" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Certification</label>
                        <input type="text" class="form-control" name="certification">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Skill</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Delete confirmation
function confirmDelete(form) {
    if (confirm('Are you sure you want to delete this item?')) {
        form.submit();
    }
}
</script>
@endpush
