<div class="editor-container">
    <textarea id="{{ $id }}" name="{{ $name }}">{{ $content }}</textarea>
</div>

<!-- Upload progress indicator -->
<div id="{{ $id }}-upload-progress" class="progress mt-2" style="display: none;">
    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
</div>
<div id="{{ $id }}-upload-status" class="mt-1 small"></div>

@push('css')
@once
<!-- Froala Editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css" rel="stylesheet">
<!-- Font Awesome for Froala icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<!-- CodeMirror CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/theme/monokai.min.css">
<!-- Froala Code View Plugin CSS -->
<link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/plugins/code_view.min.css" rel="stylesheet">

<style>
    /* Editor container styling */
    .editor-container {
        position: relative;
        margin-bottom: 20px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    
    /* Froala editor styling */
    .fr-box {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 14px;
    }
    
    /* Upload progress indicator */
    .upload-progress {
        display: none;
        margin-top: 10px;
    }
    
    .upload-progress.show {
        display: block;
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
        .fr-box {
            height: 250px !important;
            font-size: 13px;
        }
    }
</style>
@endonce
@endpush

@push('scripts')
@once
<!-- Toast Message Container -->
<div id="toast-message" class="toast-message"></div>

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
// Show toast message function (globally available)
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

// Parse YouTube URL function
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

// Create YouTube embed HTML
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
</script>
@endonce

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editorId = "{{ $id }}";
    const uploadProgress = document.getElementById(`${editorId}-upload-progress`);
    const progressBar = uploadProgress.querySelector('.progress-bar');
    const uploadStatus = document.getElementById(`${editorId}-upload-status`);
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Initialize Froala Editor with license key
    const editor = new FroalaEditor(`#${editorId}`, {
        // License key (you should store this in your .env file and access via config)
        key: "1C%kZPBZ`JWSDBCQ@ZGD\\@\\JXDAAOZWJh,/.!==",
        
        // Basic configuration
        height: {{ $height }},
        placeholderText: "{{ $placeholder }}",
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
                    
                    fetch('{{ route("admin.upload-image") }}', {
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
});
</script>
@endpush