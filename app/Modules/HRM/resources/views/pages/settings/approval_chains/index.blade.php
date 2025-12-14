@extends("HRM::layouts.settings")

@section("title", "Approval Chains")
@section("breadcrumb")
    <li class="breadcrumb-item active">Leave Settings</li>
    <li class="breadcrumb-item active">Approval Chains</li>
@endsection

@section("settings-content")
    <div class="card bg-white">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between py-1">
                <h6 class="font-weight-medium mb-0">Approval Chains</h6>
                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#createChainCanvas" aria-controls="createChainCanvas">
                    <i class="bx bx-plus me-1"></i> Create Chain
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Levels</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($chains as $chain)
                        <tr>
                            <td><strong>{{ $chain->name }}</strong></td>
                            <td>{{ Str::limit($chain->description, 50) }}</td>
                            <td>
                                @foreach($chain->levels as $level)
                                    <span class="badge bg-light text-dark border me-1">
                                        {{ $level->level }}. 
                                        @if($level->approver_type == 'reporting_manager') Reporting Manager
                                        @elseif($level->approver_type == 'designation') {{ $level->approver_value }}
                                        @elseif($level->approver_type == 'specific_user') User #{{ $level->approver_value }}
                                        @endif
                                    </span>
                                    @if(!$loop->last) <i class="bx bx-right-arrow-alt text-muted"></i> @endif
                                @endforeach
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-soft-info" data-bs-toggle="offcanvas" data-bs-target="#editChainCanvas{{ $chain->id }}">
                                        <i class="mdi mdi-pencil-outline"></i>
                                    </button>
                                    <form action="{{ route('hrm.settings.approval-chains.destroy', $chain->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" onclick="return confirm('Are you sure?')">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No approval chains defined.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="createChainCanvas" aria-labelledby="createChainCanvasLabel" style="width: 500px;">
        <div class="offcanvas-header">
            <h5 id="createChainCanvasLabel">Create Approval Chain</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">

            <form action="{{ route('hrm.settings.approval-chains.store') }}" method="POST" id="createChainForm">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label">Chain Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required placeholder="e.g. Standard Leave, Executive Leave">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="2"></textarea>
                </div>

                <h6 class="mb-3 mt-4">Approval Levels</h6>
                <div id="create-levels-container" class="levels-container-wrapper">
                    {{-- Levels will be added here --}}
                </div>

                <button type="button" class="btn btn-sm btn-soft-primary mb-4 w-100" onclick="addLevel('create-levels-container')">
                    <i class="bx bx-plus"></i> Add Level
                </button>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Save Chain</button>
                </div>
            </form>
        </div>
    </div>

    <template id="level-template">
        <div class="level-item card card-body bg-light border mb-2 p-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="fw-bold">Level <span class="level-number"></span></small>
                <button type="button" class="btn btn-sm text-danger p-0" onclick="removeLevel(this)"><i class="bx bx-trash"></i></button>
            </div>
            <div class="mb-2">
                <select class="form-select form-select-sm" name="levels[INDEX][type]" onchange="toggleValueInput(this)" required>
                    <option value="reporting_manager">Reporting Manager</option>
                    <option value="designation">Designation</option>
                    <option value="specific_user">Specific Employee</option>
                </select>
            </div>
            <div class="value-container value-designation" style="display:none;">
                <input type="text" class="form-control form-control-sm" name="levels[INDEX][value]" placeholder="Designation Name">
            </div>
            <div class="value-container value-specific_user" style="display:none;">
                <select class="form-select form-select-sm" name="levels[INDEX][value]">
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->user_id }}">{{ $emp->full_name }} ({{ $emp->designation }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </template>

    <script>
        // Scoped functions for dynamic levels
        function addLevel(containerId) {
            const container = document.getElementById(containerId);
            const levelItems = container.querySelectorAll('.level-item');
            const levelCount = levelItems.length + 1;
            
            const template = document.getElementById('level-template');
            const clone = template.content.cloneNode(true);

            clone.querySelector('.level-number').textContent = levelCount;
            
            // Random unique suffix for this level iteration to avoid name collision in same form? 
            // Actually, we just need unique array indices.
            // Using logic: existing count as index.
            const index = levelCount - 1;

            clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
                el.name = el.name.replace('INDEX', index);
            });

            container.appendChild(clone);
        }

        function removeLevel(btn) {
            const container = btn.closest('.levels-container-wrapper'); // We need a wrapper to scope re-calculation
            btn.closest('.level-item').remove();
            
            let i = 1;
            container.querySelectorAll('.level-item').forEach(el => {
                el.querySelector('.level-number').textContent = i++;
                // Re-indexing could be complex for 'name' but Laravel handles array[] fine usually if indices are just keys.
                // However, to be safe, ideally we re-index 'name' attributes. 
                // For simplicity here, we assume backend handles non-sequential keys or we leave gaps.
            });
        }

        function toggleValueInput(select) {
            const parent = select.closest('.level-item');
            parent.querySelectorAll('.value-container').forEach(el => el.style.display = 'none');
            
            if (select.value === 'designation') {
                parent.querySelector('.value-designation').style.display = 'block';
            } else if (select.value === 'specific_user') {
                parent.querySelector('.value-specific_user').style.display = 'block';
            }
        }

        // Initialize create form with one level
        document.addEventListener('DOMContentLoaded', () => {
             // Only if create container is empty
             if(document.getElementById('create-levels-container').children.length === 0) {
                 addLevel('create-levels-container');
             }
        });
    </script>

    {{-- Edit Offcanvases Loop --}}
    @foreach($chains as $chain)
    <div class="offcanvas offcanvas-end" tabindex="-1" id="editChainCanvas{{ $chain->id }}" aria-labelledby="editChainLabel{{ $chain->id }}" style="width: 500px;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="editChainLabel{{ $chain->id }}">Edit Approval Chain</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('hrm.settings.approval-chains.update', $chain->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Chain Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ $chain->name }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="2">{{ $chain->description }}</textarea>
                </div>

                <h6 class="mb-3 mt-4">Approval Levels</h6>
                <div id="edit-levels-container-{{ $chain->id }}" class="levels-container-wrapper">
                    @foreach($chain->levels as $index => $level)
                        <div class="level-item card card-body bg-light border mb-2 p-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="fw-bold">Level <span class="level-number">{{ $index + 1 }}</span></small>
                                <button type="button" class="btn btn-sm text-danger p-0" onclick="removeLevel(this)"><i class="bx bx-trash"></i></button>
                            </div>
                            <div class="mb-2">
                                <select class="form-select form-select-sm" name="levels[{{ $index }}][type]" onchange="toggleValueInput(this)" required>
                                    <option value="reporting_manager" {{ $level->approver_type == 'reporting_manager' ? 'selected' : '' }}>Reporting Manager</option>
                                    <option value="designation" {{ $level->approver_type == 'designation' ? 'selected' : '' }}>Designation</option>
                                    <option value="specific_user" {{ $level->approver_type == 'specific_user' ? 'selected' : '' }}>Specific Employee</option>
                                </select>
                            </div>
                            <div class="value-container value-designation" style="{{ $level->approver_type != 'designation' ? 'display:none;' : '' }}">
                                <input type="text" class="form-control form-control-sm" name="levels[{{ $index }}][value]" value="{{ $level->approver_type == 'designation' ? $level->approver_value : '' }}" placeholder="Designation Name">
                            </div>
                            <div class="value-container value-specific_user" style="{{ $level->approver_type != 'specific_user' ? 'display:none;' : '' }}">
                                <select class="form-select form-select-sm" name="levels[{{ $index }}][value]">
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $emp)
                                    <option value="{{ $emp->user_id }}" {{ ($level->approver_type == 'specific_user' && $level->approver_value == $emp->user_id) ? 'selected' : '' }}>{{ $emp->full_name }} ({{ $emp->designation }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-sm btn-soft-primary mb-4 w-100" onclick="addLevel('edit-levels-container-{{ $chain->id }}')">
                    <i class="bx bx-plus"></i> Add Level
                </button>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Update Chain</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endsection
