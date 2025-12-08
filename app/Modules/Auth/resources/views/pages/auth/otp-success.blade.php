@extends('Auth::layouts.auth')

@section('content')
    <div class="mb-4 text-center">
        <i class="bx bx-check-shield vefified-icon-custom text-info"></i>
        <h3 class="">{{ $pageName ?? "Congratulations!" }}</h3>
        <p>
            @if(session('success'))
                {{ session('success') }}
            @endif
        </p>
    </div>
    <div class="mb-3">
        <a class="btn btn-info w-100 waves-effect waves-light" href="{{ route("login") }}">Done</a>
    </div>
@endsection


@push('styles')
<style>
    .custom-form-control-opt {
        border-top-right-radius: 10px !important;
        border-bottom-right-radius: 10px !important;
        border-top-left-radius: 10px !important;
        border-bottom-left-radius: 10px !important;
        text-align: center;
        font-weight: 700;
    }
    .vefified-icon-custom {
        font-size: 80px;
        margin-bottom: 15px;
    }
</style>
@endpush
