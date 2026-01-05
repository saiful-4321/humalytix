@extends('Main::layouts.app')

@section('title', 'My Goals')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">My Performance Goals</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                    <li class="breadcrumb-item active">Performance</li>
                    <li class="breadcrumb-item active">My Goals</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-xl-4">
        <div class="card bg-white h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="avatar-sm me-3">
                        <span class="avatar-title bg-soft-primary text-primary rounded-circle font-size-20">
                            <i class="mdi mdi-target"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="font-size-14 mb-0">Total Goals</h5>
                        <p class="text-muted mb-0">Active & Completed</p>
                    </div>
                    <h4 class="mb-0">{{ $activeGoals->count() + $completedGoals->count() }}</h4>
                </div>
                
                <hr>

                <h5 class="font-size-14 mb-3">Goal Application</h5>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('hrm.performance-goals.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                    <input type="hidden" name="status" value="not_started">
                    <input type="hidden" name="progress" value="0">
                    
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="Goal Title">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Target Date</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="date" name="start_date" class="form-control" required value="{{ date('Y-m-d') }}">
                                <small class="text-muted">Start</small>
                            </div>
                            <div class="col-6">
                                <input type="date" name="due_date" class="form-control" required>
                                <small class="text-muted">Due</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Create Goal</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card bg-white">
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#active_goals" role="tab">
                            <i class="bx bx-target-lock font-size-20 me-1 align-middle"></i>
                            <span class="d-none d-sm-block">Active Goals</span> 
                            <span class="badge bg-soft-primary text-primary ms-1">{{ $activeGoals->count() }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#overdue_goals" role="tab">
                            <i class="bx bx-time-five font-size-20 me-1 align-middle"></i>
                            <span class="d-none d-sm-block">Overdue</span>
                            @if($overdueGoals->count() > 0)
                            <span class="badge bg-soft-danger text-danger ms-1">{{ $overdueGoals->count() }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#completed_goals" role="tab">
                            <i class="bx bx-check-circle font-size-20 me-1 align-middle"></i>
                            <span class="d-none d-sm-block">Completed</span>
                        </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content p-3 text-muted">
                    <div class="tab-pane active" id="active_goals" role="tabpanel">
                        @if($activeGoals->count() > 0)
                            <div class="vstack gap-3">
                                @foreach($activeGoals as $goal)
                                    <div class="border rounded p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h5 class="font-size-15 mb-0 text-truncate">{{ $goal->title }}</h5>
                                            <span class="badge bg-soft-{{ $goal->priority == 'high' ? 'danger' : 'info' }} text-{{ $goal->priority == 'high' ? 'danger' : 'info' }}">{{ ucfirst($goal->priority) }}</span>
                                        </div>
                                        <p class="text-muted mb-2">{{ Str::limit($goal->description, 100) }}</p>
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <small class="text-muted"><i class="bx bx-calendar me-1"></i> Due: {{ $goal->due_date->format('d M, Y') }}</small>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1" style="height: 6px;">
                                                        <div class="progress-bar" role="progressbar" style="width: {{ $goal->progress }}%"></div>
                                                    </div>
                                                    <span class="ms-2 font-size-12 fw-bold">{{ $goal->progress }}%</span>
                                                    <button class="btn btn-sm btn-link update-progress" data-id="{{ $goal->id }}" data-progress="{{ $goal->progress }}">
                                                        <i class="bx bx-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bx bx-target-lock display-4 text-muted"></i>
                                <h5 class="mt-3 text-muted">No active goals found</h5>
                            </div>
                        @endif
                    </div>

                    <div class="tab-pane" id="overdue_goals" role="tabpanel">
                         @if($overdueGoals->count() > 0)
                            <div class="vstack gap-3">
                                @foreach($overdueGoals as $goal)
                                    <div class="border border-danger rounded p-3 bg-soft-danger">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h5 class="font-size-15 mb-0 text-truncate">{{ $goal->title }}</h5>
                                            <span class="badge bg-danger">Overdue</span>
                                        </div>
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <small class="text-danger fw-bold"><i class="bx bx-calendar me-1"></i> Due: {{ $goal->due_date->format('d M, Y') }}</small>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1" style="height: 6px;">
                                                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $goal->progress }}%"></div>
                                                    </div>
                                                    <span class="ms-2 font-size-12 fw-bold">{{ $goal->progress }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bx bx-check-shield display-4 text-success"></i>
                                <h5 class="mt-3 text-muted">No overdue goals! Great job!</h5>
                            </div>
                        @endif
                    </div>
                    
                    <div class="tab-pane" id="completed_goals" role="tabpanel">
                        @if($completedGoals->count() > 0)
                            <div class="vstack gap-3">
                                @foreach($completedGoals as $goal)
                                    <div class="border rounded p-3 bg-light opacity-75">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h5 class="font-size-15 mb-0 text-truncate text-decoration-line-through">{{ $goal->title }}</h5>
                                            <span class="badge bg-soft-success text-success">Completed</span>
                                        </div>
                                        <small class="text-muted"><i class="bx bx-calendar-check me-1"></i> Completed on: {{ $goal->updated_at->format('d M, Y') }}</small>
                                    </div>
                                @endforeach
                            </div>
                        @else
                             <div class="text-center py-5">
                                <i class="bx bx-trophy display-4 text-muted"></i>
                                <h5 class="mt-3 text-muted">No completed goals yet</h5>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Progress Modal -->
<div class="modal fade" id="updateProgressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Goal Progress</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="updateProgressForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="goal_id" id="prog_goal_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Progress (%)</label>
                        <input type="range" class="form-range" id="prog_range" min="0" max="100" step="5">
                        <div class="d-flex justify-content-between">
                            <span id="prog_val">0%</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any comments on progress?"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Save Progress</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.update-progress').on('click', function() {
            const id = $(this).data('id');
            const progress = $(this).data('progress');
            
            $('#prog_goal_id').val(id);
            $('#prog_range').val(progress);
            $('#prog_val').text(progress + '%');
            
            $('#updateProgressModal').modal('show');
        });

        $('#prog_range').on('input', function() {
            $('#prog_val').text($(this).val() + '%');
        });

        $('#updateProgressForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#prog_goal_id').val();
            const progress = $('#prog_range').val();
            const notes = $('[name="notes"]').val();

            $.ajax({
                url: `/hrm/performance-goals/${id}/progress`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    progress: progress,
                    notes: notes
                },
                success: function(response) {
                    $('#updateProgressModal').modal('hide');
                    Swal.fire('Success', response.message, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Failed to update progress', 'error');
                }
            });
        });
    });
</script>
@endpush
