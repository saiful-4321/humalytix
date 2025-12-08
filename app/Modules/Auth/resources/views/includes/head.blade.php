<meta charset="utf-8" />
<title>{{ (config('common.cms.short_title') . " Bo Account") }} {{ !empty($pageName) ? " | {$pageName}" : null }}</title>
<meta charset="utf-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="description" content="">
<meta name="author" content="">
<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
<!-- Bootstrap Css -->
<link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<!-- daterangepicker  -->
<link href="{{ asset('assets/libs/daterangepicker/daterangepicker.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
<!-- Icons Css -->
<link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<!-- App Css-->
<link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
<!-- Custom Css-->
<link href="{{ asset('assets/css/custom.css') }}" id="app-style" rel="stylesheet" type="text/css" />

<style type="text/css">
body{
    background-color:#fff !important;
}
@media only screen and (min-width: 320px) and (max-width:767px) {

body, html, .container { 
    overflow-x:hidden;
  }

}
@media only screen and (min-width: 320px) and (max-width:750px) {

.auth-left-content {
    display: none;
  }

}
</style>

@stack('styles')