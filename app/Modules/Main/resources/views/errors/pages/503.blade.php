@extends('Main::errors.layouts.app', [
    'code' => 500
])

@section("content")
<div class="card rounded-0">
    <div class="header">
        <p class="lead">Error 5<i class="fa fa-smile-o"></i>3</p>
        <span>Service Unavailable</span>
    </div>
    <div class="body">
        <p>Please try after some time, This site is getting up in few minutes.</p>
        @include("Main::errors.includes.back-to-home")
    </div>
</div>
@endsection

