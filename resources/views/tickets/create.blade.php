@extends('layouts.master')

@section('title') @lang('translation.Create_Ticket') @endsection

@section('css')
<!-- Froala Editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css" rel="stylesheet">
<!-- Font Awesome for Froala icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<!-- CodeMirror CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/theme/monokai.min.css">
<!-- Froala Code View Plugin CSS -->
<link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/plugins/code_view.min.css" rel="stylesheet">

<!-- Custom CSS -->
<style>
    /* Editor container styling */
    .editor-container {
        position: relative;
        margin-bottom: 20px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    
    /* Froala editor styling */
    #froala-editor {
        height: 300px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 14px;
    }
    
    /* Upload progress indicator */
    #upload-progress {
        display: none;
        margin-top: 10px;
    }
    
    #upload-progress.show {
        display: block;
    }
    
    #upload-status {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    /* Toast message */
    .toast-message {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 12px 20px;
        background-color: #28a745;
        color: #fff;
        border-radius: 4px;
        z-index: 9999;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: none;
    }
    
    .toast-message.error {
        background-color: #dc3545;
    }
    
    .toast-message.show {
        display: block;
        animation: fadeInOut 3s ease-in-out;
    }
    
    @keyframes fadeInOut {
        0% { opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { opacity: 0; }
    }
    
    /* Responsive YouTube Embeds */
    .responsive-embed-container {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
        max-width: 100%;
        margin: 15px 0;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .responsive-embed-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
        border-radius: 8px;
    }
    
    /* Knowledge Base Suggestions Styles */
    .knowledge-suggestions {
        transition: all 0.3s ease;
    }

    .knowledge-suggestions .card-header {
        padding: 0.75rem 1rem;
    }

    .kb-article-item {
        padding: 0.75rem;
        border-bottom: 1px solid rgba(0,0,0,.1);
        transition: background-color 0.2s;
    }
    
    .kb-article-item:last-child {
        border-bottom: none;
    }
    
    .kb-article-item:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .kb-article-item a {
        color: var(--bs-primary);
    }

    .kb-article-item h6 {
        margin-bottom: 0.25rem;
        color: var(--bs-primary);
    }
    
    .kb-category {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .kb-excerpt {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }
    
    .knowledge-suggestions .card {
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
    }

    /* Animation for suggestions appearance */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .knowledge-suggestions:not(.d-none) {
        animation: fadeIn 0.3s ease-out;
    }
    
    /* Make toolbar scrollable on small screens */
    @media (max-width: 767.98px) {
        .fr-toolbar {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 8px;
        }
    }
    
    /* Mobile optimizations */
    @media (max-width: 767.98px) {
        #froala-editor {
            height: 250px;
            font-size: 13px;
        }
    }
    
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
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.Tickets') @endslot
        @slot('title') @lang('translation.Create_Ticket') @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">@lang('translation.Create_New_Support_Ticket')</h4>

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show">
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('user.tickets.store') }}" method="POST" class="{{ \App\Models\IconCaptchaSetting::isEnabled('enabled', false) ? 'needs-captcha' : '' }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">@lang('translation.Title')</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           name="title" value="{{ old('title', $prefilled['title'] ?? '') }}" required>
                                    @error('title')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">@lang('translation.Category')</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            name="category_id" required>
                                        <option value="">@lang('translation.Select_Category')</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">@lang('translation.Priority')</label>
                                    <select class="form-select @error('priority') is-invalid @enderror" 
                                            name="priority" required>
                                        <option value="low" {{ old('priority', $prefilled['priority'] ?? '') == 'low' ? 'selected' : '' }}>@lang('translation.Low')</option>
                                        <option value="medium" {{ old('priority', $prefilled['priority'] ?? '') == 'medium' ? 'selected' : '' }}>@lang('translation.Medium')</option>
                                        <option value="high" {{ old('priority', $prefilled['priority'] ?? '') == 'high' ? 'selected' : '' }}>@lang('translation.High')</option>
                                    </select>
                                    @error('priority')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">@lang('translation.Related_Service')</label>
                                    <select id="service_type" name="service_type" 
                                            class="form-select @error('service_type') is-invalid @enderror" required>
                                        <option value="">@lang('translation.Select_Service_Type')</option>
                                        <option value="hosting" {{ old('service_type', $prefilled['service_type'] ?? '') == 'hosting' ? 'selected' : '' }}>@lang('translation.Hosting')</option>
                                        <option value="ssl" {{ old('service_type', $prefilled['service_type'] ?? '') == 'ssl' ? 'selected' : '' }}>@lang('translation.SSL_Certificate')</option>
                                    </select>
                                    @error('service_type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3" id="hosting_services" style="display: none;">
                                    <label class="form-label">@lang('translation.Select_Hosting_Account')</label>
                                    <select name="hosting_id" class="form-select @error('hosting_id') is-invalid @enderror">
                                        <option value="">@lang('translation.Select_Hosting_Account')</option>
                                        @foreach($hostingAccounts as $account)
                                            <option value="{{ $account->id }}" 
                                                {{ old('hosting_id', $prefilled['hosting_id'] ?? '') == $account->id ? 'selected' : '' }}
                                                class="{{ $account->admin_deactivated ? 'text-danger' : '' }}">
                                                {{ $account->domain }} ({{ $account->username }})
                                                @if($account->status != 'active')
                                                    - {{ ucfirst($account->status) }}
                                                @endif
                                                @if($account->admin_deactivated)
                                                    - @lang('translation.Admin_Suspended')
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('hosting_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted mt-1 d-block">
                                        <i class="bx bx-info-circle me-1"></i> @lang('translation.Suspended_Account_Ticket_Info')
                                    </small>
                                </div>

                                <div class="mb-3" id="ssl_services" style="display: none;">
                                    <label class="form-label">@lang('translation.Select_SSL_Certificate')</label>
                                    <select name="certificate_id" class="form-select @error('certificate_id') is-invalid @enderror">
                                        <option value="">@lang('translation.Select_SSL_Certificate')</option>
                                        @foreach($certificates as $cert)
                                            <option value="{{ $cert->id }}" {{ old('certificate_id', $prefilled['certificate_id'] ?? '') == $cert->id ? 'selected' : '' }}>
                                                {{ $cert->domain }} ({{ ucfirst($cert->status) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('certificate_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">@lang('translation.Message')</label>
                                    
                                    <!-- Editor Container -->
                                    <div class="editor-container">
                                        <textarea id="froala-editor" name="message">{{ old('message', $prefilled['message'] ?? '') }}</textarea>
                                    </div>
                                    
                                    <!-- Upload progress indicator -->
                                    <div id="upload-progress" class="progress mt-2">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <div id="upload-status" class="mt-1 small"></div>
                                    
                                    @error('message')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if(\App\Models\IconCaptchaSetting::isEnabled('enabled', false))
                        <!-- Verification Required Section -->
                        <div class="card border mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">@lang('translation.Verification_Required')</h5>
                            </div>
                            <div class="card-body" id="captcha-container">
                                <p class="text-muted mb-3">@lang('translation.Complete_verification_for_ticket')</p>
                                
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
                                    @lang('translation.Please_complete_verification_for_ticket')
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i data-feather="send" class="me-1"></i> @lang('translation.Submit_Ticket')
                            </button>
                            <a href="{{ route('user.tickets.index') }}" class="btn btn-secondary">@lang('translation.Cancel')</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast Message -->
    <div id="toast-message" class="toast-message"></div>
    
    <!-- Knowledge Base suggestions container will be dynamically inserted after the title input -->
@endsection

@section('script')
<!-- Froala Editor JS -->
<script src="https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js"></script>
<!-- CodeMirror JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/mode/htmlmixed/htmlmixed.min.js"></script>
<!-- Froala Code View Plugin -->
<script src="https://cdn.jsdelivr.net/npm/froala-editor@latest/js/plugins/code_view.min.js"></script>
<!-- JS Beautify for HTML formatting -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.15.1/beautify-html.min.js"></script>

<!-- CDN for IconCaptcha if enabled -->
@if(\App\Models\IconCaptchaSetting::isEnabled('enabled', false))
<script src="{{ asset('vendor/iconcaptcha/js/iconcaptcha.min.js') }}"></script>
@endif

<script>
// Khởi tạo tất cả các chuỗi đã được dịch
const appTranslations = {
    // Thông báo và hướng dẫn
    imageUploadDisabled: '@lang("translation.Image_upload_disabled")',
    uploadingImage: '@lang("translation.Uploading_image")',
    imageUploadedSuccessfully: '@lang("translation.Image_uploaded_successfully")',
    failedToUploadImage: '@lang("translation.Failed_to_upload_image")',
    errorUploadingImage: '@lang("translation.Error_uploading_image")',
    insertYouTubeVideo: '@lang("translation.Insert_YouTube_Video")',
    youtubeEmbeddedSuccessfully: '@lang("translation.YouTube_embedded_successfully")',
    invalidYouTubeURL: '@lang("translation.Invalid_YouTube_URL")',
    describeYourIssueHere: '@lang("translation.Describe_your_issue_here")',
    
    // KB Search
    relatedKnowledgeBaseArticles: '@lang("translation.Related_Knowledge_Base_Articles")',
    loading: '@lang("translation.Loading")',
    searchingKnowledgeBase: '@lang("translation.Searching_knowledge_base")',
    suggestedCategories: '@lang("translation.Suggested_Categories")',
    articles: '@lang("translation.articles")',
    noKBArticlesFoundPrefix: '@lang("translation.No_results_found_for")',
    continueCreatingTicket: '@lang("translation.Continue_creating_ticket")',
    errorSearchingKnowledgeBase: '@lang("translation.Error_searching_knowledge_base")',
    tryAgainLater: '@lang("translation.Try_again_later")',
    
    // CAPTCHA
    completeCaptchaFirst: '@lang("translation.Complete_CAPTCHA_first")',
    creating: '@lang("translation.Creating")',
    verifyHuman: '@lang("translation.Verify_human")',
    loadingChallenge: '@lang("translation.Loading_challenge")',
    selectLeastImage: '@lang("translation.Select_least_image")',
    verificationSuccessful: '@lang("translation.Verification_successful")',
    incorrectSelection: '@lang("translation.Incorrect_selection")',
    wrongImageSelected: '@lang("translation.Wrong_image_selected")',
    pleaseWait: '@lang("translation.Please_Wait")',
    tooManyAttempts: '@lang("translation.Too_many_attempts")',
    verificationSuccessfulCreateTicket: '@lang("translation.Verification_successful_create_ticket")'
};

document.addEventListener('DOMContentLoaded', function() {
    // Initialize elements
    const uploadProgress = document.getElementById('upload-progress');
    const progressBar = uploadProgress.querySelector('.progress-bar');
    const uploadStatus = document.getElementById('upload-status');
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Global config for various functionalities
    const config = {
        imageUpload: {
            enabled: {{ \App\Models\Setting::get('enable_image_upload', '0') === '1' ? 'true' : 'false' }},
            endpoint: '/admin/upload-image'
        },
        captcha: {
            enabled: {{ \App\Models\IconCaptchaSetting::isEnabled('enabled', false) ? 'true' : 'false' }},
            endpoint: '{{ route("iconcaptcha.request") }}',
            theme: '{{ \App\Models\IconCaptchaSetting::get("theme", "light") }}'
        },
        knowledgeBase: {
            endpoint: '{{ route("knowledge.ajax-search") }}'
        }
    };
    
    // Function to parse YouTube URL
    function parseYoutubeUrl(url) {
        if (!url) return false;
        
        // Support various YouTube URL formats
        const patterns = [
            /(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([^&]+)/i,
            /(?:https?:\/\/)?(?:www\.)?youtube\.com\/embed\/([^/?]+)/i,
            /(?:https?:\/\/)?(?:www\.)?youtube\.com\/v\/([^/?]+)/i,
            /(?:https?:\/\/)?(?:www\.)?youtu\.be\/([^/?]+)/i
        ];
        
        for (const pattern of patterns) {
            const match = url.match(pattern);
            if (match && match[1]) {
                return match[1];
            }
        }
        
        return false;
    }
    
    // Function to create YouTube embed HTML
    function createYoutubeEmbed(videoId) {
        return `<div class="responsive-embed-container">
            <iframe 
                width="560" 
                height="315" 
                src="https://www.youtube-nocookie.com/embed/${videoId}?rel=0&showinfo=0&modestbranding=1&playsinline=1&fs=1" 
                title="YouTube video player" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen
                loading="lazy">
            </iframe>
        </div><p><br></p>`;
    }
    
    // Initialize Froala Editor with license key
    const editor = new FroalaEditor('#froala-editor', {
        // License key
        key: "1C%kZPBZ`JWSDBCQ@ZGD\\@\\JXDAAOZWJh,/.!==",
        
        // Basic configuration
        height: 300,
        placeholderText: appTranslations.describeYourIssueHere,
        charCounterCount: true,
        attribution: false, // Disable Froala branding with license key
        
        // CodeMirror configuration
        codeMirror: window.CodeMirror,
        codeMirrorOptions: {
            lineNumbers: true,
            lineWrapping: true,
            mode: 'text/html',
            theme: 'monokai',
            tabSize: 2,
            indentWithTabs: false
        },
        
        // Simplified toolbar with only basic formatting, YouTube, HTML mode and image upload
        toolbarButtons: [
            'bold', 'italic', 'underline', '|',
            'paragraphFormat', 'formatOL', 'formatUL', '|',
            'insertImage', 'insertVideo', '|',  
            'html'
        ],
        
        // Disable default image upload to use our custom handler
        imageUpload: false,
        
        // Events
        events: {
            'initialized': function() {
                // Set up custom image upload handler
                const that = this;
                
                // Create a function to upload images
                function uploadImage(file, editor) {
                    // Check if image upload is enabled
                    if (!config.imageUpload.enabled) {
                        alert(appTranslations.imageUploadDisabled);
                        return;
                    }
                    
                    // Show upload progress
                    uploadProgress.style.display = 'block';
                    progressBar.style.width = '30%';
                    uploadStatus.innerHTML = appTranslations.uploadingImage + '...';
                    
                    const formData = new FormData();
                    formData.append('image', file);
                    formData.append('_token', csrfToken);
                    
                    fetch(config.imageUpload.endpoint, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => response.json())
                    .then(data => {
                        progressBar.style.width = '100%';
                        
                        if (data.success && data.data && data.data.url) {
                            // Success! Insert the image
                            editor.image.insert(data.data.url, null, null, editor.image.get());
                            
                            uploadStatus.innerHTML = '<span class="text-success"><i class="bx bx-check-circle me-1"></i> ' + appTranslations.imageUploadedSuccessfully + '</span>';
                            
                            // Hide progress bar after a delay
                            setTimeout(() => {
                                uploadProgress.style.display = 'none';
                                progressBar.style.width = '0%';
                                uploadStatus.innerHTML = '';
                            }, 3000);
                        } else {
                            // Error from server
                            uploadStatus.innerHTML = `<span class="text-danger"><i class="bx bx-error-circle me-1"></i> ${data.error || appTranslations.failedToUploadImage}</span>`;
                            
                            // Hide progress bar after a delay
                            setTimeout(() => {
                                uploadProgress.style.display = 'none';
                                progressBar.style.width = '0%';
                            }, 5000);
                        }
                    })
                    .catch(error => {
                        console.error('Image upload error:', error);
                        uploadStatus.innerHTML = '<span class="text-danger"><i class="bx bx-error-circle me-1"></i> ' + appTranslations.errorUploadingImage + '</span>';
                        
                        // Hide progress bar after a delay
                        setTimeout(() => {
                            uploadProgress.style.display = 'none';
                            progressBar.style.width = '0%';
                        }, 5000);
                    });
                }
                
                // Override the default image insertion method
                this.image.upload = function() {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/jpeg,image/png,image/gif,image/webp');
                    input.click();
                    
                    input.onchange = function() {
                        if (input.files && input.files[0]) {
                            uploadImage(input.files[0], that);
                        }
                    };
                };
                
                // Custom YouTube button handler
                this.registerCommand('insertVideo', {
                    title: appTranslations.insertYouTubeVideo,
                    focus: true,
                    undo: true,
                    refreshAfterCallback: true,
                    callback: function () {
                        const videoUrl = prompt(appTranslations.insertYouTubeVideo + ':');
                        if (videoUrl) {
                            const videoId = parseYoutubeUrl(videoUrl);
                            if (videoId) {
                                const embedHtml = createYoutubeEmbed(videoId);
                                this.html.insert(embedHtml);
                                showToast(appTranslations.youtubeEmbeddedSuccessfully);
                            } else {
                                showToast(appTranslations.invalidYouTubeURL, 'error');
                            }
                        }
                    }
                });
                
                // Enable paste functionality for images
                this.$el.on('paste', function(e) {
                    if (e.originalEvent && e.originalEvent.clipboardData && e.originalEvent.clipboardData.items) {
                        const items = e.originalEvent.clipboardData.items;
                        
                        for (let i = 0; i < items.length; i++) {
                            if (items[i].type.indexOf('image') !== -1) {
                                e.preventDefault();
                                const blob = items[i].getAsFile();
                                if (blob) {
                                    uploadImage(blob, that);
                                }
                                break;
                            }
                        }
                        
                        // Check for YouTube URLs in text
                        const clipboardText = e.originalEvent.clipboardData.getData('text');
                        if (clipboardText && (clipboardText.includes('youtube.com/watch') || clipboardText.includes('youtu.be/'))) {
                            const videoId = parseYoutubeUrl(clipboardText);
                            if (videoId) {
                                e.preventDefault();
                                const embedHtml = createYoutubeEmbed(videoId);
                                that.html.insert(embedHtml);
                                showToast(appTranslations.youtubeEmbeddedSuccessfully);
                            }
                        }
                    }
                });
            },
            
            // Handle image insertion after upload
            'image.inserted': function($img) {
                // Ensure images are responsive
                $img.addClass('img-fluid');
            }
        }
    });
    
    // Show toast message
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast-message');
        toast.textContent = message;
        
        if (type === 'error') {
            toast.classList.add('error');
        } else {
            toast.classList.remove('error');
        }
        
        toast.classList.add('show');
        
        // Auto hide after 3 seconds
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
    
    // Knowledge Base Suggestions
    const titleInput = document.querySelector('input[name="title"]');
    
    // Create a container for knowledge base suggestions
    const suggestionsContainer = document.createElement('div');
    suggestionsContainer.className = 'knowledge-suggestions mt-2 d-none';
    suggestionsContainer.innerHTML = `
        <div class="card border">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <h6 class="mb-0">${appTranslations.relatedKnowledgeBaseArticles}</h6>
                <button type="button" class="btn-close btn-sm" aria-label="Close"></button>
            </div>
            <div class="card-body p-0">
                <div class="kb-results p-3">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">${appTranslations.loading}</span>
                    </div>
                    <span class="ms-2">${appTranslations.searchingKnowledgeBase}</span>
                </div>
            </div>
        </div>
    `;
    
    // Insert after title input
    titleInput.parentNode.appendChild(suggestionsContainer);
    
    // Get results container and close button
    const resultsContainer = suggestionsContainer.querySelector('.kb-results');
    const closeButton = suggestionsContainer.querySelector('.btn-close');
    
    // Close button handler
    closeButton.addEventListener('click', function() {
        suggestionsContainer.classList.add('d-none');
    });
    
    // Debounce function to limit API calls
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                func.apply(context, args);
            }, wait);
        };
    }
    
    // Function to fetch knowledge base articles
    const fetchKnowledgeArticles = debounce(function(query) {
        if (query.length < 3) {
            suggestionsContainer.classList.add('d-none');
            return;
        }
        
        // Show suggestions container with loading state
        suggestionsContainer.classList.remove('d-none');
        resultsContainer.innerHTML = `
            <div class="d-flex align-items-center p-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">${appTranslations.loading}</span>
                </div>
                <span class="ms-2">${appTranslations.searchingKnowledgeBase}</span>
            </div>
        `;
        
        // Fetch from API
        fetch(config.knowledgeBase.endpoint + '?query=' + encodeURIComponent(query), {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && (data.results.articles.length > 0 || data.results.categories.length > 0)) {
                let html = '';
                
                // Add articles
                if (data.results.articles.length > 0) {
                    data.results.articles.forEach(article => {
                        html += `
                            <div class="kb-article-item">
                                <a href="${article.url}" target="_blank" class="text-decoration-none">
                                    <h6 class="mb-1">${article.title}</h6>
                                </a>
                                <div class="kb-category">
                                    <a href="${article.category.url}" class="text-decoration-none">
                                        <i class="bx bx-folder-open me-1"></i>${article.category.name}
                                    </a>
                                </div>
                                <div class="kb-excerpt">${article.excerpt}</div>
                            </div>
                        `;
                    });
                }
                
                // Add categories if no articles match
                if (data.results.articles.length === 0 && data.results.categories.length > 0) {
                    html += `<h6 class="mb-3 mt-2 px-3">${appTranslations.suggestedCategories}:</h6>`;
                    
                    data.results.categories.forEach(category => {
                        html += `
                            <div class="kb-article-item">
                                <a href="${category.url}" target="_blank" class="text-decoration-none">
                                    <h6 class="mb-1">${category.name} (${category.article_count} ${appTranslations.articles})</h6>
                                </a>
                                <div class="kb-excerpt">${category.description}</div>
                            </div>
                        `;
                    });
                }
                
                resultsContainer.innerHTML = html;
            } else {
                // No results
                resultsContainer.innerHTML = `
                    <div class="p-3 text-center">
                        <i class="bx bx-info-circle fs-3 text-muted"></i>
                        <p class="mb-0 mt-2">${appTranslations.noKBArticlesFoundPrefix} "${query}"</p>
                        <small class="text-muted">${appTranslations.continueCreatingTicket}</small>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error searching knowledge base:', error);
            resultsContainer.innerHTML = `
                <div class="p-3 text-center text-danger">
                    <i class="bx bx-error-circle fs-3"></i>
                    <p class="mb-0 mt-2">${appTranslations.errorSearchingKnowledgeBase}</p>
                    <small>${appTranslations.tryAgainLater}</small>
                </div>
            `;
        });
    }, 500); // 500ms debounce
    
    // Listen for title input changes
    titleInput.addEventListener('input', function() {
        const query = this.value.trim();
        fetchKnowledgeArticles(query);
    });
    
    // Add integration with form completion
    // If user clicks on an article, open it in a new tab
    document.addEventListener('click', function(e) {
        if (e.target.closest('.kb-article-item a')) {
            const articleUrl = e.target.closest('.kb-article-item a').getAttribute('href');
            window.open(articleUrl, '_blank');
        }
    });
    
    // Handle service type selection
    const serviceType = document.getElementById('service_type');
    const hostingServices = document.getElementById('hosting_services');
    const sslServices = document.getElementById('ssl_services');
    
    function updateServiceVisibility() {
        if (serviceType.value === 'hosting') {
            hostingServices.style.display = 'block';
            sslServices.style.display = 'none';
        } else if (serviceType.value === 'ssl') {
            hostingServices.style.display = 'none';
            sslServices.style.display = 'block';
        } else {
            hostingServices.style.display = 'none';
            sslServices.style.display = 'none';
        }
    }
    
    // Set initial visibility
    updateServiceVisibility();
    
    // Update on change
    serviceType.addEventListener('change', updateServiceVisibility);
    
    // CAPTCHA Setup
    const captchaEnabled = {{ \App\Models\IconCaptchaSetting::isEnabled('enabled', false) ? 'true' : 'false' }};
    
    // Global marker for captcha verification status (true by default when captcha is disabled)
    window.captchaVerified = !captchaEnabled;
    
    // Handle form submission to ensure content is saved
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Check CAPTCHA if needed
            if (this.classList.contains('needs-captcha') && !window.captchaVerified) {
                e.preventDefault();
                alert(appTranslations.completeCaptchaFirst);
                document.querySelector('#captcha-container').scrollIntoView({
                    behavior: 'smooth'
                });
                return false;
            }
            
            // Add loading state to button
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const icon = '<span class="spinner-border spinner-border-sm me-1"></span>';
                submitBtn.innerHTML = icon + ' ' + appTranslations.creating + '...';
                submitBtn.disabled = true;
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
                        endpoint: config.captcha.endpoint,
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
                            verify: appTranslations.verifyHuman,
                            loading: appTranslations.loadingChallenge + '...',
                        },
                        header: appTranslations.selectLeastImage,
                        correct: appTranslations.verificationSuccessful,
                        incorrect: {
                            title: appTranslations.incorrectSelection,
                            subtitle: appTranslations.wrongImageSelected,
                        },
                        timeout: {
                            title: appTranslations.pleaseWait,
                            subtitle: appTranslations.tooManyAttempts
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
                    statusEl.innerHTML = '<i class="bx bx-check-circle me-1"></i> ' + appTranslations.verificationSuccessfulCreateTicket;
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
    
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection