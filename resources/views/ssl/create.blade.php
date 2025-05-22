@extends('layouts.master')

@section('title') @lang('translation.Create_SSL_Certificate') @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.SSL') @endslot
        @slot('title') @lang('translation.Create_Certificate') @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">@lang('translation.Create_New_SSL_Certificate')</h4>

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('ssl.store') }}" method="POST" class="{{ \App\Models\IconCaptchaSetting::isEnabled('enabled', false) ? 'needs-captcha' : '' }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">@lang('translation.Certificate_Type')</label>
                                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="">@lang('translation.Select_Type')</option>
                                        <option value="letsencrypt" {{ old('type') == 'letsencrypt' ? 'selected' : '' }}>Let's Encrypt</option>
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">@lang('translation.Domain_Name')</label>
                                    <input type="text" name="domain" 
                                        class="form-control @error('domain') is-invalid @enderror"
                                        value="{{ old('domain') }}"
                                        placeholder="example.com" required>
                                    <div class="form-text text-muted">@lang('translation.Enter_domain_without_http')</div>
                                    @error('domain')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-12">
                                <div class="alert alert-info">
                                    <h5 class="alert-heading">@lang('translation.Important_Notes'):</h5>
                                    <ul class="mb-0 ps-3">
                                        <li>@lang('translation.SSL_Note_1')</li>
                                        <li>@lang('translation.SSL_Note_2')</li>
                                        <li>@lang('translation.SSL_Note_3')</li>
                                        <li>@lang('translation.SSL_Note_4')</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        @if(\App\Models\IconCaptchaSetting::isEnabled('enabled', false))
                        <div class="row mb-4">
                            <div class="col-lg-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">@lang('translation.Verification_Required')</h5>
                                    </div>
                                    <div class="card-body" id="captcha-container">
                                        <p class="text-muted mb-3">@lang('translation.Complete_verification_for_ssl')</p>
                                        
                                        <!-- IconCaptcha Verification -->
                                        <div class="mb-3">
                                            <div class="iconcaptcha-widget" data-theme="{{ \App\Models\IconCaptchaSetting::get('theme', 'light') }}"></div>
                                            
                                            <!-- Security token -->
                                            @php echo \IconCaptcha\Token\IconCaptchaToken::render(); @endphp
                                            
                                            <!-- Hidden fields to store captcha verification data -->
                                            <div id="captcha-data">
                                                <input type="hidden" id="captcha-challenge-id" name="ic-cid" value="placeholder">
                                                <input type="hidden" id="captcha-widget-id" name="ic-wid" value="">
                                                <input type="hidden" id="captcha-solved" name="ic-hp" value="0">
                                            </div>
                                        </div>
                                        <!-- End IconCaptcha -->
                                        
                                        <div id="verification-status" class="alert alert-warning">
                                            @lang('translation.Please_complete_verification_for_ssl')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-lock me-1"></i> @lang('translation.Create_Certificate')
                            </button>
                            <a href="{{ route('ssl.index') }}" class="btn btn-secondary ms-2">@lang('translation.Cancel')</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
@if(\App\Models\IconCaptchaSetting::isEnabled('enabled', false))
<style>
    .iconcaptcha-widget {
        margin-bottom: 15px;
        border: 1px solid #eee;
        min-height: 90px;
    }
    #verification-status.success {
        background-color: #d1e7dd;
        color: #0f5132;
        border-color: #badbcc;
    }
</style>
@endif
@endsection

