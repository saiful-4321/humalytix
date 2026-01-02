<div class="offcanvas offcanvas-end" tabindex="-1" id="editAccountOffcanvas-{{ $account->id }}">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Edit Account</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('finance.accounts.update', $account->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Account Code</label>
                <input type="text" class="form-control" name="code" value="{{ $account->code }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Account Name</label>
                <input type="text" class="form-control" name="name" value="{{ $account->name }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3">{{ $account->description }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
