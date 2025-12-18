@extends('Main::layouts.app')

@section('title', 'Policy Library')

@section('content')
@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Policy Library</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('hrm.dashboard') }}">HRM</a></li>
                <li class="breadcrumb-item">Compliance</li>
                <li class="breadcrumb-item active">Policies</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">Company Policies</h6>
                    <div class="d-flex gap-2">
                        <div class="search-box d-inline-block">
                            <div class="position-relative">
                                <input type="text" class="form-control" id="searchPolicy" placeholder="Search Policies...">
                                <i class="bx bx-search-alt search-icon"></i>
                            </div>
                        </div>
                        @can('hrm.policies.create')
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#uploadPolicyOffcanvas">
                            <i class="bx bx-upload me-1"></i> Upload Policy
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="policyGrid">
    @foreach($policies as $policy)
    <div class="col-xl-3 col-sm-6 policy-item" data-title="{{ strtolower($policy->title) }}">
        <div class="card text-center">
            <div class="card-body">
                <div class="avatar-sm mx-auto mb-4">
                    <span class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-24">
                        <i class="bx bxs-file-pdf"></i>
                    </span>
                </div>
                <h5 class="font-size-15 mb-1"><a href="{{ Storage::url($policy->file_path) }}" target="_blank" class="text-dark">{{ $policy->title }}</a></h5>
                <p class="text-muted">{{ $policy->description ?? 'No description' }}</p>

                <div>
                    <span class="badge bg-soft-primary text-primary font-size-11">v{{ $policy->version }}</span>
                    <span class="badge bg-soft-secondary text-secondary font-size-11">{{ $policy->effective_date ? $policy->effective_date->format('d M Y') : 'N/A' }}</span>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top">
                <div class="contact-links d-flex font-size-20">
                    <div class="flex-fill">
                        <a href="{{ Storage::url($policy->file_path) }}" target="_blank" data-bs-toggle="tooltip" title="View"><i class="bx bx-show"></i></a>
                    </div>
                    <div class="flex-fill">
                        <a href="{{ Storage::url($policy->file_path) }}" download data-bs-toggle="tooltip" title="Download"><i class="bx bx-download"></i></a>
                    </div>
                    @can('hrm.policies.delete')
                    <div class="flex-fill">
                        <form action="{{ route('hrm.policies.destroy', $policy->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this policy?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger p-0" style="font-size: 20px;"><i class="bx bx-trash"></i></button>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<script>
    document.getElementById('searchPolicy').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let items = document.querySelectorAll('.policy-item');
        items.forEach(function(item) {
            let title = item.getAttribute('data-title');
            if (title.indexOf(value) > -1) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>

<!-- Upload Policy Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="uploadPolicyOffcanvas" style="width: 500px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Upload New Policy</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('hrm.policies.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Policy Title</label>
                <input type="text" class="form-control" name="title" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3"></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Effective Date</label>
                    <input type="date" class="form-control" name="effective_date">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Version</label>
                    <input type="text" class="form-control" name="version" value="1.0">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Policy File (PDF)</label>
                <input type="file" class="form-control" name="file" accept="application/pdf" required>
            </div>
            <div class="d-flex gap-2 justify-content-end mt-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>
@endsection
