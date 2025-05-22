@extends('layouts.master')

@section('title') @lang('translation.Account_Details')
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.17/dist/sweetalert2.min.css">
<style>
    .verification-banner {
        background-color: #f8f9fa;
        border-left: 4px solid #4338ca;
        border-radius: 0.25rem;
        margin-bottom: 20px;
        padding: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        opacity: 0;
        transform: translateY(-10px);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    
    .verification-banner.show {
        opacity: 1;
        transform: translateY(0);
    }
    
    .copyable {
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 4px;
        transition: background-color 0.2s ease;
        position: relative;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    
    .copyable:hover {
        background-color: rgba(67, 56, 202, 0.1);
    }
    
    /* Hiển thị "Click to copy" chỉ trên desktop */
    @media (min-width: 769px) {
        .copyable:hover::after {
            content: "@lang('translation.Click_to_copy')";
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 100;
        }
    }
    
    /* Animation for copying */
    @keyframes copiedAnimation {
        0% { background-color: rgba(16, 185, 129, 0); }
        50% { background-color: rgba(16, 185, 129, 0.2); }
        100% { background-color: rgba(16, 185, 129, 0); }
    }
    
    .copied {
        animation: copiedAnimation 1s ease;
    }
    
    /* Admin Deactivation Notice */
    .admin-deactivation-notice {
        border-left: 4px solid #ef4444;
    }
    
    /* Feature locked styling */
    .feature-locked .feature-btn {
        position: relative;
    }
    
    .feature-locked .feature-btn::before {
        content: "@lang('translation.Verify_cPanel_first')";
        position: absolute;
        top: -30px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #333;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        opacity: 0;
        transition: opacity 0.2s ease;
        z-index: 10;
    }
    
    .feature-locked .feature-btn:hover::before {
        opacity: 1;
    }
    
    /* Thêm style cho nút copy trên mobile */
    @media (max-width: 768px) {
        .copy-btn-container {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .mobile-copy-btn {
            background-color: transparent;
            border: none;
            color: #4338ca;
            padding: 4px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 30px;
            height: 30px;
        }
        
        .mobile-copy-btn:hover, .mobile-copy-btn:active {
            background-color: rgba(67, 56, 202, 0.1);
        }
        
        .mobile-copy-btn i {
            font-size: 16px;
        }
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.Hosting') @endslot
        @slot('title') @lang('translation.Account_Details') @endslot
    @endcomponent

    {{-- Verification Banner --}}
    @if($account->status === 'active' && !$account->cpanel_verified)
    <div id="verificationBanner" class="verification-banner">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i data-feather="alert-circle" class="font-size-24"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="mb-1">@lang('translation.Verify_Your_cPanel_Access')</h5>
                <p class="mb-0">@lang('translation.cpanel_verify_messs')</p>
            </div>
            <div class="ms-3">
                <button type="button" id="loginNowBtn" class="btn btn-light btn-sm waves-effect">
                    <i data-feather="log-in" class="me-1"></i> @lang('translation.Login_Now')
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Alert Messages --}}
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row">
        {{-- Status Card --}}
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary-subtle text-primary rounded-3">
                                <i data-feather="server" class="font-size-24"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-2">@lang('translation.Status')</p>
                            <h5 class="mb-0">
                                @if($account->status === 'active')
                                    <span class="badge rounded-pill bg-success">@lang('translation.status_active')</span>
                                @elseif($account->status === 'suspended')
                                    <span class="badge rounded-pill bg-danger">@lang('translation.status_suspended')</span>
                                @else
                                    <span class="badge rounded-pill bg-warning">@lang('translation.status_' . $account->status)</span>
                                @endif
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Admin Deactivation Notice --}}
        @if($account->admin_deactivated)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger admin-deactivation-notice">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <i data-feather="alert-octagon" class="font-size-24"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mt-0 mb-1">@lang('translation.admin_deactivated')</h5>
                            <p class="mb-0">@lang('translation.can_reactivated')</p>
                            <p class="mt-2 mb-0"><strong>@lang('translation.Reason'):</strong> {{ $account->admin_deactivation_reason }}</p>
                            <p class="mt-2 mb-0">@lang('translation.If_Questions') <a href="{{ route('user.tickets.create') }}">{{ __('Open') . ' ' . __('Tickets') }}</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        {{-- Dynamic Stats Cards --}}
        @foreach(['disk', 'bandwidth', 'inodes'] as $stat)
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card" data-stat="{{ $stat }}" style="display: none;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center" style="min-height: 120px;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">@lang('translation.Loading')...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Quick Actions --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Control Panel --}}
                        <div class="col-md-6 col-lg-3">
                            <button type="button" 
                                   onclick="handleCpanelLogin()" 
                                   class="btn btn-primary w-100 waves-effect waves-light cpanel-btn {{ $account->status !== 'active' ? 'disabled' : '' }}">
                                <i data-feather="monitor" class="font-size-16 align-middle me-2"></i>
                                @lang('translation.Control_Panel')
                            </button>
                        </div>

                        {{-- File Manager --}}
                        <div class="col-md-6 col-lg-3 {{ (!$account->cpanel_verified) ? 'feature-locked' : '' }}">
                            @if(App\Models\WebFtpSetting::isEnabled())
                                <a href="{{ route('webftp.index', $account->username) }}"
                                   class="btn btn-info w-100 waves-effect waves-light feature-btn {{ (!$account->cpanel_verified || $account->status !== 'active') ? 'disabled' : '' }}"
                                   target="_blank">
                                    <i data-feather="folder" class="font-size-16 align-middle me-2"></i>
                                    @lang('translation.File_Manager')
                                </a>
                            @else
                                <a href="{{ route('hosting.filemanager', $account->username) }}"
                                   class="btn btn-info w-100 waves-effect waves-light feature-btn {{ (!$account->cpanel_verified || $account->status !== 'active') ? 'disabled' : '' }}"
                                   target="_blank">
                                    <i data-feather="folder" class="font-size-16 align-middle me-2"></i>
                                    @lang('translation.File_Manager')
                                </a>
                            @endif
                        </div>

                        {{-- Softaculous --}}
                        <div class="col-md-6 col-lg-3 {{ (!$account->cpanel_verified) ? 'feature-locked' : '' }}">
                            <a href="{{ route('hosting.softaculous', $account->username) }}" target="_blank"
                               class="btn btn-secondary w-100 waves-effect waves-light feature-btn {{ (!$account->cpanel_verified || $account->status !== 'active') ? 'disabled' : '' }}">
                                <i data-feather="box" class="font-size-16 align-middle me-2"></i>
                                @lang('translation.Softaculous')
                            </a>
                        </div>

                        {{-- Settings/Actions --}}
                        <div class="col-md-6 col-lg-3 {{ (!$account->cpanel_verified) ? 'feature-locked' : '' }}">
                            @if($account->status === 'deactivated')
                                <a href="{{ route('hosting.reactivate', $account->username) }}"
                                   class="btn btn-success w-100 waves-effect waves-light">
                                    <i data-feather="refresh-cw" class="font-size-16 align-middle me-2"></i>
                                    @lang('translation.Reactivate')
                                </a>
                            @elseif($account->status === 'suspended')
                                <a href="{{ route('user.tickets.create') }}"
                                   class="btn btn-warning w-100 waves-effect waves-light">
                                    <i data-feather="message-square" class="font-size-16 align-middle me-2"></i>
                                    @lang('translation.Open') @lang('translation.Tickets')
                                </a>
                            @else
                                <a href="{{ route('hosting.settings', $account->username) }}"
                                   class="btn btn-dark w-100 waves-effect waves-light feature-btn {{ (!$account->cpanel_verified || $account->status !== 'active') ? 'disabled' : '' }}">
                                    <i data-feather="settings" class="font-size-16 align-middle me-2"></i>
                                    @lang('translation.Settings')
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Account Info & Connection Details --}}
    <div class="row">
        {{-- Account Info --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">@lang('translation.Account_Details')</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="user" class="font-size-20 text-primary me-2"></i>
                                            @lang('translation.Username')
                                        </div>
                                    </th>
                                    <td>
                                        <div class="copy-btn-container">
                                            <span id="account-username" class="copyable">
                                                {{ $account->status === 'active' ? $account->username : __('translation.Loading') . '...' }}
                                            </span>
                                            <button class="mobile-copy-btn d-md-none" data-copy-target="account-username">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="key" class="font-size-20 text-primary me-2"></i>
                                            @lang('translation.Password')
                                        </div>
                                    </th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span id="password-hidden">••••••••</span>
                                            <div class="copy-btn-container">
                                                <span id="password-shown" class="copyable d-none">
                                                    {{ $account->status === 'active' ? $account->password : '••••••••' }}
                                                </span>
                                                <button class="mobile-copy-btn d-md-none" data-copy-target="password-shown">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                            <button type="button" onclick="togglePassword()" class="btn btn-link text-muted p-0 ms-2">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="globe" class="font-size-20 text-primary me-2"></i>
                                            @lang('translation.Server_IP')
                                        </div>
                                    </th>
                                    <td>
                                        @if($serverIp)
                                            <div class="copy-btn-container">
                                                <span id="server-ip" class="copyable">{{ $serverIp }}</span>
                                                <button class="mobile-copy-btn d-md-none" data-copy-target="server-ip">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="globe" class="font-size-20 text-primary me-2"></i>
                                            @lang('translation.Domains')
                                        </div>
                                    </th>
                                    <td>
                                        @if($account->status === 'active' && count($domains) > 0)
                                            @foreach($domains as $domain)
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <div class="copy-btn-container">
                                                        <span class="domain-item copyable">{{ $domain['domain'] ?? $domain }}</span>
                                                        <button class="mobile-copy-btn d-md-none" data-copy-target="domain-item">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                    <a href="{{ route('hosting.builder', [
                                                        'username' => $account->username,
                                                        'domain' => is_array($domain) ? $domain['domain'] : $domain
                                                    ]) }}"
                                                        class="btn btn-sm btn-primary waves-effect waves-light feature-btn {{ !$account->cpanel_verified ? 'disabled' : '' }}"
                                                        target="_blank">
                                                        <i class="fas fa-paint-brush me-1"></i>
                                                        SitePro
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-muted">@lang('translation.No_domains')</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Connection Details --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">@lang('translation.Connection_Details')</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="database" class="font-size-20 text-primary me-2"></i>
                                            {{ __('MySQL') . ' ' . __('Host') }}
                                        </div>
                                    </th>
                                    <td class="{{ !$account->cpanel_verified ? 'text-muted' : '' }}">
                                        @if($account->status === 'active' && $account->cpanel_verified)
                                            <div class="copy-btn-container">
                                                <span id="mysql-host" class="copyable">
                                                    {{ $account->mysql_host }}
                                                    @if(empty($account->sql_server))
                                                        <span class="text-warning">(@lang('translation.SQL_server_not_assigned'))</span>
                                                    @endif
                                                </span>
                                                <button class="mobile-copy-btn d-md-none" data-copy-target="mysql-host">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted">@lang('translation.Verify_cPanel_to_view')</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="git-commit" class="font-size-20 text-primary me-2"></i>
                                            {{ __('MySQL') . ' ' . __('Port') }}
                                        </div>
                                    </th>
                                    <td class="{{ !$account->cpanel_verified ? 'text-muted' : '' }}">
                                        @if($account->cpanel_verified)
                                            3306
                                        @else
                                            <span class="text-muted">@lang('translation.Verify_cPanel_to_view')</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="server" class="font-size-20 text-primary me-2"></i>
                                            {{ __('FTP') . ' ' . __('Host') }}
                                        </div>
                                    </th>
                                    <td class="{{ !$account->cpanel_verified ? 'text-muted' : '' }}">
                                        @if($account->cpanel_verified)
                                            <div class="copy-btn-container">
                                                <span id="ftp-host" class="copyable">ftpupload.net</span>
                                                <button class="mobile-copy-btn d-md-none" data-copy-target="ftp-host">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted">@lang('translation.Verify_cPanel_to_view')</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="git-commit" class="font-size-20 text-primary me-2"></i>
                                            {{ __('FTP') . ' ' . __('Port') }}
                                        </div>
										</th>
                                    <td class="{{ !$account->cpanel_verified ? 'text-muted' : '' }}">
                                        @if($account->cpanel_verified)
                                            21
                                        @else
                                            <span class="text-muted">@lang('translation.Verify_cPanel_to_view')</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    {{-- Usage Statistics --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">@lang('translation.Usage_Statistics')</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Hits Chart --}}
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="hitsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Inodes Chart --}}
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="inodesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Bandwidth Chart --}}
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="bandwidthChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Disk Space Chart --}}
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="diskspaceChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden account username for JS --}}
    <input type="hidden" id="accountUsername" value="{{ $account->username }}">
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17/dist/sweetalert2.all.min.js"></script>
<script>
// Constants and State
const CPANEL_VERIFY_KEY = 'cpanel_verify_{{ $account->username }}';
const VERIFICATION_CHECK_INTERVAL = 5000; // 5 seconds
let verificationCheckTimer = null;
let isVerifying = false; // Track verification state
let charts = {}; // Store chart instances

// Debug mode
const DEBUG = {{ config('app.debug') ? 'true' : 'false' }};

function debug(message, data = null) {
    if (DEBUG) {
        console.log(`[DEBUG] ${message}`, data || '');
    }
}

// Password Toggle Function
function togglePassword() {
    const hiddenEl = document.getElementById('password-hidden');
    const shownEl = document.getElementById('password-shown');
    const icon = document.querySelector('.fa-eye');

    if (hiddenEl.classList.contains('d-none')) {
        hiddenEl.classList.remove('d-none');
        shownEl.classList.add('d-none');
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        hiddenEl.classList.add('d-none');
        shownEl.classList.remove('d-none');
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}

// Handle cPanel login and verification process
function handleCpanelLogin() {
    debug('@lang("translation.Handling_cPanel")');
    
    const isCpanelVerified = "{{ $account->cpanel_verified }}" === "1";
    
    if (isCpanelVerified) {
        window.open("{{ route('hosting.cpanel', $account->username) }}", '_blank');
        return;
    }
    
    if (isVerifying) {
        debug('@lang("translation.Verification_progress")');
        return;  
    }

    isVerifying = true;
    localStorage.setItem(CPANEL_VERIFY_KEY, '1');
    
    const cpanelWindow = window.open("{{ route('hosting.cpanel', $account->username) }}", '_blank');
    
    if (cpanelWindow) {
        debug('cPanel window opened successfully');
        
        Swal.fire({
            title: '@lang("translation.Control_Panel") @lang("translation.Login")',
            text: '@lang("translation.Please_wait_verification")',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        startVerificationCheck();
    } else {
        debug('Failed to open cPanel window');
        isVerifying = false;
        
        Swal.fire({
            title: '@lang("translation.Popup_Blocked")',
            text: '@lang("translation.Allow_popups_try_again")',
            icon: 'warning',
            confirmButtonText: '@lang("translation.Try_Again")'
        });
    }
}

function startVerificationCheck() {
    if (!isVerifying) return;
    
    checkVerificationStatus();
    verificationCheckTimer = setInterval(checkVerificationStatus, VERIFICATION_CHECK_INTERVAL);
}

function checkVerificationStatus() {
    if (!isVerifying) {
        clearInterval(verificationCheckTimer);
        return;
    }

    const accountUsername = document.getElementById('accountUsername').value;
    const url = "{{ route('hosting.verify-cpanel', ':username') }}".replace(':username', accountUsername);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            handleVerificationSuccess();
        }
    })
    .catch(error => {
        debug('Verification failed:', error);
        handleError(error);
    });
}

function handleVerificationSuccess() {
    clearInterval(verificationCheckTimer);
    localStorage.removeItem(CPANEL_VERIFY_KEY);
    isVerifying = false;

    Swal.fire({
        title: '@lang("translation.Verification_Successful")',
        text: '@lang("translation.cPanel_verified_features_unlocked")',
        icon: 'success',
        confirmButtonText: '@lang("translation.Continue")',
        confirmButtonColor: '#4338ca',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            location.reload();
        }
    });
}

// Handle error display
function handleError(error) {
    Swal.fire({
        title: '@lang("translation.Verification_Error")',
        text: error.message || '@lang("translation.Error_during_verification")',
        icon: 'error',
        confirmButtonText: '@lang("translation.Try_Again")'
    });
    
    clearInterval(verificationCheckTimer);
    isVerifying = false;
}

function showToast(message, type = 'success') {
    const isMobile = window.innerWidth <= 768;
    
    const Toast = Swal.mixin({
        toast: true,
        position: isMobile ? 'top-end' : 'bottom-end', 
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        width: isMobile ? 'auto' : null
    });
    
    Toast.fire({
        icon: type,
        title: message
    });
}

// Hàm copy không cần chọn văn bản - phương pháp số 4
function copyWithoutSelection(text, element) {
    if (!text) return;
    
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text)
            .then(() => {
                showCopyAnimation(element);
                showToast('@lang("translation.Copied_to_clipboard")');
            })
            .catch(err => {
                console.error('Copy failed:', err);
                fallbackCopyMethod(text, element);
            });
    } else {
        fallbackCopyMethod(text, element);
    }
}

function fallbackCopyMethod(text, element) {
    try {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        textarea.style.left = '-9999px';
        textarea.style.top = '-9999px';
        
        document.body.appendChild(textarea);
        
        textarea.focus();
        const successful = document.execCommand('copy');
        
        document.body.removeChild(textarea);
        
        if (successful) {
            showCopyAnimation(element);
            showToast('@lang("translation.Copied_to_clipboard")');
        } else {
            showToast('@lang("translation.Copy_failed_try_again")', 'error');
        }
    } catch (err) {
        console.error('Fallback copy error:', err);
        showToast('@lang("translation.Copy_failed_try_again")', 'error');
    }
}

function showCopyAnimation(element) {
    if (!element) return;
    
    const isMobile = window.innerWidth <= 768;
    
    if (isMobile) {
        if (element.tagName === 'BUTTON') {
            const originalIcon = element.innerHTML;
            element.innerHTML = '<i class="fas fa-check text-success"></i>';
            
            setTimeout(() => {
                element.innerHTML = originalIcon;
            }, 1000);
        } else {
            element.classList.add('copied');
            setTimeout(() => {
                element.classList.remove('copied');
            }, 1000);
        }
    } else {
        element.classList.add('copied');
        setTimeout(() => {
            element.classList.remove('copied');
        }, 1000);
    }
}

