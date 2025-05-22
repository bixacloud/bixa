@extends('layouts.master')

@section('title') Create Advertisement @endsection

@section('css')
<!-- Ace Editor CSS -->
<style>
    .ad-preview {
        border: 1px solid #e2e8f0;
        padding: 15px;
        border-radius: 5px;
        background-color: #f8fafc;
        min-height: 100px;
    }
    .slot-images {
        max-height: 300px;
        max-width: 100%;
        object-fit: contain;
    }
    
    /* Editor container */
    #editor-container {
        position: relative;
        height: 400px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    
    /* Ace editor */
    #html-editor {
        width: 100%;
        height: 100%;
    }
    
    /* Editor toolbar */
    .editor-toolbar {
        padding: 8px;
        background: #f8f9fa;
        border: 1px solid #ced4da;
        border-bottom: none;
        border-radius: 0.25rem 0.25rem 0 0;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    
    /* Toolbar buttons */
    .editor-toolbar button {
        padding: 5px 10px;
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: 3px;
        cursor: pointer;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .editor-toolbar button:hover {
        background: #f1f3f5;
    }
    
    .editor-toolbar button i {
        font-size: 16px;
    }
    
    /* Button groups */
    .btn-group {
        display: inline-flex;
        border: 1px solid #ced4da;
        border-radius: 3px;
        overflow: hidden;
    }
    
    .btn-group button {
        border: none;
        border-right: 1px solid #ced4da;
        border-radius: 0;
        margin: 0;
    }
    
    .btn-group button:last-child {
        border-right: none;
    }
    
    /* Upload progress indicator */
    #upload-progress {
        display: none;
    }
    
    #upload-progress.show {
        display: block;
    }
    
    /* Responsive YouTube Embeds */
    .responsive-embed-container, .youtube-embed-container {
        position: relative;
        padding-bottom: 100%; /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
        max-width: 100%;
        margin: 15px 0;
        border-radius: 8px;
        background-color: #f8f8f8;
    }

    .responsive-embed-container iframe,
    .responsive-embed-container object,
    .responsive-embed-container embed,
    .youtube-embed-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
        border-radius: 8px;
    }
    
    /* Fix specific for YouTube iframes */
    .card-body iframe[src*="youtube.com/embed"],
    .card-body iframe[src*="youtu.be"],
    .card-body iframe[src*="youtube-nocookie.com/embed"] {
        max-width: 100%;
    }

    /* Style images */
    .ad-preview img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        margin: 5px 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    /* Mobile optimizations */
    @media (max-width: 767.98px) {
        #editor-container {
            height: 300px;
        }
        
        .editor-toolbar {
            padding: 5px;
            overflow-x: auto;
        }
        
        .editor-toolbar button {
            padding: 4px 8px;
            font-size: 12px;
        }
    }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Home @endslot
@slot('li_2') Advertisements @endslot
@slot('title') Create Advertisement @endslot
@endcomponent

