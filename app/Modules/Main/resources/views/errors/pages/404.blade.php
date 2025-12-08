@extends('Main::errors.layouts.app', [
    'code' => 404
])

@section("content")
<div class="card rounded-0">
    <div class="header">
        <p class="lead">404 <span class="text">Oops!</span></p>
        <span>Page Not Found</span>
    </div>
    <div class="body">
        <p>The page you were looking for could not be found, please <a href="javascript:void(0);">contact us</a> to report this issue.</p>
        <div class="margin-top-30">
            <a href="{{ route('dashboard.home') }}" class="btn btn-lg btn-primary"><i class="fa fa-home"></i> <span>Home</span></a>
        </div>
    </div>
</div>
@endsection
