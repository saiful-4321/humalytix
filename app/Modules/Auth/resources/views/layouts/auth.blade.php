<!doctype html>
<html lang="en">
    <head> 
        @include("Auth::includes.head")

        <style type="text/css">
            @media only screen and (min-width: 320px) and (max-width:767px) {
                body, html, .container { 
                    overflow-x:hidden;
                }
            }
            @media  (max-height: 900px) { 
                .left-media-image { 
                width: 100%;
                }
                body{
                    overflow: hidden;
                }
            }

            @media only screen  (min-width: 1366px), (max-height: 768px) { 
                .left-media-image { 
                width: 100%;
                }
                body{
                    overflow: hidden;
                }
            }
            @media only screen  (min-width: 1920px), (max-height: 750px) { 
                .left-media-image { 
                width: 90%;
                }
                body{
                    overflow: hidden;
                }
            }

            
        </style>
    </head>
    <body data-layout-mode="light">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

            body {
                background: url('https://images.unsplash.com/photo-1557683316-973673baf926?q=80&w=2029&auto=format&fit=crop') no-repeat center center fixed;
                background-size: cover;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Poppins', sans-serif;
                overflow-x: hidden;
            }
            
            /* Overlay to darken background slightly */
            body::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.4);
                z-index: -1;
            }

            .auth-card {
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border-radius: 25px;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
                border: 1px solid rgba(255, 255, 255, 0.2);
                padding: 3rem;
                width: 100%;
                max-width: 480px;
                position: relative;
                overflow: hidden;
                animation: fadeInUp 0.8s ease-out forwards;
            }

            .auth-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: -50%;
                width: 100%;
                height: 100%;
                background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.3), transparent);
                transform: skewX(-25deg);
                transition: 0.5s;
                pointer-events: none;
            }

            .auth-card:hover::before {
                left: 150%;
                transition: 0.5s;
            }

            .auth-logo {
                margin-bottom: 2rem;
                text-align: center;
            }
            .auth-logo img {
                height: 80px;
                width: auto;
                filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
            }
            
            .auth-footer {
                margin-top: 2rem;
                text-align: center;
                color: rgba(255, 255, 255, 0.9);
                font-size: 0.85rem;
                font-weight: 300;
            }
            .auth-footer a {
                color: #fff;
                font-weight: 500;
                text-decoration: none;
                position: relative;
            }
            .auth-footer a::after {
                content: '';
                position: absolute;
                width: 100%;
                height: 1px;
                bottom: -2px;
                left: 0;
                background-color: #fff;
                transform: scaleX(0);
                transform-origin: bottom right;
                transition: transform 0.3s ease-out;
            }
            .auth-footer a:hover::after {
                transform: scaleX(1);
                transform-origin: bottom left;
            }

            /* Animations */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Custom Input Styles for Child Pages */
            .custom-input-group {
                position: relative;
                margin-bottom: 1.5rem;
            }
            .custom-input-group i {
                position: absolute;
                top: 50%;
                left: 15px;
                transform: translateY(-50%);
                color: #fff;
                font-size: 1.2rem;
                z-index: 10;
                pointer-events: none;
            }
            .custom-input-group .form-control {
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 50px;
                padding: 12px 20px 12px 50px; /* Left padding for icon */
                color: #fff;
                font-size: 0.95rem;
                transition: all 0.3s ease;
            }
            .custom-input-group .form-control::placeholder {
                color: rgba(255, 255, 255, 0.7);
            }
            .custom-input-group .form-control:focus {
                background: rgba(255, 255, 255, 0.2);
                border-color: rgba(255, 255, 255, 0.5);
                box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
                color: #fff;
            }
            
            .btn-glass {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                border-radius: 50px;
                padding: 12px;
                font-weight: 600;
                letter-spacing: 0.5px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                transition: transform 0.2s, box-shadow 0.2s;
                width: 100%;
                color: #fff;
            }
            .btn-glass:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
                color: #fff;
            }

            /* Text Colors */
            h2, h4, p, label {
                color: #fff !important;
                text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            }
            .text-muted {
                color: rgba(255, 255, 255, 0.8) !important;
            }
        </style>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="auth-card">
                        <div class="auth-logo">
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('assets/images/auth-logo.png') }}" alt="" class="img-fluid">
                            </a>
                        </div>
                        
                        @yield('content')

                    </div>
                    <div class="auth-footer">
                        <p class="mb-0">
                            {{ date("Y") }} &copy; {{ config('common.cms.short_title') }}. 
                            Design & Develop by <a href="https://quantfintech.ai" target="_blank">QFL</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Js Library & Scripts --}}
        @include("Auth::includes.scripts")

    </body>
</html>