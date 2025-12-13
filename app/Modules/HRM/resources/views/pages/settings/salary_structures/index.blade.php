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
                <a href="{{ route('hrm.settings.salary-structures.create') }}" class="btn btn-primary btn-sm d-flex align-items-center font-weight-medium">
                    <i class="mdi mdi-plus me-1"></i> Add Structure
                </a>
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
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted font-size-16 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('hrm.settings.salary-structures.edit', $structure->id) }}"><i class="bx bx-edit me-2"></i> Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('hrm.settings.salary-structures.destroy', $structure->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure? This will affect employees assigned to this structure.')"><i class="bx bx-trash me-2"></i> Delete</button>
                                            </form>
                                        </li>
                                    </ul>
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
@endsection
