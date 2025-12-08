@inject('themeService', 'App\Modules\Settings\Services\ThemeService')
@php
    $theme = $themeService->get();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include("Main::includes.head")
        <style>
            /* Custom Theme Colors - High Specificity Overrides */
            @if(($theme['topbar_color'] ?? '') == 'custom')
                body[data-layout-mode] #page-topbar { background: {{ $theme['topbar_custom_color'] ?? '#ffffff' }} !important; }
                
                @if($themeService->isDarkColor($theme['topbar_custom_color'] ?? '#ffffff'))
                    /* Dark Topbar Logic */
                    body[data-layout-mode] .navbar-header .dropdown .header-item { color: rgba(255, 255, 255, 0.9) !important; }
                    body[data-layout-mode] .header-item i { color: rgba(255, 255, 255, 0.9) !important; }
                    body[data-layout-mode] .app-search span, body[data-layout-mode] .app-search input.form-control { color: rgba(255, 255, 255, 0.9) !important; }
                    body[data-layout-mode] .app-search input.form-control { background-color: rgba(255,255,255,0.05) !important; border-color: transparent !important; }
                @else
                    /* Light Topbar Logic */
                    body[data-layout-mode] .navbar-header .dropdown .header-item { color: #555b6d !important; }
                    body[data-layout-mode] .header-item i { color: #555b6d !important; }
                @endif
            @endif

            @if(($theme['sidebar_color'] ?? '') == 'custom')
                /* Target both the menu and the brand box for seamless look - High Specificity */
                body[data-sidebar] .vertical-menu, body[data-sidebar] .navbar-brand-box { background: {{ $theme['sidebar_custom_color'] ?? '#2c3e50' }} !important; }
                
                @if($themeService->isDarkColor($theme['sidebar_custom_color'] ?? '#2c3e50'))
                    /* Dark Sidebar Logic */
                    body[data-sidebar] .vertical-menu .metismenu li a { color: rgba(255, 255, 255, 0.7) !important; }
                    body[data-sidebar] .vertical-menu .metismenu li a:hover, body[data-sidebar] .vertical-menu .metismenu li a:active, body[data-sidebar] .vertical-menu .metismenu li.mm-active .active { color: #ffffff !important; }
                    body[data-sidebar] .vertical-menu .mm-active > a { background-color: rgba(255, 255, 255, 0.1) !important; color: #ffffff !important; }
                    body[data-sidebar] .vertical-menu .metismenu li.mm-active > a i { color: #ffffff !important; }
                    
                    /* Force Logo White */
                    body[data-sidebar] .navbar-brand-box .logo-dark { display: none !important; }
                    body[data-sidebar] .navbar-brand-box .logo-light { display: block !important; }
                @else
                    /* Light Sidebar Logic */
                    body[data-sidebar] .vertical-menu .metismenu li a { color: #545a6d !important; }
                    body[data-sidebar] .vertical-menu .metismenu li a:hover, body[data-sidebar] .vertical-menu .metismenu li a:active, body[data-sidebar] .vertical-menu .metismenu li.mm-active .active { color: #2a3042 !important; }
                    body[data-sidebar] .vertical-menu .mm-active > a { background-color: rgba(0, 0, 0, 0.05) !important; color: #2a3042 !important; }
                    body[data-sidebar] .vertical-menu .metismenu li.mm-active > a i { color: #2a3042 !important; }

                    /* Force Logo Dark */
                    body[data-sidebar] .navbar-brand-box .logo-dark { display: block !important; }
                    body[data-sidebar] .navbar-brand-box .logo-light { display: none !important; }
                @endif
            @endif

            @if(($theme['footer_color'] ?? '') == 'custom')
                body[data-layout-mode] .footer { background: {{ $theme['footer_custom_color'] ?? '#ffffff' }} !important; }
                @if($themeService->isDarkColor($theme['footer_custom_color'] ?? '#ffffff'))
                    body[data-layout-mode] .footer { color: #e9ecef !important; }
                @else
                    body[data-layout-mode] .footer { color: #74788d !important; }
                @endif
            @elseif(($theme['footer_color'] ?? '') == 'dark')
                body[data-layout-mode] .footer { background-color: #2a3042 !important; color: #a6b0cf !important; }
            @endif

            @if(($theme['body_color'] ?? '') == 'custom')
                 /* Custom Body Background */
                body[data-layout-mode] { background: {{ $theme['body_custom_color'] ?? '#ffffff' }} !important; }
                body[data-layout-mode] .page-content { background: transparent !important; }
            @endif
        </style>
    </head>

    @php
        $topbarAttribute = $theme['topbar_color'] ?? 'light';
        if ($topbarAttribute == 'custom') {
            $topbarAttribute = $themeService->isDarkColor($theme['topbar_custom_color'] ?? '#ffffff') ? 'dark' : 'light';
        }

        $sidebarAttribute = $theme['sidebar_color'] ?? 'dark';
        if ($sidebarAttribute == 'custom') {
            $sidebarAttribute = $themeService->isDarkColor($theme['sidebar_custom_color'] ?? '#2c3e50') ? 'dark' : 'light';
        }
    @endphp

    <body data-layout-mode="{{ $theme['layout_mode'] ?? 'light' }}"
          data-topbar="{{ $topbarAttribute }}"
          data-sidebar="{{ $sidebarAttribute }}"
          data-sidebar-size="{{ $theme['sidebar_size'] ?? 'lg' }}"
          data-layout="{{ $theme['layout_type'] ?? 'vertical' }}"
          data-layout-size="{{ $theme['layout_width'] ?? 'fluid' }}"
          data-layout-scrollable="{{ ($theme['layout_position'] ?? 'fixed') == 'scrollable' ? 'true' : 'false' }}">
    
    <!-- Modern Glassy Loader -->
    <div id="preloader">
        <div class="glassy-loader-container">
            <div class="glassy-loader">
                <div class="loader-circle"></div>
                <div class="loader-circle"></div>
                <div class="loader-circle"></div>
            </div>
            <div class="loader-text">Loading...</div>
        </div>
    </div>

    <style>
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 99999;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
        }
        
        body[data-layout-mode="dark"] #preloader {
            background: rgba(34, 39, 54, 0.8);
        }

        .glassy-loader-container {
            text-align: center;
            padding: 30px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .glassy-loader {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .loader-circle {
            width: 20px;
            height: 20px;
            margin: 0 5px;
            background-color: #556ee6;
            border-radius: 50%;
            animation: bounce 1.4s infinite ease-in-out both;
        }

        .loader-circle:nth-child(1) { animation-delay: -0.32s; }
        .loader-circle:nth-child(2) { animation-delay: -0.16s; }

        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        .loader-text {
            color: #556ee6;
            font-weight: 600;
            letter-spacing: 1px;
            font-size: 16px;
        }
    </style>

    <div id="layout-wrapper">
        @include("Main::includes.navbar")
        
        @if(($theme['layout_type'] ?? 'vertical') == 'vertical')
            @include("Main::includes.sidebar")
        @else
            @include("Main::includes.horizontal-nav")
        @endif

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">

                    @include("Main::widgets.message.alert")

                    {{-- page content --}}
                    @yield('content')
 
                    @if(($theme['footer_enabled'] ?? 1) == 1)
                        @include("Main::includes.footer")
                    @endif
                    <!-- end main content-->
                </div>
            </div>
        </div>
    </div>

        @include("Main::includes.scripts")

    </body>
</html>