@section('script')
<script>
    // Sanitize domain input
    document.querySelector('input[name="domain"]').addEventListener('input', function() {
        this.value = this.value.toLowerCase()
            .replace(/^https?:\/\//i, '')
            .replace(/^www\./i, '')
            .replace(/[^a-z0-9.-]/g, '');
    });

    // CAPTCHA Setup
    document.addEventListener('DOMContentLoaded', function() {
        // Check if captcha is enabled
        const captchaEnabled = {{ \App\Models\IconCaptchaSetting::isEnabled('enabled', false) ? 'true' : 'false' }};
        
        // Global marker for captcha verification status (true by default when captcha is disabled)
        window.captchaVerified = !captchaEnabled;

        // Handle form submission for captcha verification
        const form = document.querySelector('form.needs-captcha');
        if (form && captchaEnabled) {
            form.addEventListener('submit', function(e) {
                if (!window.captchaVerified) {
                    e.preventDefault();
                    alert('@lang("translation.Complete_CAPTCHA_first")');
                    document.querySelector('#captcha-container').scrollIntoView({
                        behavior: 'smooth'
                    });
                    return false;
                }
            });
        }

        // Handle captcha verification if enabled
        if (captchaEnabled && document.getElementById('captcha-container')) {
            // Reference elements
            const statusEl = document.getElementById('verification-status');
            const captchaSolvedInput = document.getElementById('captcha-solved');
            const challengeIdField = document.getElementById('captcha-challenge-id');
            const widgetIdField = document.getElementById('captcha-widget-id');
            
            // Initialize IconCaptcha
            if (typeof IconCaptcha !== 'undefined') {
                try {
                    let captchaInstance = IconCaptcha.init('.iconcaptcha-widget', {
                        general: {
                            endpoint: '{{ route('iconcaptcha.request') }}',
                            fontFamily: 'inherit',
                            showCredits: false,
                        },
                        security: {
                            interactionDelay: 500,
                            hoverProtection: true,
                            displayInitialMessage: true,
                            initializationDelay: 300,
                            incorrectSelectionResetDelay: 1000,
                            loadingAnimationDuration: 500,
                        },
                        locale: {
                            initialization: {
                                verify: '@lang("translation.Verify_human")',
                                loading: '@lang("translation.Loading_challenge")...',
                            },
                            header: '@lang("translation.Select_least_image")',
                            correct: '@lang("translation.Verification_successful").',
                            incorrect: {
                                title: '@lang("translation.Incorrect_selection")',
                                subtitle: '@lang("translation.Wrong_image_selected")',
                            },
                            timeout: {
                                title: '@lang("translation.Please_Wait")',
                                subtitle: '@lang("translation.Too_many_attempts")'
                            }
                        }
                    });
                    
                    console.log('IconCaptcha initialized successfully');
                    
                    // Get widget ID during initialization
                    captchaInstance.bind('init', function(e) {
                        console.log('Captcha initialized with widget ID:', e.detail.captchaId);
                        widgetIdField.value = e.detail.captchaId;
                    });
                    
                    // Success event - mark verification as complete
                    captchaInstance.bind('success', function(e) {
                        console.log('Captcha verified successfully!');
                        
                        // Mark as verified
                        window.captchaVerified = true;
                        captchaSolvedInput.value = '1';
                        
                        // Update UI
                        statusEl.innerHTML = '<i class="bx bx-check-circle me-1"></i> @lang("translation.Verification_successful_ssl")';
                        statusEl.classList.remove('alert-warning');
                        statusEl.classList.add('alert-success', 'success');
                        
                        // Try to get challenge ID
                        setTimeout(function() {
                            const widget = document.querySelector('.iconcaptcha-widget');
                            if (widget) {
                                const cid = widget.getAttribute('data-challenge-id');
                                if (cid) {
                                    challengeIdField.value = cid;
                                    console.log('Found challenge ID:', cid);
                                }
                            }
                        }, 300);
                    });
                    
                } catch (e) {
                    console.error('Error initializing IconCaptcha:', e);
                }
            }
        }

        // Prevent double submission
        document.querySelector('form').addEventListener('submit', function(e) {
            // Check if captcha is enabled and not verified
            const captchaEnabled = {{ \App\Models\IconCaptchaSetting::isEnabled('enabled', false) ? 'true' : 'false' }};
            if (captchaEnabled && !window.captchaVerified) {
                e.preventDefault();
                alert('@lang("translation.Complete_CAPTCHA_first")');
                document.querySelector('#captcha-container').scrollIntoView({
                    behavior: 'smooth'
                });
                return false;
            }
            
            const btn = this.querySelector('button[type="submit"]');
            if(btn.disabled) {
                e.preventDefault();
                return;
            }
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> @lang("translation.Creating")...';
        });
    });
</script>
@endsection