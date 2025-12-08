@extends("Main::layouts.app")

@section('content')
<div class="row">
    <div class="col-lg-5 col-md-8 col-sm-12">                        
        <h2>{{ __('Cache Management') }}</h2>
    </div>            
    <div class="col-lg-7 col-md-4 col-sm-12 text-right">
        <ul class="breadcrumb justify-content-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}"><i class="fas fa-home"></i></a></li>                            
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">Cache Management</li>
        </ul>
    </div>

    <div class="col-lg-12 col-md-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                 @php
                    $activeTab = (request()->has('scan') || request('tab') == 'explorer') ? 'explorer' : 'management';
                 @endphp
                 <ul class="nav nav-tabs nav-tabs-custom card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == 'management' ? 'active' : '' }}" data-bs-toggle="tab" href="#management" role="tab">
                            <i class="mdi mdi-cog-outline me-1"></i> Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == 'explorer' ? 'active' : '' }}" data-bs-toggle="tab" href="#explorer" role="tab">
                            <i class="mdi mdi-database-search-outline me-1"></i> Cache Explorer
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <!-- Management Tab -->
                    <div class="tab-pane {{ $activeTab == 'management' ? 'active' : '' }}" id="management" role="tabpanel">
                        <div class="row">
                            <!-- Clear Cache Section -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0"><i class="mdi mdi-delete-sweep text-danger me-2"></i>Clear Cache</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="p-2">
                                            <p class="text-muted">Remove cached data to free up space and ensure fresh data loading.</p>
                                        
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-outline-danger" onclick="clearCache('all')">
                                                    <i class="mdi mdi-delete-sweep me-1"></i> Clear All Caches
                                                </button>
                                                <button class="btn btn-outline-secondary" onclick="clearCache('application')">
                                                    <i class="mdi mdi-application me-1"></i> Clear Application Cache
                                                </button>
                                                <button class="btn btn-outline-secondary" onclick="clearCache('config')">
                                                    <i class="mdi mdi-cog me-1"></i> Clear Config Cache
                                                </button>
                                                <button class="btn btn-outline-secondary" onclick="clearCache('route')">
                                                    <i class="mdi mdi-routes me-1"></i> Clear Route Cache
                                                </button>
                                                <button class="btn btn-outline-secondary" onclick="clearCache('view')">
                                                    <i class="mdi mdi-eye me-1"></i> Clear View Cache
                                                </button>
                                                <button class="btn btn-outline-secondary" onclick="clearCache('event')">
                                                    <i class="mdi mdi-calendar-check me-1"></i> Clear Event Cache
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Rebuild Cache Section -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0"><i class="mdi mdi-refresh text-success me-2"></i>Rebuild Cache</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="p-2">
                                            <p class="text-muted">Rebuild caches to improve application performance.</p>
                                        
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-outline-success" onclick="recache('all')">
                                                    <i class="mdi mdi-refresh me-1"></i> Rebuild All Caches
                                                </button>
                                                <button class="btn btn-outline-secondary" onclick="recache('config')">
                                                    <i class="mdi mdi-cog me-1"></i> Rebuild Config Cache
                                                </button>
                                                <button class="btn btn-outline-secondary" onclick="recache('route')">
                                                    <i class="mdi mdi-routes me-1"></i> Rebuild Route Cache
                                                </button>
                                                <button class="btn btn-outline-secondary" onclick="recache('view')">
                                                    <i class="mdi mdi-eye me-1"></i> Rebuild View Cache
                                                </button>
                                                <button class="btn btn-outline-secondary" onclick="recache('event')">
                                                    <i class="mdi mdi-calendar-check me-1"></i> Rebuild Event Cache
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Optimization Section -->
                            <div class="col-md-12 mt-3">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0"><i class="mdi mdi-speedometer text-primary me-2"></i>Application Optimization</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row p-2">
                                            <div class="col-md-6">
                                                <p class="text-muted">Optimize the application for better performance.</p>
                                                <button class="btn btn-primary w-100" onclick="optimize()">
                                                    <i class="mdi mdi-rocket me-1"></i> Optimize Application
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="text-muted">Clear all optimization caches.</p>
                                                <button class="btn btn-warning w-100" onclick="clearOptimize()">
                                                    <i class="mdi mdi-broom me-1"></i> Clear Optimization
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Explorer Tab -->
                    <div class="tab-pane {{ $activeTab == 'explorer' ? 'active' : '' }}" id="explorer" role="tabpanel">
                        @if($driver == 'database')
                            @if(count($cacheData) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-centered mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Key</th>
                                                <th>Expiration</th>
                                                <th>Value Preview</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cacheData as $item)
                                            <tr>
                                                <td><code class="text-primary">{{ Str::limit($item->key, 50) }}</code></td>
                                                <td>{{ \Carbon\Carbon::createFromTimestamp($item->expiration)->diffForHumans() }}</td>
                                                <td>
                                                    <span class="text-muted small">
                                                        {{ Str::limit($item->value, 80) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($stats['count'] > 100)
                                    <div class="alert alert-info mt-3 mb-0">
                                        Showing top 100 of {{ $stats['count'] }} cache entries.
                                    </div>
                                @endif
                            @else
                                <div class="text-center p-5">
                                    <div class="avatar-md mx-auto mb-3">
                                        <div class="avatar-title rounded-circle bg-soft-primary text-primary font-size-24">
                                            <i class="mdi mdi-database-off"></i>
                                        </div>
                                    </div>
                                    <h5>Cache is Empty</h5>
                                    <p class="text-muted">There are no items in the cache database.</p>
                                </div>
                            @endif
                        @elseif($driver == 'file')
                             <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card border shadow-none h-100">
                                        <div class="card-body text-center">
                                            <div class="avatar-sm mx-auto mb-4">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-20">
                                                    <i class="mdi mdi-file-multiple"></i>
                                                </span>
                                            </div>
                                            <h5 class="font-size-15 mb-1">File Cache Driver</h5>
                                            <p class="text-muted">Cache is stored in the filesystem.</p>
                                            <div class="row mt-4">
                                                <div class="col-6">
                                                    <h5 class="mb-1">{{ $stats['count'] }}</h5>
                                                    <p class="text-muted mb-0 font-size-12">Cached Files</p>
                                                </div>
                                                <div class="col-6">
                                                    <h5 class="mb-1">{{ $stats['size'] }} MB</h5>
                                                    <p class="text-muted mb-0 font-size-12">Total Size</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                     <div class="card border shadow-none h-100">
                                         <div class="card-body d-flex flex-column justify-content-center text-center">
                                             <h5 class="font-size-14 mb-3">Inspect Cache Content</h5>
                                             <p class="text-muted mb-4">
                                                 You can scan the file system to inspect cached values. 
                                                 <br><span class="text-warning"><i class="mdi mdi-alert"></i> Keys are hashed and unreadable.</span>
                                             </p>
                                             @if(request()->has('scan'))
                                                 <a href="{{ route('dashboard.cache') }}#explorer" class="btn btn-outline-secondary">
                                                     <i class="mdi mdi-eye-off me-1"></i> Hide Content
                                                 </a>
                                             @else
                                                 <a href="{{ route('dashboard.cache', ['scan' => 'true']) }}#explorer" class="btn btn-primary">
                                                     <i class="mdi mdi-eye me-1"></i> Scan Files (Limit 50)
                                                 </a>
                                             @endif
                                         </div>
                                     </div>
                                </div>
                             </div>

                             @if(count($cacheData) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-centered mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Hash (Key)</th>
                                                <th>Expiration</th>
                                                <th>Value Preview</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cacheData as $item)
                                            <tr>
                                                <td>
                                                    <code class="text-muted">{{ Str::limit($item->key, 20) }}</code>
                                                </td>
                                                <td>
                                                    @if($item->expiration >= 9999999999)
                                                        <span class="badge badge-soft-success">Forever</span>
                                                    @else
                                                        {{ \Carbon\Carbon::createFromTimestamp($item->expiration)->diffForHumans() }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-dark font-family-monospace">
                                                        {{ Str::limit($item->value, 100) }}
                                                    </small>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                             @elseif(request()->has('scan'))
                                <div class="alert alert-info text-center">No cache files found to scan.</div>
                             @endif
                        @else
                             <div class="alert alert-secondary">
                                 Cache viewer is not supported for <strong>{{ $driver }}</strong> driver.
                             </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function clearCache(type) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will clear the ' + type + ' cache.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, clear it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("dashboard.cache.clear") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    type: type
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Clearing cache, please wait.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 3000
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'An error occurred'
                    });
                }
            });
        }
    });
}

function recache(type) {
    Swal.fire({
        title: 'Rebuild Cache?',
        text: 'This will rebuild the ' + type + ' cache.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, rebuild it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("dashboard.cache.recache") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    type: type
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Rebuilding cache, please wait.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 3000
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'An error occurred'
                    });
                }
            });
        }
    });
}

function optimize() {
    Swal.fire({
        title: 'Optimize Application?',
        text: 'This will optimize the application for better performance.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, optimize it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("dashboard.cache.optimize") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Optimizing application, please wait.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 3000
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'An error occurred'
                    });
                }
            });
        }
    });
}

function clearOptimize() {
    Swal.fire({
        title: 'Clear Optimization?',
        text: 'This will clear all optimization caches.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, clear it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("dashboard.cache.clear-optimize") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Clearing optimization, please wait.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 3000
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'An error occurred'
                    });
                }
            });
        }
    });
}
</script>
@endpush
