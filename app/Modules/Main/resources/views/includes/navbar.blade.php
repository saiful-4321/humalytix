@inject('companyService', 'App\Modules\Settings\Services\CompanyService')
@php
    $company = $companyService->get();
@endphp
<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO --> 
            <div class="navbar-brand-box">
                <a href="{{ route("dashboard.home") }}" class="logo logo-dark">
                    <span class="logo-sm">
                        @if(!empty($company['logo_dark_small']))
                            <img src="{{ asset($company['logo_dark_small']) }}" alt="" style="height: {{ $company['logo_dark_small_height'] ?? '24px' }}; width: {{ $company['logo_dark_small_width'] ?? 'auto' }};">
                        @else
                            <img src="{{ asset('assets/images/favicon.png') }}" alt="" height="24">
                        @endif
                    </span>
                    <span class="logo-lg">
                        @if(!empty($company['logo_dark']))
                            <img src="{{ asset($company['logo_dark']) }}" alt="" style="height: {{ $company['logo_dark_height'] ?? '24px' }}; width: {{ $company['logo_dark_width'] ?? 'auto' }};">
                        @else
                            <img src="{{ asset('assets/images/logo.png') }}" alt="" height="20"> <span class="logo-txt"></span>
                        @endif
                    </span>
                </a>

                <a href="{{ route("dashboard.home") }}" class="logo logo-light">
                    <span class="logo-sm">
                        @if(!empty($company['logo_white_small']))
                            <img src="{{ asset($company['logo_white_small']) }}" alt="" style="height: {{ $company['logo_white_small_height'] ?? '24px' }}; width: {{ $company['logo_white_small_width'] ?? 'auto' }};">
                        @else
                            <img src="{{ asset('assets/images/favicon.png') }}" alt="" height="24">
                        @endif
                    </span>
                    <span class="logo-lg">
                        @if(!empty($company['logo_white']))
                            <img src="{{ asset($company['logo_white']) }}" alt="" style="height: {{ $company['logo_white_height'] ?? '24px' }}; width: {{ $company['logo_white_width'] ?? 'auto' }};">
                        @else
                            <img src="{{ asset('assets/images/logo-white.png') }}" alt="" height="20" > <span class="logo-txt"></span>
                        @endif
                    </span>
                </a>
            </div>
            <!-- end logo  -->

            @inject('themeService', 'App\Modules\Settings\Services\ThemeService')
            @php
                $layoutType = $themeService->get('layout_type') ?? 'vertical';
            @endphp

            @if($layoutType == 'vertical')
                <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                    <i class="fa fa-fw fa-bars"></i>
                </button>
            @else
                <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="horizontal-menu-btn" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                    <i class="fa fa-fw fa-bars"></i>
                </button>
            @endif
            <!-- end toggle  -->

            @if(auth()->user()->employee)
                <a href="{{ route('hrm.ess.holidays') }}">
                    <button class="btn header-item noti-icon">
                        <i class="bx bx-calendar"></i>
                    </button>
                </a>
            @endif
        </div>

        <div class="d-flex">
            @php
                $theme = $themeService->get();
                $isCustomTheme = ($theme['topbar_color'] ?? '') == 'custom' || ($theme['sidebar_color'] ?? '') == 'custom' || ($theme['footer_color'] ?? '') == 'custom';
            @endphp
            @if(!$isCustomTheme)
            <div class="dropdown d-none d-sm-inline-block">
                <button type="button" class="btn header-item" id="mode-setting-btn">
                    <i  class="fs-5 dripicons-brightness-max icon-lg layout-mode-dark "></i>
                    <i  class="fs-5 dripicons-brightness-medium con-lg layout-mode-light "></i>
                </button>
            </div>
            @endif
            <!-- end dark light  -->


            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon position-relative" id="page-header-notifications-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="dripicons-bell"></i>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="badge bg-danger rounded-pill notification-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-xl dropdown-menu-end p-0 notification-dropdown"
                    aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3 bg-primary bg-gradient text-white">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0 text-white fw-semibold">
                                    <i class="bx bx-bell me-1"></i> Notifications
                                </h6>
                            </div>
                            <div class="col-auto">
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <button type="button" class="btn btn-sm btn-light mark-all-read" title="Mark all as read">
                                        <i class="mdi mdi-check-all"></i>
                                    </button>
                                @endif
                                <a href="{{ route('hrm.notifications.index') }}" class="btn btn-sm btn-light ms-1">
                                    <i class="mdi mdi-view-list"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 350px;">
                        @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                        <a href="{{ route('hrm.notifications.show', $notification->id) }}" 
                           class="text-reset notification-item d-block dropdown-item position-relative notification-clickable"
                           data-notification-id="{{ $notification->id }}">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm">
                                        <span class="avatar-title rounded-circle font-size-16 notification-icon-{{ $loop->index % 5 }}">
                                            @php
                                                $type = $notification->data['type'] ?? 'default';
                                                $icon = match($type) {
                                                    'leave' => 'bx-calendar',
                                                    'expense' => 'bx-wallet',
                                                    'training' => 'bx-book-reader',
                                                    'document' => 'bx-file',
                                                    'approval' => 'bx-check-circle',
                                                    default => 'bx-bell'
                                                };
                                            @endphp
                                            <i class="bx {{ $icon }}"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-semibold">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                                    <div class="font-size-13 text-muted">
                                        <p class="mb-1">{{ \Illuminate\Support\Str::limit($notification->data['message'] ?? '', 45) }}</p>
                                        <p class="mb-0">
                                            <i class="mdi mdi-clock-outline"></i>
                                            <span class="ms-1">{{ $notification->created_at->diffForHumans() }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 ms-2">
                                    <span class="badge badge-soft-primary rounded-pill">New</span>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bx bx-bell-off font-size-24 d-block mb-2"></i>
                            <p class="mb-0">No new notifications</p>
                        </div>
                        @endforelse
                    </div>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link font-size-14 text-center" href="{{ route('hrm.notifications.index') }}">
                            <i class="mdi mdi-arrow-right-circle me-1"></i> <span>View All ({{ auth()->user()->unreadNotifications->count() }})</span> 
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            <!-- end notification  -->


            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="{{ asset('assets/images/users/avatar-1.png') }}"
                        alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1 fw-medium">{{ auth()->user()->name ?? null }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('dashboard.user.profile') }}"><i class="mdi mdi-account font-size-16 align-middle me-1"></i> Profile</a>
                    
                    @can("user-change-password")                   
                    <a class="dropdown-item" href="javascript:void(0)" route="{{ route('dashboard.user.profile.change-password') }}" data-toggle="dynamicModal">
                        <i class="mdi mdi-lock font-size-16 align-middle me-1"></i>
                        Change Password
                    </a>
                    @endcan

                    <div class="dropdown-divider"></div>
                    
                    @if (session()->has('impersonate_id'))
                        <a class="dropdown-item text-warning" href="{{ route('dashboard.user.remove-pretend') }}" ><i class="fa fa-times"></i> Remove Pretend</a>
                    @endif

                    <a class="dropdown-item" href="{{ route('auth.logout') }}"><i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Logout</a>
                </div>
            </div>
            <!-- end avater -->

        </div>
    </div>
</header>

<style>
/* Modern Notification Styles */
.notification-badge {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.notification-dropdown {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: none;
    border-radius: 0.5rem;
    overflow: hidden;
    width: 450px !important;
}

.notification-item {
    transition: all 0.3s ease;
    border-bottom: 1px solid #f0f0f0;
}

.notification-item:hover {
    background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);
    transform: translateX(5px);
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-clickable {
    cursor: pointer;
    text-decoration: none;
}

.notification-icon-0 {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.notification-icon-1 {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.notification-icon-2 {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.notification-icon-3 {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.notification-icon-4 {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.mark-all-read {
    transition: all 0.3s ease;
}

.mark-all-read:hover {
    transform: scale(1.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark all as read functionality
    const markAllBtn = document.querySelector('.mark-all-read');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            fetch('{{ route("hrm.notifications.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge
                    const badge = document.querySelector('.notification-badge');
                    if (badge) {
                        badge.remove();
                    }
                    
                    // Reload to show updated notifications
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }
    
    // Auto-mark as read when clicking notification
    document.querySelectorAll('.notification-clickable').forEach(item => {
        item.addEventListener('click', function(e) {
            const notificationId = this.dataset.notificationId;
            
            // Mark as read via AJAX
            fetch(`/hrm/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge count
                    const badge = document.querySelector('.notification-badge');
                    if (badge && data.unread_count > 0) {
                        badge.textContent = data.unread_count;
                    } else if (badge && data.unread_count === 0) {
                        badge.remove();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>

 