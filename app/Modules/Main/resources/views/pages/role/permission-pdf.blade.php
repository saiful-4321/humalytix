@extends('Main::layouts.pdf')

@section('content')
    @include('Main::pages.role.permission-list-table', ['isPdf' => true])
@endsection
