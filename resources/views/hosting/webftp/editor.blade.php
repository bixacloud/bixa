@extends('layouts.master')

@section('title') @lang('translation.Web_File_Editor') - {{ $filename }} @endsection

@section('css')
<link href="{{ URL::asset('/build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Ace Editor CSS -->
<style>
    /* Clean, modern interface */
    .file-editor-container {
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    /* Editor sizing and appearance */
    #editor {
        position: relative;
        height: 350px;
        width: 100%;
        font-size: 14px;
        border: 1px solid #e2e8f0;
        border-top: none;
        border-radius: 0 0 5px 5px;
    }
    
    /* Line numbers and gutter - automatically managed by Ace editor */
    .ace_gutter {
        /* No need to set !important here as it will be managed by the theme */
        background-color: #f8f9fa;
    }
    
    /* Editor toolbar - using email editor style */
    .editor-toolbar {
        padding: 8px;
        background-color: #f8f9fa;
        border: 1px solid #e2e8f0;
        border-radius: 5px 5px 0 0;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    
    /* Button groups style from email editor */
    .btn-group {
        display: inline-flex;
        border: 1px solid #ced4da;
        border-radius: 3px;
        overflow: hidden;
        margin-right: 5px;
    }
    
    .btn-group button {
        border: none;
        border-right: 1px solid #ced4da;
        border-radius: 0;
        margin: 0;
        padding: 5px 10px;
        background: #fff;
        color: #495057;
    }
    
    .btn-group button:last-child {
        border-right: none;
    }
    
    .btn-group button:hover {
        background-color: #f1f3f5;
    }
    
    /* Individual toolbar buttons */
    .toolbar-button {
        padding: 5px 10px;
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: 3px;
        cursor: pointer;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #495057;
    }
    
    .toolbar-button:hover {
        background-color: #f1f3f5;
    }
    
    .toolbar-button i {
        font-size: 16px;
    }
    
    /* Preview section */
    .preview-section {
        margin-top: 15px;
    }
    
    .preview-container {
        border: 1px solid #e2e8f0;
        border-radius: 5px;
        background-color: #fff;
        padding: 20px;
        min-height: 150px;
    }
    
    /* Status bar */
    .editor-statusbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px;
        background-color: #f8f9fa;
        border: 1px solid #e2e8f0;
        border-top: none;
        border-radius: 0 0 5px 5px;
        font-size: 12px;
    }
    
    /* Actions buttons */
    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        #editor {
            height: 300px;
        }
        
        .editor-toolbar {
            flex-wrap: wrap;
            justify-content: flex-start;
            padding: 6px;
        }
        
        .btn-group button, .toolbar-button {
            padding: 4px 8px;
            font-size: 13px;
        }
        
        .editor-statusbar {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .editor-statusbar > div:first-child {
            margin-bottom: 5px;
        }
    }

    /* Breadcrumb fixes for mobile */
    .breadcrumb-file {
        overflow-x: auto;
        white-space: nowrap;
        padding: 0.5rem;
        background-color: #f8f9fa;
        border: 1px solid #e2e8f0;
        border-radius: 5px;
        margin-bottom: 1rem;
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
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.Hosting') @endslot
        @slot('li_2') 
            <a href="{{ route('hosting.view', $account->username) }}">{{ $account->label }}</a>
        @endslot
        @slot('li_3')
            <a href="{{ route('webftp.index', ['username' => $account->username, 'path' => dirname($path)]) }}">@lang('translation.Web_File_Manager')</a>
        @endslot
        @slot('title') @lang('translation.File_Editor') @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">@lang('translation.Edit_File')</h4>
                        <a href="{{ route('webftp.index', ['username' => $account->username, 'path' => dirname($path)]) }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> @lang('translation.Back_to_List')
                        </a>
                    </div>

                    <!-- File Path -->
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb breadcrumb-file">
                            <li class="breadcrumb-item">
                                <i class="bx bx-home"></i>
                            </li>
                            @foreach($pathParts as $part)
                                @if($loop->last)
                                    <li class="breadcrumb-item active">{{ $part['name'] }}</li>
                                @else
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('webftp.index', ['username' => $account->username, 'path' => $part['path']]) }}">
                                            {{ $part['name'] }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                    </nav>

                    <!-- Subject/Filename -->
                    <div class="mb-3">
                        <label class="form-label">@lang('translation.Filename')</label>
                        <input type="text" class="form-control" value="{{ basename($path) }}" disabled>
                    </div>

                    <!-- Content label -->
                    <div class="mb-2">
                        <label class="form-label">@lang('translation.Content')</label>
                    </div>

                    <!-- File Editor Container -->
                    <div class="file-editor-container">
                        <!-- Editor Toolbar - Updated to match email editor style -->
                        <div class="editor-toolbar">
                            <!-- Text formatting buttons -->
                            <div class="btn-group">
                                <button type="button" id="boldBtn" title="@lang('translation.Bold')">
                                    <i class="bx bx-bold"></i>
                                </button>
                                <button type="button" id="italicBtn" title="@lang('translation.Italic')">
                                    <i class="bx bx-italic"></i>
                                </button>
                                <button type="button" id="underlineBtn" title="@lang('translation.Underline')">
                                    <i class="bx bx-underline"></i>
                                </button>
                            </div>
                            
                            <!-- Alignment buttons -->
                            <div class="btn-group">
                                <button type="button" id="alignLeftBtn" title="@lang('translation.Align_Left')">
                                    <i class="bx bx-align-left"></i>
                                </button>
                                <button type="button" id="alignCenterBtn" title="@lang('translation.Align_Center')">
                                    <i class="bx bx-align-middle"></i>
                                </button>
                                <button type="button" id="alignRightBtn" title="@lang('translation.Align_Right')">
                                    <i class="bx bx-align-right"></i>
                                </button>
                            </div>
                            
                            <!-- Special functions -->
                            <button type="button" id="linkBtn" class="toolbar-button" title="@lang('translation.Insert_Link')">
                                <i class="bx bx-link"></i> @lang('translation.Link')
                            </button>
                            
                            <button type="button" id="formatBtn" class="toolbar-button" title="@lang('translation.Format_Code')">
                                <i class="bx bx-code-block"></i> @lang('translation.Format')
                            </button>
                            
                            <button type="button" id="findBtn" class="toolbar-button" title="@lang('translation.Find')">
                                <i class="bx bx-search"></i>
                            </button>
                            
                            <!-- Theme selector -->
                            <button type="button" id="toggleThemeBtn" class="toolbar-button" title="@lang('translation.Toggle_Theme')">
                                <i class="bx bx-moon"></i> @lang('translation.Theme')
                            </button>
                            
                            <!-- Mode selector - moved to the end on the right -->
                            <div class="ms-auto">
                                <select class="form-select form-select-sm" id="modeSelector">
                                    <option value="html" @if($editorMode == 'html') selected @endif>HTML</option>
                                    <option value="css" @if($editorMode == 'css') selected @endif>CSS</option>
                                    <option value="javascript" @if($editorMode == 'javascript') selected @endif>JavaScript</option>
                                    <option value="php" @if($editorMode == 'php') selected @endif>PHP</option>
                                    <option value="json" @if($editorMode == 'json') selected @endif>JSON</option>
                                    <option value="xml" @if($editorMode == 'xml') selected @endif>XML</option>
                                    <option value="markdown" @if($editorMode == 'markdown') selected @endif>Markdown</option>
                                    <option value="plain_text" @if($editorMode == 'text') selected @endif>@lang('translation.Plain_Text')</option>
                                </select>
                            </div>
                        </div>

                        <!-- Editor -->
                        <div id="editor">{{ $content }}</div>

                        <!-- Status Bar -->
                        <div class="editor-statusbar">
                            <div id="position">@lang('translation.Line'): 1, @lang('translation.Column'): 1</div>
                            <div class="d-flex align-items-center">
                                <div id="fileSize"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Section - Only for HTML, Markdown, etc. -->
                    <div id="preview-section" class="preview-section" style="display: none;">
                        <h5 class="mb-3">@lang('translation.Preview')</h5>
                        <div id="preview-container" class="preview-container"></div>
                    </div>

                    <!-- Action buttons -->
                    <div class="action-buttons text-end">
                        <a href="{{ route('webftp.download', ['username' => $account->username]) }}?path={{ urlencode($path) }}" class="btn btn-info">
                            <i class="bx bx-download me-1"></i> @lang('translation.Download')
                        </a>
                        <button type="button" id="saveBtn" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> @lang('translation.Save_Changes')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast Notification -->
    <div id="toast-notification"></div>
@endsection
@section('script')
<!-- SweetAlert2 -->
<script src="{{ URL::asset('/build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<!-- Ace Editor and extensions -->
<script src="{{ URL::asset('/build/libs/ace/ace.js') }}"></script>
<script src="{{ URL::asset('/build/libs/ace/ext-language_tools.js') }}"></script>
<script src="{{ URL::asset('/build/libs/ace/ext-beautify.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.15.3/beautify-html.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Ace Editor
    var editor = ace.edit("editor");
    var beautify = ace.require("ace/ext/beautify");
    
    // Set theme and mode
    // Luôn bắt đầu với theme màu sáng
    editor.setTheme("ace/theme/chrome");
    var currentMode = "{{ $editorMode }}";
    editor.session.setMode("ace/mode/" + currentMode);
    var isDarkMode = false;
    
    // Enable features based on settings
    editor.setOptions({
        enableBasicAutocompletion: true,
        enableLiveAutocompletion: true,
        enableSnippets: true,
        fontSize: "14px",
        showPrintMargin: false,
        highlightActiveLine: true,
        wrap: true
    });
    
    // Track content changes for preview updates
    var debounceTimer;
    
    // Calculate file size
    function calculateFileSize() {
        var content = editor.getValue();
        var bytes = new Blob([content]).size;
        var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        if (bytes == 0) return '0 Byte';
        var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    }
    
    document.getElementById('fileSize').textContent = calculateFileSize();
    
    // Update cursor position display
    editor.selection.on('changeCursor', function() {
        var position = editor.getCursorPosition();
        document.getElementById('position').textContent = '@lang("translation.Line"): ' + (position.row + 1) + ', @lang("translation.Column"): ' + (position.column + 1);
    });
    
    // Show/hide preview based on file type
    function updatePreviewVisibility() {
        var previewableTypes = ['html', 'markdown', 'xml'];
        var showPreview = previewableTypes.indexOf(currentMode) !== -1;
        document.getElementById('preview-section').style.display = showPreview ? 'block' : 'none';
        
        if (showPreview) {
            updatePreview();
        }
    }
    
    // Update initial visibility
    updatePreviewVisibility();
    
    // Mode selector
    document.getElementById('modeSelector').addEventListener('change', function() {
        currentMode = this.value;
        editor.session.setMode("ace/mode/" + currentMode);
        
        // Update preview visibility
        updatePreviewVisibility();
    });
    
    // Format button
    document.getElementById('formatBtn').addEventListener('click', function() {
        formatHtml();
    });
    
    // Find button
    document.getElementById('findBtn').addEventListener('click', function() {
        editor.execCommand("find");
    });
    
    // Link button
    document.getElementById('linkBtn').addEventListener('click', function() {
        var selectedText = editor.getSelectedText();
        var linkText = selectedText || prompt('@lang("translation.Enter_link_text"):');
        if (!linkText) return;
        
        var url = prompt('@lang("translation.Enter_URL"):', 'https://');
        if (!url) return;
        
        var linkHtml = '<a href="' + url + '">' + linkText + '</a>';
        editor.insert(linkHtml);
    });
    
    // Toggle theme - only changes the editor theme, doesn't affect the toolbar
    document.getElementById('toggleThemeBtn').addEventListener('click', function() {
        isDarkMode = !isDarkMode;
        
        // Only change the editor theme
        editor.setTheme(isDarkMode ? "ace/theme/monokai" : "ace/theme/chrome");
        
        // Update button icon
        this.innerHTML = isDarkMode ? 
            '<i class="bx bx-sun"></i> @lang("translation.Theme")' : 
            '<i class="bx bx-moon"></i> @lang("translation.Theme")';
    });
    
    // Format HTML using js-beautify
    function formatHtml() {
        try {
            if (typeof html_beautify !== 'undefined' && (currentMode === 'html' || currentMode === 'xml' || currentMode === 'php')) {
                // Use js-beautify for HTML-type content
                const content = editor.getValue();
                const formatted = html_beautify(content, {
                    indent_size: 2,
                    wrap_line_length: 80,
                    preserve_newlines: true,
                    max_preserve_newlines: 2,
                    end_with_newline: false
                });
                editor.setValue(formatted, -1);
            } else {
                // Use Ace's built-in beautify for other languages
                beautify.beautify(editor.session);
            }
            
            // Show success toast
            showToast('@lang("translation.Code_formatted_successfully")');
        } catch (err) {
            console.error('Error formatting code:', err);
            showToast('@lang("translation.Error_formatting_code")');
        }
    }
    
    // Format buttons (only active for certain file types)
    function setupFormattingButtons() {
        var formattableTypes = ['html', 'markdown', 'xml', 'php'];
        var enableFormatting = formattableTypes.indexOf(currentMode) !== -1;
        
        // Bold
        document.getElementById('boldBtn').addEventListener('click', function() {
            var selectedText = editor.getSelectedText();
            if (!selectedText) return;
            
            var format = '';
            if (currentMode === 'html' || currentMode === 'xml' || currentMode === 'php') {
                format = '<strong>' + selectedText + '</strong>';
            } else if (currentMode === 'markdown') {
                format = '**' + selectedText + '**';
            }
            
            if (format) {
                editor.insert(format);
            }
        });
        
        // Italic
        document.getElementById('italicBtn').addEventListener('click', function() {
            var selectedText = editor.getSelectedText();
            if (!selectedText) return;
            
            var format = '';
            if (currentMode === 'html' || currentMode === 'xml' || currentMode === 'php') {
                format = '<em>' + selectedText + '</em>';
            } else if (currentMode === 'markdown') {
                format = '*' + selectedText + '*';
            }
            
            if (format) {
                editor.insert(format);
            }
        });
        
        // Underline
        document.getElementById('underlineBtn').addEventListener('click', function() {
            var selectedText = editor.getSelectedText();
            if (!selectedText) return;
            
            if (currentMode === 'html' || currentMode === 'xml' || currentMode === 'php') {
                editor.insert('<u>' + selectedText + '</u>');
            }
        });
        
        // Align buttons (only for HTML-like formats)
        var alignBtns = [
            {id: 'alignLeftBtn', align: 'left'},
            {id: 'alignCenterBtn', align: 'center'},
            {id: 'alignRightBtn', align: 'right'}
        ];
        
        alignBtns.forEach(function(btn) {
            document.getElementById(btn.id).addEventListener('click', function() {
                var selectedText = editor.getSelectedText();
                if (!selectedText) return;
                
                if (currentMode === 'html' || currentMode === 'xml' || currentMode === 'php') {
                    editor.insert('<div style="text-align: ' + btn.align + ';">' + selectedText + '</div>');
                }
            });
        });
    }
    
    // Set up formatting buttons
    setupFormattingButtons();
    
    // Update preview
    function updatePreview() {
        var content = editor.getValue();
        var previewContainer = document.getElementById('preview-container');
        
        // Handle different content types
        if (currentMode === 'html') {
            // Create iframe for HTML preview (sandboxed)
            previewContainer.innerHTML = `<iframe id="preview-iframe" 
                style="width: 100%; border: none; min-height: 250px;" 
                sandbox="allow-same-origin allow-scripts"></iframe>`;
                
            var iframe = document.getElementById('preview-iframe');
            var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            
            iframeDoc.open();
            iframeDoc.write(content);
            iframeDoc.close();
            
            // Adjust iframe height to content
            setTimeout(function() {
                if (iframe.contentWindow.document.body) {
                    iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 20 + 'px';
                }
            }, 100);
        } 
        else if (currentMode === 'markdown') {
            // Simple markdown to HTML conversion (very basic)
            var html = content
                .replace(/^# (.*)/gm, '<h1>$1</h1>')
                .replace(/^## (.*)/gm, '<h2>$1</h2>')
                .replace(/^### (.*)/gm, '<h3>$1</h3>')
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2">$1</a>')
                .replace(/^\s*\n/gm, '<br>')
                .replace(/^- (.*)/gm, '<li>$1</li>')
                .replace(/<\/li>\n<li>/g, '</li><li>');
                
            previewContainer.innerHTML = html;
        }
        else if (currentMode === 'xml') {
            // Format XML with syntax highlighting
            previewContainer.innerHTML = '<pre>' + escapeHtml(content) + '</pre>';
        }
    }
    
    // Escape HTML for safe display
    function escapeHtml(html) {
        return html
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    // Update preview on content change with debounce
    editor.session.on('change', function() {
        // Update file size immediately
        document.getElementById('fileSize').textContent = calculateFileSize();
        
        // Debounce preview update
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            if (document.getElementById('preview-section').style.display !== 'none') {
                updatePreview();
            }
        }, 300); // 300ms debounce delay
    });
    
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
    
    // Save button
    document.getElementById('saveBtn').addEventListener('click', function() {
        var content = editor.getValue();
        
        // Show loading state
        var saveBtn = this;
        var originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="bx bx-loader bx-spin me-1"></i> @lang("translation.Saving")...';
        saveBtn.disabled = true;
        
        // Send content to server
        fetch('{{ route('webftp.saveFile', ['username' => $account->username]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                content: content,
                path: '{{ $path }}'
            })
        })
        .then(response => response.json())
        .then(data => {
            // Restore button state
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
            
            if (data.success) {
                showToast('@lang("translation.File_saved_successfully")');
                
                // Update preview immediately after save
                if (document.getElementById('preview-section').style.display !== 'none') {
                    updatePreview();
                }
            } else {
                Swal.fire({
                    title: '@lang("translation.Error")!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonColor: '#556ee6'
                });
            }
        })
        .catch(error => {
            // Restore button state
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
            
            Swal.fire({
                title: '@lang("translation.Error")!',
                text: '@lang("translation.Failed_to_save_file")',
                icon: 'error',
                confirmButtonColor: '#556ee6'
            });
        });
    });
    
    // Auto-save on Ctrl+S
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            document.getElementById('saveBtn').click();
        }
    });
    
    // Handle mobile optimizations
    function adjustForScreenSize() {
        if (window.innerWidth < 768) {
            editor.setFontSize(13);
        } else {
            editor.setFontSize(14);
        }
    }
    
    // Apply responsive adjustments
    adjustForScreenSize();
    window.addEventListener('resize', adjustForScreenSize);
    
    // Make content elements responsive in preview
    function makePreviewResponsive() {
        // Force all tables to be mobile-friendly
        const tables = document.querySelectorAll('#preview-container table');
        tables.forEach(function(table) {
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
        const images = document.querySelectorAll('#preview-container img');
        images.forEach(function(img) {
            img.style.maxWidth = '100%';
            img.style.height = 'auto';
        });
        
        // Ensure all links wrap properly
        const links = document.querySelectorAll('#preview-container a');
        links.forEach(function(link) {
            link.style.wordBreak = 'break-all';
        });
    }
    
    // Update preview and apply responsive styles
    updatePreview();
    makePreviewResponsive();
});
</script>
@endsection