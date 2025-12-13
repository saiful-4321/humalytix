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
                                <a href="{{ route('hrm.settings.approval-chains.edit', $chain->id) }}" class="btn btn-sm btn-link text-primary"><i class="bx bx-edit font-size-18"></i></a>
                                <form action="{{ route('hrm.settings.approval-chains.destroy', $chain->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger" onclick="return confirm('Are you sure?')"><i class="bx bx-trash font-size-18"></i></button>
                                </form>
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
                <div id="levels-container">
                    {{-- Levels will be added here --}}
                </div>

                <button type="button" class="btn btn-sm btn-soft-primary mb-4 w-100" onclick="addLevel()">
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
        let levelCount = 0;

        function addLevel() {
            levelCount++;
            const template = document.getElementById('level-template');
            const container = document.getElementById('levels-container');
            const clone = template.content.cloneNode(true);

            clone.querySelector('.level-number').textContent = levelCount;
            
            // Update names
            clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
                el.name = el.name.replace('INDEX', levelCount - 1);
            });

            container.appendChild(clone);
        }

        function removeLevel(btn) {
            btn.closest('.level-item').remove();
            let i = 1;
            document.querySelectorAll('.level-item').forEach(el => {
                el.querySelector('.level-number').textContent = i++;
            });
            levelCount = document.querySelectorAll('.level-item').length;
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

        // Initialize with one level
        document.addEventListener('DOMContentLoaded', () => {
            addLevel();
        });
    </script>
@endsection
