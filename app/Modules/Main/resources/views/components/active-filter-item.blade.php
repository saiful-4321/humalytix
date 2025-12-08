@props(['key', 'label', 'value'])

@if(request($key))
    <span class="badge bg-white text-dark border d-flex align-items-center p-1 ps-2 pe-1 shadow-sm rounded-pill">
        <span class="text-muted font-size-11 me-1">{{ $label }}:</span>
        <span class="fw-bold font-size-12 text-primary">{{ $value }}</span>
        <a href="{{ request()->fullUrlWithQuery([$key => null]) }}" class="ms-2 text-secondary hover-text-danger d-flex align-items-center justify-content-center transition-all" style="width: 18px; height: 18px; background: #f3f4f6; border-radius: 50%; text-decoration: none;">
            <i class="mdi mdi-close font-size-10"></i>
        </a>
    </span>
@endif
