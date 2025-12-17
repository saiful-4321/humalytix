@extends("Main::layouts.app")

@section("title", "Expense Management")

@section("content")
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Expense Management</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                    <li class="breadcrumb-item active">Expenses</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#my-expenses" role="tab">
                            <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                            <span class="d-none d-sm-block">My Expenses</span>
                        </a>
                    </li>
                    @can('hrm.expenses.approve')
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#approvals" role="tab">
                            <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                            <span class="d-none d-sm-block">Pending Approvals 
                                @if($pendingApprovals->count() > 0)
                                    <span class="badge bg-danger rounded-pill">{{ $pendingApprovals->count() }}</span>
                                @endif
                            </span>
                        </a>
                    </li>
                    @endcan
                </ul>

                <!-- Tab panes -->
                <div class="tab-content p-3 text-muted">
                    <!-- My Expenses Tab -->
                    <div class="tab-pane active" id="my-expenses" role="tabpanel">
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#addExpenseCanvas">
                                <i class="bx bx-plus me-1"></i> New Claim
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Receipt</th>
                                        <th>Status</th>
                                        <th>Approved/Rejected By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($myExpenses as $expense)
                                        <tr>
                                            <td>{{ $expense->expense_date->format('d M, Y') }}</td>
                                            <td>{{ $expense->category->name }}</td>
                                            <td title="{{ $expense->description }}">{{ Str::limit($expense->description, 30) }}</td>
                                            <td>{{ number_format($expense->amount, 2) }}</td>
                                            <td>
                                                @if($expense->receipt_path)
                                                    <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank" class="text-primary">View</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($expense->status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($expense->status == 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @elseif($expense->status == 'paid')
                                                    <span class="badge bg-info">Paid</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>{{ $expense->approver->name ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No expenses found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $myExpenses->links() }}
                        </div>
                    </div>

                    @can('hrm.expenses.approve')
                    <!-- Approvals Tab -->
                    <div class="tab-pane" id="approvals" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Receipt</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingApprovals as $expense)
                                        <tr>
                                            <td>{{ $expense->employee->full_name }}</td>
                                            <td>{{ $expense->expense_date->format('d M, Y') }}</td>
                                            <td>{{ $expense->category->name }}</td>
                                            <td title="{{ $expense->description }}">{{ Str::limit($expense->description, 30) }}</td>
                                            <td>{{ number_format($expense->amount, 2) }}</td>
                                            <td>
                                                @if($expense->receipt_path)
                                                    <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank" class="text-primary">View</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <form action="{{ route('hrm.expenses.approve', $expense->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-success confirm-action" data-message="Approve this expense?">
                                                            <i class="bx bx-check"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $expense->id }}">
                                                        <i class="bx bx-x"></i>
                                                    </button>
                                                </div>

                                                <!-- Reject Modal -->
                                                <div class="modal fade" id="rejectModal{{ $expense->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Reject Expense</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('hrm.expenses.reject', $expense->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                                                                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-danger">Reject</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No pending approvals.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Expense Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="addExpenseCanvas" aria-labelledby="addExpenseCanvasLabel">
    <div class="offcanvas-header">
        <h5 id="addExpenseCanvasLabel">New Expense Claim</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.expenses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <select class="form-control" name="expense_category_id" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="expense_date" required value="{{ date('Y-m-d') }}">
            </div>

             <div class="mb-3">
                <label class="form-label">Amount <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" name="amount" required placeholder="0.00">
            </div>

            <div class="mb-3">
                <label class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control" name="description" rows="3" required placeholder="Details about the expense..."></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Receipt</label>
                <input type="file" class="form-control" name="receipt" accept="image/*,.pdf">
                <small class="text-muted">JPG, PNG or PDF (Max 2MB)</small>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Submit Claim</button>
            </div>
        </form>
    </div>
</div>

@endsection
