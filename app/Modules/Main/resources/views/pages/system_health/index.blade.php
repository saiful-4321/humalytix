@extends("Main::layouts.app")

@section("content")
<div class="row mb-2">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">System Health</h4>
            <div class="page-title-right">
                <a href="{{ route('dashboard.system-health') }}" class="btn btn-primary btn-sm">
                    <i class="mdi mdi-refresh me-1"></i> Refresh Status
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards (Quick View) -->
<div class="row">
    <!-- Disk -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center p-2">
                    <div class="avatar-sm flex-shrink-0 me-3">
                        <span class="avatar-title bg-soft-primary text-primary rounded-circle font-size-24">
                            <i class="mdi mdi-harddisk"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="font-size-15 mb-1">Disk Usage</h5>
                        <div class="progress animated-progess progress-sm">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $health['disk']['percentage'] }}%" aria-valuenow="{{ $health['disk']['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-muted mt-2 mb-0">{{ $health['disk']['used'] }} Used / {{ $health['disk']['total'] }} Total</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Memory -->
     <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center p-2">
                    <div class="avatar-sm flex-shrink-0 me-3">
                        <span class="avatar-title bg-soft-info text-info rounded-circle font-size-24">
                            <i class="mdi mdi-memory"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="font-size-15 mb-1">App Memory</h5>
                        <p class="text-muted mb-0">{{ $health['memory']['usage'] }} (Peak: {{ $health['memory']['peak'] }})</p>
                        <small class="text-muted">Limit: {{ $health['memory']['limit'] }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Database -->
     <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center p-2">
                    <div class="avatar-sm flex-shrink-0 me-3">
                        <span class="avatar-title bg-soft-success text-success rounded-circle font-size-24">
                            <i class="mdi mdi-database"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="font-size-15 mb-1">Database</h5>
                        <p class="text-muted mb-0">{{ $health['database']['size'] }} MB</p>
                        <small class="text-muted">{{ $health['database']['tables'] }} Tables ({{ $health['database']['connection'] }})</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Server -->
     <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center p-2">
                    <div class="avatar-sm flex-shrink-0 me-3">
                        <span class="avatar-title bg-soft-warning text-warning rounded-circle font-size-24">
                            <i class="mdi mdi-server"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="font-size-15 mb-1">Server</h5>
                        <p class="text-muted mb-0">{{ $health['server']['server_ip'] }}</p>
                        <small class="text-muted" title="{{ $health['server']['os'] }}">{{ Str::limit($health['server']['os'], 20) }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Application Details -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Application Status</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <tbody>
                            <tr>
                                <th scope="row">Laravel Version</th>
                                <td><span class="badge badge-soft-primary font-size-12">{{ $health['application']['laravel_version'] }}</span></td>
                            </tr>
                            <tr>
                                <th scope="row">Environment</th>
                                <td>
                                    @if($health['application']['environment'] == 'production')
                                        <span class="badge badge-soft-success font-size-12">Production</span>
                                    @else
                                        <span class="badge badge-soft-warning font-size-12">{{ ucfirst($health['application']['environment']) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Debug Mode</th>
                                <td>
                                    @if($health['application']['debug_mode'])
                                        <span class="badge badge-soft-danger font-size-12">Enabled</span>
                                    @else
                                         <span class="badge badge-soft-success font-size-12">Disabled</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">App URL</th>
                                <td>{{ $health['application']['url'] }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Timezone</th>
                                <td>{{ $health['server']['timezone'] }}</td>
                            </tr>
                             <tr>
                                <th scope="row">Locale</th>
                                <td>{{ $health['application']['locale'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- PHP Configuration -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">PHP Configuration</h4>
            </div>
            <div class="card-body">
                 <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <tbody>
                            <tr>
                                <th scope="row">PHP Version</th>
                                <td><span class="badge badge-soft-info font-size-12">{{ $health['php']['version'] }}</span></td>
                            </tr>
                            <tr>
                                <th scope="row">Memory Limit</th>
                                <td>{{ $health['php']['memory_limit'] }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Upload Max Filesize</th>
                                <td>{{ $health['php']['upload_max_filesize'] }}</td>
                            </tr>
                             <tr>
                                <th scope="row">Post Max Size</th>
                                <td>{{ $health['php']['post_max_size'] }}</td>
                            </tr>
                             <tr>
                                <th scope="row">Max Execution Time</th>
                                <td>{{ $health['php']['max_execution_time'] }}s</td>
                            </tr>
                             <tr>
                                <th scope="row">Server Time</th>
                                <td>{{ $health['server']['server_time'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Extensions -->
    <div class="col-12">
        <div class="card">
             <div class="card-header">
                <h4 class="card-title">Loaded Extensions</h4>
            </div>
            <div class="card-body">
                <div>
                    @foreach($health['php']['extensions'] as $ext)
                        <span class="badge badge-soft-secondary font-size-12 m-1">{{ $ext }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
