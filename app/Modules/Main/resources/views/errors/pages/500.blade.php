@extends('Main::errors.layouts.app', [
    'code' => 500
])

@section("content")
<div class="card rounded-0">
    <div class="header">
        <p class="lead">500</p>
        <span>Internal Server Error</span>
    </div>
    <div class="body">
        <p>Apparently we're experiencing an error. But don't worry, we will solve it shortly. <br>Please try after some time.</p>
        @include('Main::errors.includes.back-to-home')
    </div>
</div>
@endsection