function initCopyableElements() {
    document.querySelectorAll('.copyable').forEach(el => {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const textToCopy = this.textContent.trim();
            copyWithoutSelection(textToCopy, this);
        });
        
        el.addEventListener('touchstart', function(e) {
            if (e.touches.length > 1) return; 
            e.preventDefault();
        }, { passive: false }); 
    });
    
    document.querySelectorAll('.mobile-copy-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const targetId = this.getAttribute('data-copy-target');
            const targetEl = targetId ? 
                document.getElementById(targetId) : 
                this.closest('.copy-btn-container').querySelector('.copyable');
                
            if (targetEl) {
                const textToCopy = targetEl.textContent.trim();
                copyWithoutSelection(textToCopy, this);
            }
        });
    });
}

// Stats Management
function showStatsError(message) {
    debug('Showing stats error:', message);
    
    document.querySelectorAll('[data-stat]').forEach(card => {
        card.style.display = 'block';
        card.querySelector('.card-body').innerHTML = `
            <div class="d-flex align-items-center justify-content-center" style="min-height: 120px;">
                <div class="text-center">
                    <i class="fas fa-exclamation-circle text-danger mb-2" style="font-size: 24px;"></i>
                    <p class="text-danger mb-2">${message}</p>
                    <button onclick="retryLoadStats()" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-sync-alt me-1"></i> @lang("translation.Retry")
                    </button>
                </div>
            </div>
        `;
    });
}

function loadAccountStats() {
    const accountUsername = document.getElementById('accountUsername').value;
    
    const url = "{{ route('hosting.stats', ':username') }}".replace(':username', accountUsername);
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('@lang("translation.Failed_to_fetch_stats")');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updateStatsCards(data.data);
                initializeCharts();
            } else {
                throw new Error(data.message || '@lang("translation.Failed_to_load_stats")');
            }
        })
        .catch(error => {
            console.error('Error loading stats:', error);
            showStatsError(error.message || '@lang("translation.Failed_to_fetch_stats_data")');
        });
}

function retryLoadStats() {
    debug('Retrying stats load');
    
    document.querySelectorAll('[data-stat]').forEach(card => {
        card.querySelector('.card-body').innerHTML = `
            <div class="d-flex align-items-center justify-content-center" style="min-height: 120px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">@lang("translation.Loading")...</span>
                </div>
            </div>
        `;
    });

    setTimeout(loadAccountStats, 1000);
}

function updateStatsCards(stats) {
    const formatNumber = (num) => num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    
    const createStatCard = (title, used, total, unit, percent, icon) => `
        <div class="d-flex align-items-center">
            <div class="avatar-sm">
                <span class="avatar-title bg-primary-subtle text-primary rounded-3">
                    <i data-feather="${icon}" class="font-size-24"></i>
                </span>
            </div>
            <div class="flex-grow-1 ms-3">
                <p class="text-muted mb-2">${title}</p>
                <div class="progress mb-2" style="height: 6px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: ${percent}%;" 
                         aria-valuenow="${percent}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>
                <h5 class="mb-0 font-size-14">
                    ${formatNumber(used)} ${unit} 
                    ${total === 'Unlimited' ? '' : `/ ${formatNumber(total)} ${unit}`}
                    <small class="ms-1">(${percent}%)</small>
                </h5>
            </div>
        </div>
    `;

    Object.entries(stats).forEach(([type, data]) => {
        const card = document.querySelector(`[data-stat="${type}"]`);
        if (!card) return;

        let title, icon;
        switch(type) {
            case 'disk':
                title = '@lang("translation.Disk_Space")';
                icon = 'hard-drive';
                break;
            case 'bandwidth':
                title = '@lang("translation.Bandwidth")';
                icon = 'wifi';
                break;
            case 'inodes':
                title = '@lang("translation.Inodes")';
                icon = 'file';
                break;
            default:
                return;
        }

        card.style.display = 'block';
        card.querySelector('.card-body').innerHTML = createStatCard(
            title,
            data.used,
            data.total,
            data.unit || '',
            data.percent,
            icon
        );

        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
}

// Chart Management
function initializeCharts() {
    try {
        const types = ['hits', 'bandwidth', 'inodes', 'diskspace'];
        const chartColors = {
            usage: '#4B6CBF',
            limit: '#DC3545',
            average: '#28A745'
        };

        types.forEach(type => {
            const canvas = document.getElementById(`${type}Chart`);
            if (!canvas) {
                debug(`Canvas not found for ${type}`);
                return;
            }

            const ctx = canvas.getContext('2d');
            if (!ctx) {
                debug(`Could not get context for ${type}`);
                return;
            }

            charts[type] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: '@lang("translation.Usage")',
                            data: [],
                            borderColor: chartColors.usage,
                            borderWidth: 2,
                            tension: 0.4,
                            fill: false
                        },
                        {
                            label: '@lang("translation.Limit")',
                            data: [],
                            borderColor: chartColors.limit,
                            borderWidth: 1,
                            borderDash: [5, 5],
                            fill: false
                        },
                        {
                            label: '@lang("translation.Average")',
                            data: [],
                            borderColor: chartColors.average,
                            borderWidth: 1,
                            borderDash: [3, 3],
                            fill: false
                        }
                    ]
                },
                options: createChartOptions(type)
            });
        });

        loadChartData();
    } catch (error) {
        debug('Error initializing charts:', error);
    }
}

function createChartOptions(type) {
    if(!type) return {};

    const titles = {
        hits: '@lang("translation.Daily_Hits")',
        bandwidth: '@lang("translation.Bandwidth_Usage")',
        inodes: '@lang("translation.Inodes_Usage")',
        diskspace: '@lang("translation.Disk_Space_Usage")'
    };

    const units = {
        hits: '@lang("translation.Hits")',
        bandwidth: 'MB',
        inodes: '@lang("translation.Files")',
        diskspace: 'MB'
    };

    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: titles[type] || ''
            },
            tooltip: {
                mode: 'index',
                intersect: false
            }
        },
        scales: {
            x: {
                display: true,
                title: {
                    display: true,
                    text: '@lang("translation.Date")'
                }
            },
            y: {
                display: true,
                title: {
                    display: true,
                    text: units[type] || ''
                },
                suggestedMin: 0
            }
        }
    };
}

function loadChartData() {
    const accountUsername = document.getElementById('accountUsername').value;

    fetch("{{ route('hosting.chart.stats', ':username') }}".replace(':username', accountUsername))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Object.keys(charts).forEach(type => {
                    if (data.data[type]) {
                        updateChart(charts[type], data.data[type]);
                    }
                });
            }
        })
        .catch(error => {
            debug('Error loading chart data:', error);
        });
}

