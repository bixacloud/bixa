@extends('layouts.master')

@section('title') Site Settings @endsection

@section('css')
<!-- Froala Editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css" rel="stylesheet">
<!-- Font Awesome for Froala icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    /* Custom styling for Froala editor */
    .fr-box {
        border-radius: 0.25rem;
    }
    
    .fr-toolbar {
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }
    
    .fr-wrapper {
        border-bottom-left-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }
    
    /* Make toolbar scrollable on small screens */
    @media (max-width: 767.98px) {
        .fr-toolbar {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 8px;
        }
    }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Settings @endslot
@slot('title') Site Settings @endslot
@endcomponent

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">General Settings</h4>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Site Title -->
                    <div class="mb-3 row">
                        <label for="site_title" class="col-md-2 col-form-label">Site Title</label>
                        <div class="col-md-10">
                            <input type="text" 
                                   class="form-control" 
                                   id="site_title" 
                                   name="site_title" 
                                   value="{{ old('site_title', $settings['site_title']) }}" 
                                   required>
                            <small class="text-muted">
                                This will appear in the browser tab and various places throughout the site.
                            </small>
                        </div>
                    </div>

                    <!-- Footer Text -->
                    <div class="mb-3 row">
                        <label for="footer_text" class="col-md-2 col-form-label">Footer Text</label>
                        <div class="col-md-10">
                            <textarea class="form-control" 
                                      id="footer_text" 
                                      name="footer_text">{{ old('footer_text', $settings['footer_text']) }}</textarea>
                            <small class="text-muted">
                                Copyright text or any other information to display in the footer.
                            </small>
                        </div>
                    </div>
                    
                    <!-- Affiliate Settings -->
                    <h5 class="mt-4 mb-3">Affiliate Settings</h5>
                    
                    <div class="mb-3 row">
                        <label for="affiliate_id" class="col-md-2 col-form-label">Affiliate ID</label>
                        <div class="col-md-10">
                            <input type="text" 
                                   class="form-control" 
                                   id="affiliate_id" 
                                   name="affiliate_id" 
                                   value="{{ old('affiliate_id', $settings['affiliate_id'] ?? '') }}">
                            <small class="text-muted">
                                Your affiliate ID for the hosting partner. This will be used in all upgrade links to track your referrals.
                            </small>
                        </div>
                    </div>
                   
   
<!-- PageSpeed Insights Settings -->
<h5 class="mt-4 mb-3">PageSpeed Insights Settings</h5>
                    
<div class="mb-3 row">
    <label for="pagespeed_api_key" class="col-md-2 col-form-label">Google API Key</label>
    <div class="col-md-10">
        <input type="text" 
               class="form-control" 
               id="pagespeed_api_key" 
               name="pagespeed_api_key" 
               value="{{ old('pagespeed_api_key', $settings['pagespeed_api_key'] ?? '') }}">
        <small class="text-muted">
            Your Google API key for PageSpeed Insights. <a href="https://developers.google.com/speed/docs/insights/v5/get-started" target="_blank">Learn how to get one</a>.
        </small>
    </div>
</div>

<div class="mb-3 row">
    <label class="col-md-2 col-form-label">Enable PageSpeed</label>
    <div class="col-md-10">
        <div class="form-check form-switch">
            <input class="form-check-input" 
                   type="checkbox" 
                   id="enable_pagespeed" 
                   name="enable_pagespeed" 
                   value="1" 
                   {{ old('enable_pagespeed', $settings['enable_pagespeed'] ?? '') == '1' ? 'checked' : '' }}>
            <label class="form-check-label" for="enable_pagespeed">Enable Google PageSpeed Insights testing</label>
        </div>
        <small class="text-muted">
            Enable PageSpeed Insights integration in the website speed test tool.
        </small>
    </div>
</div>

<div class="mb-3 row">
    <label for="pagespeed_default_strategy" class="col-md-2 col-form-label">Default Strategy</label>
    <div class="col-md-10">
        <select class="form-select" id="pagespeed_default_strategy" name="pagespeed_default_strategy">
            <option value="mobile" {{ old('pagespeed_default_strategy', $settings['pagespeed_default_strategy'] ?? '') == 'mobile' ? 'selected' : '' }}>Mobile</option>
            <option value="desktop" {{ old('pagespeed_default_strategy', $settings['pagespeed_default_strategy'] ?? '') == 'desktop' ? 'selected' : '' }}>Desktop</option>
        </select>
        <small class="text-muted">
            Default device strategy to use for PageSpeed Insights testing.
        </small>
    </div>
</div> 
                    <!-- Imgur API Settings -->
                    <h5 class="mt-4 mb-3">Image Upload Settings</h5>
                    
                    <div class="mb-3 row">
                        <label for="imgur_client_id" class="col-md-2 col-form-label">Imgur Client ID</label>
                        <div class="col-md-10">
                            <input type="text" 
                                   class="form-control" 
                                   id="imgur_client_id" 
                                   name="imgur_client_id" 
                                   value="{{ old('imgur_client_id', $settings['imgur_client_id'] ?? '') }}">
                            <small class="text-muted">
                                Enter your Imgur API Client ID. <a href="https://api.imgur.com/oauth2/addclient" target="_blank">Register here</a> if you don't have one.
                            </small>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="imgur_client_secret" class="col-md-2 col-form-label">Imgur Client Secret</label>
                        <div class="col-md-10">
                            <input type="password" 
                                   class="form-control" 
                                   id="imgur_client_secret" 
                                   name="imgur_client_secret" 
                                   value="{{ old('imgur_client_secret', $settings['imgur_client_secret'] ?? '') }}">
                            <small class="text-muted">
                                Your Imgur API Client Secret.
                            </small>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label class="col-md-2 col-form-label">Image Upload</label>
                        <div class="col-md-10">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="enable_image_upload" 
                                       name="enable_image_upload" 
                                       value="1" 
                                       {{ old('enable_image_upload', $settings['enable_image_upload'] ?? '') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="enable_image_upload">Enable screenshot pasting & automatic image uploads</label>
                            </div>
                            <small class="text-muted">
                                Allow users to paste screenshots directly into ticket forms. Images will be automatically uploaded to Imgur.
                            </small>
                        </div>
                    </div>

                    

                    <div class="row mt-4">
                        <div class="col-md-10 offset-md-2">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="icon-sm me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- Froala Editor JS -->
<script src="https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Froala Editor for footer text with license key
    const editor = new FroalaEditor('#footer_text', {
        // License key
        key: "1C%kZPBZ`JWSDBCQ@ZGD\\@\\JXDAAOZWJh,/.!==",
        
        // Basic configuration
        height: 200,
        placeholderText: 'Enter footer text here...',
        attribution: false, // Disable Froala branding with license key
        
        // Simplified toolbar for footer editor - similar to TinyMCE setup
        toolbarButtons: [
            'undo', 'redo', '|',
            'bold', 'italic', 'underline', '|',
            'formatOL', 'formatUL', '|',
            'align', '|',
            'link', 'html'
        ],
        
        // Keep it simple and clean
        iframe: false,
        charCounterCount: false,
        
        // Options to match TinyMCE behavior
        linkAlwaysBlank: true,
        linkAlwaysNoFollow: false,
        pastePlain: true
    });
    
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection