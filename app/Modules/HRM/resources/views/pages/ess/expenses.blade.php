@extends('HRM::layouts.master')

@section('title', 'Expense Claims | ESS')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                     <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Expense Claims</h4>
                        <div class="page-title-right">
                             <button class="btn btn-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#expenseOffcanvas">
                                <i class="bx bx-plus"></i> New Claim
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                     <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Your Claims</h4>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($expenses as $expense)
                                        <tr>
                                            <td>{{ $expense->date->format('d M, Y') }}</td>
                                            <td class="fw-bold">{{ $expense->title }}</td>
                                            <td>{{ $expense->category ?? '-' }}</td>
                                            <td>{{ number_format($expense->amount, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $expense->status == 'pending' ? 'warning' : ($expense->status == 'approved' ? 'success' : 'danger') }}">
                                                    {{ ucfirst($expense->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $expense->remarks ?? '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No expense claims found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $expenses->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Expense Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="expenseOffcanvas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">New Expense Claim</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.ess.expenses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control" required max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" required placeholder="e.g. Travel to Client">
            </div>

            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="">Select Category</option>
                    <option value="Travel">Travel</option>
                    <option value="Meal">Meal</option>
                    <option value="Accommodation">Accommodation</option>
                    <option value="Office Supplies">Office Supplies</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Amount <span class="text-danger">*</span></label>
                <input type="number" name="amount" class="form-control" required step="0.01" min="0">
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Attachment</label>
                <input type="file" name="attachment" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary w-100">Submit Claim</button>
        </form>
    </div>
</div>
@endsection