<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Create New Advertisement</h4>
                    <a href="{{ route('admin.advertisements.index') }}" class="btn btn-secondary waves-effect">
                        <i data-feather="arrow-left" class="icon-sm me-1"></i> Back to List
                    </a>
                </div>

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

                <form method="POST" action="{{ route('admin.advertisements.store') }}">
                    @csrf
                    
                    <!-- Name -->
                    <div class="row mb-3">
                        <label for="name" class="col-sm-3 col-form-label">Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            <small class="form-text text-muted">Internal name for this advertisement</small>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Slot Position -->
                    <div class="row mb-3">
                        <label for="slot_position" class="col-sm-3 col-form-label">Position</label>
                        <div class="col-sm-9">
                            <select class="form-select @error('slot_position') is-invalid @enderror" 
                                   id="slot_position" name="slot_position" required>
                                <option value="">Select a position</option>
                                @foreach($slots as $slot)
                                    <option value="{{ $slot->code }}" 
                                            data-type="{{ $slot->type }}"
                                            data-description="{{ $slot->description }}"
                                            data-image="{{ $slot->image }}"
                                            {{ old('slot_position') == $slot->code ? 'selected' : '' }}>
                                        {{ $slot->name }} ({{ $slot->code }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Where this ad will appear on the site</small>
                            @error('slot_position')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- HTML Content -->
                    <div class="row mb-3">
                        <label for="html_content" class="col-sm-3 col-form-label">HTML Content</label>
                        <div class="col-sm-9">
                            <!-- Editor Toolbar -->
                            <div class="editor-toolbar">
                                <!-- Text formatting -->
                                <div class="btn-group">
                                    <button type="button" data-action="bold" title="Bold">
                                        <i class="bx bx-bold"></i>
                                    </button>
                                    <button type="button" data-action="italic" title="Italic">
                                        <i class="bx bx-italic"></i>
                                    </button>
                                    <button type="button" data-action="underline" title="Underline">
                                        <i class="bx bx-underline"></i>
                                    </button>
                                </div>
                                
                                <!-- Alignment -->
                                <div class="btn-group">
                                    <button type="button" data-action="align-left" title="Align Left">
                                        <i class="bx bx-align-left"></i>
                                    </button>
                                    <button type="button" data-action="align-center" title="Align Center">
                                        <i class="bx bx-align-middle"></i>
                                    </button>
                                    <button type="button" data-action="align-right" title="Align Right">
                                        <i class="bx bx-align-right"></i>
                                    </button>
                                </div>
                                
                                <!-- Special functions -->
                                <button type="button" id="insert-link-btn" title="Insert Link">
                                    <i class="bx bx-link"></i> Link
                                </button>
                                <button type="button" id="upload-image-btn" title="Upload Image">
                                    <i class="bx bx-image"></i> Image
                                </button>
                                <button type="button" id="insert-youtube-btn" title="Insert YouTube Video">
                                    <i class="bx bxl-youtube" style="color: #FF0000;"></i> YouTube
                                </button>
                                
                                <!-- Format and theme controls -->
                                <button type="button" id="format-html-btn" title="Format HTML">
                                    <i class="bx bx-code-block"></i> Format
                                </button>
                                <button type="button" id="toggle-theme-btn" title="Toggle Dark/Light Mode">
                                    <i class="bx bx-moon"></i> Theme
                                </button>
                            </div>
                            
                            <!-- Hidden file input for image upload -->
                            <input type="file" id="image-upload" accept="image/*" style="display: none;">
                            
                            <!-- Ace Editor Container -->
                            <div id="editor-container">
                                <div id="html-editor"></div>
                            </div>
                            
                            <!-- Hidden textarea to store editor content -->
                            <input type="hidden" id="html_content" name="html_content" class="@error('html_content') is-invalid @enderror" value="{{ old('html_content') }}" required>
                            
                            <!-- Upload progress indicator -->
                            <div id="upload-progress" class="progress mt-2">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                            
                            <!-- Upload status message -->
                            <div id="upload-status" class="mt-2 small"></div>
                            
                            <small class="form-text text-muted">HTML for the advertisement. You can include links, images, etc.</small>
                            @error('html_content')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Status</label>
                        <div class="col-sm-9">
                            <div class="form-check form-switch form-switch-md">
                                <input type="checkbox" class="form-check-input" id="is_active" 
                                       name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            <small class="form-text text-muted">Only active ads will be displayed</small>
                        </div>
                    </div>
                    
                    <div class="row justify-content-end">
                        <div class="col-sm-9">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <i data-feather="save" class="icon-sm me-1"></i> Create Advertisement
                                </button>
                                <a href="{{ route('admin.advertisements.index') }}" class="btn btn-secondary waves-effect">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Preview & Information</h4>
                
                <div class="mb-4">
                    <h5 class="font-size-14 mb-2">Ad Preview</h5>
                    <div class="ad-preview" id="ad-preview">
                        <div class="text-center text-muted py-4">
                            <i data-feather="eye" style="width: 36px; height: 36px;"></i>
                            <p class="mt-2">HTML content preview will appear here</p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h5 class="font-size-14 mb-2">Selected Position</h5>
                    <div id="slot-info" class="d-none">
                        <div id="slot-description" class="mb-3"></div>
                        
                        <div id="slot-image-container" class="text-center mb-3 d-none">
                            <img id="slot-image" class="img-fluid slot-images border rounded" src="" alt="Slot position visualization">
                        </div>
                        
                        <div id="slot-type-badge" class="mb-2"></div>
                    </div>
                    
                    <div id="no-slot-selected" class="text-center text-muted py-4">
                        <i data-feather="map-pin" style="width: 36px; height: 36px;"></i>
                        <p class="mt-2">Select a position to see details</p>
                    </div>
                </div>
                
                <div class="alert alert-info" role="alert">
                    <h5 class="alert-heading font-size-14">Tips</h5>
                    <p class="mb-0">
                        Use the toolbar to format your content. You can add images, videos, and links.
                        Click the Format button to beautify your HTML code.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- Ace Editor and JS-Beautify libraries -->
<script src="{{ URL::asset('/build/libs/ace/ace.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.15.3/beautify-html.min.js"></script>

<script>
/**
 * Advertisement Editor with Ace
 */

// Global variables
var editor = null;
var uploadProgress = null;
var progressBar = null;
var uploadStatus = null;
var isDarkMode = false;

// -------------------- EDITOR INITIALIZATION --------------------

// Initialize Ace Editor
function initEditor() {
    // Create editor
    editor = ace.edit("html-editor");
    editor.setTheme("ace/theme/chrome");
    editor.session.setMode("ace/mode/html");
    editor.setOptions({
        fontSize: "14px",
        wrap: true,
        showPrintMargin: false,
        highlightActiveLine: true,
        enableBasicAutocompletion: true,
        enableLiveAutocompletion: true,
        enableSnippets: true
    });
    
    // Set initial content
    const content = document.getElementById('html_content').value;
    editor.setValue(content, -1);
    
    // Format initial content
    formatHtml();
    
    // Update preview on change
    editor.session.on('change', function() {
        const content = editor.getValue();
        document.getElementById('html_content').value = content;
        updatePreview(content);
    });
    
    // Update preview initially
    updatePreview(content);
    
    // Set up paste handlers
    setupPasteHandlers();
}

// -------------------- CONTENT HANDLING FUNCTIONS --------------------

// Update preview when content changes
function updatePreview(html) {
    const adPreview = document.getElementById('ad-preview');
    if (adPreview) {
        adPreview.innerHTML = html || `
            <div class="text-center text-muted py-4">
                <i data-feather="eye" style="width: 36px; height: 36px;"></i>
                <p class="mt-2">HTML content preview will appear here</p>
            </div>
        `;
        
        if (!html) {
            feather.replace();
        }
        
        // Make YouTube videos responsive
        makeYouTubeVideosResponsive();
    }
}

// Format HTML using js-beautify
function formatHtml() {
    try {
        const content = editor.getValue();
        const formatted = html_beautify(content, {
            indent_size: 2,
            wrap_line_length: 80,
            preserve_newlines: true,
            max_preserve_newlines: 2,
            end_with_newline: false
        });
        editor.setValue(formatted, -1);
    } catch (err) {
        console.error('Error formatting HTML:', err);
    }
}

// Toggle dark/light theme
function toggleTheme() {
    isDarkMode = !isDarkMode;
    editor.setTheme(isDarkMode ? "ace/theme/monokai" : "ace/theme/chrome");
    
    // Update button icon
    const themeBtn = document.getElementById('toggle-theme-btn');
    themeBtn.innerHTML = isDarkMode ? 
        '<i class="bx bx-sun"></i> Theme' : 
        '<i class="bx bx-moon"></i> Theme';
}

// -------------------- YOUTUBE HANDLING FUNCTIONS --------------------

// Make YouTube videos responsive
function makeYouTubeVideosResponsive() {
    // Find all YouTube iframes not already in responsive containers
    const youtubeIframes = document.querySelectorAll('.ad-preview iframe[src*="youtube.com/embed"], .ad-preview iframe[src*="youtu.be"], .ad-preview iframe[src*="youtube-nocookie.com/embed"]');
    
    youtubeIframes.forEach(function(iframe) {
        // Skip if already in a proper container
        if (iframe.parentNode.className === 'responsive-embed-container' || 
            iframe.parentNode.className === 'youtube-embed-container') {
            return;
        }
        
        // Create new container
        const container = document.createElement('div');
        container.className = 'responsive-embed-container';
        
        // Get iframe's parent
        const parent = iframe.parentNode;
        
        // Replace iframe with container
        parent.replaceChild(container, iframe);
        
        // Add iframe to container
        container.appendChild(iframe);
        
        // Ensure iframe has proper mobile attributes
        if (!iframe.getAttribute('loading')) {
            iframe.setAttribute('loading', 'lazy');
        }
        
        if (!iframe.getAttribute('allowfullscreen')) {
            iframe.setAttribute('allowfullscreen', '');
        }
        
        // Update URL to use youtube-nocookie.com
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

// Parse YouTube URL to get video ID
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
            src="https://www.youtube-nocookie.com/embed/${videoId}?playsinline=1&fs=1" 
            title="YouTube video player" 
            frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
            loading="lazy"
            allowfullscreen>
        </iframe>
    </div>`;
}

// Insert YouTube embed when user clicks the button
function insertYoutubeEmbed() {
    const url = prompt('Enter YouTube URL (ex: https://www.youtube.com/watch?v=dQw4w9WgXcQ):');
    if (!url) return;
    
    const videoId = parseYoutubeUrl(url);
    if (!videoId) {
        alert('Invalid YouTube URL');
        return;
    }
    
    // Insert at current cursor position
    const embedHtml = createYoutubeEmbed(videoId);
    editor.insert(embedHtml);
    
    // Show success message
    uploadStatus.innerHTML = '<span class="text-success"><i class="bx bx-check-circle me-1"></i> YouTube video embedded successfully!</span>';
    setTimeout(() => { uploadStatus.innerHTML = ''; }, 3000);
}

// -------------------- HTML FORMATTING FUNCTIONS --------------------

// Insert HTML for text formatting
function insertFormattingHtml(action) {
    const selectedText = editor.getSelectedText();
    let html = '';
    
    switch(action) {
        case 'bold':
            html = `<strong>${selectedText || 'Text'}</strong>`;
            break;
        case 'italic':
            html = `<em>${selectedText || 'Text'}</em>`;
            break;
        case 'underline':
            html = `<u>${selectedText || 'Text'}</u>`;
            break;
        case 'align-left':
            html = `<div style="text-align: left;">${selectedText || 'Text'}</div>`;
            break;
        case 'align-center':
            html = `<div style="text-align: center;">${selectedText || 'Text'}</div>`;
            break;
        case 'align-right':
            html = `<div style="text-align: right;">${selectedText || 'Text'}</div>`;
            break;
    }
    
    if (selectedText) {
        editor.insert(html);
    } else {
        editor.insert(html);
        
        // Position cursor inside the tags
        const currentPosition = editor.getCursorPosition();
        const tag = action.includes('align') ? 'div' : (action === 'bold' ? 'strong' : (action === 'italic' ? 'em' : 'u'));
        const tagLength = tag.length + 2;  // <tag>
        editor.gotoLine(currentPosition.row + 1, currentPosition.column - tagLength - 5); // 5 is 'Text</tag>'
    }
}

// Insert link
function insertLink() {
    const selectedText = editor.getSelectedText();
    const linkText = selectedText || prompt('Enter display text for the link:', 'Link Text');
    if (!linkText) return;
    
    const url = prompt('Enter URL:', 'https://');
    if (!url) return;
    
    const html = `<a href="${url}" target="_blank">${linkText}</a>`;
    
    if (selectedText) {
        // Replace selection
        editor.insert(html);
    } else {
        // Insert at cursor
        editor.insert(html);
    }
}

// -------------------- IMAGE UPLOAD FUNCTIONS --------------------

// Upload image to Imgur through controller
function uploadImageToImgur(file) {
    // Show upload progress
    uploadProgress.classList.add('show');
    progressBar.style.width = '0%';
    uploadStatus.innerHTML = 'Uploading image...';
    
    // Create FormData
    const formData = new FormData();
    formData.append('image', file);
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Send request with proper headers
    fetch('/upload/image', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
            // Don't set Content-Type, it will be set automatically for FormData
        },
        body: formData
    })
    .then(response => {
        if (progressBar) progressBar.style.width = '50%';
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.error || `Server responded with ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (progressBar) progressBar.style.width = '100%';
        
        if (data.success && data.data.url) {
            // Insert the image HTML at cursor position
            const imageUrl = data.data.url;
            const html = `<img src="${imageUrl}" alt="Advertisement Image" style="max-width: 100%; height: auto;">`;
            editor.insert(html);
            
            // Update the preview
            const content = editor.getValue();
            document.getElementById('html_content').value = content;
            updatePreview(content);
            
            // Update status message
            uploadStatus.innerHTML = '<span class="text-success"><i class="bx bx-check-circle me-1"></i> Image uploaded successfully!</span>';
            setTimeout(() => {
                uploadProgress.classList.remove('show');
                uploadStatus.innerHTML = '';
            }, 3000);
        } else {
            throw new Error(data.error || 'Upload failed for unknown reason');
        }
    })
    .catch(error => {
        console.error('Upload failed:', error);
        progressBar.style.width = '0%';
        uploadStatus.innerHTML = `<span class="text-danger"><i class="bx bx-error-circle me-1"></i> ${error.message || 'Upload failed'}</span>`;
        
        setTimeout(() => {
            uploadProgress.classList.remove('show');
        }, 5000);
    });
}

// -------------------- PASTE HANDLING FUNCTIONS --------------------

// Process pasted content for YouTube links
function processPastedContent(e) {
    // Only process if editor has focus
    if (!editor || !editor.isFocused()) return false;
    
    // Get the clipboard text
    const text = e.clipboardData.getData('text/plain');
    
    // Check if it's a YouTube URL
    if (text && (text.includes('youtube.com/watch') || text.includes('youtu.be/'))) {
        const videoId = parseYoutubeUrl(text);
        if (videoId) {
            // Stop the default paste operation
            e.preventDefault();
            
            // Insert YouTube embed
            const embedHtml = createYoutubeEmbed(videoId);
            editor.insert(embedHtml);
            
            // Show success message
            uploadStatus.innerHTML = '<span class="text-success"><i class="bx bx-check-circle me-1"></i> YouTube video embedded!</span>';
            setTimeout(() => { uploadStatus.innerHTML = ''; }, 3000);
            
            return true;
        }
    }
    
    // Check for image data
    if (e.clipboardData.items) {
        const items = e.clipboardData.items;
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                // Stop the default paste operation
                e.preventDefault();
                
                // Upload the image
                const file = items[i].getAsFile();
                if (file) {
                    uploadImageToImgur(file);
                }
                return true;
            }
        }
    }
    
    return false;
}

