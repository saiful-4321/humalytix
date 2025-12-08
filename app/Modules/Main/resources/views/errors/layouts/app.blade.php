<!doctype html>
<html lang="en">
<head>
<title>:: Online Bo :: {{ $code ?? null }}</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="description" content="Online Bo - SMS Push Pull Services">
<meta name="author" content="Online Bo">

<link rel="icon" href="{{ asset('assets/images/favicon.jpg') }}" type="image/x-icon">
<!-- VENDOR CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/animate-css/animate.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- MAIN CSS -->
<link rel="stylesheet" href="assets/css/main.css">
<link rel="stylesheet" href="assets/css/color_skins.css">
<link rel="stylesheet" href="assets/css/smasung-sms.css">
</head>

<body class="theme-blue">
    <!-- WRAPPER -->
    <div id="wrapper">
        <div class="vertical-align-wrap">
            <div class="vertical-align-middle auth-main">
                <div class="auth-box">
                    <div class="mobile-logo"><a href="{{ route('dashboard.home') }}"><img src="{{ asset('assets/images/logo-icon.svg') }}" alt="iSMS"></a></div>
                    <div class="auth-left">
                        <div class="mobile-logo">
                            <a href="{{ route('dashboard.home') }}">
                                <img src="{{ asset('assets/images/main-logo-white.png') }}" alt="iSMS">
                            </a>
                        </div>
                        <div class="auth-left">
                            <div class="left-top">
                                <a href="{{ route('dashboard.home') }}">
                                    <img src="{{ asset('assets/images/main-logo-white.png') }}" alt="iSMS">
                                </a>
                            </div>
                            <div class="left-slider">
                                <img src="{{ asset('assets/images/login/1.jpg') }}" class="img-fluid" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="auth-right">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>	
	<!-- END WRAPPER -->
</body>
</html>

