@extends('layouts.master')

@section('title') @lang('translation.Ticket_Details') @endsection

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

    /* Rating star styles */
    .rating-stars {
        margin: 0 auto;
        max-width: 300px;
    }
    
    .rating-label {
        cursor: pointer;
        color: #ccc;
        transition: color 0.2s;
        padding: 5px;
    }
    
    .rating-label:hover,
    .rating-label:hover ~ .rating-label,
    .rating-input:checked ~ .rating-label {
        color: #ffab00;
    }
    
    .rating-input:checked + .rating-label i {
        font-weight: 900;
    }
    
    /* Stars CSS - fill selected stars */
    .rating-stars .d-flex {
        flex-direction: row-reverse;
        justify-content: center;
    }
    
    .rating-stars .form-check-inline {
        margin-right: 0.25rem;
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
        @slot('li_1') @lang('translation.Tickets') @endslot
        @slot('title') @lang('translation.Ticket_Details') @endslot
    @endcomponent

    <!-- Initialize variables to track rated staff -->
    @php
        $ratedStaffIds = App\Models\StaffRating::where('ticket_id', $ticket->id)
            ->where('user_id', auth()->id())
            ->pluck('admin_id')
            ->toArray();
            
        // Get existing ratings information
        $existingRatings = App\Models\StaffRating::where('ticket_id', $ticket->id)
            ->where('user_id', auth()->id())
            ->get()
            ->keyBy('admin_id');
    @endphp

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">@lang('translation.Ticket') #{{ $ticket->id }}</h4>
                        <div>
                            @if($ticket->status !== 'closed')
                                <form action="{{ route('user.tickets.status.update', $ticket) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('@lang('translation.Close_Ticket_Confirm')');">
                                    @csrf
                                    <input type="hidden" name="status" value="closed">
                                    <button type="submit" class="btn btn-sm btn-danger me-2">
                                        @lang('translation.Close_Ticket')
                                    </button>
                                </form>
                            @endif
                            <span class="badge bg-{{ 
                                $ticket->status === 'open' ? 'success' : 
                                ($ticket->status === 'answered' ? 'info' : 
                                ($ticket->status === 'customer-reply' ? 'warning' : 
                                ($ticket->status === 'pending' ? 'secondary' : 'dark'))) 
                            }} font-size-16">
                                {{ $ticket->status === 'answered' ? __('translation.Answered_by_Staff') : 
                                   ($ticket->status === 'customer-reply' ? __('translation.Your_Reply_Sent') : ucfirst($ticket->status)) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <i data-feather="bookmark"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="font-size-14 mb-1">@lang('translation.Title')</h5>
                                    <p class="text-muted mb-0">{{ $ticket->title }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex mb-3">
                                <i data-feather="link" class="me-2"></i>
                                <div>
                                    <h6 class="mb-1">@lang('translation.Related_Service')</h6>
                                    @if($ticket->service_type === 'hosting')
                                        @php
                                            $hosting = DB::table('hosting_accounts')
                                                ->where('id', $ticket->service_id)
                                                ->first();
                                        @endphp
                                        @if($hosting)
                                            <p class="text-muted mb-0">
                                                @lang('translation.Hosting'): {{ $hosting->domain }}
                                                @if($hosting->admin_deactivated)
                                                    <span class="badge bg-danger ms-2">@lang('translation.Suspended_by_Admin')</span>
                                                @else
                                                    <span class="badge bg-info ms-2">{{ $hosting->status }}</span>
                                                @endif
                                            </p>
                                            @if($hosting->admin_deactivated && $hosting->admin_deactivation_reason)
                                                <small class="text-danger d-block mt-1">
                                                    <i class="bx bx-info-circle me-1"></i> @lang('translation.Reason'): {{ $hosting->admin_deactivation_reason }}
                                                </small>
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
                                                @lang('translation.SSL'): {{ $certificate->domain }}
                                                <span class="badge bg-info ms-2">{{ $certificate->status }}</span>
                                            </p>
                                        @endif
                                    @else
                                        <p class="text-muted mb-0">@lang('translation.No_related_service')</p>
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
                                    <h5 class="font-size-14 mb-1">@lang('translation.Priority')</h5>
                                    <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning' : 'info') }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5 class="font-size-16 mb-3">@lang('translation.Messages')</h5>

                        <div class="messages-list">
                            @foreach($ticket->messages as $message)
                                <div class="card mb-3" id="message-{{ $message->id }}">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                @php
                                                    $isAdmin = $message->user && $message->user->isAdmin();
                                                    $isSupport = $message->user && $message->user->isSupport(); 
                                                    $isStaff = $isAdmin || $isSupport;
                                                    
                                                    // Check if this staff has been rated
                                                    $staffRated = $isStaff ? in_array($message->user->id, $ratedStaffIds) : false;
                                                    
                                                    // Get rating if exists
                                                    $rating = $staffRated ? $existingRatings[$message->user->id] : null;
                                                @endphp
                                                <h6 class="mb-1 {{ $isAdmin ? 'text-danger fw-bold' : ($isSupport ? 'text-success fw-bold' : '') }}">
                                                    {{ $message->user->name }}
                                                    @if($isAdmin)
                                                        <span class="badge bg-danger ms-2">@lang('translation.Admin')</span>
                                                        
                                                        @if($staffRated && $rating)
                                                            <!-- Show existing rating -->
                                                            <div class="d-inline-block ms-2">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <i class="bx {{ $i <= $rating->rating ? 'bxs-star' : 'bx-star' }} text-warning"></i>
                                                                @endfor
                                                            </div>
                                                        @else
                                                            <!-- Show rating button -->
                                                            <button type="button" class="btn btn-sm btn-outline-warning ms-2" 
                                                                    data-bs-toggle="modal" data-bs-target="#rateModal-{{ $message->id }}">
                                                                <i class="bx bx-star me-1"></i> @lang('translation.Rate')
                                                            </button>
                                                        @endif
                                                        
                                                    @elseif($isSupport)
                                                        <span class="badge bg-success ms-2">@lang('translation.Support')</span>
                                                        
                                                        @if($staffRated && $rating)
                                                            <!-- Show existing rating -->
                                                            <div class="d-inline-block ms-2">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <i class="bx {{ $i <= $rating->rating ? 'bxs-star' : 'bx-star' }} text-warning"></i>
                                                                @endfor
                                                            </div>
                                                        @else
                                                            <!-- Show rating button -->
                                                            <button type="button" class="btn btn-sm btn-outline-warning ms-2" 
                                                                    data-bs-toggle="modal" data-bs-target="#rateModal-{{ $message->id }}">
                                                                <i class="bx bx-star me-1"></i> @lang('translation.Rate')
                                                            </button>
                                                        @endif
                                                        
                                                    @else
                                                        <span class="badge bg-info ms-2">@lang('translation.You')</span>
                                                    @endif
                                                </h6>
                                                <small class="text-muted">{{ $message->created_at->format('d M Y, h:i A') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        {!! $message->message !!}
                                    </div>
                                </div>
                                
                                @if($isStaff && !$staffRated)
                                <!-- Rating Modal -->
                                <div class="modal fade" id="rateModal-{{ $message->id }}" tabindex="-1" 
                                     aria-labelledby="rateModalLabel-{{ $message->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="rateModalLabel-{{ $message->id }}">
                                                    @lang('translation.Rate_Staff', ['type' => $isAdmin ? __('translation.Admin') : __('translation.Support'), 'name' => $message->user->name])
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('user.tickets.rate', $message->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">@lang('translation.Your_Rating')</label>
                                                        <div class="rating-stars">
                                                            <div class="d-flex">
                                                                @for($i = 5; $i >= 1; $i--)
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input d-none rating-input" 
                                                                               type="radio" name="rating" 
                                                                               id="rating{{ $i }}-{{ $message->id }}" 
                                                                               value="{{ $i }}" required>
                                                                        <label class="form-check-label rating-label" 
                                                                               for="rating{{ $i }}-{{ $message->id }}">
                                                                            <i class="bx bx-star fs-2"></i>
                                                                        </label>
                                                                    </div>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="comment-{{ $message->id }}" class="form-label">
                                                            @lang('translation.Comment_Optional')
                                                        </label>
                                                        <textarea class="form-control" id="comment-{{ $message->id }}" 
                                                                  name="comment" rows="3"></textarea>
                                                    </div>
                                                    <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                                    <input type="hidden" name="admin_id" value="{{ $message->user->id }}">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        @lang('translation.Cancel')
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        @lang('translation.Submit_Rating')
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>

                        @if($ticket->status !== 'closed')
                            <div class="mt-4">
                                <form action="{{ route('user.tickets.reply', $ticket) }}" method="POST" class="{{ \App\Models\IconCaptchaSetting::isEnabled('enabled', false) ? 'needs-captcha' : '' }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">@lang('translation.Reply_Message')</label>
                                        
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
                                    
                                    @if(\App\Models\IconCaptchaSetting::isEnabled('enabled', false))
                                    <!-- Verification Required Section -->
                                    <div class="card border mb-4">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">@lang('translation.Verification_Required')</h5>
                                        </div>
                                        <div class="card-body" id="captcha-container">
                                            <p class="text-muted mb-3">@lang('translation.Complete_verification_for_reply')</p>
                                            
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
                                                @lang('translation.Please_complete_verification_for_reply')
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i data-feather="send" class="me-1"></i> @lang('translation.Send_Reply')
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
    
    // Fix YouTube embeds in existing content
    makeYouTubeVideosResponsive();
    
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
        placeholderText: '@lang("translation.Write_your_reply_here")',
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
                    uploadStatus.innerHTML = '@lang("translation.Uploading_image")...';
                    
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
                            
                            uploadStatus.innerHTML = '<span class="text-success"><i class="bx bx-check-circle me-1"></i> @lang("translation.Image_uploaded_successfully")</span>';
                            
                            // Hide progress bar after a delay
                            setTimeout(() => {
                                uploadProgress.style.display = 'none';
                                progressBar.style.width = '0%';
                                uploadStatus.innerHTML = '';
                            }, 3000);
                        } else {
                            // Error from server
                            uploadStatus.innerHTML = `<span class="text-danger"><i class="bx bx-error-circle me-1"></i> ${data.error || '@lang("translation.Failed_to_upload_image")'}</span>`;
                            
                            // Hide progress bar after a delay
                            setTimeout(() => {
                                uploadProgress.style.display = 'none';
                                progressBar.style.width = '0%';
                            }, 5000);
                        }
                    })
                    .catch(error => {
                        console.error('Image upload error:', error);
                        uploadStatus.innerHTML = '<span class="text-danger"><i class="bx bx-error-circle me-1"></i> @lang("translation.Error_uploading_image")</span>';
                        
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
                    title: '@lang("translation.Insert_YouTube_Video")',
                    focus: true,
                    undo: true,
                    refreshAfterCallback: true,
                    callback: function () {
                        const videoUrl = prompt('@lang("translation.Enter_YouTube_URL"):');
                        if (videoUrl) {
                            const videoId = parseYoutubeUrl(videoUrl);
                            if (videoId) {
                                const embedHtml = createYoutubeEmbed(videoId);
                                this.html.insert(embedHtml);
                                showToast('@lang("translation.YouTube_embedded_successfully")');
                            } else {
                                showToast('@lang("translation.Invalid_YouTube_URL")', 'error');
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
                                showToast('@lang("translation.YouTube_embedded_successfully")');
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
    
    // Star rating functionality
    const ratingInputs = document.querySelectorAll('.rating-input');
    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Get the message ID from the input's ID
            const parts = this.id.split('-');
            const rating = parseInt(parts[0].replace('rating', ''));
            const messageId = parts[1];
            
            // Get all labels for this message
            const labels = document.querySelectorAll(`[id^="rating"][id$="-${messageId}"] + label i`);
            
            // Update labels based on selected rating
            labels.forEach((star, index) => {
                // For the reverse order, we need to adjust the index
                const adjustedIndex = labels.length - index - 1;
                
                if (adjustedIndex < rating) {
                    star.classList.remove('bx-star');
                    star.classList.add('bxs-star');
                    star.style.color = '#ffab00';
                } else {
                    star.classList.add('bx-star');
                    star.classList.remove('bxs-star');
                    star.style.color = '#ccc';
                }
            });
        });
    });
    
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
                alert('@lang("translation.Complete_CAPTCHA_first")');
                document.querySelector('#captcha-container').scrollIntoView({
                    behavior: 'smooth'
                });
                return false;
            }
            
            // Add loading state to button
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const icon = '<span class="spinner-border spinner-border-sm me-1"></span>';
                submitBtn.innerHTML = icon + ' @lang("translation.Sending")...';
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
                    statusEl.innerHTML = '<i class="bx bx-check-circle me-1"></i> @lang("translation.Verification_successful_reply")';
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