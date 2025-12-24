@extends('Main::layouts.app')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Notifications</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Notifications</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-primary bg-gradient">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title text-white mb-0">
                                    <i class="bx bx-bell me-2"></i>All Notifications
                                </h5>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-sm btn-light me-2" data-bs-toggle="offcanvas" data-bs-target="#sendNotificationOffcanvas">
                                    <i class="mdi mdi-send me-1"></i> Send Custom Notification
                                </button>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                <form action="{{ route('hrm.notifications.mark-all-read') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light">
                                        <i class="mdi mdi-check-all me-1"></i> Mark All as Read
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <!-- Filter Tabs -->
                        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#all" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                    <span class="d-none d-sm-block">All ({{ $notifications->total() }})</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#unread" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                    <span class="d-none d-sm-block">Unread ({{ auth()->user()->unreadNotifications->count() }})</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#read" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-envelope-open"></i></span>
                                    <span class="d-none d-sm-block">Read</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content p-3">
                            <div class="tab-pane active" id="all" role="tabpanel">
                                @forelse($notifications as $notification)
                                <div class="notification-card {{ $notification->read_at ? 'read' : 'unread' }} mb-3">
                                    <div class="d-flex align-items-start p-3">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-md">
                                                <span class="avatar-title rounded-circle font-size-20 notification-icon-{{ $loop->index % 5 }}">
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
                                            <h5 class="font-size-15 mb-1">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                                @if(!$notification->read_at)
                                                    <span class="badge badge-soft-primary ms-2">New</span>
                                                @endif
                                            </h5>
                                            <p class="text-muted mb-2">{{ \Illuminate\Support\Str::limit($notification->data['message'] ?? '', 120) }}</p>
                                            <p class="text-muted font-size-13 mb-0">
                                                <i class="mdi mdi-clock-outline"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 ms-3">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('hrm.notifications.show', $notification->id) }}" 
                                                   class="btn btn-sm btn-soft-primary" title="View Details">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                @if(!$notification->read_at)
                                                <form action="{{ route('hrm.notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-soft-success" title="Mark as Read">
                                                        <i class="mdi mdi-check"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-5">
                                    <i class="bx bx-bell-off font-size-48 text-muted d-block mb-3"></i>
                                    <p class="text-muted">No notifications found</p>
                                </div>
                                @endforelse

                                <!-- Pagination -->
                                <div class="mt-4">
                                    {{ $notifications->links() }}
                                </div>
                            </div>

                            <div class="tab-pane" id="unread" role="tabpanel">
                                @forelse(auth()->user()->unreadNotifications as $notification)
                                <div class="notification-card unread mb-3">
                                    <div class="d-flex align-items-start p-3">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-md">
                                                <span class="avatar-title rounded-circle font-size-20 notification-icon-{{ $loop->index % 5 }}">
                                                    <i class="bx bx-bell"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="font-size-15 mb-1">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                                <span class="badge badge-soft-primary ms-2">New</span>
                                            </h5>
                                            <p class="text-muted mb-2">{{ $notification->data['message'] ?? '' }}</p>
                                            <p class="text-muted font-size-13 mb-0">
                                                <i class="mdi mdi-clock-outline"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 ms-3">
                                            <a href="{{ route('hrm.notifications.show', $notification->id) }}" 
                                               class="btn btn-sm btn-soft-primary">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-5">
                                    <i class="bx bx-check-circle font-size-48 text-success d-block mb-3"></i>
                                    <p class="text-muted">You're all caught up!</p>
                                </div>
                                @endforelse
                            </div>

                            <div class="tab-pane" id="read" role="tabpanel">
                                @forelse($notifications->where('read_at', '!=', null) as $notification)
                                <div class="notification-card read mb-3">
                                    <div class="d-flex align-items-start p-3">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-md">
                                                <span class="avatar-title rounded-circle font-size-20 bg-soft-secondary text-secondary">
                                                    <i class="bx bx-check"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="font-size-15 mb-1 text-muted">{{ $notification->data['title'] ?? 'Notification' }}</h5>
                                            <p class="text-muted mb-2">{{ \Illuminate\Support\Str::limit($notification->data['message'] ?? '', 120) }}</p>
                                            <p class="text-muted font-size-13 mb-0">
                                                <i class="mdi mdi-clock-outline"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-5">
                                    <i class="bx bx-envelope font-size-48 text-muted d-block mb-3"></i>
                                    <p class="text-muted">No read notifications</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Send Notification Offcanvas -->
@include('HRM::pages.notifications.send_offcanvas')

<style>
.notification-card {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.notification-card.unread {
    background: linear-gradient(90deg, #f8f9fa 0%, #ffffff 100%);
    border-left: 4px solid #556ee6;
}

.notification-card.read {
    opacity: 0.7;
}

.notification-card:hover {
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
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
</style>

                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#unread" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                    <span class="d-none d-sm-block">Unread ({{ auth()->user()->unreadNotifications->count() }})</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#read" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-envelope-open"></i></span>
                                    <span class="d-none d-sm-block">Read</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content p-3">
                            <div class="tab-pane active" id="all" role="tabpanel">
                                @forelse($notifications as $notification)
                                <div class="notification-card {{ $notification->read_at ? 'read' : 'unread' }} mb-3">
                                    <div class="d-flex align-items-start p-3">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-md">
                                                <span class="avatar-title rounded-circle font-size-20 notification-icon-{{ $loop->index % 5 }}">
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
                                            <h5 class="font-size-15 mb-1">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                                @if(!$notification->read_at)
                                                    <span class="badge badge-soft-primary ms-2">New</span>
                                                @endif
                                            </h5>
                                            <p class="text-muted mb-2">{{ $notification->data['message'] ?? '' }}</p>
                                            <p class="text-muted font-size-13 mb-0">
                                                <i class="mdi mdi-clock-outline"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 ms-3">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('hrm.notifications.show', $notification->id) }}" 
                                                   class="btn btn-sm btn-soft-primary" title="View Details">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                @if(!$notification->read_at)
                                                <form action="{{ route('hrm.notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-soft-success" title="Mark as Read">
                                                        <i class="mdi mdi-check"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-5">
                                    <i class="bx bx-bell-off font-size-48 text-muted d-block mb-3"></i>
                                    <p class="text-muted">No notifications found</p>
                                </div>
                                @endforelse

                                <!-- Pagination -->
                                <div class="mt-4">
                                    {{ $notifications->links() }}
                                </div>
                            </div>

                            <div class="tab-pane" id="unread" role="tabpanel">
                                @forelse(auth()->user()->unreadNotifications as $notification)
                                <div class="notification-card unread mb-3">
                                    <div class="d-flex align-items-start p-3">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-md">
                                                <span class="avatar-title rounded-circle font-size-20 notification-icon-{{ $loop->index % 5 }}">
                                                    <i class="bx bx-bell"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="font-size-15 mb-1">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                                <span class="badge badge-soft-primary ms-2">New</span>
                                            </h5>
                                            <p class="text-muted mb-2">{{ $notification->data['message'] ?? '' }}</p>
                                            <p class="text-muted font-size-13 mb-0">
                                                <i class="mdi mdi-clock-outline"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 ms-3">
                                            <a href="{{ route('hrm.notifications.show', $notification->id) }}" 
                                               class="btn btn-sm btn-soft-primary">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-5">
                                    <i class="bx bx-check-circle font-size-48 text-success d-block mb-3"></i>
                                    <p class="text-muted">You're all caught up!</p>
                                </div>
                                @endforelse
                            </div>

                            <div class="tab-pane" id="read" role="tabpanel">
                                @forelse($notifications->where('read_at', '!=', null) as $notification)
                                <div class="notification-card read mb-3">
                                    <div class="d-flex align-items-start p-3">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-md">
                                                <span class="avatar-title rounded-circle font-size-20 bg-soft-secondary text-secondary">
                                                    <i class="bx bx-check"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="font-size-15 mb-1 text-muted">{{ $notification->data['title'] ?? 'Notification' }}</h5>
                                            <p class="text-muted mb-2">{{ $notification->data['message'] ?? '' }}</p>
                                            <p class="text-muted font-size-13 mb-0">
                                                <i class="mdi mdi-clock-outline"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-5">
                                    <i class="bx bx-envelope font-size-48 text-muted d-block mb-3"></i>
                                    <p class="text-muted">No read notifications</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.notification-card {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.notification-card.unread {
    background: linear-gradient(90deg, #f8f9fa 0%, #ffffff 100%);
    border-left: 4px solid #556ee6;
}

.notification-card.read {
    opacity: 0.7;
}

.notification-card:hover {
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
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
</style>
@endsection
