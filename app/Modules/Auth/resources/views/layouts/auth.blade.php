@inject('themeService', 'App\Modules\Settings\Services\ThemeService')
@php
    $theme = $themeService->get();
    $layout = $theme['auth_layout'] ?? 'split';
    
    // Fallback if layout file doesn't exist (though ideally we validate this)
    if (!view()->exists('Auth::layouts.auth-' . $layout)) {
        $layout = 'split'; 
    }
@endphp

@include('Auth::layouts.auth-' . $layout)