<div class="col-sm-6">
    <ol class="breadcrumb float-sm-right">
        @foreach($breadcrumbs as $breadcrumb)
            @if($loop->last || !isset($breadcrumb['url']))
                <li class="breadcrumb-item active">{{ $breadcrumb['name'] }}</li>
            @else
                <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a></li>
            @endif
        @endforeach
    </ol>
</div>
