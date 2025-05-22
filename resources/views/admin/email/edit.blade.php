@extends('layouts.master')

@section('title') Edit Email Template @endsection

@section('css')
<!-- Ace Editor CSS -->
<style>
    /* Chỉ áp dụng style cho email template editor, không ảnh hưởng các trang khác */
    .email-template-editor .email-preview {
        border: 1px solid #e2e8f0;
        padding: 15px;
        border-radius: 5px;
        background-color: #f8fafc;
        height: 400px; /* Fixed height */
        margin-top: 20px;
        overflow-y: auto; /* Enable vertical scrolling */
        overflow-x: auto; /* Allow horizontal scrolling for wide content */
        max-width: 100%; /* Ensure it doesn't overflow its container */
        position: relative; /* For the collapse toggle */
    }
    
    /* Toggle button for preview collapse/expand */
    .email-template-editor .preview-toggle {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
        background-color: rgba(255, 255, 255, 0.9);
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 5px 10px;
        cursor: pointer;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .email-template-editor .preview-toggle:hover {
        background-color: #f1f5f9;
    }
    
    /* Preview collapsed state */
    .email-template-editor .email-preview.collapsed {
        height: 80px;
        overflow: hidden;
    }
    
    /* Email preview inner wrapper for better mobile control */
    .email-template-editor .email-preview-inner {
        max-width: 100%;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }
    
    /* Email container to maintain proper structure */
    .email-template-editor .email-container {
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }
    
    /* Ensure footer stays at the bottom */
    .email-template-editor .email-preview .footer,
    .email-template-editor .email-preview .copyright-element {
        margin-top: auto;
        position: relative;
    }
    
    /* Force images to be responsive in preview */
    .email-template-editor .email-preview img {
        max-width: 100% !important;
        height: auto !important;
    }
    
    /* Force tables to be responsive in preview */
    .email-template-editor .email-preview table {
        max-width: 100% !important;
        width: 100% !important;
    }
    
    /* Force links to wrap */
    .email-template-editor .email-preview a {
        word-break: break-all;
    }
    
    /* Upload progress indicator */
    #upload-progress {
        display: none;
    }
    
    #upload-progress.show {
        display: block;
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
    
    /* Editor toolbar - use flex-wrap and ensure buttons fit */
    .email-template-editor .editor-toolbar {
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
    .email-template-editor .editor-toolbar button {
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
    
    .email-template-editor .editor-toolbar button:hover {
        background: #f1f3f5;
    }
    
    .email-template-editor .editor-toolbar button i {
        font-size: 16px;
    }
    
    /* Button groups */
    .email-template-editor .btn-group {
        display: inline-flex;
        border: 1px solid #ced4da;
        border-radius: 3px;
        overflow: hidden;
    }
    
    .email-template-editor .btn-group button {
        border: none;
        border-right: 1px solid #ced4da;
        border-radius: 0;
        margin: 0;
    }
    
    .email-template-editor .btn-group button:last-child {
        border-right: none;
    }
    
    /* Variable pills with full notation */
    .email-template-editor .variable-pill {
        display: inline-block;
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        border-radius: 3px;
        padding: 6px 10px;
        margin: 4px;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 0.875em;
        cursor: text;
        user-select: all; /* Makes the content automatically selected when clicked */
    }
    
    .email-template-editor .variable-pill:hover {
        background-color: #dee2e6;
    }
    
    /* Toast notification */
    #toast-notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #28a745;
        color: white;
        padding: 12px 20px;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
        display: none;
    }
    
    #toast-notification.show {
        display: block;
        animation: fadeInOut 3s ease-in-out;
    }
    
    @keyframes fadeInOut {
        0% { opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { opacity: 0; }
    }
    
    /* Highlight variables in preview */
    .email-template-editor .variable-highlight {
        display: inline-block;
        background-color: #e2e8f0;
        padding: 2px 4px;
        border-radius: 3px;
        color: #3949ab;
        font-family: monospace;
    }
    
    /* Variables section styling */
    .email-template-editor .variables-section {
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .email-template-editor .variables-heading {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .email-template-editor .variables-heading i {
        margin-right: 8px;
        color: #4263eb;
    }
    
    .email-template-editor .variables-note {
        font-size: 0.9em;
        color: #6c757d;
        margin-bottom: 15px;
    }
    
    /* Sticky footer for save buttons */
    .email-template-editor .sticky-footer {
        position: sticky;
        bottom: 0;
        background-color: #fff;
        padding: 15px 0;
        border-top: 1px solid #e2e8f0;
        margin-top: 20px;
        z-index: 100;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
    }
    
    /* Responsive adjustments ONLY for email editor */
    @media (max-width: 767.98px) {
        .email-template-editor #editor-container {
            height: 300px;
        }
        
        /* Make email preview mobile-friendly but still scrollable */
        .email-template-editor .email-preview {
            height: 300px;
            padding: 10px;
        }
        
        /* Fix variable container overflow */
        .email-template-editor .variables-container {
            max-height: 150px;
            overflow-y: auto;
            display: flex;
            flex-wrap: wrap;
        }
        
        /* Ensure toolbar buttons wrap correctly on mobile */
        .email-template-editor .editor-toolbar {
            flex-wrap: wrap;
            justify-content: flex-start;
        }
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Email Templates @endslot
        @slot('title') Edit Template @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card email-template-editor">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Edit Email Template</h4>
                        <a href="{{ route('admin.email.index') }}" class="btn btn-secondary waves-effect">
                            <i data-feather="arrow-left" class="icon-sm me-1"></i> Back to List
                        </a>
                    </div>

                    <form action="{{ route('admin.email.update', $template->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Subject -->
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" 
                                name="subject" 
                                class="form-control @error('subject') is-invalid @enderror"
                                value="{{ old('subject', $template->subject) }}">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Available Variables - With full notation for easy copy -->
                        @if(count($templateVariables) > 0)
                        <div class="variables-section">
                            <div class="variables-heading">
                                <i class="bx bx-code-curly"></i>
                                <h5 class="mb-0">Available Variables</h5>
                            </div>
                            <p class="variables-note">Click on a variable to select it, then copy (Ctrl+C or ⌘+C) and paste it into the editor:</p>
                            <div class="variables-container">
                                @foreach($templateVariables as $variable)
                                    <span class="variable-pill">@php echo '{{'.$variable.'}}'; @endphp </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- HTML Content -->
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            
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
                            <input type="hidden" id="html_template" name="html_template" value="{{ old('html_template', $template->html_template) }}">
                            
                            <!-- Upload progress indicator -->
                            <div id="upload-progress" class="progress mt-2">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                            
                            <!-- Upload status message -->
                            <div id="upload-status" class="mt-2 small"></div>
                            
                            @error('html_template')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Email Preview with Toggle -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label">Preview</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="toggle-preview-btn">
                                    <i class="bx bx-expand-alt"></i> Expand/Collapse
                                </button>
                            </div>
                            <div class="email-preview collapsed">
                                <div class="email-preview-inner" id="email-preview"></div>
                            </div>
                        </div>

                        <!-- Sticky Save Button Area -->
                        <div class="sticky-footer">
                            <div class="text-end">
                                <a href="{{ route('admin.email.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast Notification -->
    <div id="toast-notification"></div>
@endsection

@section('script')
<!-- Ace Editor Script -->
<script src="{{ URL::asset('/build/libs/ace/ace.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.15.3/beautify-html.min.js"></script>

<script>
/**
 * Email Template Editor
 * Simple version with manually copy-paste variables
 * IMPROVED FOR RESPONSIVE PREVIEW
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
    const content = document.getElementById('html_template').value;
    editor.setValue(content, -1);
    
    // Format initial content
    formatHtml();
    
    // Update preview on change
    editor.session.on('change', function() {
        const content = editor.getValue();
        document.getElementById('html_template').value = content;
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
    const emailPreview = document.getElementById('email-preview');
    if (emailPreview) {
        // Process template variables with placeholders for preview
        let processedHtml = processVariablesForPreview(html);
        
        // Wrap entire email in a container to maintain structure
        const emailContent = processedHtml ? 
            `<div class="email-container">${processedHtml}</div>` : 
            `<div class="text-center text-muted py-4">
                <i data-feather="mail" style="width: 36px; height: 36px;"></i>
                <p class="mt-2">Email content preview will appear here</p>
            </div>`;
            
        emailPreview.innerHTML = emailContent;
        
        if (!html) {
            feather.replace();
        }
        
        // Make YouTube videos responsive
        makeYouTubeVideosResponsive();
        
        // Apply responsiveness fixes
        makePreviewResponsive();
        
        // Fix email structure to prevent footer misplacement
        fixEmailStructure();
    }
}

// Apply additional responsive fixes to the preview
function makePreviewResponsive() {
    // Force all tables to be mobile-friendly
    const tables = document.querySelectorAll('.email-preview table');
    tables.forEach(function(table) {
        table.classList.add('responsive-table');
        table.style.width = '100%';
        table.style.maxWidth = '100%';
        
        // Ensure table cells wrap text
        const cells = table.querySelectorAll('td, th');
        cells.forEach(function(cell) {
            cell.style.wordBreak = 'break-word';
            cell.style.maxWidth = '100%';
        });
    });
    
    // Force all images to be responsive
    const images = document.querySelectorAll('.email-preview img');
    images.forEach(function(img) {
        img.classList.add('responsive-img');
        img.style.maxWidth = '100%';
        img.style.height = 'auto';
    });
    
    // Ensure all links wrap properly
    const links = document.querySelectorAll('.email-preview a');
    links.forEach(function(link) {
        link.style.wordBreak = 'break-all';
    });
}

// Process variables for preview by highlighting them with dedicated spans
function processVariablesForPreview(html) {
    if (!html) return '';
    
    // Replace variable placeholders with highlighted spans
    // Using traditional string replacement to avoid template literal issues
    let processed = html.replace(/\{\{([^}]+)\}\}/g, function(match, variable) {
        return '<span class="variable-highlight">' + match + '</span>';
    });
    
    // Prevent incorrect document structure by ensuring proper nesting
    // This helps maintain proper structure for footers and other elements
    return processed;
}

// Fix email structure issues, especially with footers
function fixEmailStructure() {
    const preview = document.querySelector('.email-preview');
    if (!preview) return;
    
    // Fix 1: Ensure proper html/body structure
    const htmlRoot = preview.querySelector('html');
    if (htmlRoot) {
        // Email has complete HTML structure, ensure proper rendering
        // Clone the content to manipulate safely
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = preview.innerHTML;
        
        // Extract only the body content if needed
        const bodyContent = tempDiv.querySelector('body');
        if (bodyContent) {
            preview.innerHTML = bodyContent.innerHTML;
        }
    }
    
    // Fix 2: Fix footer placement
    const footers = preview.querySelectorAll('.footer');
    footers.forEach(footer => {
        // Ensure footer is at the end
        const parent = footer.parentNode;
        parent.appendChild(footer);
    });
    
    // Fix 3: Ensure any copyright sections stay in their containers
    const copyrightSections = preview.querySelectorAll('p, div, span').forEach(el => {
        if (el.textContent.includes('©') || el.textContent.includes('&copy;')) {
            // Add a class to identify copyright elements
            el.classList.add('copyright-element');
            
            // Make sure it's properly contained and styled
            el.style.clear = 'both';
            
            // If it's in a footer, make sure the footer is at the end
            const footerParent = el.closest('.footer');
            if (footerParent) {
                const container = footerParent.parentNode;
                container.appendChild(footerParent);
            }
        }
    });
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

// Show toast notification
function showToast(message) {
    const toast = document.getElementById('toast-notification');
    if (!toast) return;
    
    toast.textContent = message;
    toast.classList.add('show');
    
    // Remove after animation completes
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// -------------------- YOUTUBE HANDLING FUNCTIONS --------------------

// Make YouTube videos responsive
function makeYouTubeVideosResponsive() {
    // Find all YouTube iframes not already in embed-container
    const youtubeIframes = document.querySelectorAll('.email-preview iframe[src*="youtube.com/embed"], .email-preview iframe[src*="youtu.be"]');
    
    youtubeIframes.forEach(function(iframe) {
        // Skip if already in a proper container
        if (iframe.parentNode.className === 'embed-container') {
            return;
        }
        
        // Check if there's already a style tag for embed-container
        let styleExists = false;
        document.querySelectorAll('style').forEach(function(style) {
            if (style.textContent.includes('.embed-container')) {
                styleExists = true;
            }
        });
        
        // Create container with style if needed
        let containerHtml = '<div class="embed-container"></div>';
        if (!styleExists) {
            containerHtml = '<style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style>' + containerHtml;
        }
        
        // Create temporary element to hold the HTML
        const temp = document.createElement('div');
        temp.innerHTML = containerHtml;
        
        // Get the container div
        const container = temp.querySelector('.embed-container');
        
        // Get iframe's parent
        const parent = iframe.parentNode;
        
        // Replace iframe with container
        parent.replaceChild(container, iframe);
        
        // Add iframe to container
        container.appendChild(iframe);
        
        // Ensure iframe has proper attributes
        if (!iframe.getAttribute('allowfullscreen')) {
            iframe.setAttribute('allowfullscreen', '');
        }
        
        iframe.setAttribute('frameborder', '0');
    });
}

// Parse YouTube URL to get video ID - Enhanced for reliability
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
    // Get current cursor position to restore later
    const currentPosition = editor.getCursorPosition();
    
    // Get current content
    const htmlContent = editor.getValue();
    
    // YouTube CSS
    const youtubeCss = `.embed-container{position:relative;padding-bottom:56.25%;height:0;overflow:hidden;max-width:100%}.embed-container iframe,.embed-container object,.embed-container embed{position:absolute;top:0;left:0;width:100%;height:100%}`;
    
    // Create the iframe HTML
    const iframeHtml = `<div class="embed-container"><iframe src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe></div>`;
    
    // Check if style tag already exists
    if (htmlContent.includes('<style>') && htmlContent.includes('</style>')) {
        // Look for style tag in the document
        const styleTagRegex = /<style>([\s\S]*?)<\/style>/;
        const match = htmlContent.match(styleTagRegex);
        
        if (match) {
            // Only add the YouTube CSS if it's not already in the style tag
            if (!match[1].includes('.embed-container')) {
                // Create the new style content
                const newStyleContent = match[1] + '\n' + youtubeCss;
                
                // Replace the old style tag with the new one
                const newHtml = htmlContent.replace(styleTagRegex, `<style>${newStyleContent}</style>`);
                
                // Update editor
                editor.setValue(newHtml, -1);
                
                // Position cursor where it needs to be for insertion
                editor.gotoLine(currentPosition.row + 1, currentPosition.column);
                
                // Insert the iframe HTML
                editor.insert(iframeHtml);
            } else {
                // If CSS already exists, just insert the iframe
                editor.insert(iframeHtml);
            }
        }
    } else {
        // If no style tag exists, add both the style and iframe
        const styleAndIframe = `<style>${youtubeCss}</style>${iframeHtml}`;
        editor.insert(styleAndIframe);
    }
    
    // Update the hidden input field with the latest content
    document.getElementById('html_template').value = editor.getValue();
    
    // Update preview
    updatePreview(editor.getValue());
    
    return true;
}

// Insert YouTube embed when user clicks the button
function insertYoutubeEmbed() {
    const url = prompt('Enter YouTube URL (ex: https://www.youtube.com/watch?v=biwC8jK7YYY):');
    if (!url) return;
    
    const videoId = parseYoutubeUrl(url);
    if (!videoId) {
        alert('Invalid YouTube URL');
        return;
    }
    
    createYoutubeEmbed(videoId);
    
    // Show success message
    uploadStatus.innerHTML = '<span class="text-success"><i class="bx bx-check-circle me-1"></i> YouTube video embedded successfully!</span>';
    setTimeout(() => { uploadStatus.innerHTML = ''; }, 3000);
}

// -------------------- PASTE HANDLING WITH MOBILE SUPPORT --------------------

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
            
            // Create the YouTube embed
            createYoutubeEmbed(videoId);
            
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
                    uploadImageToServer(file);
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
                    createYoutubeEmbed(videoId);
                    return; // Don't call original paste
                }
            }
            
            // Call original paste for non-YouTube content
            originalOnPaste.call(editor, text);
        };
        
        // 4. Add mobile-specific input handlers
        handleMobilePaste();
    }
}

// Special handler for mobile devices
function handleMobilePaste() {
    // Get the editor DOM element
    const editorElement = document.getElementById('html-editor');
    if (!editorElement) return;
    
    // Track input changes to detect pastes on mobile
    let lastValue = editor.getValue();
    
    // Many mobile devices trigger input events on paste
    editor.session.on('change', function(delta) {
        // Skip if we're programmatically updating (e.g. from our own functions)
        if (editor.curOp && editor.curOp.command.name) return;
        
        const currentValue = editor.getValue();
        const newText = detectNewText(lastValue, currentValue);
        
        // Only process if significant new text was added
        if (newText && newText.length > 5) {
            // Check if the new text contains a YouTube URL
            if (newText.includes('youtube.com/watch') || newText.includes('youtu.be/')) {
                // Extract the YouTube URL from the pasted text
                const youtubeUrl = extractYoutubeUrl(newText);
                if (youtubeUrl) {
                    const videoId = parseYoutubeUrl(youtubeUrl);
                    if (videoId) {
                        // Store cursor position
                        const cursorPos = editor.getCursorPosition();
                        
                        // Remove the pasted URL text
                        editor.setValue(lastValue);
                        editor.gotoLine(cursorPos.row + 1, cursorPos.column);
                        
                        // Insert properly formatted embed
                        createYoutubeEmbed(videoId);
                        
                        // Show success message
                        uploadStatus.innerHTML = '<span class="text-success"><i class="bx bx-check-circle me-1"></i> YouTube video embedded!</span>';
                        setTimeout(() => { uploadStatus.innerHTML = ''; }, 3000);
                        
                        // Don't update lastValue here, as we've reverted
                        return;
                    }
                }
            }
        }
        
        // Update last value
        lastValue = currentValue;
    });
    
    // Tablet and iPad detection (may use different paste mechanisms)
    // Add touch listeners to potentially detect changes
    editorElement.addEventListener('touchend', function() {
        // Set a timeout to check content after mobile keyboards might have pasted
        setTimeout(function() {
            const currentValue = editor.getValue();
            if (currentValue !== lastValue) {
                // Similar logic as above, but after a touch event
                const newText = detectNewText(lastValue, currentValue);
                
                if (newText && newText.length > 5) {
                    if (newText.includes('youtube.com/watch') || newText.includes('youtu.be/')) {
                        const youtubeUrl = extractYoutubeUrl(newText);
                        if (youtubeUrl) {
                            const videoId = parseYoutubeUrl(youtubeUrl);
                            if (videoId) {
                                // Store cursor position
                                const cursorPos = editor.getCursorPosition();
                                
                                // Remove the pasted URL text
                                editor.setValue(lastValue);
                                editor.gotoLine(cursorPos.row + 1, cursorPos.column);
                                
                                // Insert properly formatted embed
                                createYoutubeEmbed(videoId);
                                
                                // Show success message
                                uploadStatus.innerHTML = '<span class="text-success"><i class="bx bx-check-circle me-1"></i> YouTube video embedded!</span>';
                                setTimeout(() => { uploadStatus.innerHTML = ''; }, 3000);
                                
                                return;
                            }
                        }
                    }
                }
                
                lastValue = currentValue;
            }
        }, 100);
    });
}

// Helper function to detect new text added between two states
function detectNewText(oldText, newText) {
    if (oldText === newText) return '';
    if (!oldText) return newText;
    
    // Simple case: text was added at the end
    if (newText.startsWith(oldText)) {
        return newText.slice(oldText.length);
    }
    
    // Simple case: text was added at the beginning
    if (newText.endsWith(oldText)) {
        return newText.slice(0, newText.length - oldText.length);
    }
    
    // More complex case: find the longest common prefix and suffix
    let i = 0;
    while (i < oldText.length && i < newText.length && oldText[i] === newText[i]) {
        i++;
    }
    
    let j = 0;
    while (
        j < oldText.length - i &&
        j < newText.length - i &&
        oldText[oldText.length - 1 - j] === newText[newText.length - 1 - j]
    ) {
        j++;
    }
    
    return newText.slice(i, newText.length - j);
}

// Extract YouTube URL from a string that might contain other text
function extractYoutubeUrl(text) {
    // YouTube URL patterns
    const patterns = [
        /(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=[^&\s]+/i,
        /(?:https?:\/\/)?(?:www\.)?youtu\.be\/[^/\s]+/i
    ];
    
    for (const pattern of patterns) {
        const match = text.match(pattern);
        if (match && match[0]) {
            return match[0];
        }
    }
    
    return null;
}

// -------------------- IMAGE UPLOAD FUNCTIONS --------------------

// Upload image to server - improved implementation
function uploadImage(file) {
    // Show upload progress
    uploadProgress.classList.add('show');
    progressBar.style.width = '0%';
    uploadStatus.innerHTML = 'Uploading image...';
    
    // Create FormData
    const formData = new FormData();
    formData.append('image', file);
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Send request with proper headers
    fetch('/admin/upload-image', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
            // Don't set Content-Type, it will be set automatically for FormData
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.error || `Server responded with ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.data.url) {
            // Insert the image HTML at cursor position
            const imageUrl = data.data.url;
            const html = `<img src="${imageUrl}" alt="Email Image" style="max-width: 100%; height: auto;">`;
            editor.insert(html);
            
            // Update the preview
            const content = editor.getValue();
            document.getElementById('html_template').value = content;
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

// Upload image to server - function reference
function uploadImageToServer(file) {
    uploadImage(file);
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
    
    const html = `<a href="${url}">${linkText}</a>`;
    
    if (selectedText) {
        // Replace selection
        editor.insert(html);
    } else {
        // Insert at cursor
        editor.insert(html);
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
    
    // Make variable pills automatically select their content when clicked
    document.querySelectorAll('.variable-pill').forEach(function(pill) {
        pill.addEventListener('click', function() {
            // This will automatically select all text in the pill
            // Combined with user-select: all CSS, it makes copying easy
            
            // For mobile devices that don't support user-select: all
            if (window.getSelection) {
                const selection = window.getSelection();
                const range = document.createRange();
                range.selectNodeContents(this);
                selection.removeAllRanges();
                selection.addRange(range);
            }
        });
    });
    
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
                uploadImageToServer(this.files[0]);
            }
        });
    }
    
    // Set up formatting buttons
    document.querySelectorAll('.editor-toolbar button[data-action]').forEach(function(button) {
        button.addEventListener('click', function() {
            insertFormattingHtml(this.dataset.action);
        });
    });
    
    // Handle form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            // Make sure the hidden input is updated with the latest content
            if (editor) {
                document.getElementById('html_template').value = editor.getValue();
            }
        });
    }
    
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Handle window resize for responsive preview
    window.addEventListener('resize', function() {
        // Re-apply responsive fixes
        makePreviewResponsive();
    });
    
    // Set up preview toggle button
    const togglePreviewBtn = document.getElementById('toggle-preview-btn');
    const previewContainer = document.querySelector('.email-preview');
    
    if (togglePreviewBtn && previewContainer) {
        togglePreviewBtn.addEventListener('click', function() {
            previewContainer.classList.toggle('collapsed');
            
            // Update button text
            if (previewContainer.classList.contains('collapsed')) {
                this.innerHTML = '<i class="bx bx-expand"></i> Expand';
            } else {
                this.innerHTML = '<i class="bx bx-collapse"></i> Collapse';
            }
        });
    }
    
    // Initial responsive check
    makePreviewResponsive();
    
    // Fix any email structure issues initially
    setTimeout(function() {
        fixEmailStructure();
    }, 500);
});
</script>
@endsection