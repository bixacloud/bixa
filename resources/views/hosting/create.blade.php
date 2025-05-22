@extends('layouts.master')

@section('title')  {{ __('Create') . ' ' . __('Hosting_Accounts') }} @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.Hosting') @endslot
        @slot('title') {{ __('Create') . ' ' . __('Hosting_Accounts') }} @endslot
    @endcomponent

    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">@lang('translation.Create') @lang('translation.Hosting_Accounts')</h4>
                </div>

                <div class="card-body">
                    {{-- Domain Check Form --}}
                    <form action="{{ route('hosting.check-domain') }}" method="POST" id="checkDomainForm">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label">@lang('translation.Domain')</label>
                            <div class="row g-3">
                                <div class="col-lg-7">
                                    <input type="text" name="domain" class="form-control form-control-lg" 
                                        placeholder="@lang('translation.Enter_subdomain_name')" required
                                        value="{{ old('domain') }}">
                                </div>
                                <div class="col-lg-3">
                                    <select name="ext" class="form-select form-select-lg">
                                        @foreach($allowedDomains as $domain)
                                            <option value="{{ $domain }}"
                                                @selected(old('ext') == $domain)>
                                                {{ $domain }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="bx bx-search me-1"></i> @lang('translation.Check')
                                    </button>
                                </div>
                            </div>
                            @error('domain')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </form>

                    @if(Session::has('domain'))
                        <div class="account-setup mt-4">
                            <hr>
                            <form action="{{ route('hosting.store') }}" method="POST" id="createAccountForm" class="{{ \App\Models\IconCaptchaSetting::isEnabled('enabled', false) ? 'needs-captcha' : '' }}">
                                @csrf
                                <h5 class="mb-4">@lang('translation.Account_Configuration')</h5>

                                <div class="alert alert-info mb-4">
                                    @lang('translation.Domain'): <strong>{{ Session::get('domain') }}</strong>
                                    <a href="{{ route('hosting.cancel') }}" class="float-end">@lang('translation.Change')</a>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">{{ __('Account') . ' ' . __('Label') }}</label>
                                    <input type="text" name="label" class="form-control form-control-lg" 
                                        required placeholder="@lang('translation.Enter_account_label')" 
                                        value="{{ old('label') }}"
                                        autocomplete="off">
                                    @error('label')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Hidden action field -->
                                <input type="hidden" name="action" value="create_account">
                                
                                @if(\App\Models\IconCaptchaSetting::isEnabled('enabled', false))
                                <!-- Verification Required Section -->
                                <div class="card border mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">@lang('translation.Verification') @lang('translation.Required')</h5>
                                    </div>
                                    <div class="card-body" id="captcha-container">
                                        <p class="text-muted mb-3">@lang('translation.Complete_verification_to_create')</p>
                                        
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
                                            @lang('translation.Please_complete_verification_before_creating')
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="bx bx-paper-plane me-1"></i> {{ __('Create') . ' ' . __('Account') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
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
document.addEventListener('DOMContentLoaded', function() {
    // Check if captcha is enabled
    const captchaEnabled = {{ \App\Models\IconCaptchaSetting::isEnabled('enabled', false) ? 'true' : 'false' }};
    
    // Global marker for captcha verification status (true by default when captcha is disabled)
    window.captchaVerified = !captchaEnabled;

    // Add loading state to buttons
    document.getElementById('checkDomainForm')?.addEventListener('submit', function(e) {
        let btn = this.querySelector('button[type="submit"]');
        let icon = '<span class="spinner-border spinner-border-sm me-1"></span>';
        btn.innerHTML = icon + ' @lang("translation.Checking")...';
        btn.disabled = true;
    });

    // Handle captcha verification for account creation if enabled
    if (captchaEnabled && document.getElementById('createAccountForm')) {
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
                        correct: '@lang("translation.Verification_successful")',
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
                    statusEl.innerHTML = '<i class="bx bx-check-circle me-1"></i> @lang("translation.Verification_successful_create_account")';
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
    
    // Handle form submission
    document.getElementById('createAccountForm')?.addEventListener('submit', function(e) {
        if (captchaEnabled && !window.captchaVerified) {
            e.preventDefault();
            alert('@lang("translation.Complete_CAPTCHA_first")');
            document.querySelector('#captcha-container').scrollIntoView({
                behavior: 'smooth'
            });
            return false;
        }
        
        // Apply captcha data if needed
        if (captchaEnabled) {
            // Add captcha data to form
            const captchaSolvedInput = document.getElementById('captcha-solved');
            const challengeIdField = document.getElementById('captcha-challenge-id');
            const widgetIdField = document.getElementById('captcha-widget-id');
            
            const formHp = this.querySelector('input[name="ic-hp"]');
            if (!formHp) {
                const hpClone = document.createElement('input');
                hpClone.type = 'hidden';
                hpClone.name = 'ic-hp';
                hpClone.value = '1';
                this.appendChild(hpClone);
            } else {
                formHp.value = '1';
            }
            
            // Add token
            const tokenEl = document.querySelector('input[name="_iconcaptcha-token"]');
            if (tokenEl) {
                const formToken = this.querySelector('input[name="_iconcaptcha-token"]');
                if (!formToken) {
                    const tokenClone = document.createElement('input');
                    tokenClone.type = 'hidden';
                    tokenClone.name = '_iconcaptcha-token';
                    tokenClone.value = tokenEl.value;
                    this.appendChild(tokenClone);
                }
            }
        }
        
        // Add loading state
        let btn = this.querySelector('button[type="submit"]');
        let icon = '<span class="spinner-border spinner-border-sm me-1"></span>';
        btn.innerHTML = icon + ' @lang("translation.Creating")...';  
        btn.disabled = true;
    });
});
</script>
@endsection