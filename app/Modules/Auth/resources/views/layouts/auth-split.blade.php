<!doctype html>
<html lang="en">
    <head> 
        @include("Auth::includes.head")
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
            
            body {
                font-family: 'Inter', sans-serif;
                background-color: #f8f9fa;
                overflow-x: hidden;
            }

            .auth-page-wrapper {
                min-height: 100vh;
                display: flex;
            }

            .auth-side-content {
                background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
                color: white;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                padding: 3rem;
                position: relative;
                overflow: hidden;
                height: 100vh;
            }

            .auth-side-content::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-image: url('https://images.unsplash.com/photo-1497215728101-856f4ea42174?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
                background-size: cover;
                background-position: center;
                opacity: 0.1;
                z-index: 1;
            }

            .auth-content-overlay {
                position: relative;
                z-index: 2;
            }

            .auth-form-container {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                background: #ffffff;
                padding: 2rem;
            }
            
            .auth-form-wrapper {
                width: 100%;
                max-width: 450px;
            }

            .auth-logo {
                margin-bottom: 2rem;
            }
            .auth-logo img {
                height: 48px;
            }

            .quote-text {
                font-size: 1.5rem;
                font-weight: 300;
                line-height: 1.5;
                font-style: italic;
            }

            /* Form Styles to match User Idol */
            .form-control {
                padding: 0.75rem 1rem;
                border-radius: 0.375rem;
                border: 1px solid #e2e8f0;
                font-size: 0.95rem;
            }
            .form-control:focus {
                border-color: #3b82f6;
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
            }
            .form-label {
                font-weight: 500;
                color: #334155;
                margin-bottom: 0.5rem;
            }
            .btn-primary {
                background-color: #0f172a;
                border-color: #0f172a;
                padding: 0.75rem 1.5rem;
                font-weight: 500;
                border-radius: 0.375rem;
                width: 100%;
            }
            .btn-primary:hover {
                background-color: #1e293b;
                border-color: #1e293b;
            }
            
            /* Password Toggle */
            .password-input-group {
                position: relative;
            }
            .password-toggle-icon {
                position: absolute;
                top: 50%;
                right: 15px;
                transform: translateY(-50%);
                cursor: pointer;
                color: #94a3b8;
                z-index: 10;
            }

            @media (max-width: 991.98px) {
                .auth-side-content {
                    display: none;
                }
                .auth-form-container {
                    flex: 1;
                    padding: 2rem 1rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="container-fluid p-0">
            <div class="row g-0">
                <!-- Left Side: Branding & Quote -->
                <div class="col-lg-6 d-none d-lg-flex auth-side-content">
                    <div class="auth-content-overlay">
                        <div class="auth-logo">
                             @inject('companyService', 'App\Modules\Settings\Services\CompanyService')
                             @php $company = $companyService->get(); @endphp
                             @if(!empty($company['logo_white']))
                                <img src="{{ asset($company['logo_white']) }}" alt="{{ $company['company_name'] ?? 'HRM' }}" style="height: {{ $company['logo_white_height'] ?? '48px' }}; width: {{ $company['logo_white_width'] ?? 'auto' }};">
                             @else
                                <h2 class="text-white">{{ $company['company_name'] ?? config('common.cms.title') }}</h2>
                             @endif
                        </div>
                    </div>
                    
                    <div class="auth-content-overlay">
                        <blockquote class="blockquote mb-0">
                            <p class="quote-text mb-4">"The only way to do great work is to love what you do."</p>
                            <footer class="blockquote-footer text-white-50">Steve Jobs</footer>
                        </blockquote>
                    </div>
                    
                    <div class="auth-content-overlay">
                        <p class="mb-0 text-white-50">
                            {{ date("Y") }} &copy; {{ $company['company_name'] ?? config('common.cms.title') }}. 
                        </p>
                    </div>
                </div>

                <!-- Right Side: Form -->
                <div class="col-lg-6 auth-form-container">
                    <div class="auth-form-wrapper">
                         <!-- Mobile Logo -->
                         <div class="text-center d-lg-none mb-4">
                            @if(!empty($company['logo_dark']))
                                <img src="{{ asset($company['logo_dark']) }}" alt="" style="height: {{ $company['logo_dark_height'] ?? '40px' }}; width: {{ $company['logo_dark_width'] ?? 'auto' }};">
                            @else
                                <h3>{{ $company['company_name'] ?? 'Humalytix' }}</h3>
                            @endif
                         </div>

                        @yield('content')
                        
                        <div class="mt-5 text-center text-muted">
                             <!-- Footer content if any -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Js Library & Scripts --}}
        @include("Auth::includes.scripts")
        
        <script>
            // Password Show/Hide Toggle
            document.addEventListener('DOMContentLoaded', function() {
                const togglePassword = document.querySelectorAll('.password-toggle-icon');
                togglePassword.forEach(function(icon) {
                    icon.addEventListener('click', function() {
                        const input = this.previousElementSibling;
                        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                        input.setAttribute('type', type);
                        this.classList.toggle('mdi-eye');
                        this.classList.toggle('mdi-eye-off');
                    });
                });
            });
        </script>
    </body>
</html>
