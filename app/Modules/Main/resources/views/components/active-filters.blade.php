<div class="card-body border-bottom bg-light p-2">
    <div class="d-flex align-items-center flex-wrap gap-2">
        <span class="text-muted font-size-12 fw-bold me-2 ms-2 text-uppercase"><i class="mdi mdi-filter-check text-primary me-1"></i>Active Filters:</span>
        
        {{ $slot }}

        <a href="{{ $url ?? url()->current() }}" class="btn btn-sm btn-soft-danger rounded-pill font-size-11 px-3 ms-auto waves-effect waves-light">
            <i class="mdi mdi-close-circle-outline me-1"></i>Clear All
        </a>
    </div>
</div>
