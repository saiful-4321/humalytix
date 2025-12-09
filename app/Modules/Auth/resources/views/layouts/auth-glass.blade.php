<!doctype html>
<html lang="en">
    <head> 
        @include("Auth::includes.head")
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
            
            /* Text Colors */
            h1, h2, h3, h4, h5, h6, p, label {
                color: #fff !important;
                text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            }
            .text-muted {
                color: rgba(255, 255, 255, 0.8) !important;
            }
            .form-control {
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                color: #fff;
            }
            .form-control:focus {
                background: rgba(255, 255, 255, 0.2);
                border-color: rgba(255, 255, 255, 0.5);
                color: #fff;
            }
            .form-control::placeholder {
                color: rgba(255, 255, 255, 0.6);
            }

            /* Animations */
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    </head>
    <body data-layout-mode="light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="auth-card">
                        <div class="auth-logo">
                             @inject('companyService', 'App\Modules\Settings\Services\CompanyService')
                             @php $company = $companyService->get(); @endphp
                             @if(!empty($company['logo_white']))
                                <img src="{{ asset($company['logo_white']) }}" alt="{{ $company['company_name'] ?? 'HRM' }}">
                             @else
                                <h2 class="text-white">{{ $company['company_name'] ?? config('common.cms.title') }}</h2>
                             @endif
                        </div>
                        
                        @yield('content')

                    </div>
                    <div class="auth-footer">
                        <p class="mb-0">
                            {{ date("Y") }} &copy; {{ config('common.cms.short_title') }}. 
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @include("Auth::includes.scripts")
    </body>
</html>
