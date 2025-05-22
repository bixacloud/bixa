@extends('layouts.master')

@section('title', 'IconCaptcha Settings')

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Admin @endslot
        @slot('title') IconCaptcha Settings @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">IconCaptcha Configuration</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('admin.captcha.update') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Enable Captcha</label>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               name="enabled" 
                                               id="captcha-enabled" 
                                               {{ isset($enabled) && $enabled ? 'checked' : '' }}>
                                        <label class="form-check-label" for="captcha-enabled">
                                            {{ isset($enabled) && $enabled ? 'Captcha is currently enabled' : 'Captcha is currently disabled' }}
                                        </label>
                                    </div>
                                    <div class="text-muted small">
                                        When enabled, the captcha will be displayed on forms that use it.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="captcha-theme" class="form-label">Captcha Theme</label>
                                    <select class="form-select" 
                                           name="theme" 
                                           id="captcha-theme">
                                        <option value="light" {{ (isset($theme) && $theme === 'light') ? 'selected' : '' }}>Light</option>
                                        <option value="dark" {{ (isset($theme) && $theme === 'dark') ? 'selected' : '' }}>Dark</option>
                                    </select>
                                    <div class="text-muted small">
                                        Choose the visual theme for the captcha widget.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <i class="bx bx-info-circle text-info me-2"></i>
                                <div>
                                    <strong>How It Works:</strong>
                                    <p class="mb-0">
                                        IconCaptcha is a user-friendly captcha that asks users to select the image appearing the least number of times. 
                                        It's an effective solution for preventing automated form submissions while maintaining a good user experience.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Captcha Section -->
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Test IconCaptcha</h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p>
                                    <strong>Current status:</strong> 
                                    @if(isset($enabled) && $enabled)
                                        <span class="badge bg-success">Enabled</span>
                                    @else
                                        <span class="badge bg-danger">Disabled</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <strong>Current theme:</strong> 
                                    <span class="badge bg-info">{{ isset($theme) ? $theme : 'light' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    @if(isset($enabled) && $enabled)
                        <div class="alert alert-info mb-4">
                            <div class="d-flex">
                                <i class="bx bx-info-circle text-info me-2"></i>
                                <div>
                                    <strong>Test Instructions:</strong>
                                    <p class="mb-0">
                                        Complete the captcha verification below and submit the form to test the captcha functionality.
                                        If successful, you'll see a success message.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <form action="{{ route('admin.captcha.validate-test') }}" method="POST" id="testForm">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label">IconCaptcha Test</label>
                                
                                <!-- IconCaptcha widget -->
                                <div class="iconcaptcha-widget" data-theme="{{ isset($theme) ? $theme : 'light' }}" 
                                     ></div>
                                
                                <!-- Security token -->
                                @php echo \IconCaptcha\Token\IconCaptchaToken::render(); @endphp
                            </div>
                            
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bx bx-check me-1"></i> Submit Test
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            <div class="d-flex">
                                <i class="bx bx-error-circle text-warning me-2"></i>
                                <div>
                                    <strong>Captcha is disabled!</strong>
                                    <p class="mb-0">
                                        Enable the captcha in the settings above to test its functionality.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
    /* Style cho debug */
    .iconcaptcha-widget {
        position: relative;
    }
</style>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle label text when checkbox is clicked
    document.getElementById('captcha-enabled')?.addEventListener('change', function() {
        const label = this.nextElementSibling;
        if (this.checked) {
            label.textContent = 'Captcha is currently enabled';
        } else {
            label.textContent = 'Captcha is currently disabled';
        }
    });
    
    // Form submission handling
    document.getElementById('testForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const icon = btn.querySelector('i');
        
        // Lưu nội dung ban đầu
        const originalContent = btn.innerHTML;
        
        // Thay đổi nội dung button
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Verifying...';
        btn.disabled = true;
        
        // Thêm xử lý để reset button sau 5 giây nếu form không submit được
        setTimeout(function() {
            if (btn.disabled) {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }
        }, 5000);
    });
    
   
    
    // Initialize IconCaptcha if enabled
    @if(isset($enabled) && $enabled)
    try {
        console.log('Initializing IconCaptcha...');
        
        setTimeout(function() {
            IconCaptcha.init('.iconcaptcha-widget', {
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
        verify: 'Verify that you are human.',
        loading: 'Loading...',
    },
    header: 'Select the image that appears <u>at least</u> once.',
    correct: 'Verification successful.',
    incorrect: {
        title: 'Sorry.',
        subtitle: "You have selected the wrong image.",
    },
    timeout: {
        title: 'Please wait.',
        subtitle: 'You have selected incorrectly too many times.'
    }
}

            });
            console.log('IconCaptcha initialized successfully');
        }, 500);
    } catch (e) {
        console.error('Error initializing IconCaptcha:', e);
    }
    @endif
});
</script>
@endsection