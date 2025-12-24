@extends('Main::layouts.app')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Notification Details</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('hrm.notifications.index') }}">Notifications</a></li>
                            <li class="breadcrumb-item active">Details</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card notification-detail-card">
                    <!-- Header -->
                    <div class="card-header bg-primary bg-gradient">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title text-white mb-0">
                                    <i class="bx bx-bell me-2"></i>{{ $notification->data['title'] ?? 'Notification' }}
                                </h5>
                            </div>
                            <div class="flex-shrink-0">
                                @if(!$notification->read_at)
                                    <span class="badge bg-light text-primary">New</span>
                                @else
                                    <span class="badge bg-light text-muted">Read</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="card-body">
                        <div class="notification-content">
                            <!-- Icon and Type -->
                            <div class="text-center mb-4">
                                <div class="avatar-lg mx-auto">
                                    <span class="avatar-title rounded-circle font-size-24 notification-icon-gradient">
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

                            <!-- Message -->
                            <div class="notification-message mb-4">
                                <h6 class="text-muted mb-2">Message:</h6>
                                <p class="font-size-15">{{ $notification->data['message'] ?? 'No message content' }}</p>
                            </div>

                            <!-- Metadata -->
                            <div class="notification-meta">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-clock-outline text-primary font-size-20 me-2"></i>
                                            <div>
                                                <p class="text-muted mb-0 font-size-12">Received</p>
                                                <p class="mb-0 fw-medium">{{ $notification->created_at->format('M d, Y h:i A') }}</p>
                                                <p class="text-muted mb-0 font-size-12">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @if($notification->read_at)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-check-circle text-success font-size-20 me-2"></i>
                                            <div>
                                                <p class="text-muted mb-0 font-size-12">Read At</p>
                                                <p class="mb-0 fw-medium">{{ $notification->read_at->format('M d, Y h:i A') }}</p>
                                                <p class="text-muted mb-0 font-size-12">{{ $notification->read_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Action Button -->
                            @if(isset($notification->data['action_url']) && $notification->data['action_url'] !== '#')
                            <div class="mt-4 text-center">
                                <a href="{{ $notification->data['action_url'] }}" class="btn btn-primary">
                                    <i class="mdi mdi-open-in-new me-1"></i> View Related Item
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('hrm.notifications.index') }}" class="btn btn-soft-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i> Back to Notifications
                            </a>
                            <div>
                                <form action="{{ route('hrm.notifications.toggle-read', $notification->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-soft-{{ $notification->read_at ? 'warning' : 'success' }}">
                                        <i class="mdi mdi-{{ $notification->read_at ? 'email-mark-as-unread' : 'check' }} me-1"></i>
                                        Mark as {{ $notification->read_at ? 'Unread' : 'Read' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation to Next/Previous -->
                @php
                    $allNotifications = auth()->user()->notifications;
                    $currentIndex = $allNotifications->search(fn($n) => $n->id === $notification->id);
                    $prevNotification = $currentIndex > 0 ? $allNotifications[$currentIndex - 1] : null;
                    $nextNotification = $currentIndex < $allNotifications->count() - 1 ? $allNotifications[$currentIndex + 1] : null;
                @endphp

                @if($prevNotification || $nextNotification)
                <div class="d-flex justify-content-between mt-3">
                    @if($prevNotification)
                    <a href="{{ route('hrm.notifications.show', $prevNotification->id) }}" class="btn btn-outline-primary">
                        <i class="mdi mdi-chevron-left"></i> Previous
                    </a>
                    @else
                    <div></div>
                    @endif

                    @if($nextNotification)
                    <a href="{{ route('hrm.notifications.show', $nextNotification->id) }}" class="btn btn-outline-primary">
                        Next <i class="mdi mdi-chevron-right"></i>
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.notification-detail-card {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    border: none;
    border-radius: 0.75rem;
    overflow: hidden;
}

.notification-icon-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.notification-message {
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 0.5rem;
    border-left: 4px solid #556ee6;
}

.notification-meta {
    padding: 1.5rem;
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
}
</style>
@endsection
