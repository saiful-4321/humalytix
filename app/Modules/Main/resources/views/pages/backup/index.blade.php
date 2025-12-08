@extends("Main::layouts.app")

@section('content')
<div class="row">
    <div class="col-lg-5 col-md-8 col-sm-12">                        
        <h2>{{ __('Backup Management') }}</h2>
    </div>            
    <div class="col-lg-7 col-md-4 col-sm-12 text-right">
        <ul class="breadcrumb justify-content-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}"><i class="fas fa-home"></i></a></li>                            
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">Backup Management</li>
        </ul>
    </div>

    <div class="col-lg-12 col-md-12">
        <!-- System Requirements Check -->
        @if(!$systemCheck['mysqldump'] || !$systemCheck['zip'] || !$systemCheck['storage_writable'])
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h6><i class="mdi mdi-alert me-2"></i>System Requirements</h6>
            <p class="mb-2">Some system requirements are not met. Backups may not work correctly:</p>
            <ul class="mb-0">
                @if(!$systemCheck['mysqldump'])
                <li>
                    <strong>mysqldump:</strong> <span class="badge bg-danger">Not Found</span>
                    <br><small class="text-muted">Install with: <code>brew install mysql-client</code> then add to PATH</small>
                </li>
                @endif
                @if(!$systemCheck['zip'])
                <li>
                    <strong>zip:</strong> <span class="badge bg-danger">Not Found</span>
                    <br><small class="text-muted">Install with: <code>brew install zip</code></small>
                </li>
                @endif
                @if(!$systemCheck['storage_writable'])
                <li>
                    <strong>Storage:</strong> <span class="badge bg-danger">Not Writable</span>
                    <br><small class="text-muted">Fix permissions: <code>chmod -R 775 storage/</code></small>
                </li>
                @endif
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Create Backup Section -->
        <div class="card bg-white mb-3">
            <div class="card-header border-bottom">
                <h6 class="font-weight-medium mb-0">Create New Backup</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Full Backup -->
                    <div class="col-md-3">
                        <div class="card border h-100 hover-shadow">
                            <div class="card-body text-center p-3">
                                <i class="mdi mdi-backup-restore text-primary" style="font-size: 42px;"></i>
                                <h5 class="mt-2 mb-1">Full Backup</h5>
                                <p class="text-muted small mb-3">Database + Files + Storage</p>
                                <button class="btn btn-sm btn-primary w-100" onclick="createBackup('full')">
                                    <i class="mdi mdi-play me-1"></i> Create
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Database Only -->
                    <div class="col-md-3">
                        <div class="card border h-100 hover-shadow">
                            <div class="card-body text-center p-3">
                                <i class="mdi mdi-database text-success" style="font-size: 42px;"></i>
                                <h5 class="mt-2 mb-1">Database Only</h5>
                                <p class="text-muted small mb-3">Complete SQL Dump</p>
                                <button class="btn btn-success btn-sm w-100" onclick="createBackup('db')">
                                    <i class="mdi mdi-play me-1"></i> Create
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Code Only -->
                    <div class="col-md-3">
                        <div class="card border h-100 hover-shadow">
                            <div class="card-body text-center p-3">
                                <i class="mdi mdi-code-tags text-info" style="font-size: 42px;"></i>
                                <h5 class="mt-2 mb-1">Source Code</h5>
                                <p class="text-muted small mb-3">App Code Only (No Storage)</p>
                                <button class="btn btn-info btn-sm w-100 text-white" onclick="createBackup('code')">
                                    <i class="mdi mdi-play me-1"></i> Create
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Storage Only -->
                    <div class="col-md-3">
                        <div class="card border h-100 hover-shadow">
                            <div class="card-body text-center p-3">
                                <i class="mdi mdi-folder-multiple-image text-warning" style="font-size: 42px;"></i>
                                <h5 class="mt-2 mb-1">Files & Storage</h5>
                                <p class="text-muted small mb-3">User Uploads & Documents</p>
                                <button class="btn btn-warning btn-sm w-100 text-white" onclick="createBackup('files')">
                                    <i class="mdi mdi-play me-1"></i> Create
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backup List -->
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <h6 class="font-weight-medium mb-0">Available Backups ({{ count($backups) }})</h6>
                    </div>
                    <div class="col-md-7">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <div class="btn-group btn-group-sm me-2" role="group">
                                <button type="button" class="btn btn-outline-secondary active filter-btn" data-filter="all">All</button>
                                <button type="button" class="btn btn-outline-primary filter-btn" data-filter="full">Full</button>
                                <button type="button" class="btn btn-outline-success filter-btn" data-filter="db">DB</button>
                                <button type="button" class="btn btn-outline-info filter-btn" data-filter="code">Code</button>
                                <button type="button" class="btn btn-outline-warning filter-btn" data-filter="files">Files</button>
                            </div>
                            <button class="btn btn-warning btn-sm" onclick="cleanBackups()">
                                <i class="mdi mdi-delete-sweep me-1"></i> Clean Old
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                @if(count($backups) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-top-0 align-middle ps-4">File Name</th>
                                <th class="border-top-0 align-middle text-center">Size</th>
                                <th class="border-top-0 align-middle text-center">Created Date</th>
                                <th class="border-top-0 align-middle text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $backup)
                            <tr class="backup-row" data-name="{{ $backup['name'] }}">
                                <td class="align-middle ps-4">
                                    @if(str_contains($backup['name'], 'db_'))
                                        <i class="mdi mdi-database text-success me-2 font-size-16"></i>
                                    @elseif(str_contains($backup['name'], 'code_'))
                                        <i class="mdi mdi-code-tags text-info me-2 font-size-16"></i>
                                    @elseif(str_contains($backup['name'], 'files_'))
                                        <i class="mdi mdi-folder-multiple-image text-warning me-2 font-size-16"></i>
                                    @else
                                        <i class="mdi mdi-package-variant text-primary me-2 font-size-16"></i>
                                    @endif
                                    <span class="text-dark fw-medium">{{ $backup['name'] }}</span>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge bg-light text-dark font-size-12">{{ $backup['size'] }}</span>
                                </td>
                                <td class="align-middle text-center text-muted">
                                    <i class="mdi mdi-calendar-clock me-1"></i> {{ $backup['date'] }}
                                </td>
                                <td class="align-middle text-end pe-4">
                                    <a href="{{ route('dashboard.backup.download', $backup['name']) }}" 
                                       class="btn btn-sm btn-soft-success me-1" 
                                       title="Download">
                                        <i class="mdi mdi-download"></i>
                                    </a>
                                    <button class="btn btn-sm btn-soft-danger" 
                                            onclick="deleteBackup('{{ $backup['name'] }}')" 
                                            title="Delete">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="mdi mdi-folder-open text-muted" style="font-size: 64px;"></i>
                    <p class="text-muted mt-3">No backups available. Create your first backup!</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Backup Information -->
        <div class="card bg-white mt-3 mb-5">
            <div class="card-header border-bottom">
                <h6 class="font-weight-medium mb-0"><i class="mdi mdi-information me-2"></i>Backup Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 px-4 pt-3">
                        <h6><i class="mdi mdi-package-variant me-1"></i>What gets backed up?</h6>
                        
                        <div class="mb-3">
                            <strong class="text-success"><i class="mdi mdi-backup-restore me-1"></i>Full Backup Includes:</strong>
                            <ul class="text-muted small mt-2">
                                <li><strong>Source Code:</strong> app/, routes/, config/, resources/</li>
                                <li><strong>Database:</strong> Complete database dump</li>
                                <li><strong>Storage:</strong> Uploaded files, documents, images</li>
                                <li><strong>Public Assets:</strong> CSS, JS, images</li>
                                <li><strong>Configuration:</strong> .env, composer.json, package.json</li>
                                <li><strong>Database Schema:</strong> Migrations, seeders</li>
                            </ul>
                            <small class="text-info"><i class="mdi mdi-information me-1"></i>Excludes: vendor/, node_modules/, cache, logs</small>
                        </div>

                        <div>
                            <strong class="text-info"><i class="mdi mdi-database me-1"></i>Database Only Includes:</strong>
                            <ul class="text-muted small mt-2">
                                <li>Complete database structure and data</li>
                                <li>All tables, indexes, and relationships</li>
                                <li>Compressed SQL dump</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-4 pt-3">
                        <h6><i class="mdi mdi-shield-check me-1"></i>Best Practices</h6>
                        <ul class="text-muted small">
                            <li>Create backups before major updates</li>
                            <li>Download important backups locally</li>
                            <li>Test restore process regularly</li>
                            <li>Keep backups in multiple locations</li>
                            <li>Use automated schedules</li>
                            <li>Monitor backup health</li>
                            <li>Clean old backups periodically</li>
                        </ul>
                    </div>

                    <div class="col-md-4 pt-3">
                        <h6><i class="mdi mdi-clock-outline me-1"></i>Automated Backups</h6>
                        <p class="text-muted small mb-2">
                            Configure automatic backup schedules, retention policies, and cleanup tasks.
                        </p>
                        @can('backup-management')
                        <a href="{{ route('dashboard.backup.schedule') }}" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-cog me-1"></i>Configure Schedule
                        </a>
                        @else
                        <p class="text-muted small">
                            <i class="mdi mdi-lock me-1"></i>Contact admin to configure schedules
                        </p>
                        @endcan
                        <hr>
                        <small class="text-muted">
                            <i class="mdi mdi-alert-circle-outline me-1"></i>
                            Requires cron job: <code>* * * * * php artisan schedule:run</code>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createBackup(type) {
    let typeName = '';
    let confirmColor = '#007bff';
    
    switch(type) {
        case 'db': typeName = 'Database Only'; confirmColor = '#28a745'; break;
        case 'code': typeName = 'Source Code'; confirmColor = '#17a2b8'; break;
        case 'files': typeName = 'Files & Storage'; confirmColor = '#ffc107'; break;
        case 'full': 
        default: 
            typeName = 'Full'; confirmColor = '#007bff'; break;
    }
    
    // Backwards compatibility if boolean is passed
    if (type === true) { type = 'db'; typeName = 'Database Only'; confirmColor = '#28a745'; }
    if (type === false) { type = 'full'; typeName = 'Full'; confirmColor = '#007bff'; }
    
    Swal.fire({
        title: 'Create ' + typeName + ' Backup?',
        text: 'This may take a few moments depending on the size of your data.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: confirmColor,
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, create backup!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("dashboard.backup.create") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    type: type
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Creating Backup...',
                        text: 'Please wait while we create your backup.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
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
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'An error occurred while creating backup'
                    });
                }
            });
        }
    });
}

function deleteBackup(fileName) {
    Swal.fire({
        title: 'Delete Backup?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("dashboard.backup.delete", ":fileName") }}'.replace(':fileName', fileName),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message,
                        timer: 2000
                    }).then(() => {
                        location.reload();
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

function cleanBackups() {
    Swal.fire({
        title: 'Clean Old Backups?',
        text: 'This will remove backups according to your retention policy.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, clean them!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("dashboard.backup.clean") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Cleaning...',
                        text: 'Please wait while we clean old backups.',
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
                        timer: 2000
                    }).then(() => {
                        location.reload();
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

$(document).ready(function() {
    $('.filter-btn').on('click', function() {
        const filter = $(this).data('filter');
        
        // Update active button state
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Filter table rows
        if (filter === 'all') {
            $('.backup-row').show();
            // Show empty state if no rows exist, but we handle that in blade
        } else {
            $('.backup-row').each(function() {
                const name = $(this).data('name');
                if (name.includes(filter + '_')) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });
});
</script>
@endpush