// Set up paste event handlers with mobile support
function setupPasteHandlers() {
    // 1. Main document paste handler
    document.addEventListener('paste', function(e) {
        processPastedContent(e);
    }, true); // Use capturing phase to intercept before ACE editor
    
    // 2. Direct ACE editor paste handler
    if (editor) {
        editor.commands.on('paste', function(e) {
            // We need to access the clipboardData from the original event
            const originalEvent = e.originalEvent || e;
            if (originalEvent.clipboardData) {
                if (processPastedContent(originalEvent)) {
                    return false; // Prevent default if we handled it
                }
            }
        });
        
        // 3. Intercept Ace's built-in paste behavior for URLs
        const originalOnPaste = editor.onPaste;
        editor.onPaste = function(text) {
            // Check if text is a YouTube URL
            if (text && (text.includes('youtube.com/watch') || text.includes('youtu.be/'))) {
                const videoId = parseYoutubeUrl(text);
                if (videoId) {
                    const embedHtml = createYoutubeEmbed(videoId);
                    editor.insert(embedHtml);
                    return; // Don't call original paste
                }
            }
            
            // Call original paste for non-YouTube content
            originalOnPaste.call(editor, text);
        };
    }
}

// Update slot info in sidebar
function updateSlotInfo() {
    const slotSelect = document.getElementById('slot_position');
    const slotInfo = document.getElementById('slot-info');
    const noSlotSelected = document.getElementById('no-slot-selected');
    const slotDescription = document.getElementById('slot-description');
    const slotTypeBadge = document.getElementById('slot-type-badge');
    const slotImageContainer = document.getElementById('slot-image-container');
    const slotImage = document.getElementById('slot-image');
    
    if (slotSelect.value) {
        slotInfo.classList.remove('d-none');
        noSlotSelected.classList.add('d-none');
        
        // Get selected option
        const selectedOption = slotSelect.options[slotSelect.selectedIndex];
        
        // Update description
        const description = selectedOption.dataset.description || 'No description available.';
        slotDescription.innerHTML = description;
        
        // Update type badge
        const type = selectedOption.dataset.type || 'predefined';
        if (type === 'dynamic') {
            slotTypeBadge.innerHTML = '<span class="badge bg-info">Dynamic Position</span>';
        } else {
            slotTypeBadge.innerHTML = '<span class="badge bg-primary">Predefined Position</span>';
        }
        
        // Update image if available
        const image = selectedOption.dataset.image;
        if (image) {
            slotImageContainer.classList.remove('d-none');
            slotImage.src = '/storage/' + image;
        } else {
            slotImageContainer.classList.add('d-none');
        }
    } else {
        slotInfo.classList.add('d-none');
        noSlotSelected.classList.remove('d-none');
    }
}

// -------------------- INITIALIZATION --------------------

// Initialize page when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Set variables for upload functionality
    uploadProgress = document.getElementById('upload-progress');
    if (uploadProgress) {
        progressBar = uploadProgress.querySelector('.progress-bar');
        uploadStatus = document.getElementById('upload-status');
    }
    
    // Initialize Ace Editor
    initEditor();
    
    // Set up formatter button
    const formatHtmlBtn = document.getElementById('format-html-btn');
    if (formatHtmlBtn) {
        formatHtmlBtn.addEventListener('click', formatHtml);
    }
    
    // Set up theme toggle
    const toggleThemeBtn = document.getElementById('toggle-theme-btn');
    if (toggleThemeBtn) {
        toggleThemeBtn.addEventListener('click', toggleTheme);
    }
    
    // Set up YouTube button
    const insertYoutubeBtn = document.getElementById('insert-youtube-btn');
    if (insertYoutubeBtn) {
        insertYoutubeBtn.addEventListener('click', insertYoutubeEmbed);
    }
    
    // Set up link button
    const insertLinkBtn = document.getElementById('insert-link-btn');
    if (insertLinkBtn) {
        insertLinkBtn.addEventListener('click', insertLink);
    }
    
    // Set up image upload
    const uploadImageBtn = document.getElementById('upload-image-btn');
    const imageUploadInput = document.getElementById('image-upload');
    
    if (uploadImageBtn && imageUploadInput) {
        uploadImageBtn.addEventListener('click', function() {
            imageUploadInput.click();
        });
        
        imageUploadInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                uploadImageToImgur(this.files[0]);
            }
        });
    }
    
    // Set up formatting buttons
    document.querySelectorAll('.editor-toolbar button[data-action]').forEach(function(button) {
        button.addEventListener('click', function() {
            insertFormattingHtml(this.dataset.action);
        });
    });
    
    // Handle slot selection changes
    const slotSelect = document.getElementById('slot_position');
    if (slotSelect) {
        slotSelect.addEventListener('change', updateSlotInfo);
        
        // Initial update
        updateSlotInfo();
    }
    
    // Handle form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            // Make sure the hidden input is updated with the latest content
            if (editor) {
                document.getElementById('html_content').value = editor.getValue();
            }
        });
    }
    
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection