<tr>
    <td style="padding-left: {{ $level * 20 + 10 }}px;">
        @if($account->is_group)
            <strong>{{ $account->code }}</strong>
        @else
            {{ $account->code }}
        @endif
    </td>
    <td style="padding-left: {{ $level * 20 + 10 }}px;">
        @if($account->is_group)
            <strong>{{ $account->name }}</strong>
        @else
            {{ $account->name }}
        @endif
    </td>
    <td>
        <span class="badge bg-{{ $account->type->normal_balance == 'debit' ? 'info' : 'warning' }}-subtle text-{{ $account->type->normal_balance == 'debit' ? 'info' : 'warning' }}">
            {{ $account->type->name }}
        </span>
    </td>
    <td>
        {{ number_format($account->current_balance ?? 0, 2) }}
    </td>
    <td>
        @if($account->is_active)
            <span class="badge bg-success-subtle text-success">Active</span>
        @else
            <span class="badge bg-danger-subtle text-danger">Inactive</span>
        @endif
    </td>
    <td>
        <div class="dropdown d-inline-block">
            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ri-more-fill align-middle"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                @can('finance-coa-edit')
                <li>
                    <a class="dropdown-item edit-item-btn" href="javascript:void(0)" 
                       data-bs-toggle="offcanvas" data-bs-target="#editAccountOffcanvas-{{ $account->id }}">
                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                    </a>
                </li>
                @endcan
                @can('finance-coa-delete')
                    @if(!$account->is_system)
                    <li>
                        <a class="dropdown-item remove-item-btn" href="javascript:void(0)" onclick="confirmDelete('{{ route('finance.accounts.destroy', $account->id) }}')">
                            <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                        </a>
                    </li>
                    @endif
                @endcan
            </ul>
        </div>
        
        <!-- Edit Offcanvas (Inline to keep simple, ideally loaded via AJAX) -->
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
    </td>
</tr>

@if($account->children->isNotEmpty())
    @foreach($account->children as $child)
        @include('Finance::pages.accounts.row', ['account' => $child, 'level' => $level + 1])
    @endforeach
@endif
