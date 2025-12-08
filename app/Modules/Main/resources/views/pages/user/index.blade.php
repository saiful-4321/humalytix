@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Users - <small>({{ request()->get("status") ? request()->get("status") : "All"  }})</small> </h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.user') }}">User Management</a></li>
                <li class="breadcrumb-item active">Users</li>
            </ul>
        </div>
    </div>
</div>


@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12 col-md-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Users - (showing {{ $result->firstItem()??0 }} to {{ $result->lastItem()??0 }} of total {{ $result->total()??0 }} entries)</h6>
                    
                    <div class="d-flex align-items-center gap-2">
                        @can('user-create')
                        <a href="javascript:void(0)" route="{{ route('dashboard.user.create') }}" data-toggle="dynamicModal" class="btn btn-info btn-sm d-flex align-items-center font-weight-medium">
                            <i class="mdi mdi-plus me-1"></i> Add New User
                        </a>
                        @endcan
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#userFilter" aria-controls="userFilter">
                            <i class="mdi mdi-filter-variant me-1"></i> Filter Users
                        </button>

                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-export me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('dashboard.user.export', ['format' => 'xlsx']) }}">Excel (XLSX)</a></li>
                                <li><a class="dropdown-item" href="{{ route('dashboard.user.export', ['format' => 'csv']) }}">CSV</a></li>
                                <li><a class="dropdown-item" href="{{ route('dashboard.user.export', ['format' => 'pdf']) }}">PDF</a></li>
                            </ul>
                        </div>
                        
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="offcanvas" data-bs-target="#importUserOffcanvas">
                            <i class="mdi mdi-import me-1"></i> Import
                        </button>
                    </div>
                </div>
            </div>

            <!-- Active Filters Section -->
            @if(request()->hasAny(['user_id', 'name', 'email', 'mobile', 'role', 'nid', 'status']))
                <x-Main::active-filters :url="route('dashboard.user')">
                    <x-Main::active-filter-item key="user_id" label="User" :value="$userDropdown[request('user_id')] ?? request('user_id')" />
                    <x-Main::active-filter-item key="name" label="Name" :value="request('name')" />
                    <x-Main::active-filter-item key="email" label="Email" :value="request('email')" />
                    <x-Main::active-filter-item key="mobile" label="Mobile" :value="request('mobile')" />
                    <x-Main::active-filter-item key="role" label="Role" :value="$roles[request('role')] ?? request('role')" />
                    <x-Main::active-filter-item key="nid" label="NID" :value="request('nid')" />
                    <x-Main::active-filter-item key="status" label="Status" :value="$status[request('status')] ?? request('status')" />
                </x-Main::active-filters>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border">
                    @include('Main::pages.user.list-table')
                </div>
            </div>

            @if (!empty($result->count()))
                <div class="d-flex justify-content-end mt-3 mb-0">
                    {!! $result->appends(request()->all())->links() !!}
                </div>
            @endif 
        </div>
    </div>
</div>

@include("Main::pages.user.filter")

