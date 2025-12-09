@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-sm-12">
            <h2>Organization Chart</h2>
        </div>
        <div class="col-lg-5 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hrm.departments.index') }}">Departments</a></li>
                <li class="breadcrumb-item active">Org Chart</li>
            </ul>
        </div>
    </div>
</div>

@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h4>Company Organization Structure</h4>
                    <p class="text-muted">Hierarchical view of departments and their heads</p>
                </div>

                <div class="org-chart-container" style="overflow-x: auto;">
                    @foreach($departments as $dept)
                    <div class="org-chart-item mb-4">
                        <!-- Root Department -->
                        <div class="text-center">
                            <div class="org-box root-dept">
                                <h5 class="mb-1">{{ $dept->name }}</h5>
                                <p class="text-muted mb-1 small">{{ $dept->code }}</p>
                                @if($dept->headEmployee)
                                <p class="mb-0"><strong>{{ $dept->headEmployee->full_name }}</strong></p>
                                <small class="text-muted">{{ $dept->headEmployee->designation }}</small>
                                @else
                                <small class="text-muted">No Head Assigned</small>
                                @endif
                            </div>
                        </div>

                        <!-- Sub-Departments -->
                        @if($dept->children->count() > 0)
                        <div class="org-line"></div>
                        <div class="row justify-content-center mt-3">
                            @foreach($dept->children as $child)
                            <div class="col-md-4 mb-3">
                                <div class="org-box sub-dept">
                                    <h6 class="mb-1">{{ $child->name }}</h6>
                                    <p class="text-muted mb-1 small">{{ $child->code }}</p>
                                    @if($child->headEmployee)
                                    <p class="mb-0 small"><strong>{{ $child->headEmployee->full_name }}</strong></p>
                                    <small class="text-muted">{{ $child->headEmployee->designation }}</small>
                                    @else
                                    <small class="text-muted">No Head Assigned</small>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @if(!$loop->last)
                    <hr class="my-4">
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.org-chart-container {
    padding: 20px;
}

.org-box {
    background: #fff;
    border: 2px solid #e3e6f0;
    border-radius: 8px;
    padding: 20px;
    margin: 0 auto;
    max-width: 300px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.org-box:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.org-box.root-dept {
    border-color: #4e73df;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.org-box.root-dept .text-muted {
    color: rgba(255,255,255,0.8) !important;
}

.org-box.sub-dept {
    border-color: #1cc88a;
    background: #f8f9fc;
}

.org-line {
    width: 2px;
    height: 30px;
    background: #e3e6f0;
    margin: 10px auto;
}
</style>
@endpush
