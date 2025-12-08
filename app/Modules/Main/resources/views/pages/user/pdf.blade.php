@extends('Main::layouts.pdf')

@section('content')
    @include('Main::pages.user.list-table', ['isPdf' => true])
@endsection
