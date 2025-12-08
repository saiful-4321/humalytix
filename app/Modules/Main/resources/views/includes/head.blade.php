@inject('companyService', 'App\Modules\Settings\Services\CompanyService')
@php
    $company = $companyService->get();
@endphp
<title>{{ $company['meta_title'] ?? config('common.cms.short_title') . " Online BO" }} {{ !empty($pageName) ? " | {$pageName}" : null }}</title>
<meta charset="utf-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="description" content="{{ $company['meta_desc'] ?? '' }}">
<meta name="keywords" content="{{ $company['meta_tags'] ?? '' }}">
<meta name="author" content="{{ $company['company_name'] ?? '' }}">

<!-- App favicon --> 
@if(!empty($company['favicon']))
<link rel="icon" href="{{ asset($company['favicon']) }}" type="image/x-icon">
<link rel="shortcut icon" href="{{ asset($company['favicon']) }}">
@else
<link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
<link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.png') }}">
@endif

<!-- bootstrap css -->
<link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<!-- plugin css -->
<link href="{{ asset('assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}" rel="stylesheet" type="text/css" />
<!-- sweetalert2 css -->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Icons css -->
<link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" /> 
<!-- select2  -->
<link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" />
<!-- daterangepicker  -->
<link href="{{ asset('assets/libs/daterangepicker/daterangepicker.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
<!-- twitter-bootstrap-wizard css -->
<link href="{{ asset('assets/libs/twitter-bootstrap-wizard/prettify.css') }}" rel="stylesheet">

<!-- app css-->
<link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
<!-- custom css-->
<link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css" />

@stack("styles")

{{-- Jquery --}}
<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>