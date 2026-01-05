@props(['id', 'title', 'position' => 'end', 'width' => null])

<div class="offcanvas offcanvas-{{ $position }} border-0" tabindex="-1" id="{{ $id }}" aria-labelledby="{{ $id }}Label" style="{{ $width ? 'width: '.$width : '' }}" {{ $attributes }}>
    <div class="offcanvas-header bg-light">
        <h5 class="offcanvas-title" id="{{ $id }}Label">{!! $title !!}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        {{ $slot }}
    </div>
    @if(isset($footer))
        <div class="offcanvas-footer p-3 border-top bg-light-subtle">
            {{ $footer }}
        </div>
    @endif
</div>
