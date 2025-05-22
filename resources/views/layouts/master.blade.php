<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <title> @yield('title') | {{ $siteSettings['site_title'] ?? config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ $siteSettings['meta_description'] ?? 'Free Web Hosting Control Panel' }}" name="description" />
    <meta content="{{ $siteSettings['meta_author'] ?? 'Free Hosting Admin' }}" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('build/images/favicon.ico') }}">
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    <!-- Boxicons for notification system -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    @include('layouts.head-css')
    
    <!-- Notification Styles -->
    <style>
        /* Floating Notification Styles */
        .floating-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .btn-notification {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #556ee6;
            color: white;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-notification:hover {
            background-color: #4458cb;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .btn-notification i {
            font-size: 24px;
        }

        .notification-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #f46a6a;
            color: white;
            border-radius: 50%;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-panel {
            position: absolute;
            bottom: 60px;
            right: 0;
            width: 350px;
            max-height: 500px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
            display: none;
            flex-direction: column;
            overflow: hidden;
            transition: transform 0.3s ease, opacity 0.3s ease;
            transform: translateY(20px);
            opacity: 0;
            visibility: hidden;
        }

        .notification-panel.show {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
            display: flex;
        }

        .notification-header {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-header h5 {
            margin: 0;
            font-weight: 600;
            font-size: 16px;
        }

        .notification-header .btn-link {
            padding: 0;
            background: none;
            border: none;
            color: #556ee6;
            font-size: 13px;
            cursor: pointer;
            margin-right: 10px;
            text-decoration: none;
        }

        .notification-header .btn-close {
            background: none;
            border: none;
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
            color: #6c757d;
        }

        .notification-body {
            overflow-y: auto;
            max-height: 350px;
        }

        .notification-item {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: background-color 0.15s ease;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-item.unread {
            background-color: #f8f9fa;
            position: relative;
        }

        .notification-item.unread::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background-color: #556ee6;
        }

        .notification-icon {
            font-size: 24px;
            display: block;
            margin: 10px auto;
        }

        .notification-content {
            display: flex;
        }

        .notification-content .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #556ee6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .notification-content .avatar i {
            font-size: 18px;
        }

        .notification-content .details {
            flex: 1;
            min-width: 0;
        }

        .notification-content .title {
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .notification-content .message {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .notification-content .time {
            font-size: 12px;
            color: #adb5bd;
        }

        .notification-footer {
            padding: 10px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

        .notification-footer .btn-link {
            display: inline-block;
            text-decoration: none;
            color: #556ee6;
            font-size: 13px;
        }

        /* Loading spinner */
        .spinner {
            width: 30px;
            height: 30px;
            border: 3px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top-color: #556ee6;
            animation: spin 1s ease-in-out infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Overlay for mobile */
        .notification-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            display: none;
        }

        .notification-overlay.show {
            display: block;
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .floating-notification {
                bottom: 70px; /* Higher position to avoid navigation bar */
            }
            
            .notification-panel {
                position: fixed; /* Change to fixed to avoid positioning issues */
                left: 0;
                right: 0;
                bottom: 130px; /* Higher position above the button */
                width: 100%; /* Full screen width */
                max-width: 100%;
                border-radius: 12px 12px 0 0; /* Round corners only at top */
                max-height: 80vh; /* Limit height */
            }
            
            .notification-body {
                max-height: calc(80vh - 110px); /* Maximum height for body */
            }
        }

        /* Language selector */
        .language-selector {
            display: inline-flex;
            align-items: center;
            margin-right: 15px;
        }
        
        .language-selector .flag-icon {
            width: 20px;
            height: 15px;
            margin-right: 5px;
        }
        
        /* Ad slot styles */
        .ad-slot {
            min-height: 10px;
            width: 100%;
            display: block;
            overflow: hidden;
            margin: 15px 0;
        }
        
        .ad-slot-header {
            margin-top: 200px;
        }
        
        .ad-slot-footer {
            margin-top: 20px;
        }
        
        .ad-slot-sidebar-top {
            margin-bottom: 20px;
        }
        
        .ad-slot-sidebar-bottom {
            margin-top: 20px;
        }
        
        .ad-slot-content-top {
            margin-bottom: 25px;
        }
        
        .ad-slot-content-bottom {
            margin-top: 25px;
        }
    </style>
    
    @yield('css')
</head>

<body class="pace-done" data-lang="{{ app()->getLocale() }}">
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.topbar')
        
        <!-- Header Banner Ad Slot - Only show to non-admin users -->
        <br>
        <br>
        <br>
        @if(!(auth()->user()->role === 'admin' || auth()->user()->role === 'support'))
            @if(class_exists('\\App\\View\\Components\\AdSlot'))
                <x-ad-slot code="header_banner" class="ad-slot-header" />
            @endif
        @endif
        
        @include('layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <!-- Content Top Ad Slot - Only show to non-admin users -->
                    @if(!(auth()->user()->role === 'admin' || auth()->user()->role === 'support'))
                        @if(class_exists('\\App\\View\\Components\\AdSlot'))
                            <x-ad-slot code="content_top" class="ad-slot-content-top" />
                        @endif
                    @endif
                    
                    @yield('content')
                    
                    <!-- Content Bottom Ad Slot - Only show to non-admin users -->
                    @if(!(auth()->user()->role === 'admin' || auth()->user()->role === 'support'))
                        @if(class_exists('\\App\\View\\Components\\AdSlot'))
                            <x-ad-slot code="content_bottom" class="ad-slot-content-bottom" />
                        @endif
                    @endif
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            
            <!-- Footer Top Ad Slot - Only show to non-admin users -->
            @if(!(auth()->user()->role === 'admin' || auth()->user()->role === 'support'))
                @if(class_exists('\\App\\View\\Components\\AdSlot'))
                    <x-ad-slot code="footer_top" class="ad-slot-footer" />
                @endif
            @endif
            
            @include('layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
    
    <!-- Right Sidebar -->
    @include('layouts.right-sidebar')
    <!-- /Right-bar -->
    
    <!-- Notification overlay for mobile -->
    <div class="notification-overlay" id="notification-overlay"></div>
    
    <!-- Floating Notification Button -->
    <div class="floating-notification">
        <button type="button" class="btn-notification" id="float-notification-btn">
            <i class="bx bx-bell"></i>
            <span class="notification-count" id="float-notification-count" style="display: none;">0</span>
        </button>
        
        <div class="notification-panel" id="notification-panel">
            <div class="notification-header">
                <h5>@lang('notifications.notifications')</h5>
                <div>
                    <button class="btn-link" id="mark-all-read-btn">@lang('notifications.mark_all_read')</button>
                    <button class="btn-close" id="close-notification-btn">&times;</button>
                </div>
            </div>
            
            <div class="notification-body">
                <div id="float-notifications-loading" class="text-center p-3">
                    <div class="spinner"></div>
                    <p>@lang('notifications.loading')</p>
                </div>
                
                <div id="float-notifications-empty" class="text-center p-3" style="display: none;">
                    <i class="bx bx-bell-off text-muted notification-icon"></i>
                    <p>@lang('notifications.no_notifications')</p>
                </div>
                
                <div id="float-notifications-error" class="text-center p-3" style="display: none;">
                    <i class="bx bx-error-circle text-danger notification-icon"></i>
                    <p class="text-danger">@lang('notifications.errors.failed_to_load')</p>
                    <button class="btn btn-sm btn-primary" id="float-retry-btn">@lang('notifications.retry')</button>
                </div>
                
                <div id="float-notifications-list">
                    <!-- Notifications will be populated here -->
                </div>
            </div>
            
            <div class="notification-footer">
                <a href="{{ route('notifications.index') }}" class="btn-link">
                    @lang('notifications.view_all')
                </a>
            </div>
        </div>
    </div>
    
    <!-- JAVASCRIPT -->
    @include('layouts.vendor-scripts')
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Notification System JS -->
    <script src="{{ asset('/build/js/notifications.js') }}"></script>
    
    @yield('script')
    
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '@lang("translation.Success")',
                text: '{{ session("success") }}',
                icon: 'success',
                confirmButtonColor: '#556ee6',
                confirmButtonText: '@lang("translation.OK")'
            });
        });
    </script>
    @endif
    
    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '@lang("translation.Error")',
                text: '{{ session("error") }}',
                icon: 'error',
                confirmButtonColor: '#f46a6a',
                confirmButtonText: '@lang("translation.OK")'
            });
        });
    </script>
    @endif
    
    @if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '@lang("translation.Info")',
                text: '{{ session("info") }}',
                icon: 'info',
                confirmButtonColor: '#50a5f1',
                confirmButtonText: '@lang("translation.OK")'
            });
        });
    </script>
    @endif
    
    @if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '@lang("translation.Warning")',
                text: '{{ session("warning") }}',
                icon: 'warning',
                confirmButtonColor: '#f1b44c',
                confirmButtonText: '@lang("translation.OK")'
            });
        });
    </script>
    @endif
    
    @stack('modals')
</body>
</html>