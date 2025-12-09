<!doctype html>
<html lang="en">
    <head> 
        @include("Auth::includes.head")
        <style>
             body {
                background-color: #ffffff;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            .navbar-brand {
                font-weight: bold;
                font-size: 1.5rem;
                color: #333;
            }
            .main-content {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .auth-container {
                display: flex;
                width: 100%;
                max-width: 900px;
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            }
            .auth-image {
                flex: 1;
                background-image: url('https://images.unsplash.com/photo-1542744173-8e7e53415bb0?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
                background-size: cover;
                background-position: center;
                position: relative;
            }
            .auth-image::after {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
            }
            .auth-image-text {
                position: absolute;
                bottom: 2rem;
                left: 2rem;
                right: 2rem;
                color: white;
                z-index: 2;
            }
            .auth-form {
                flex: 1;
                padding: 3rem;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            @media (max-width: 768px) {
                .auth-container {
                    flex-direction: column;
                    margin: 1rem;
                }
                .auth-image {
                    height: 200px;
                    flex: none;
                }
            }
        </style>
    </head>
    <body data-layout-mode="light">
        
        <nav class="navbar navbar-light bg-white px-4 py-3 border-bottom">
            <div class="container-fluid">
                 @inject('companyService', 'App\Modules\Settings\Services\CompanyService')
                 @php $company = $companyService->get(); @endphp
                 <a class="navbar-brand" href="#">
                     @if(!empty($company['logo_dark']))
                        <img src="{{ asset($company['logo_dark']) }}" alt="" height="30">
                     @else
                        {{ $company['company_name'] ?? 'HRM' }}
                     @endif
                 </a>
            </div>
        </nav>

        <div class="main-content">
            <div class="auth-container">
                <div class="auth-image d-none d-md-block">
                    <div class="auth-image-text">
                        <h3>Empowering Your Workforce</h3>
                        <p class="mb-0">Manage your team efficiently with our advanced HR solutions.</p>
                    </div>
                </div>
                <div class="auth-form">
                    @yield('content')
                </div>
            </div>
        </div>

        <footer class="py-3 text-center text-muted small bg-light mt-auto">
             {{ date("Y") }} &copy; {{ $company['company_name'] ?? config('common.cms.title') }}
        </footer>

        @include("Auth::includes.scripts")
    </body>
</html>
