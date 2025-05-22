@extends('layouts.master')

@section('title') Manage Ticket @endsection

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
    
    /* Style card body images */
    .card-body img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        margin: 5px 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    /* Message highlighting */
    .message-highlighted {
        animation: highlight-message 2s ease-in-out;
    }
    
    @keyframes highlight-message {
        0%, 100% { 
            box-shadow: none; 
        }
        50% { 
            box-shadow: 0 0 20px rgba(255, 193, 7, 0.7); 
        }
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
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Admin @endslot
        @slot('li_2') Tickets @endslot
        @slot('title') Manage Ticket @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">Ticket #{{ $ticket->id }}</h4>
                        <div>
                            <form action="{{ route('admin.tickets.status.update', $ticket) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm d-inline-block w-auto me-2" onchange="this.form.submit()">
                                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="answered" {{ $ticket->status === 'answered' ? 'selected' : '' }}>Answered</option>
                                    <option value="customer-reply" {{ $ticket->status === 'customer-reply' ? 'selected' : '' }}>Customer Reply</option>
                                    <option value="pending" {{ $ticket->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </form>
                            <span class="badge bg-{{ 
                                $ticket->status === 'open' ? 'success' : 
                                ($ticket->status === 'answered' ? 'info' : 
                                ($ticket->status === 'customer-reply' ? 'warning' : 
                                ($ticket->status === 'pending' ? 'secondary' : 'dark'))) 
                            }} ms-2">
                                {{ $ticket->status === 'answered' ? 'Answered by Staff' : 
                                   ($ticket->status === 'customer-reply' ? 'Customer Reply' : ucfirst($ticket->status)) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <i data-feather="user"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="font-size-14 mb-1">User</h5>
                                    <p class="text-muted mb-0">
                                        {{ $ticket->user->name }}
                                        @if($ticket->user->role === 'admin')
                                            <span class="badge bg-danger ms-2">Admin</span>
                                        @elseif($ticket->user->role === 'support')
                                            <span class="badge bg-success ms-2">Support</span>
                                        @else
                                            <span class="badge bg-info ms-2">User</span>
                                        @endif
                                    </p>
                                    <small class="text-muted">{{ $ticket->user->email }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <i data-feather="bookmark"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="font-size-14 mb-1">Title</h5>
                                    <p class="text-muted mb-0">{{ $ticket->title }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex mb-3">
                                <i data-feather="link" class="me-2"></i>
                                <div>
                                    <h6 class="mb-1">Related Service</h6>
                                    @if($ticket->service_type === 'hosting')
                                        @php
                                            $hosting = DB::table('hosting_accounts')
                                                ->where('id', $ticket->service_id)
                                                ->first();
                                        @endphp
                                        @if($hosting)
                                            <p class="text-muted mb-0">
                                                Hosting: {{ $hosting->domain }}
                                                @if($hosting->admin_deactivated)
                                                    <span class="badge bg-danger ms-2">Suspended by Admin</span>
                                                @else
                                                    <span class="badge bg-info ms-2">{{ $hosting->status }}</span>
                                                @endif
                                            </p>
                                            
                                            @if($hosting->admin_deactivated)
                                                @if($hosting->admin_deactivation_reason)
                                                    <div class="mt-1">
                                                        <small class="text-danger">
                                                            <i class="bx bx-info-circle me-1"></i> Suspension reason: {{ $hosting->admin_deactivation_reason }}
                                                        </small>
                                                    </div>
                                                @endif
                                                
                                                @if($hosting->admin_deactivated_at)
                                                    <div class="mt-1">
                                                        <small class="text-muted">
                                                            <i class="bx bx-calendar me-1"></i> Suspended on: {{ date('Y-m-d H:i', strtotime($hosting->admin_deactivated_at)) }}
                                                        </small>
                                                    </div>
                                                @endif
                                                
                                                @if($hosting->status === 'deactivated')
                                                    <div class="mt-2">
                                                        <form action="{{ route('admin.hosting.settings.update', $hosting->username) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to reactivate this account?');">
                                                            @csrf
                                                            <input type="hidden" name="action" value="admin_reactivate">
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="bx bx-refresh me-1"></i> Reactivate Account
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endif
                                        @endif
                                    @elseif($ticket->service_type === 'ssl')
                                        @php
                                            $certificate = DB::table('certificates')
                                                ->where('id', $ticket->service_id)
                                                ->first();
                                        @endphp
                                        @if($certificate)
                                            <p class="text-muted mb-0">
                                                SSL: {{ $certificate->domain }}
                                                <span class="badge bg-info ms-2">{{ $certificate->status }}</span>
                                            </p>
                                        @endif
                                    @else
                                        <p class="text-muted mb-0">No related service</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <i data-feather="flag"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="font-size-14 mb-1">Priority</h5>
                                    <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning' : 'info') }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5 class="font-size-16 mb-3">Messages</h5>

                        <div class="messages-list">
                            @foreach($ticket->messages as $message)
                                <div class="card mb-3" id="message-{{ $message->id }}">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                @php
                                                    $isAdmin = $message->user && $message->user->role == 'admin';
                                                    $isSupport = $message->user && $message->user->role == 'support';
                                                    $isStaff = $isAdmin || $isSupport;
                                                @endphp
                                                <h6 class="mb-1 {{ $isAdmin ? 'text-danger fw-bold' : ($isSupport ? 'text-success fw-bold' : '') }}">
                                                    {{ $message->user->name }}
                                                    @if($isAdmin)
                                                        <span class="badge bg-danger ms-2">Admin</span>
                                                    @elseif($isSupport)
                                                        <span class="badge bg-success ms-2">Support</span>
                                                    @else
                                                        <span class="badge bg-info ms-2">User</span>
                                                    @endif
                                                </h6>
                                                <small class="text-muted">{{ $message->created_at->format('d M Y, h:i A') }}</small>
                                            </div>
                                            <div>
                                                <a href="#message-{{ $message->id }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bx bx-link"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        {!! $message->message !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($ticket->status !== 'closed')
                            <div class="mt-4">
                                <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">
                                            {{ auth()->user()->role == 'admin' ? 'Admin' : 'Support' }} Reply
                                        </label>
                                        
                                        <!-- Editor Container -->
                                        <div class="editor-container">
                                            <textarea id="froala-editor" name="message">{{ old('message') }}</textarea>
                                        </div>
                                        
                                        <!-- Upload progress indicator -->
                                        <div id="upload-progress" class="progress mt-2">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <div id="upload-status" class="mt-1 small"></div>
                                        
                                        @error('message')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <button type="submit" class="btn btn-{{ auth()->user()->role == 'admin' ? 'danger' : 'success' }}">
                                        <i data-feather="send" class="me-1"></i> Send Reply
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast Message -->
    <div id="toast-message" class="toast-message"></div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize elements
    const uploadProgress = document.getElementById('upload-progress');
    const progressBar = uploadProgress.querySelector('.progress-bar');
    const uploadStatus = document.getElementById('upload-status');
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Highlight message if URL has a message ID hash
    highlightTargetMessage();
    
    // Add event listener for future hash changes
    window.addEventListener('hashchange', highlightTargetMessage);
    
    // Fix existing YouTube embeds
    makeYouTubeVideosResponsive();
    
    function highlightTargetMessage() {
        // Remove any existing highlights
        document.querySelectorAll('.message-highlighted').forEach(el => {
            el.classList.remove('message-highlighted');
        });
        
        // Check if there's a hash in the URL
        if (window.location.hash) {
            const targetId = window.location.hash.substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                // Add highlight class
                targetElement.classList.add('message-highlighted');
                
                // Scroll into view with a slight delay for better UX
                setTimeout(() => {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 100);
            }
        }
    }
    
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
    
    // Function to make existing YouTube embeds responsive
    function makeYouTubeVideosResponsive() {
        // Find all YouTube iframes not already in responsive containers
        const youtubeIframes = document.querySelectorAll('.card-body iframe[src*="youtube.com/embed"], .card-body iframe[src*="youtu.be"], .card-body iframe[src*="youtube-nocookie.com/embed"]');
        
        youtubeIframes.forEach(function(iframe) {
            // Skip if already in proper container
            if (iframe.parentNode.className === 'responsive-embed-container' || 
                iframe.parentNode.className === 'youtube-embed-container') {
                return;
            }
            
            // Create new container
            const container = document.createElement('div');
            container.className = 'responsive-embed-container';
            
            // Get parent of iframe
            const parent = iframe.parentNode;
            
            // Replace iframe with container
            parent.replaceChild(container, iframe);
            
            // Add iframe to container
            container.appendChild(iframe);
            
            // Ensure iframe has proper attributes
            if (!iframe.getAttribute('loading')) {
                iframe.setAttribute('loading', 'lazy');
            }
            
            if (!iframe.getAttribute('allowfullscreen')) {
                iframe.setAttribute('allowfullscreen', '');
            }
            
            // Update URL to use youtube-nocookie.com if not already
            let src = iframe.getAttribute('src');
            if (src && src.includes('youtube.com/embed') && !src.includes('youtube-nocookie.com')) {
                src = src.replace('youtube.com/embed', 'youtube-nocookie.com/embed');
                
                // Add mobile-friendly parameters
                if (!src.includes('playsinline')) {
                    src = src.includes('?') ? 
                          src + '&playsinline=1&fs=1' : 
                          src + '?playsinline=1&fs=1';
                }
                
                iframe.setAttribute('src', src);
            }
        });
    }
    
    // Initialize Froala Editor with license key
    const editor = new FroalaEditor('#froala-editor', {
        // License key
        key: "1C%kZPBZ`JWSDBCQ@ZGD\\@\\JXDAAOZWJh,/.!==",
        
        // Basic configuration
        height: 300,
        placeholderText: 'Write your reply here...',
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
                    // Show upload progress
                    uploadProgress.style.display = 'block';
                    progressBar.style.width = '30%';
                    uploadStatus.innerHTML = 'Uploading image...';
                    
                    const formData = new FormData();
                    formData.append('image', file);
                    formData.append('_token', csrfToken);
                    
                    fetch('/admin/upload-image', {
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
                            
                            uploadStatus.innerHTML = '<span class="text-success"><i class="bx bx-check-circle me-1"></i> Image uploaded successfully!</span>';
                            
                            // Hide progress bar after a delay
                            setTimeout(() => {
                                uploadProgress.style.display = 'none';
                                progressBar.style.width = '0%';
                                uploadStatus.innerHTML = '';
                            }, 3000);
                        } else {
                            // Error from server
                            uploadStatus.innerHTML = `<span class="text-danger"><i class="bx bx-error-circle me-1"></i> ${data.error || 'Failed to upload image'}</span>`;
                            
                            // Hide progress bar after a delay
                            setTimeout(() => {
                                uploadProgress.style.display = 'none';
                                progressBar.style.width = '0%';
                            }, 5000);
                        }
                    })
                    .catch(error => {
                        console.error('Image upload error:', error);
                        uploadStatus.innerHTML = '<span class="text-danger"><i class="bx bx-error-circle me-1"></i> Error uploading image. Please try again.</span>';
                        
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
                    title: 'Insert YouTube Video',
                    focus: true,
                    undo: true,
                    refreshAfterCallback: true,
                    callback: function () {
                        const videoUrl = prompt('Enter YouTube URL:');
                        if (videoUrl) {
                            const videoId = parseYoutubeUrl(videoUrl);
                            if (videoId) {
                                const embedHtml = createYoutubeEmbed(videoId);
                                this.html.insert(embedHtml);
                                showToast('YouTube video embedded successfully!');
                            } else {
                                showToast('Invalid YouTube URL', 'error');
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
                                showToast('YouTube video embedded successfully!');
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
    
    // Handle form submission to ensure content is saved
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            // Add loading state to button
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const icon = '<span class="spinner-border spinner-border-sm me-1"></span>';
                submitBtn.innerHTML = icon + ' Sending...';
                submitBtn.disabled = true;
            }
        });
    }
    
    // Mobile-specific adjustments for better usability
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (isMobile) {
        // Make toolbar scrollable on mobile
        const toolbar = document.querySelector('.fr-toolbar');
        if (toolbar) {
            toolbar.style.overflowX = 'auto';
            toolbar.style.flexWrap = 'nowrap';
            toolbar.style.whiteSpace = 'nowrap';
        }
    }
    
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection