@extends('layouts.master')

@section('title') @lang('notifications.notifications') @endsection

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
<style>
    :root {
        --primary-color: #4a6cf7;
        --primary-light: #eef2ff;
        --success-color: #13c296;
        --warning-color: #fbb040;
        --danger-color: #fb5d5d;
        --info-color: #3aa1ff;
        --secondary-color: #8f95b0;
        --text-color: #212b36;
        --text-light: #637381;
        --border-color: #e9ecef;
        --card-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        --transition-fast: 0.2s ease;
        --transition-normal: 0.3s ease;
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-circle: 50%;
    }

    /* Layout containers */
    .notifications-wrapper {
        max-width: 100%;
        margin: 0 auto;
    }
    
    @media (min-width: 992px) {
        .notifications-wrapper {
            max-width: 1100px;
        }
    }
    
    /* Navigation tabs */
    .n-tabs {
        display: flex;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 24px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
        padding-bottom: 1px;
    }
    
    .n-tabs::-webkit-scrollbar {
        display: none;
    }
    
    .n-tabs .nav-item {
        flex: 0 0 auto;
    }
    
    .n-tabs .nav-link {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: var(--text-light);
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        font-size: 15px;
        font-weight: 500;
        transition: all var(--transition-fast);
        white-space: nowrap;
    }
    
    .n-tabs .nav-link.active {
        color: var(--primary-color);
        border-bottom-color: var(--primary-color);
    }
    
    .n-tabs .nav-link i {
        margin-right: 8px;
        font-size: 18px;
        line-height: 0;
    }
    
    /* Top actions bar */
    .n-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .n-page-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-color);
        margin: 0;
    }
    
    @media (min-width: 768px) {
        .n-page-title {
            font-size: 20px;
        }
    }
    
    .n-read-all {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        background-color: #f8f9fa;
        color: var(--text-light);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 500;
        transition: all var(--transition-fast);
        cursor: pointer;
    }
    
    .n-read-all:hover {
        background-color: var(--primary-light);
        color: var(--primary-color);
    }
    
    .n-read-all i {
        margin-right: 6px;
    }
    
    /* Grid layout for notifications */
    .notification-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    @media (min-width: 768px) {
        .notification-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (min-width: 1200px) {
        .notification-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    /* Notification card */
    .notification-card {
        position: relative;
        background: #fff;
        border-radius: var(--radius-md);
        box-shadow: var(--card-shadow);
        padding: 16px;
        transition: all var(--transition-normal);
        border-left: 3px solid transparent;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .notification-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }
    
    .notification-card.unread {
        background-color: var(--primary-light);
    }
    
    .notification-card.type-login {
        border-left-color: var(--primary-color);
    }
    
    .notification-card.type-hosting {
        border-left-color: var(--success-color);
    }
    
    .notification-card.type-ticket {
        border-left-color: var(--info-color);
    }
    
    .notification-card.type-ssl {
        border-left-color: var(--warning-color);
    }
    
    .notification-card.type-account {
        border-left-color: var(--secondary-color);
    }
    
    .n-header {
        display: flex;
        margin-bottom: 12px;
    }
    
    .n-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: var(--radius-circle);
        margin-right: 12px;
        flex-shrink: 0;
    }
    
    .n-icon.icon-login {
        background-color: rgba(74, 108, 247, 0.15);
        color: var(--primary-color);
    }
    
    .n-icon.icon-hosting {
        background-color: rgba(19, 194, 150, 0.15);
        color: var(--success-color);
    }
    
    .n-icon.icon-ticket {
        background-color: rgba(58, 161, 255, 0.15);
        color: var(--info-color);
    }
    
    .n-icon.icon-ssl {
        background-color: rgba(251, 176, 64, 0.15);
        color: var(--warning-color);
    }
    
    .n-icon.icon-account {
        background-color: rgba(143, 149, 176, 0.15);
        color: var(--secondary-color);
    }
    
    .n-icon i {
        font-size: 20px;
    }
    
    .n-content {
        flex-grow: 1;
    }
    
    .n-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-color);
        margin: 0 0 6px 0;
        line-height: 1.3;
    }
    
    .n-message {
        font-size: 14px;
        color: var(--text-light);
        margin: 0 0 12px 0;
        line-height: 1.5;
        flex-grow: 1;
    }
    
    .n-time {
        display: flex;
        align-items: center;
        font-size: 12px;
        color: var(--text-light);
    }
    
    .n-time i {
        font-size: 14px;
        margin-right: 4px;
    }
    
    .n-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--border-color);
    }
    
    .n-action {
        margin-left: auto;
    }
    
    .n-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 14px;
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 500;
        transition: all var(--transition-fast);
        border: none;
        cursor: pointer;
    }
    
    .n-btn-primary {
        background-color: var(--primary-color);
        color: white;
    }
    
    .n-btn-primary:hover {
        background-color: #3d5bd9;
    }
    
    .n-btn-success {
        background-color: var(--success-color);
        color: white;
    }
    
    .n-btn-info {
        background-color: var(--info-color);
        color: white;
    }
    
    .n-btn-warning {
        background-color: var(--warning-color);
        color: white;
    }
    
    .n-btn-secondary {
        background-color: var(--secondary-color);
        color: white;
    }
    
    /* Empty State */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        text-align: center;
        grid-column: 1 / -1;
        background-color: #fff;
        border-radius: var(--radius-md);
        box-shadow: var(--card-shadow);
    }
    
    .empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 64px;
        height: 64px;
        background-color: var(--primary-light);
        color: var(--primary-color);
        border-radius: var(--radius-circle);
        margin-bottom: 16px;
    }
    
    .empty-icon i {
        font-size: 32px;
    }
    
    .empty-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-color);
        margin: 0 0 8px 0;
    }
    
    .empty-text {
        font-size: 14px;
        color: var(--text-light);
        max-width: 300px;
        margin: 0;
    }
    
    /* Unread indicator */
    .unread-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 8px;
        height: 8px;
        background-color: var(--primary-color);
        border-radius: 50%;
    }
    
    /* Pagination */
    .n-pagination {
        display: flex;
        justify-content: center;
        margin-top: 24px;
    }
    
    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .page-item {
        margin: 0 3px;
    }
    
    .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        border-radius: var(--radius-sm);
        font-size: 14px;
        color: var(--text-color);
        background-color: #fff;
        border: 1px solid var(--border-color);
        transition: all var(--transition-fast);
    }
    
    .page-item.active .page-link {
        background-color: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .page-item.disabled .page-link {
        color: #c5c5c5;
        pointer-events: none;
    }
    
    /* Responsive adjustments */
    @media (max-width: 576px) {
        .n-tabs .nav-link {
            padding: 10px 14px;
            font-size: 14px;
        }
        
        .n-icon {
            width: 36px;
            height: 36px;
        }
        
        .n-icon i {
            font-size: 18px;
        }
        
        .n-title {
            font-size: 15px;
        }
    }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') @lang('translation.Home') @endslot
@slot('title') @lang('notifications.notifications') @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="notifications-wrapper">
            <!-- Top action bar -->
            <div class="n-actions">
                <h1 class="n-page-title">@lang('notifications.notifications')</h1>
                <form method="POST" action="{{ route('notifications.mark-all-read') }}" id="markAllReadForm">
                    @csrf
                    <button type="button" class="n-read-all" id="markAllReadBtn">
                        <i class="bx bx-check-double"></i> @lang('notifications.mark_all_read')
                    </button>
                </form>
            </div>
            
            <!-- Navigation tabs -->
            <ul class="n-tabs nav" id="notificationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-tab-pane" type="button" role="tab">
                        <i class="bx bx-bell"></i> @lang('notifications.categories.all')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-tab-pane" type="button" role="tab">
                        <i class="bx bx-log-in"></i> @lang('notifications.categories.login')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="hosting-tab" data-bs-toggle="tab" data-bs-target="#hosting-tab-pane" type="button" role="tab">
                        <i class="bx bx-server"></i> @lang('notifications.categories.hosting')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tickets-tab" data-bs-toggle="tab" data-bs-target="#tickets-tab-pane" type="button" role="tab">
                        <i class="bx bx-support"></i> @lang('notifications.categories.tickets')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ssl-tab" data-bs-toggle="tab" data-bs-target="#ssl-tab-pane" type="button" role="tab">
                        <i class="bx bx-lock-alt"></i> @lang('notifications.categories.ssl')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="account-tab" data-bs-toggle="tab" data-bs-target="#account-tab-pane" type="button" role="tab">
                        <i class="bx bx-user-circle"></i> @lang('notifications.categories.account')
                    </button>
                </li>
            </ul>
            
            <!-- Tab content -->
            <div class="tab-content" id="notificationTabContent">
                <!-- All Notifications Tab -->
                <div class="tab-pane fade show active" id="all-tab-pane" role="tabpanel" aria-labelledby="all-tab" tabindex="0">
                    <div class="notification-grid">
                        @forelse($notifications as $notification)
                            <div class="notification-card type-{{ $notification->type }} {{ $notification->is_read ? '' : 'unread' }}" data-id="{{ $notification->id }}">
                                @if(!$notification->is_read)
                                    <span class="unread-badge"></span>
                                @endif
                                
                                <div class="n-header">
                                    <div class="n-icon icon-{{ $notification->type }}">
                                        <i class="{{ $notification->icon_class }}"></i>
                                    </div>
                                    <div class="n-content">
                                        <h3 class="n-title">{{ $notification->localized_title }}</h3>
                                    </div>
                                </div>
                                
                                <div class="n-message">{{ $notification->localized_content }}</div>
                                
                                <div class="n-footer">
                                    <div class="n-time">
                                        <i class="bx bx-time-five"></i>
                                        <span>{{ $notification->time_ago }}</span>
                                    </div>
                                    
                                    @if($notification->action_url)
                                    <div class="n-action">
                                        <!-- Use form with POST method instead of GET link -->
                                        <form method="POST" action="{{ route('notifications.mark-as-read', ['id' => $notification->id]) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="redirect" value="1">
                                            <button type="submit" class="n-btn n-btn-{{ $notification->type === 'login' ? 'primary' : ($notification->type === 'hosting' ? 'success' : ($notification->type === 'ticket' ? 'info' : ($notification->type === 'ssl' ? 'warning' : 'secondary'))) }}">
                                                {{ $notification->localized_action_text ?? __('notifications.actions.view') }}
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bx bx-bell-off"></i>
                                </div>
                                <h3 class="empty-title">@lang('notifications.no_notifications')</h3>
                                <p class="empty-text">@lang('notifications.no_notifications_message')</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Login Notifications Tab -->
                <div class="tab-pane fade" id="login-tab-pane" role="tabpanel" aria-labelledby="login-tab" tabindex="0">
                    <div class="notification-grid">
                        @forelse($loginNotifications as $notification)
                            <div class="notification-card type-login {{ $notification->is_read ? '' : 'unread' }}" data-id="{{ $notification->id }}">
                                @if(!$notification->is_read)
                                    <span class="unread-badge"></span>
                                @endif
                                
                                <div class="n-header">
                                    <div class="n-icon icon-login">
                                        <i class="bx bx-log-in"></i>
                                    </div>
                                    <div class="n-content">
                                        <h3 class="n-title">{{ $notification->localized_title }}</h3>
                                    </div>
                                </div>
                                
                                <div class="n-message">{{ $notification->localized_content }}</div>
                                
                                <div class="n-footer">
                                    <div class="n-time">
                                        <i class="bx bx-time-five"></i>
                                        <span>{{ $notification->time_ago }}</span>
                                    </div>
                                    
                                    @if($notification->action_url)
                                    <div class="n-action">
                                        <!-- Use form with POST method instead of GET link -->
                                        <form method="POST" action="{{ route('notifications.mark-as-read', ['id' => $notification->id]) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="redirect" value="1">
                                            <button type="submit" class="n-btn n-btn-primary">
                                                {{ $notification->localized_action_text ?? __('notifications.actions.view') }}
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bx bx-log-in-circle"></i>
                                </div>
                                <h3 class="empty-title">@lang('notifications.empty_states.login')</h3>
                                <p class="empty-text">@lang('notifications.empty_states.login_message')</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Hosting Notifications Tab -->
                <div class="tab-pane fade" id="hosting-tab-pane" role="tabpanel" aria-labelledby="hosting-tab" tabindex="0">
                    <div class="notification-grid">
                        @forelse($hostingNotifications as $notification)
                            <div class="notification-card type-hosting {{ $notification->is_read ? '' : 'unread' }}" data-id="{{ $notification->id }}">
                                @if(!$notification->is_read)
                                    <span class="unread-badge"></span>
                                @endif
                                
                                <div class="n-header">
                                    <div class="n-icon icon-hosting">
                                        <i class="bx bx-server"></i>
                                    </div>
                                    <div class="n-content">
                                        <h3 class="n-title">{{ $notification->localized_title }}</h3>
                                    </div>
                                </div>
                                
                                <div class="n-message">{{ $notification->localized_content }}</div>
                                
                                <div class="n-footer">
                                    <div class="n-time">
                                        <i class="bx bx-time-five"></i>
                                        <span>{{ $notification->time_ago }}</span>
                                    </div>
                                    
                                    @if($notification->action_url)
                                    <div class="n-action">
                                        <!-- Use form with POST method instead of GET link -->
                                        <form method="POST" action="{{ route('notifications.mark-as-read', ['id' => $notification->id]) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="redirect" value="1">
                                            <button type="submit" class="n-btn n-btn-success">
                                                {{ $notification->localized_action_text ?? __('notifications.actions.view') }}
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bx bx-server"></i>
                                </div>
                                <h3 class="empty-title">@lang('notifications.empty_states.hosting')</h3>
                                <p class="empty-text">@lang('notifications.empty_states.hosting_message')</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Tickets Notifications Tab -->
                <div class="tab-pane fade" id="tickets-tab-pane" role="tabpanel" aria-labelledby="tickets-tab" tabindex="0">
                    <div class="notification-grid">
                        @forelse($ticketNotifications as $notification)
                            <div class="notification-card type-ticket {{ $notification->is_read ? '' : 'unread' }}" data-id="{{ $notification->id }}">
                                @if(!$notification->is_read)
                                    <span class="unread-badge"></span>
                                @endif
                                
                                <div class="n-header">
                                    <div class="n-icon icon-ticket">
                                        <i class="bx bx-support"></i>
                                    </div>
                                    <div class="n-content">
                                        <h3 class="n-title">{{ $notification->localized_title }}</h3>
                                    </div>
                                </div>
                                
                                <div class="n-message">{{ $notification->localized_content }}</div>
                                
                                <div class="n-footer">
                                    <div class="n-time">
                                        <i class="bx bx-time-five"></i>
                                        <span>{{ $notification->time_ago }}</span>
                                    </div>
                                    
                                    @if($notification->action_url)
                                    <div class="n-action">
                                        <!-- Use form with POST method instead of GET link -->
                                        <form method="POST" action="{{ route('notifications.mark-as-read', ['id' => $notification->id]) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="redirect" value="1">
                                            <button type="submit" class="n-btn n-btn-info">
                                                {{ $notification->localized_action_text ?? __('notifications.actions.view') }}
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bx bx-support"></i>
                                </div>
                                <h3 class="empty-title">@lang('notifications.empty_states.ticket')</h3>
                                <p class="empty-text">@lang('notifications.empty_states.ticket_message')</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- SSL Notifications Tab -->
                <div class="tab-pane fade" id="ssl-tab-pane" role="tabpanel" aria-labelledby="ssl-tab" tabindex="0">
                    <div class="notification-grid">
                        @forelse($sslNotifications as $notification)
                            <div class="notification-card type-ssl {{ $notification->is_read ? '' : 'unread' }}" data-id="{{ $notification->id }}">
                                @if(!$notification->is_read)
                                    <span class="unread-badge"></span>
                                @endif
                                
                                <div class="n-header">
                                    <div class="n-icon icon-ssl">
                                        <i class="bx bx-lock-alt"></i>
                                    </div>
                                    <div class="n-content">
                                        <h3 class="n-title">{{ $notification->localized_title }}</h3>
                                    </div>
                                </div>
                                
                                <div class="n-message">{{ $notification->localized_content }}</div>
                                
                                <div class="n-footer">
                                    <div class="n-time">
                                        <i class="bx bx-time-five"></i>
                                        <span>{{ $notification->time_ago }}</span>
                                    </div>
                                    
                                    @if($notification->action_url)
                                    <div class="n-action">
                                        <!-- Use form with POST method instead of GET link -->
                                        <form method="POST" action="{{ route('notifications.mark-as-read', ['id' => $notification->id]) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="redirect" value="1">
                                            <button type="submit" class="n-btn n-btn-warning">
                                                {{ $notification->localized_action_text ?? __('notifications.actions.view') }}
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bx bx-lock-alt"></i>
                                </div>
                                <h3 class="empty-title">@lang('notifications.empty_states.ssl')</h3>
                                <p class="empty-text">@lang('notifications.empty_states.ssl_message')</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Account Notifications Tab -->
                <div class="tab-pane fade" id="account-tab-pane" role="tabpanel" aria-labelledby="account-tab" tabindex="0">
                    <div class="notification-grid">
                        @forelse($accountNotifications as $notification)
                            <div class="notification-card type-account {{ $notification->is_read ? '' : 'unread' }}" data-id="{{ $notification->id }}">
                                @if(!$notification->is_read)
                                    <span class="unread-badge"></span>
                                @endif
                                
                                <div class="n-header">
                                    <div class="n-icon icon-account">
                                        <i class="bx bx-user-circle"></i>
                                    </div>
                                    <div class="n-content">
                                        <h3 class="n-title">{{ $notification->localized_title }}</h3>
                                    </div>
                                </div>
                                
                                <div class="n-message">{{ $notification->localized_content }}</div>
                                
                                <div class="n-footer">
                                    <div class="n-time">
                                        <i class="bx bx-time-five"></i>
                                        <span>{{ $notification->time_ago }}</span>
                                    </div>
                                    
                                    @if($notification->action_url)
                                    <div class="n-action">
                                        <!-- Use form with POST method instead of GET link -->
                                        <form method="POST" action="{{ route('notifications.mark-as-read', ['id' => $notification->id]) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="redirect" value="1">
                                            <button type="submit" class="n-btn n-btn-secondary">
                                                {{ $notification->localized_action_text ?? __('notifications.actions.view') }}
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bx bx-user-circle"></i>
                                </div>
                                <h3 class="empty-title">@lang('notifications.empty_states.account')</h3>
                                <p class="empty-text">@lang('notifications.empty_states.account_message')</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Pagination -->
            @if($notifications->lastPage() > 1)
    <nav class="n-pagination" aria-label="@lang('notifications.pagination')">
        <ul class="pagination justify-content-center">
            @for ($i = 1; $i <= $notifications->lastPage(); $i++)
                <li class="page-item {{ ($notifications->currentPage() == $i) ? 'active' : '' }}">
                    <a class="page-link" href="{{ $notifications->url($i) }}">{{ $i }}</a>
                </li>
            @endfor
        </ul>
    </nav>
@endif
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mark all notifications as read
        const markAllReadBtn = document.getElementById('markAllReadBtn');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function() {
                fetch('{{ route("notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI to mark all as read
                        document.querySelectorAll('.notification-card.unread').forEach(card => {
                            card.classList.remove('unread');
                            const unreadBadge = card.querySelector('.unread-badge');
                            if (unreadBadge) {
                                unreadBadge.remove();
                            }
                        });
                        
                        // Show success message
                        alert('@lang("notifications.messages.all_marked_as_read")');
                    }
                })
                .catch(error => {
                    console.error('@lang("notifications.errors.failed_to_mark_all_read")', error);
                    alert('@lang("notifications.errors.failed_to_mark_all_read")');
                });
            });
        }
    });
</script>
@endsection