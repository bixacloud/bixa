@extends('layouts.master')

@section('title') @lang('translation.Account_Settings') @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.Hosting') @endslot
        @slot('title') @lang('translation.Account_Settings') @endslot
    @endcomponent

    {{-- Alert Messages --}}
    @if(session('success'))
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

    <div class="row">
        {{-- Left Column --}}
        <div class="col-xl-6">
            {{-- General Settings --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="card-title mb-0">@lang('translation.General')</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('hosting.settings.update', $account->id) }}" method="POST" id="update-label-form" class="{{ \App\Models\IconCaptchaSetting::isEnabled('enabled', false) ? 'needs-captcha' : '' }}">
                        @csrf
                        <!-- Important: Hidden action field -->
                        <input type="hidden" name="action" value="update_label">
                        <div class="mb-3">
                            <label class="form-label">@lang('translation.Account') @lang('translation.Label')</label>
                            <input type="text" name="label" class="form-control" 
                                   value="{{ $account->label }}"
                                   placeholder="@lang('translation.Enter_account_label')">
                        </div>
                        
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="bx bx-save me-1"></i> @lang('translation.Update') @lang('translation.Label')
                        </button>
                    </form>
                </div>
            </div>

            {{-- Security Settings --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">@lang('translation.Security')</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('hosting.settings.update', $account->id) }}" method="POST" id="update-password-form" class="{{ \App\Models\IconCaptchaSetting::isEnabled('enabled', false) ? 'needs-captcha' : '' }}">
                        @csrf
                        <!-- Important: Hidden action field -->
                        <input type="hidden" name="action" value="update_password">
                        <div class="mb-3">
                            <label class="form-label">@lang('translation.New_Password')</label>
                            <input type="password" name="password" class="form-control" 
                                   placeholder="@lang('translation.Enter_new_password')">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('translation.Current_Password')</label>
                            <input type="password" name="old_password" class="form-control" 
                                   placeholder="@lang('translation.Enter_current_password')">
                        </div>
                        
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="bx bx-lock me-1"></i> @lang('translation.Change_Password')
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-xl-6">
            {{-- Deactivation Card --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="card-title mb-0">@lang('translation.Account_Deactivation')</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('hosting.settings.update', $account->id) }}" method="POST" id="deactivation-form" class="{{ \App\Models\IconCaptchaSetting::isEnabled('enabled', false) ? 'needs-captcha' : '' }}">
                        @csrf
                        <!-- Important: Hidden action field -->
                        <input type="hidden" name="action" value="deactivate">
                        <div class="alert alert-danger mb-3">
                            <div class="d-flex">
                                <i class="bx bx-error-circle me-2"></i>
                                <div>
                                    <strong>@lang('translation.Warning'):</strong> @lang('translation.Account_deactivation_warning')
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('translation.Reason')</label>
                            <textarea name="reason" class="form-control" rows="4"
                                      placeholder="@lang('translation.Deactivation_reason_placeholder')"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-danger waves-effect waves-light">
                            <i class="bx bx-trash me-1"></i> @lang('translation.Deactivate') @lang('translation.Account')
                        </button>
                    </form>
                </div>
            </div>
            
            {{-- Separate CAPTCHA Card - Only show if CAPTCHA is enabled --}}
            @if(\App\Models\IconCaptchaSetting::isEnabled('enabled', false))
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">@lang('translation.Verification') @lang('translation.Required')</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">@lang('translation.Complete_verification_for_actions')</p>
                    
                    <div id="captcha-container">
                        <!-- IconCaptcha Verification -->
                        <div class="mb-3">
                            <div class="iconcaptcha-widget" data-theme="{{ $theme ?? 'light' }}"></div>
                            
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
                            @lang('translation.Please_complete_verification_before_submitting')
                        </div>
                    </div>
                </div>
            </div>
            @endif
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

    // Only initialize CAPTCHA if enabled
    if (captchaEnabled) {
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
                    statusEl.innerHTML = '<i class="bx bx-check-circle me-1"></i> @lang("translation.Verification_successful_submit_forms")';
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
    
    // Apply captcha data to a form (only if CAPTCHA is enabled)
    function applyCaptchaDataToForm(form) {
        if (!captchaEnabled) return;
        
        // Add token
        const formToken = form.querySelector('input[name="_iconcaptcha-token"]');
        if (!formToken) {
            const tokenEl = document.querySelector('#captcha-data input[name="_iconcaptcha-token"]');
            if (tokenEl) {
                const tokenClone = document.createElement('input');
                tokenClone.type = 'hidden';
                tokenClone.name = '_iconcaptcha-token';
                tokenClone.value = tokenEl.value;
                form.appendChild(tokenClone);
            }
        }
        
        // Add challenge ID (even if it's just a placeholder)
        const formCid = form.querySelector('input[name="ic-cid"]');
        if (!formCid) {
            const cidClone = document.createElement('input');
            cidClone.type = 'hidden';
            cidClone.name = 'ic-cid';
            cidClone.value = document.getElementById('captcha-challenge-id').value;
            form.appendChild(cidClone);
        } else {
            formCid.value = document.getElementById('captcha-challenge-id').value;
        }
        
        // Add widget ID
        const formWid = form.querySelector('input[name="ic-wid"]');
        if (!formWid) {
            const widClone = document.createElement('input');
            widClone.type = 'hidden';
            widClone.name = 'ic-wid';
            widClone.value = document.getElementById('captcha-widget-id').value;
            form.appendChild(widClone);
        } else {
            formWid.value = document.getElementById('captcha-widget-id').value;
        }
        
        // Add hp flag
        const formHp = form.querySelector('input[name="ic-hp"]');
        if (!formHp) {
            const hpClone = document.createElement('input');
            hpClone.type = 'hidden';
            hpClone.name = 'ic-hp';
            hpClone.value = '1';
            form.appendChild(hpClone);
        } else {
            formHp.value = '1';
        }
    }

    // Handle form submissions
    document.querySelectorAll('form.needs-captcha').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (captchaEnabled && !window.captchaVerified) {
                e.preventDefault();
                alert('@lang("translation.Complete_CAPTCHA_first")');
                document.querySelector('#captcha-container').scrollIntoView({
                    behavior: 'smooth'
                });
                return false;
            }
            
            // Add all captcha data to the form (only if CAPTCHA is enabled)
            if (captchaEnabled) {
                applyCaptchaDataToForm(this);
                console.log('Form submitted with captcha data', {
                    'token': this.querySelector('input[name="_iconcaptcha-token"]')?.value,
                    'cid': this.querySelector('input[name="ic-cid"]')?.value,
                    'wid': this.querySelector('input[name="ic-wid"]')?.value,
                    'hp': this.querySelector('input[name="ic-hp"]')?.value
                });
            }
        });
    });

    // Add loading state to form submission buttons
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            if (!btn) return;
            
            const originalText = btn.innerHTML;
            const loadingIcon = '<span class="spinner-border spinner-border-sm me-1"></span>';
            
            // Get action from form hidden field
            const actionField = this.querySelector('input[name="action"]');
            const actionValue = actionField ? actionField.value : '';
            
            const actionText = actionValue === 'deactivate' ? '@lang("translation.Deactivating")...' : 
                               actionValue === 'update_password' ? '@lang("translation.Changing")...' : '@lang("translation.Updating")...';
            
            btn.innerHTML = loadingIcon + ' ' + actionText;
            btn.disabled = true;
            
            // Store original text to restore if form validation fails
            btn.setAttribute('data-original-text', originalText);
            
            // Check for form validation errors (if any)
            setTimeout(function() {
                if (document.querySelector('.alert-danger')) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            }, 500);
        });
    });
});
</script>
@endsection