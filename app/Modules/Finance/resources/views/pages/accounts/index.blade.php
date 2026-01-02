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
        <div class="card shadow-none bg-white">
            <div class="card-header align-items-center d-flex border-bottom-0 rounded-top p-3 shadow-sm">
                <h4 class="card-title mb-0 flex-grow-1 font-weight-bold text-dark">
                    <i class="fas fa-sitemap me-2 text-primary"></i> Account List
                </h4>
                <div class="flex-shrink-0 d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    
                    <div class="dropdown">
                        <button class="btn btn-outline-info btn-sm dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cloud-download-alt me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu shadow bg-light">
                            <li><a class="dropdown-item" href="{{ route('finance.accounts.export', ['type' => 'excel']) }}"><i class="fas fa-file-excel text-success me-2"></i> Excel (XLSX)</a></li>
                            <li><a class="dropdown-item" href="{{ route('finance.accounts.export', ['type' => 'csv']) }}"><i class="fas fa-file-csv text-muted me-2"></i> CSV</a></li>
                            <li><a class="dropdown-item" href="{{ route('finance.accounts.export', ['type' => 'pdf']) }}"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</a></li>
                        </ul>
                    </div>

                    @can('finance-coa-create')
                    <button class="btn btn-primary btn-sm add-btn shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#addAccountOffcanvas">
                        <i class="fas fa-plus me-1"></i> Add Account
                    </button>
                    @endcan
                </div>
            </div>

            <!-- Active Filters Bar -->
            <div id="active-filters" class="border-top bg-light-subtle px-3 py-2 d-none card bg-white">
                <div class="d-flex align-items-center">
                    <span class="text-uppercase fs-7 fw-bold text-muted me-2"><small><i class="fas fa-filter me-1"></i> Active Filters:</small></span>
                    <div id="filter-chips" class="d-flex flex-wrap gap-2">
                        <!-- Chips inserted via JS -->
                    </div>
                    <button id="clear-all-filters" class="btn btn-link text-danger btn-sm ms-auto py-0 fs-12 text-decoration-none">Clear All</button>
                </div>
            </div>
            
            <div class="card-body p-3 bg-light-subtle mt-3">
                <div class="row g-3 flex-nowrap overflow-auto pb-3" style="scroll-behavior: smooth;">
                    @foreach($accountTypes as $type)
                    <div class="col-xl-4 col-lg-4 col-md-6 " style="min-width: 320px;">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden bg-white">
                            <div class="card-header border-bottom border-light py-3 d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0 text-uppercase fs-14 fw-bold text-primary d-flex align-items-center flex-grow-1">
                                    <span class="bg-primary-subtle text-primary rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="fas fa-layer-group fs-14"></i>
                                    </span>
                                    {{ $type->name }}
                                </h5>
                                <button class="btn btn-sm btn-soft-primary add-sub-account" 
                                        data-bs-toggle="offcanvas" data-bs-target="#addAccountOffcanvas" 
                                        data-type-id="{{ $type->id }}" data-parent-id="" title="Add Top-Level {{ $type->name }}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="card-body p-2" style="max-height: 75vh; overflow-y: auto;">
                                <ul class="list-unstyled account-tree m-0" id="tree-type-{{ $type->id }}">
                                    @foreach($type->accounts as $account)
                                        @include('Finance::pages.accounts.tree-node', ['account' => $account])
                                    @endforeach
                                    @if($type->accounts->isEmpty())
                                        <div class="text-center bg-white py-5">
                                            <i class="fas fa-folder-open text-muted opacity-25" style="font-size: 3rem;"></i>
                                            <p class="text-muted fs-13 mt-2">No accounts in this group</p>
                                        </div>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div id="no-result" class="text-center py-5 d-none">
                    <div class="py-4">
                        <i class="fas fa-search text-muted opacity-25" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">No results found</h5>
                        <p class="text-muted fs-13">Try adjusting your filters</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Offcanvas --}}
<div class="offcanvas offcanvas-end border-0" tabindex="-1" id="filterOffcanvas">
    <div class="offcanvas-header bg-light">
        <h5 class="offcanvas-title" id="filterOffcanvasLabel"><i class="fas fa-filter me-2 text-muted"></i> Filter Accounts</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="mb-4">
            <label class="form-label text-muted fs-12 text-uppercase fw-bold">Name</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="filter-name" class="form-control" placeholder="Search by name...">
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label text-muted fs-12 text-uppercase fw-bold">Code</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-hashtag text-muted"></i></span>
                <input type="text" id="filter-code" class="form-control" placeholder="Search by code...">
            </div>
        </div>
        {{-- Sort Options --}}
        <div class="mb-4">
           <label class="form-label text-muted fs-12 text-uppercase fw-bold">Sort By</label>
           <div class="input-group">
               <span class="input-group-text bg-white"><i class="fas fa-sort text-muted"></i></span>
               <select id="filter-sort" class="form-select">
                   <option value="default">Default (Code)</option>
                   <option value="date_desc">Created Date (Newest First)</option>
                   <option value="date_asc">Created Date (Oldest First)</option>
               </select>
           </div>
        </div>

        <div class="d-grid gap-2">
            <button class="btn btn-primary" id="btn-filter" data-bs-dismiss="offcanvas"><i class="fas fa-check me-1"></i> Apply Filter</button>
            <button class="btn btn-light" id="btn-reset"><i class="fas fa-undo me-1"></i> Reset</button>
        </div>
    </div>
</div>

<style>
    .tree-item {
        position: relative;
    }
    .hover-bg-light:hover {
        background-color: #f8f9fa;
    }
    .hover-scale {
        transition: transform 0.2s;
    }
    .hover-scale:hover {
        transform: scale(1.2);
    }
    .transition-all {
        transition: all 0.2s ease;
    }
    .transition-transform {
        transition: transform 0.2s ease;
    }
    .toggle-tree.collapsed i {
        transform: rotate(-90deg);
    }
    /* Filter Chip Styles */
    .filter-chip {
        background: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        color: var(--bs-body-color);
        display: flex;
        align-items: center;
        transition: all 0.2s;
    }
    .filter-chip:hover {
        background-color: var(--bs-tertiary-bg);
        border-color: var(--bs-border-color-translucent);
    }
    .filter-chip i {
        cursor: pointer;
        margin-left: 6px;
        color: var(--bs-secondary);
        font-size: 12px;
    }
    .filter-chip i:hover {
        color: #f06548;
    }
    /* Scrollbar styling for horizontal scroll */
    .overflow-auto::-webkit-scrollbar {
        height: 6px;
    }
    .overflow-auto::-webkit-scrollbar-track {
        background: var(--bs-light);
    }
    .overflow-auto::-webkit-scrollbar-thumb {
        background: var(--bs-secondary-bg); 
        border-radius: 3px;
    }
    .overflow-auto::-webkit-scrollbar-thumb:hover {
        background: var(--bs-secondary);
    }
</style>


{{-- Delete Confirmation Script --}}
<script>
    function confirmDelete(url) {
        if(confirm('Are you sure you want to delete this account? This action cannot be undone.')) {
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            
            let csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            let methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Event Delegation for Tree Interactions (Expand/Collapse & Add Sub-Account)
        document.body.addEventListener('click', function(e) {
            
            // Toggle Tree
            const toggle = e.target.closest('.toggle-tree');
            if (toggle) {
                const targetId = toggle.getAttribute('data-target');
                const targetUl = document.querySelector(targetId);
                
                if (targetUl) {
                    if (targetUl.style.display === 'none') {
                        targetUl.style.display = 'block';
                        toggle.classList.remove('collapsed');
                    } else {
                        targetUl.style.display = 'none';
                        toggle.classList.add('collapsed');
                    }
                }
            }
            
            // Add Sub-Account Prefill
            const addBtn = e.target.closest('.add-sub-account');
            if (addBtn) {
                const parentId = addBtn.getAttribute('data-parent-id');
                const typeId = addBtn.getAttribute('data-type-id');
                
                const parentSelect = document.querySelector('#addAccountOffcanvas select[name="parent_id"]');
                const typeSelect = document.querySelector('#addAccountOffcanvas select[name="type_id"]');
                
                if (parentSelect) {
                    parentSelect.value = parentId;
                    // Trigger change for Select2 or other listeners
                    parentSelect.dispatchEvent(new Event('change'));
                }
                
                if (typeSelect) {
                    typeSelect.value = typeId;
                    typeSelect.dispatchEvent(new Event('change'));
                }
            }
        });

        // Filter Logic
        const btnFilter = document.getElementById('btn-filter');
        const btnReset = document.getElementById('btn-reset');
        const filterName = document.getElementById('filter-name');
        const filterCode = document.getElementById('filter-code');
        const filterSort = document.getElementById('filter-sort');
        
        const activeFiltersContainer = document.getElementById('active-filters');
        const filterChips = document.getElementById('filter-chips');
        const clearAllBtn = document.getElementById('clear-all-filters');
        
        // Initial Sort Value from URL
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.has('sort') && filterSort) {
            filterSort.value = urlParams.get('sort');
        }

        // Update Active Filters UI
        function updateActiveFilters(nameVal, codeVal) {
            filterChips.innerHTML = '';
            let hasFilter = false;

            if (nameVal) {
                hasFilter = true;
                createChip(`Name: <strong>${nameVal}</strong>`, 'name');
            }
            if (codeVal) {
                hasFilter = true;
                createChip(`Code: <strong>${codeVal}</strong>`, 'code');
            }
            
            const currentSort = urlParams.get('sort');
            if(currentSort && currentSort !== 'default') {
                hasFilter = true;
                createChip(`Sort: <strong>${currentSort === 'date_desc' ? 'Newest First' : 'Oldest First'}</strong>`, 'sort');
            }

            if (hasFilter) {
                activeFiltersContainer.classList.remove('d-none');
            } else {
                activeFiltersContainer.classList.add('d-none');
            }
        }
        
        function createChip(html, type) {
            const chip = document.createElement('div');
            chip.className = 'filter-chip';
            chip.innerHTML = `${html} <i class="fas fa-times" onclick="removeGlobalFilter('${type}')"></i>`;
            filterChips.appendChild(chip);
        }

        // Apply Filter Function
        function applyFilter() {
            const nameVal = filterName.value.toLowerCase();
            const codeVal = filterCode.value.toLowerCase();
            const sortVal = filterSort ? filterSort.value : 'default';
            
            // Client-side filtering
            let matches = 0;
            const treeItems = document.querySelectorAll('.tree-item');
            
            if (nameVal === '' && codeVal === '') {
                // Show all if no text filter
                treeItems.forEach(item => item.style.display = '');
                document.getElementById('no-result').classList.add('d-none');
            } else {
                treeItems.forEach(item => {
                    const selfName = item.getAttribute('data-name');
                    const selfCode = item.getAttribute('data-code');
                    
                    const nameMatch = !nameVal || (selfName && selfName.includes(nameVal));
                    const codeMatch = !codeVal || (selfCode && selfCode.includes(codeVal));

                    if (nameMatch && codeMatch) {
                        item.style.display = 'block';
                        matches++;
                        // Show Parents
                        let parent = item.parentElement.closest('.tree-item');
                        while(parent) {
                            parent.style.display = 'block';
                            const parentUl = parent.querySelector('ul');
                            if(parentUl) parentUl.style.display = 'block';
                            const toggle = parent.querySelector('.toggle-tree');
                            if(toggle) toggle.classList.remove('collapsed');
                            parent = parent.parentElement.closest('.tree-item');
                        }
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                const noResult = document.getElementById('no-result');
                if(matches === 0) noResult.classList.remove('d-none');
                else noResult.classList.add('d-none');
            }
            
            updateActiveFilters(nameVal, codeVal);
            
            // Handle Sort (Server-side reload)
            if (sortVal !== 'default' && sortVal !== urlParams.get('sort')) {
                 const url = new URL(window.location.href);
                 url.searchParams.set('sort', sortVal);
                 window.location.href = url.toString();
            } else if (sortVal === 'default' && urlParams.has('sort')) {
                 const url = new URL(window.location.href);
                 url.searchParams.delete('sort');
                 window.location.href = url.toString();
            }
        }

        // Event Listeners
        if(btnFilter) btnFilter.addEventListener('click', applyFilter);
        
        if(btnReset) {
            btnReset.addEventListener('click', function() {
                filterName.value = '';
                filterCode.value = '';
                if(filterSort) filterSort.value = 'default';
                
                if(urlParams.has('sort')) {
                    window.location.href = window.location.pathname; // Reload to clear sort
                } else {
                    applyFilter();
                }
            });
        }
        
        if(clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                filterName.value = '';
                filterCode.value = '';
                if(urlParams.has('sort')) {
                     window.location.href = window.location.pathname;
                } else {
                    applyFilter();
                }
            });
        }

        filterName.addEventListener('keyup', function(e) { if(e.key === 'Enter') applyFilter(); });
        filterCode.addEventListener('keyup', function(e) { if(e.key === 'Enter') applyFilter(); });
        
        // Initial Run
        updateActiveFilters(filterName.value, filterCode.value);
    });

    // Global helper for onclick in HTML strings
    window.removeGlobalFilter = function(type) {
        if(type === 'sort') {
            const url = new URL(window.location.href);
            url.searchParams.delete('sort');
            window.location.href = url.toString();
        } else {
            if(type === 'name') document.getElementById('filter-name').value = '';
            if(type === 'code') document.getElementById('filter-code').value = '';
            document.getElementById('btn-filter').click();
        }
    };
</script>

<!-- Add Account Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="addAccountOffcanvas" aria-labelledby="addAccountLabel">
    <div class="offcanvas-header bg-light">
        <h5 class="offcanvas-title" id="addAccountLabel"><i class="fas fa-plus-circle me-2 text-primary"></i> Add Account</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('finance.accounts.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="code" class="form-label text-muted fs-12 fw-bold text-uppercase">Account Code <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-hashtag text-muted"></i></span>
                    <input type="text" class="form-control" id="code" name="code" required placeholder="e.g. 1001">
                </div>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label text-muted fs-12 fw-bold text-uppercase">Account Name <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-font text-muted"></i></span>
                    <input type="text" class="form-control" id="name" name="name" required placeholder="e.g. Petty Cash">
                </div>
            </div>
            <div class="mb-3">
                <label for="type_id" class="form-label text-muted fs-12 fw-bold text-uppercase">Account Type <span class="text-danger">*</span></label>
                <select class="form-select" id="type_id" name="type_id" required>
                    <option value="">Select Type</option>
                    @foreach($accountTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->normal_balance }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="parent_id" class="form-label text-muted fs-12 fw-bold text-uppercase">Parent Account</label>
                <select class="form-select" id="parent_id" name="parent_id">
                    <option value="">None (Top Level)</option>
                    @foreach($accountTypes as $type)
                        <optgroup label="{{ $type->name }}" data-type-id="{{ $type->id }}">
                            @foreach($type->accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                @foreach($acc->children as $child)
                                     <option value="{{ $child->id }}">&nbsp;&nbsp; {{ $child->code }} - {{ $child->name }}</option>
                                @endforeach
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 form-check bg-light p-3 rounded border">
                <input type="checkbox" class="form-check-input" id="is_group" name="is_group" value="1">
                <label class="form-check-label fw-bold" for="is_group">Is this a Group Account?</label>
                <small class="d-block text-muted mt-1">Group accounts can have sub-accounts but cannot record transactions directly.</small>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label text-muted fs-12 fw-bold text-uppercase">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg shadow-sm"><i class="fas fa-save me-1"></i> Save Account</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type_id');
        const parentSelect = document.getElementById('parent_id');
        
        if(typeSelect && parentSelect) {
            typeSelect.addEventListener('change', function() {
                const selectedTypeId = this.value;
                const optgroups = parentSelect.querySelectorAll('optgroup');
                
                // Reset parent selection
                parentSelect.value = "";
                
                optgroups.forEach(group => {
                    if(selectedTypeId === "" || group.getAttribute('data-type-id') == selectedTypeId) {
                        group.style.display = ''; // Show
                        group.disabled = false;
                        // For some browsers/Bootstrap styling, hiding optgroup might not be enough, 
                        // but usually it works. If not, we might need to detach/attach, but display none is simple first step.
                        // Actually, 'display: none' on optgroup works in standard Select, but Safari can be picky.
                        // Let's use 'hidden' attribute as well.
                        group.hidden = false;
                    } else {
                        group.style.display = 'none'; // Hide
                        group.disabled = true;
                        group.hidden = true;
                    }
                });
            });
        }
    });
</script>
@endsection
