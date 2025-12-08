@extends('Main::errors.layouts.app', [
    'code' => 403
])

@section("content")
<div class="card rounded-0">
    <div class="header">
        <p class="lead">Error<span class="text">403</span></p>
        <span>Forbiddon Error!</span>
    </div>
    <div class="body">
        <p>You don't have permission to access / on this server.</p>
        <div class="margin-top-30">
            @include('Main::errors.include.back-to-home')
        </div>
    </div>
</div>
@endsection