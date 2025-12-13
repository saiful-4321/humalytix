@extends('Main::layouts.app')

@section('title', 'OKR Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">{{ $okr->title }}</h4>
            <p class="text-muted mb-0">
                <span class="badge bg-{{ $okr->level == 'company' ? 'primary' : ($okr->level == 'department' ? 'info' : 'secondary') }}">
                    {{ ucfirst($okr->level) }}
                </span>
                @if($okr->quarter)
                    <span class="badge bg-dark">{{ $okr->quarter }} {{ $okr->year }}</span>
                @endif
                <span class="badge bg-{{ $okr->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($okr->status) }}</span>
            </p>
        </div>
        <a href="{{ route('hrm.okrs.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back"></i> Back
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Key Results</h6>
                </div>
                <div class="card-body">
                    @foreach($okr->keyResults as $kr)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong>{{ $kr->description }}</strong>
                                <br><small class="text-muted">Target: {{ $kr->target_value }} {{ $kr->measurement_unit }} | Weight: {{ $kr->weightage }}%</small>
                            </div>
                            <span class="badge bg-primary">{{ $kr->progress }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="width: {{ $kr->progress }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Overall Progress</h6>
                    <div class="text-center mb-3">
                        <div class="display-4 fw-bold">{{ $okr->progress }}%</div>
                    </div>
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar" style="width: {{ $okr->progress }}%">{{ $okr->progress }}%</div>
                    </div>
                    @if($okr->description)
                        <p class="text-muted">{{ $okr->description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
