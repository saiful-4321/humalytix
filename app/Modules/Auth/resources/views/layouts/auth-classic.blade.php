<!doctype html>
<html lang="en">
    <head> 
        @include("Auth::includes.head")
        <style>
            body {
                background-color: #f3f4f6;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
            }
            .auth-card {
                background: white;
                border-radius: 8px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                padding: 2rem;
                width: 100%;
                max-width: 400px;
                border-top: 5px solid #4f46e5;
            }
            .auth-logo {
                text-align: center;
                margin-bottom: 2rem;
            }
            .auth-logo img {
                height: 40px;
            }
        </style>
    </head>
    <body data-layout-mode="light">
        <div class="auth-card">
            <div class="auth-logo">
                 @inject('companyService', 'App\Modules\Settings\Services\CompanyService')
                 @php $company = $companyService->get(); @endphp
                 @if(!empty($company['logo_dark']))
                    <img src="{{ asset($company['logo_dark']) }}" alt="{{ $company['company_name'] ?? 'HRM' }}" style="height: {{ $company['logo_dark_height'] ?? '40px' }}; width: {{ $company['logo_dark_width'] ?? 'auto' }};">
                 @else
                    <h3>{{ $company['company_name'] ?? config('common.cms.title') }}</h3>
                 @endif
            </div>

            @yield('content')
            
            <div class="mt-4 text-center text-muted small">
                {{ date("Y") }} &copy; {{ config('common.cms.short_title') }}
            </div>
        </div>

        @include("Auth::includes.scripts")
    </body>
</html>
