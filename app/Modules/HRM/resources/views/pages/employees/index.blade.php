@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Employees <small>({{ $employees->total() }} total)</small></h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Employees</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12 col-md-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Employees - (showing {{ $employees->firstItem()??0 }} to {{ $employees->lastItem()??0 }} of total {{ $employees->total()??0 }} entries)</h6>
                    
                    <div class="d-flex align-items-center gap-2">
                        @can('hrm.employees.create')
                        <a href="{{ route('hrm.employees.create') }}" class="btn btn-info btn-sm d-flex align-items-center font-weight-medium">
                            <i class="mdi mdi-plus me-1"></i> Add New Employee
                        </a>
                        @endcan
                        
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#employeeFilter" aria-controls="employeeFilter">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter
                        </button>

                        @can('hrm.employees.export')
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-export me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('hrm.employees.export', ['format' => 'xlsx']) }}">Excel (XLSX)</a></li>
                                <li><a class="dropdown-item" href="{{ route('hrm.employees.export', ['format' => 'pdf']) }}">PDF</a></li>
                            </ul>
                        </div>
                        @endcan
                        
                        @can('hrm.employees.import')
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="offcanvas" data-bs-target="#importEmployeeOffcanvas">
                            <i class="mdi mdi-import me-1"></i> Import
                        </button>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Active Filters Section -->
            @if(request()->hasAny(['search', 'department_id', 'branch_id', 'status']))
                <x-Main::active-filters :url="route('hrm.employees.index')">
                    <x-Main::active-filter-item key="search" label="Search" :value="request('search')" />
                    <x-Main::active-filter-item key="department_id" label="Department" :value="$departments->where('id', request('department_id'))->first()->name ?? request('department_id')" />
                    <x-Main::active-filter-item key="branch_id" label="Branch" :value="$branches->where('id', request('branch_id'))->first()->name ?? request('branch_id')" />
                    <x-Main::active-filter-item key="status" label="Status" :value="ucfirst(str_replace('_', ' ', request('status')))" />
                </x-Main::active-filters>
            @endif

            <!-- Active Filters Section -->
            @if(request()->hasAny(['search', 'department_id', 'branch_id', 'status']))
                <x-Main::active-filters :url="route('hrm.employees.index')">
                    <x-Main::active-filter-item key="search" label="Search" :value="request('search')" />
                    <x-Main::active-filter-item key="department_id" label="Department" :value="$departments->where('id', request('department_id'))->first()->name ?? request('department_id')" />
                    <x-Main::active-filter-item key="branch_id" label="Branch" :value="$branches->where('id', request('branch_id'))->first()->name ?? request('branch_id')" />
                    <x-Main::active-filter-item key="status" label="Status" :value="ucfirst(str_replace('_', ' ', request('status')))" />
                </x-Main::active-filters>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Employee Code</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Branch</th>
                                <th>Designation</th>
                                <th>Employment Type</th>
                                <th>Joining Date</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                            <tr>
                                <td><strong>{{ $employee->employee_code }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($employee->photo)
                                        <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}" class="rounded-circle me-2" width="32" height="32">
                                        @else
                                        <div class="avatar-sm me-2">
                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                        @endif
                                        <div>
                                            <div class="font-weight-medium">{{ $employee->full_name }}</div>
                                            <small class="text-muted">{{ $employee->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $employee->department->name ?? 'N/A' }}</td>
                                <td>{{ $employee->branch->name ?? 'N/A' }}</td>
                                <td>{{ $employee->designation }}</td>
                                <td><span class="badge bg-soft-info text-info">{{ ucfirst(str_replace('_', ' ', $employee->employment_type)) }}</span></td>
                                <td>{{ $employee->joining_date?->format('d M, Y') }}</td>
                                <td>{!! $employee->status_badge !!}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('hrm.employees.view')
                                        <a href="{{ route('hrm.employees.show', $employee) }}" class="btn btn-sm btn-soft-info" title="View">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        @endcan
                                        @can('hrm.employees.edit')
                                        <a href="{{ route('hrm.employees.edit', $employee) }}" class="btn btn-sm btn-soft-primary" title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        @endcan
                                        @can('hrm.employees.delete')
                                        <form action="{{ route('hrm.employees.destroy', $employee) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger" title="Delete">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="mdi mdi-account-off mdi-48px d-block mb-2"></i>
                                        No employees found
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if (!empty($employees->count()))
                <div class="d-flex justify-content-end mt-3 mb-0">
                    {!! $employees->appends(request()->all())->links() !!}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="employeeFilter" aria-labelledby="employeeFilterLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="employeeFilterLabel">Filter Employees</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.employees.index') }}" method="GET">
            <div class="mb-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name, email, code...">
            </div>

            <div class="mb-3">
                <label for="department_id" class="form-label">Department</label>
                <select class="form-select" id="department_id" name="department_id">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="branch_id" class="form-label">Branch</label>
                <select class="form-select" id="branch_id" name="branch_id">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="probation" {{ request('status') == 'probation' ? 'selected' : '' }}>Probation</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="notice_period" {{ request('status') == 'notice_period' ? 'selected' : '' }}>Notice Period</option>
                    <option value="resigned" {{ request('status') == 'resigned' ? 'selected' : '' }}>Resigned</option>
                </select>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('hrm.employees.index') }}" class="btn btn-secondary">Clear Filters</a>
            </div>
        </form>
    </div>
</div>

<!-- Import Offcanvas -->
@can('hrm.employees.import')
<div class="offcanvas offcanvas-end w-50" tabindex="-1" id="importEmployeeOffcanvas" aria-labelledby="importEmployeeOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="importEmployeeOffcanvasLabel">Import Employees</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.employees.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label for="importFile" class="form-label mb-0">Upload File</label>
                    <div class="font-size-12">
                        <span class="text-muted me-1">Sample:</span>
                        <a href="{{ route('hrm.employees.sample') }}" class="badge bg-soft-success text-success text-decoration-none border border-success">
                            <i class="mdi mdi-download"></i> Download Sample
                        </a>
                    </div>
                </div>
                <input class="form-control" type="file" id="importFile" name="file" required accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="mdi mdi-check-circle me-1"></i> Import Employees
                </button>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection
