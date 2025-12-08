@extends("Main::layouts.app")


@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">                        
            <h2>{{ __('Permissions') }}</h2>
        </div>            
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}"><i class="fas fa-home"></i></a></li>                            
                <li class="breadcrumb-item">Role & Permission</li>
                <li class="breadcrumb-item active">Permissions</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix"> 
    <div class="col-lg-12 col-md-12">

        <div class="card bg-white"> 
            <div class="card-header border-bottom"> 
                <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Permission List - (showing {{ $result->firstItem()??0 }} to {{ $result->lastItem()??0 }} of total {{ $result->total()??0 }} entries)</h6>

                    <div class="d-flex align-items-center gap-2">
                        @can('permission-create')
                        <a href="javascript:void(0)" route="{{  route('dashboard.role.permission.create')  }}" data-toggle="dynamicModal" class="btn btn-info btn-sm d-flex align-items-center font-weight-medium">
                            <i class="mdi mdi-plus me-1"></i> Add New Permission
                        </a>
                        @endcan
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#permissionFilter" aria-controls="permissionFilter">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter Permissions
                        </button>

                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-export me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('dashboard.role.permission.export', ['format' => 'xlsx']) }}">Excel (XLSX)</a></li>
                                <li><a class="dropdown-item" href="{{ route('dashboard.role.permission.export', ['format' => 'csv']) }}">CSV</a></li>
                                <li><a class="dropdown-item" href="{{ route('dashboard.role.permission.export', ['format' => 'pdf']) }}">PDF</a></li>
                            </ul>
                        </div>
                        
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="offcanvas" data-bs-target="#importPermissionOffcanvas">
                            <i class="mdi mdi-import me-1"></i> Import
                        </button>
                    </div>
                </div> 
            </div>

            <!-- Active Filters Section -->
            @if(request()->hasAny(['name', 'module', 'start_date', 'end_date']))
                <x-Main::active-filters :url="route('dashboard.role.permission')">
                    <x-Main::active-filter-item key="name" label="Name" :value="request('name')" />
                    <x-Main::active-filter-item key="module" label="Module" :value="request('module')" />
                    <x-Main::active-filter-item key="start_date" label="Start Date" :value="request('start_date')" />
                    <x-Main::active-filter-item key="end_date" label="End Date" :value="request('end_date')" />
                </x-Main::active-filters>
            @endif

            <div class="card-body p-0"> 
                <div class="table-responsive rounded-10 border">
                    @include('Main::pages.role.permission-list-table')

                    <div class="d-flex justify-content-end mt-3 mb-0 px-3"> 
                        @if (!empty($result) && $result->count() > 0)
                            {{ $result->appends($_REQUEST)->render() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include("Main::pages.role.permission-filter")

<!-- Import Offcanvas -->
<div class="offcanvas offcanvas-end w-50" tabindex="-1" id="importPermissionOffcanvas" aria-labelledby="importPermissionOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="importPermissionOffcanvasLabel">Import Permissions</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="importPermissionForm" action="{{ route('dashboard.role.permission.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label for="importPermFile" class="form-label mb-0">Upload File</label>
                    <div class="font-size-12">
                        <span class="text-muted me-1">Sample:</span>
                        <a href="{{ route('dashboard.role.permission.sample', ['format' => 'xlsx']) }}" class="badge bg-soft-success text-success text-decoration-none border border-success me-1"><i class="mdi mdi-download"></i> XLSX</a>
                        <a href="{{ route('dashboard.role.permission.sample', ['format' => 'csv']) }}" class="badge bg-soft-primary text-primary text-decoration-none border border-primary"><i class="mdi mdi-download"></i> CSV</a>
                    </div>
                </div>
                <div class="input-group">
                    <input class="form-control" type="file" id="importPermFile" name="file" required accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                    <input type="text" class="form-control" id="previewSearchPerm" name="preview_search" placeholder="Search in file (Preview)" style="max-width: 200px;">
                    <button type="button" id="btnPreviewPermImport" class="btn btn-info text-white">
                        <i class="mdi mdi-eye me-1"></i> Preview
                    </button>
                </div>
            </div>
            
            <div id="previewContainerPerm" class="d-none mb-3">
                <h6 class="text-muted mb-2">Preview</h6>
                <div class="table-responsive border rounded" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-sm table-striped font-size-12 mb-0" id="previewTablePerm">
                        <thead class="bg-light sticky-top"></thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div id="previewSummaryPerm" class="mt-2 text-muted font-size-12"></div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" id="btnConfirmPermImport" class="btn btn-success d-none">
                    <i class="mdi mdi-check-circle me-1"></i> Confirm Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push("scripts")
<script> 
    $('#btnPreviewPermImport').click(function() {
        var fileInput = document.getElementById('importPermFile');
        if (fileInput.files.length === 0) {
            Swal.fire('Error', 'Please select a file first', 'error');
            return;
        }

        var formData = new FormData($('#importPermissionForm')[0]);
        formData.append('dry_run', '1');

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Analyzing...');

        $.ajax({
            url: "{{ route('dashboard.role.permission.import') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                btn.prop('disabled', false).html(originalText);
                
                if (response.status === 'success') {
                    var data = response.data.preview;
                    var count = response.data.count;
                    
                    var thead = '';
                    var tbody = '';
                    
                    if (data.length > 0) {
                        thead += '<tr>';
                        for (var key in data[0]) {
                            if(key !== '_status') thead += '<th>' + key + '</th>';
                        }
                        thead += '<th>Status</th></tr>';
                        
                        data.forEach(function(row) {
                            tbody += '<tr>';
                            var status = row['_status'];
                            var statusClass = status.includes('New') ? 'text-success' : 'text-warning';
                            
                            for (var key in row) {
                                if(key !== '_status') tbody += '<td>' + (row[key] ?? '') + '</td>';
                            }
                            tbody += '<td class="'+statusClass+'">' + status + '</td></tr>';
                        });
                    }
                    
                    $('#previewTablePerm thead').html(thead);
                    $('#previewTablePerm tbody').html(tbody);
                    $('#previewSummaryPerm').text('Showing all ' + count + ' rows.');
                    $('#previewContainerPerm').removeClass('d-none');
                    $('#btnConfirmPermImport').removeClass('d-none');
                } else {
                     Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalText);
                Swal.fire('Error', xhr.responseJSON?.message || 'Failed to preview file.', 'error');
            }
        });
    });
    $('#importPermissionForm').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#btnConfirmPermImport');
        var originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Importing... (This may take a while)');
        
        var formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            timeout: 0, 
            success: function(response) {
                if(response.status === 'success') {
                    Swal.fire({
                        title: 'Success',
                        text: response.message,
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                    btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                 btn.prop('disabled', false).html(originalText);
                 Swal.fire('Error', xhr.responseJSON?.message || 'Import failed.', 'error');
            }
        });
    });
</script>
@endpush