<li class="tree-item position-relative" data-name="{{ strtolower($account->name) }}" data-code="{{ strtolower($account->code) }}">
    <div class="d-flex align-items-center py-1 px-2 rounded-2 hover-bg-light transition-all tree-content">
        {{-- Expand/Collapse Icon for Groups --}}
        @if($account->children->isNotEmpty())
            <span class="cursor-pointer me-1 toggle-tree text-secondary" data-target="#subtree-{{ $account->id }}" style="width: 20px; text-align: center;">
                <i class="fas fa-caret-down fa-lg transition-transform"></i>
            </span>
        @else
            <span class="me-1" style="width: 20px; display:inline-block;"></span>
        @endif

        {{-- Node Content --}}
        <div class="d-flex align-items-center flex-grow-1 overflow-hidden" style="min-height: 28px;">
            {{-- Icon --}}
            @if($account->is_group)
                <i class="fas fa-folder text-warning me-2 fs-14"></i>
            @else
                <i class="fas fa-file-invoice-dollar text-primary me-2 opacity-50 fs-13"></i>
            @endif

            {{-- Text --}}
            <div class="d-flex align-items-baseline text-truncate {{ !$account->is_group ? 'fst-italic text-primary' : '' }}" style="min-width: 0;">
                <span class="{{ $account->is_group ? 'fw-bold text-dark' : 'fw-medium' }} fs-13 text-truncate">
                    {{ $account->name }}
                </span>
                <span class="{{ $account->is_group ? 'text-muted' : 'text-primary opacity-75' }} ms-2 fs-10" style="white-space: nowrap;">{{ $account->code }}</span>
            </div>

            {{-- Action Icons --}}
            <div class="ms-auto action-icons d-flex align-items-center gap-1 rounded-pill shadow-sm px-2 py-0 border" 
                 style="opacity: 1 !important; visibility: visible !important; display: inline-flex !important;">
                @if($account->is_group)
                    <a href="javascript:void(0)" class="text-success p-1 hover-scale add-sub-account" 
                       data-bs-toggle="offcanvas" data-bs-target="#addAccountOffcanvas" 
                       data-parent-id="{{ $account->id }}" data-type-id="{{ $account->type_id }}" title="Add Sub-Account">
                        <i class="fas fa-plus-circle fs-12"></i>
                    </a>
                @endif
                
                {{-- Edit --}}
                <a href="javascript:void(0)" class="text-info p-1 hover-scale edit-account" 
                   data-bs-toggle="offcanvas" data-bs-target="#editAccountOffcanvas-{{ $account->id }}"
                   title="Edit">
                    <i class="fas fa-edit fs-12"></i>
                </a>

                {{-- Delete --}}
                @if(!$account->is_system && $account->children->isEmpty() && $account->journalEntries->isEmpty())
                    <a href="javascript:void(0)" class="text-danger p-1 hover-scale delete-account" 
                       onclick="confirmDelete('{{ route('finance.accounts.destroy', $account->id) }}')"
                       title="Delete">
                        <i class="fas fa-trash-alt fs-12"></i>
                    </a>
                @else
                     <span class="text-muted p-1 cursor-not-allowed" title="Cannot delete: Has children or transactions">
                        <i class="fas fa-trash-alt fs-12 opacity-25"></i>
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Recursive Children --}}
    @if($account->children->isNotEmpty())
        <ul class="list-unstyled ms-3 ps-2 border-start border-2 border-primary-subtle mt-1" id="subtree-{{ $account->id }}">
            @foreach($account->children as $child)
                @include('Finance::pages.accounts.tree-node', ['account' => $child])
            @endforeach
        </ul>
    @endif
    
    {{-- Include Edit Offcanvas --}}
    @include('Finance::pages.accounts.edit-modal', ['account' => $account])
</li>
