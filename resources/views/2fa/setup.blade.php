@extends('layouts.master')

@section('title') @lang('translation.Two_Factor_Authentication_Setup') @endsection

@section('css')
<!-- QR code library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<style>
    .secret-key-container {
        position: relative;
        margin: 15px 0;
        background-color: #f8f9fa;
        border-radius: 4px;
        border: 1px solid #dee2e6;
        padding: 10px 40px 10px 12px;
        font-family: monospace;
        font-size: 1.1em;
        word-break: break-all;
    }
    .copy-btn {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        cursor: pointer;
        color: #6c757d;
        padding: 4px;
        transition: color 0.2s;
    }
    .copy-btn:hover {
        color: #0d6efd;
    }
    .qr-container {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        display: inline-block;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        margin-top: 10px;
        margin-bottom: 20px;
    }
    .authenticator-app {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        padding: 5px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }
    .authenticator-app:hover {
        background-color: #e9ecef;
    }
    .authenticator-app i {
        margin-right: 10px;
        color: #495057;
    }
    .steps-container {
        border-left: 3px solid #0d6efd;
        padding-left: 15px;
    }
    .step {
        margin-bottom: 25px;
    }
    .step-title {
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }
    .step-title i {
        margin-right: 8px;
    }
    .step-number {
        background-color: #0d6efd;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        margin-right: 10px;
        font-size: 14px;
        font-weight: bold;
    }
    .copied-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        background-color: #198754;
        color: white;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1050;
        display: none;
    }
    .option-label {
        font-weight: 500;
        margin-bottom: 10px;
        color: #495057;
    }
    .qr-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 15px 0;
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.Account') @endslot
        @slot('title') @lang('translation.Two_Factor_Authentication_Setup') @endslot
    @endcomponent

    <div class="copied-alert" id="copiedAlert">@lang('translation.Secret_key_copied_to_clipboard')</div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <h4>@lang('translation.Set_up_Two_Factor_Authentication')</h4>
                    <p>@lang('translation.Secure_your_account_with_2FA')</p>
                    
                    <div class="row mt-4">
                        <div class="col-lg-8">
                            <div class="steps-container">
                                <div class="step">
                                    <div class="step-title">
                                        <span class="step-number">1</span>
                                        @lang('translation.Download_an_Authenticator_App')
                                    </div>
                                    <div class="ps-4">
                                        <p class="text-muted mb-2">@lang('translation.Choose_authenticator_app')</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="authenticator-app">
                                                    <i data-feather="smartphone"></i>
                                                    <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">
                                                        @lang('translation.Google_Authenticator_Android')
                                                    </a>
                                                </div>
                                                <div class="authenticator-app">
                                                    <i data-feather="smartphone"></i>
                                                    <a href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_blank">
                                                        @lang('translation.Google_Authenticator_iPhone')
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="authenticator-app">
                                                    <i data-feather="shield"></i>
                                                    <a href="https://www.microsoft.com/en-us/security/mobile-authenticator-app" target="_blank">
                                                        @lang('translation.Microsoft_Authenticator')
                                                    </a>
                                                </div>
                                                <div class="authenticator-app">
                                                    <i data-feather="key"></i>
                                                    <a href="https://authy.com/download/" target="_blank">
                                                        @lang('translation.Authy_Multiplatform')
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="step">
                                    <div class="step-title">
                                        <span class="step-number">2</span>
                                        @lang('translation.Set_Up_Your_Account')
                                    </div>
                                    <div class="ps-4">
                                        <p class="text-muted mb-3">@lang('translation.Two_options_to_setup')</p>
                                        
                                        <div class="option-label">
                                            <strong>@lang('translation.Option_A'):</strong> @lang('translation.Scan_QR_code')
                                        </div>
                                        
                                        <div class="qr-section">
                                            <div class="qr-container">
                                                <canvas id="qrcode" width="200" height="200"></canvas>
                                            </div>
                                            <div class="text-muted">
                                                <i data-feather="info" class="icon-xs me-1"></i>
                                                @lang('translation.Point_camera_at_QR')
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3 mb-2 option-label">
                                            <strong>@lang('translation.Option_B'):</strong> @lang('translation.Enter_secret_key')
                                        </div>
                                        
                                        <div class="secret-key-container" id="secretKeyContainer">
                                            {{ $secret }}
                                            <button class="copy-btn" id="copyBtn" title="@lang('translation.Copy_to_clipboard')">
                                                <i data-feather="copy"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">@lang('translation.Key_shown_once')</small>
                                    </div>
                                </div>
                                
                                <div class="step">
                                    <div class="step-title">
                                        <span class="step-number">3</span>
                                        @lang('translation.Verify_Setup')
                                    </div>
                                    <div class="ps-4">
                                        <p class="text-muted">@lang('translation.Enter_verification_code')</p>
                                        
                                        <form action="{{ route('2fa.enable') }}" method="POST" class="mt-3">
                                            @csrf
                                            <input type="hidden" name="secret" value="{{ $secret }}">
                                            
                                            <div class="mb-3">
                                                <label class="form-label">@lang('translation.Verification_Code')</label>
                                                <input type="text" name="one_time_password" class="form-control" 
                                                    placeholder="@lang('translation.Enter_6_digit_code')" required
                                                    autocomplete="off" inputmode="numeric" pattern="[0-9]*" 
                                                    minlength="6" maxlength="6">
                                                <!-- Removed autofocus attribute to prevent auto-scrolling -->
                                            </div>
                                            
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary">
                                                    <i data-feather="shield" class="icon-xs me-1"></i> @lang('translation.Enable_Two_Factor_Authentication')
                                                </button>
                                                <a href="{{ route('profile') }}" class="btn btn-light">
                                                    @lang('translation.Cancel')
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="alert alert-warning">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i data-feather="alert-triangle" style="width: 24px; height: 24px;"></i>
                                    </div>
                                    <div>
                                        <h5 class="alert-heading">@lang('translation.Important_Notes')</h5>
                                        <ul class="ps-3 mb-0">
                                            <li>@lang('translation.Need_code_each_signin')</li>
                                            <li>@lang('translation.May_get_locked_out')</li>
                                            <li>@lang('translation.Save_secret_key_secure')</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-4">
                                <div class="card-body">
                                    <h5 class="card-title">@lang('translation.Why_use_2FA')</h5>
                                    <p class="card-text">@lang('translation.2FA_explanation')</p>
                                    <p class="card-text text-muted">@lang('translation.2FA_protection')</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure page scrolls to top on load
        window.scrollTo(0, 0);
        
        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Create QR code
        var qrCodeUrl = "otpauth://totp/{{ urlencode('BiXA') }}:{{ urlencode(Auth::user()->name) }}?secret={{ $secret }}&issuer={{ urlencode('BiXA') }}";
        
        var qr = new QRious({
            element: document.getElementById('qrcode'),
            value: qrCodeUrl,
            size: 200,
            level: 'H' // High error correction
        });
        
        // Handle copy secret key
        const copyBtn = document.getElementById('copyBtn');
        const secretKeyContainer = document.getElementById('secretKeyContainer');
        const copiedAlert = document.getElementById('copiedAlert');
        
        copyBtn.addEventListener('click', function() {
            const secretKey = '{{ $secret }}';
            
            // Create a temporary textarea for copying
            const textarea = document.createElement('textarea');
            textarea.value = secretKey;
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'absolute';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            
            // Select text and copy
            textarea.select();
            document.execCommand('copy');
            
            // Remove textarea
            document.body.removeChild(textarea);
            
            // Display notification
            copiedAlert.style.display = 'block';
            setTimeout(function() {
                copiedAlert.style.display = 'none';
            }, 2000);
        });
    });
</script>
@endsection