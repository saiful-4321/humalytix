@extends('Main::layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Chart of Accounts</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Finance</a></li>
                    <li class="breadcrumb-item active">Accounts</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Account List</h4>
                <div class="flex-shrink-0">
                    @can('finance-coa-create')
                    <button class="btn btn-success add-btn" data-bs-toggle="offcanvas" data-bs-target="#addAccountOffcanvas">
                        <i class="ri-add-line align-bottom me-1"></i> Add Account
                    </button>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                        <thead class="text-muted table-light">
                            <tr>
                                <th scope="col">Code</th>
                                <th scope="col">Name</th>
                                <th scope="col">Type</th>
                                <th scope="col">Balance</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $account)
                                @include('Finance::pages.accounts.row', ['account' => $account, 'level' => 0])
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Account Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="addAccountOffcanvas" aria-labelledby="addAccountLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="addAccountLabel">Add Account</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('finance.accounts.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="code" class="form-label">Account Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="code" name="code" required>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Account Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="type_id" class="form-label">Account Type <span class="text-danger">*</span></label>
                <select class="form-select" id="type_id" name="type_id" required>
                    <option value="">Select Type</option>
                    @foreach($accountTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->normal_balance }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="parent_id" class="form-label">Parent Account</label>
                <select class="form-select" id="parent_id" name="parent_id">
                    <option value="">None (Top Level)</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                        @foreach($acc->children as $child)
                             <option value="{{ $child->id }}">&nbsp;&nbsp; {{ $child->code }} - {{ $child->name }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_group" name="is_group" value="1">
                <label class="form-check-label" for="is_group">Is Group Account?</label>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Save Account</button>
            </div>
        </form>
    </div>
</div>
@endsection
