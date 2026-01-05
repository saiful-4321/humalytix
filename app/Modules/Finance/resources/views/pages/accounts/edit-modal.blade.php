<x-offcanvas id="editAccountOffcanvas-{{ $account->id }}" title="Edit Account">
    <form id="editAccountForm-{{ $account->id }}" action="{{ route('finance.accounts.update', $account->id) }}" method="POST">
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
    </form>
    <x-slot:footer>
        <div class="d-grid w-100">
            <button type="submit" form="editAccountForm-{{ $account->id }}" class="btn btn-primary">Update Account</button>
        </div>
    </x-slot:footer>
</x-offcanvas>
