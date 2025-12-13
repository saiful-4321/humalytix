@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>HRM Settings</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item active">Settings</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-3">
        <!-- Vertical Settings Menu -->
        <div class="card bg-white">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <!-- Payroll Settings -->
                    <div class="list-group-item bg-light border-0">
                        <h6 class="text-uppercase text-muted mb-0 font-size-11 fw-bold">
                            <i class="mdi mdi-cash-multiple me-1"></i> Payroll Configuration
                        </h6>
                    </div>
                    <a href="{{ route('hrm.settings.salary-components.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('hrm.settings.salary-components.*') ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-cash-plus me-2 font-size-18"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 font-size-13">Salary Components</h6>
                                <small class="text-muted">Earnings & Deductions</small>
                            </div>
                            <i class="mdi mdi-chevron-right"></i>
                        </div>
                    </a>
                    <a href="{{ route('hrm.settings.salary-structures.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('hrm.settings.salary-structures.*') ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-file-tree me-2 font-size-18"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 font-size-13">Salary Structures</h6>
                                <small class="text-muted">Templates & Grades</small>
                            </div>
                            <i class="mdi mdi-chevron-right"></i>
                        </div>
                    </a>
                    <a href="{{ route('hrm.settings.tax-slabs.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('hrm.settings.tax-slabs.*') ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-percent me-2 font-size-18"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 font-size-13">Tax Slabs</h6>
                                <small class="text-muted">Income Tax Rules</small>
                            </div>
                            <i class="mdi mdi-chevron-right"></i>
                        </div>
                    </a>

                    <!-- Leave Settings -->
                    <div class="list-group-item bg-light border-0 mt-2">
                        <h6 class="text-uppercase text-muted mb-0 font-size-11 fw-bold">
                            <i class="mdi mdi-calendar-check me-1"></i> Leave Management
                        </h6>
                    </div>
                    <a href="{{ route('hrm.settings.leave-types.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('hrm.settings.leave-types.*') ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-calendar-clock me-2 font-size-18"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 font-size-13">Leave Policies</h6>
                                <small class="text-muted">Types & Rules</small>
                            </div>
                            <i class="mdi mdi-chevron-right"></i>
                        </div>
                    </a>
                    <a href="{{ route('hrm.settings.approval-chains.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('hrm.settings.approval-chains.*') ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-account-multiple-check me-2 font-size-18"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 font-size-13">Approval Chains</h6>
                                <small class="text-muted">Workflow Setup</small>
                            </div>
                            <i class="mdi mdi-chevron-right"></i>
                        </div>
                    </a>

                    <!-- Document Templates -->
                    <div class="list-group-item bg-light border-0 mt-2">
                        <h6 class="text-uppercase text-muted mb-0 font-size-11 fw-bold">
                            <i class="mdi mdi-file-document-multiple me-1"></i> Documents
                        </h6>
                    </div>
                    <a href="{{ route('hrm.settings.letter-templates.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('hrm.settings.letter-templates.*') ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-text-box-multiple me-2 font-size-18"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 font-size-13">Letter Templates</h6>
                                <small class="text-muted">Digital Documents</small>
                            </div>
                            <i class="mdi mdi-chevron-right"></i>
                        </div>
                    </a>
                    <a href="{{ route('hrm.document-types.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('hrm.document-types.*') ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-file-cog me-2 font-size-18"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 font-size-13">Document Types</h6>
                                <small class="text-muted">Upload Categories</small>
                            </div>
                            <i class="mdi mdi-chevron-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card bg-gradient-primary text-white border-0 mt-3">
            <div class="card-body">
                <h6 class="text-white mb-3">Configuration Status</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="font-size-13">Components</span>
                    <span class="fw-bold">{{ \App\Modules\HRM\Models\SalaryComponent::count() }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="font-size-13">Structures</span>
                    <span class="fw-bold">{{ \App\Modules\HRM\Models\SalaryStructure::count() }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="font-size-13">Leave Types</span>
                    <span class="fw-bold">{{ \App\Modules\HRM\Models\LeaveType::count() }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="font-size-13">Document Types</span>
                    <span class="fw-bold">{{ \App\Modules\HRM\Models\DocumentType::count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <!-- Welcome Card -->
        <div class="card bg-white">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="mdi mdi-cog-outline font-size-48 text-primary"></i>
                </div>
                <h4 class="mb-3">HRM Settings Dashboard</h4>
                <p class="text-muted mb-4">
                    Configure your payroll, leave management, and document templates from the menu on the left.<br>
                    All your HRM settings are organized for easy access and management.
                </p>
                
                <div class="row mt-5">
                    <div class="col-md-4">
                        <div class="p-3 border rounded">
                            <i class="mdi mdi-shield-check-outline text-success font-size-24 d-block mb-2"></i>
                            <h6 class="mb-1">Secure</h6>
                            <p class="text-muted small mb-0">Role-based access control</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded">
                            <i class="mdi mdi-lightning-bolt-outline text-warning font-size-24 d-block mb-2"></i>
                            <h6 class="mb-1">Fast</h6>
                            <p class="text-muted small mb-0">Quick configuration updates</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded">
                            <i class="mdi mdi-chart-line text-info font-size-24 d-block mb-2"></i>
                            <h6 class="mb-1">Scalable</h6>
                            <p class="text-muted small mb-0">Grows with your business</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.list-group-item-action.active {
    background-color: #556ee6;
    border-color: #556ee6;
    color: white;
}
.list-group-item-action.active small {
    color: rgba(255,255,255,0.8);
}
.list-group-item-action:hover {
    background-color: #f8f9fa;
}
.list-group-item-action.active:hover {
    background-color: #4961dc;
}
</style>
@endsection