function updateChart(chart, data) {
    if (!data?.history) return;

    const dates = data.history.map(item => item.date);
    const values = data.history.map(item => item.value);
    const movingAverage = calculateMovingAverage(values);

    chart.data.labels = dates;
    chart.data.datasets[0].data = values;
    chart.data.datasets[1].data = Array(dates.length).fill(data.limit);
    chart.data.datasets[2].data = movingAverage;

    chart.update();
}

function calculateMovingAverage(values, window = 30) {
    return values.map((_, index) => {
        if (index < window - 1) return null;
        
        const sum = values.slice(index - window + 1, index + 1)
            .reduce((a, b) => a + b, 0);
        return Number((sum / window).toFixed(2));
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Setup DOM event listeners
    const domainSelect = document.getElementById('subdomain-domain');
    if (domainSelect) {
        domainSelect.addEventListener('change', updateDomainDisplay);
    }

    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    initCopyableElements();

    // Check account status and verification
    if ('{{ $account->status }}' === 'active') {
        const isCpanelVerified = "{{ $account->cpanel_verified }}" === "1";
        
        if (isCpanelVerified) {
            // Load stats and charts if verified
            loadAccountStats();
            initializeCharts();
            
            if (typeof loadDatabases === 'function') loadDatabases();
            if (typeof loadSubdomains === 'function') loadSubdomains();
        } else {
            // Show verification banner if not verified
            const banner = document.getElementById('verificationBanner');
            if (banner && !isVerifying) {
                setTimeout(() => banner.classList.add('show'), 500);
            }

            // Setup login button
            const loginBtn = document.getElementById('loginNowBtn');
            if (loginBtn) {
                loginBtn.addEventListener('click', e => {
                    e.preventDefault();
                    handleCpanelLogin();
                });
            }

            // Check for pending verification
            const hasVerifyFlag = localStorage.getItem(CPANEL_VERIFY_KEY);
            if (hasVerifyFlag && !isCpanelVerified) {
                isVerifying = true;
                startVerificationCheck();
            }
        }
    }
});

// Cleanup
window.addEventListener('beforeunload', () => {
    if (verificationCheckTimer) {
        clearInterval(verificationCheckTimer);
    }
});
</script>
@endsection