<!-- Import Offcanvas -->
<div class="offcanvas offcanvas-end w-50" tabindex="-1" id="importUserOffcanvas" aria-labelledby="importUserOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="importUserOffcanvasLabel">Import Users</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="importUserForm" action="{{ route('dashboard.user.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label for="importFile" class="form-label mb-0">Upload File</label>
                    <div class="font-size-12">
                        <span class="text-muted me-1">Sample:</span>
                        <a href="{{ route('dashboard.user.sample', ['format' => 'xlsx']) }}" class="badge bg-soft-success text-success text-decoration-none border border-success me-1"><i class="mdi mdi-download"></i> XLSX</a>
                        <a href="{{ route('dashboard.user.sample', ['format' => 'csv']) }}" class="badge bg-soft-primary text-primary text-decoration-none border border-primary"><i class="mdi mdi-download"></i> CSV</a>
                    </div>
                </div>
                <div class="input-group">
                    <input class="form-control" type="file" id="importFile" name="file" required accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                    <input type="text" class="form-control" id="previewSearchUser" name="preview_search" placeholder="Search in file (Preview)" style="max-width: 200px;">
                    <button type="button" id="btnPreviewImport" class="btn btn-info text-white">
                        <i class="mdi mdi-eye me-1"></i> Preview
                    </button>
                </div>
            </div>
            
            <div id="previewContainer" class="d-none mb-3">
                <h6 class="text-muted mb-2">Preview</h6>
                <div class="table-responsive border rounded" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-sm table-striped font-size-12 mb-0" id="previewTable">
                        <thead class="bg-light sticky-top"></thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div id="previewSummary" class="mt-2 text-muted font-size-12"></div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" id="btnConfirmImport" class="btn btn-success d-none">
                    <i class="mdi mdi-check-circle me-1"></i> Confirm Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


@push("scripts")
<script> 
    var previewData = [];
    var renderedCount = 0;
    var CHUNK_SIZE = 500;

    function renderPreviewChunk() {
        var chunk = previewData.slice(renderedCount, renderedCount + CHUNK_SIZE);
        if (chunk.length === 0) return;
        
        var tbody = '';
        chunk.forEach(function(row) {
            tbody += '<tr>';
            var status = row['_status'];
            var statusClass = status.includes('New') ? 'text-success' : 'text-warning';
            for (var key in row) {
                 if(key !== '_status') tbody += '<td>' + (row[key] ?? '') + '</td>';
            }
            tbody += '<td class="'+statusClass+'">' + status + '</td></tr>';
        });
        
        $('#previewTable tbody').append(tbody);
        renderedCount += chunk.length;
    }

    $('#previewContainer .table-responsive').on('scroll', function() {
        if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 100) {
            renderPreviewChunk();
        }
    });

    $('#btnPreviewImport').click(function() {
        var fileInput = document.getElementById('importFile');
        if (fileInput.files.length === 0) {
            Swal.fire('Error', 'Please select a file first', 'error');
            return;
        }

        var formData = new FormData($('#importUserForm')[0]);
        formData.append('dry_run', '1');

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Analyzing...');

        $.ajax({
            url: "{{ route('dashboard.user.import') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            timeout: 0, // No timeout
            success: function(response) {
                btn.prop('disabled', false).html(originalText);
                
                if (response.status === 'success') {
                    var data = response.data.preview;
                    var count = response.data.count;
                    
                    var thead = '';
                    
                    if (data.length > 0) {
                        thead += '<tr>';
                        for (var key in data[0]) {
                            if(key !== '_status') thead += '<th>' + key + '</th>';
                        }
                        thead += '<th>Status</th></tr>';
                        
                        // Initialize Batch Rendering
                        previewData = data;
                        renderedCount = 0;
                        $('#previewTable tbody').empty();
                        renderPreviewChunk();
                    } else {
                         $('#previewTable tbody').html('<tr><td colspan="5" class="text-center">No data found</td></tr>');
                    }
                    
                    $('#previewTable thead').html(thead);
                    $('#previewSummary').text('Showing all ' + count + ' rows.');
                    $('#previewContainer').removeClass('d-none');
                    $('#btnConfirmImport').removeClass('d-none');
                } else {
                     Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalText);
                Swal.fire('Error', xhr.responseJSON?.message || 'Failed to preview file (Timeout/Error).', 'error');
            }
        });
    });

    function pretendConfirm(input) {
        Swal.fire({
            title: 'Are you sure ?',
            text: "Would you like to act as this user?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Yes, I want!',
            reverseButtons:true
        }).then((result) => {
            if (result.value) {
                window.location.href=input.getAttribute("route");
            } else {
                event.preventDefault();
                return;
            }
        })
    }
    $('#importUserForm').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#btnConfirmImport');
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