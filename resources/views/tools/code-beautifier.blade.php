@extends('layouts.master')

@section('title') Code Beautifier & Minifier @endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.21.0/dist/sweetalert2.min.css">
<style>
    .header-section {
        background: linear-gradient(to right, #4338ca, #312e81);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0.25rem;
    }
    
    .header-title {
        color: white !important;
    }
    
    #codeEditor, #outputEditor {
        width: 100%;
        height: 300px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        margin-bottom: 15px;
    }
    
    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }
    
    .action-btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 10px;
    }
    
    /* Mobile optimizations */
    @media (max-width: 767.98px) {
        .header-section {
            padding: 1.5rem 0;
        }
        
        .header-title {
            font-size: 1.75rem;
        }
        
        #codeEditor, #outputEditor {
            height: 250px;
        }
        
        .card-header {
            padding: 0.75rem;
        }
        
        .form-select, .form-control {
            padding: 0.4rem 0.75rem;
            font-size: 0.9rem;
        }
        
        .btn {
            padding: 0.4rem 0.75rem;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .action-buttons {
            justify-content: center;
        }
        
        .action-btn-group {
            flex: 1 1 100%;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Tools @endslot
        @slot('title') Code Beautifier & Minifier @endslot
    @endcomponent

    <!-- Header Section -->
    <div class="header-section text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-4 header-title">Code Beautifier & Minifier</h1>
            <p class="lead mb-4">Format, beautify or minify your HTML, CSS, and JavaScript code</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="bx bx-code-alt me-2"></i>
                        Code Formatter
                    </div>
                    <div class="card-body">
                        <!-- Language Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="languageSelect" class="form-label">Select Language:</label>
                                <select class="form-select" id="languageSelect">
                                    <option value="html" selected>HTML</option>
                                    <option value="css">CSS</option>
                                    <option value="javascript">JavaScript</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="indentSize" class="form-label">Indent Size:</label>
                                <select class="form-select" id="indentSize">
                                    <option value="2">2 spaces</option>
                                    <option value="4" selected>4 spaces</option>
                                    <option value="8">8 spaces</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Editors -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="codeEditor" class="form-label">Source Code:</label>
                                    <div id="codeEditor"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="outputEditor" class="form-label">Result:</label>
                                    <div id="outputEditor"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons - Reorganized for mobile -->
                        <div class="action-buttons">
                            <div class="action-btn-group">
                                <button class="btn btn-primary" id="beautifyButton">
                                    <i class="bx bx-paint me-1"></i> Beautify
                                </button>
                                <button class="btn btn-warning" id="minifyButton">
                                    <i class="bx bx-collapse me-1"></i> Minify
                                </button>
                            </div>
                            <div class="action-btn-group">
                                <button class="btn btn-success" id="copyButton">
                                    <i class="bx bx-copy me-1"></i> Copy
                                </button>
                                <button class="btn btn-secondary" id="clearButton">
                                    <i class="bx bx-trash me-1"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<!-- First, load the basic Ace Editor -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/ace.js"></script>

<!-- Then load required extensions separately -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/ext-language_tools.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/theme-monokai.js"></script>

<!-- Beautify & Minify Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.14.9/beautify.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.14.9/beautify-css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.14.9/beautify-html.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html-minifier/4.0.0/htmlminifier.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/terser/5.17.1/bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/csso/5.0.5/csso.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.21.0/dist/sweetalert2.all.min.js"></script>


<!-- Initialize Editors and Functions -->
<script>
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM loaded, initializing editors...");
    
    // Initialize editor variables outside of try/catch
    var editor = null;
    var outputEditor = null;
    
    // Detect if we're on mobile
    const isMobile = window.innerWidth < 768;
    
    try {
        // Initialize source editor with mobile optimizations
        editor = ace.edit("codeEditor");
        editor.setTheme("ace/theme/monokai");
        editor.session.setMode("ace/mode/html");
        editor.setShowPrintMargin(false);
        editor.session.setUseWrapMode(true);
        editor.setOptions({
            fontSize: isMobile ? "12px" : "14px",
            showGutter: true,
            showLineNumbers: true,
            highlightActiveLine: true,
            enableBasicAutocompletion: true,
            enableLiveAutocompletion: false, // Disable for mobile to save resources
        });
        
        // Initialize result editor with mobile optimizations
        outputEditor = ace.edit("outputEditor");
        outputEditor.setTheme("ace/theme/monokai");
        outputEditor.session.setMode("ace/mode/html");
        outputEditor.setShowPrintMargin(false);
        outputEditor.session.setUseWrapMode(true);
        outputEditor.setReadOnly(true);
        outputEditor.setOptions({
            fontSize: isMobile ? "12px" : "14px",
            showGutter: true,
            showLineNumbers: true,
        });
        
        // Set initial sample code
        editor.setValue(`<!DOCTYPE html>
<html>
<head>
    <title>Example</title>
</head>
<body>
    <h1>Hello World</h1>
    <p>This is a sample HTML document.</p>
</body>
</html>`, -1);
        
        console.log("Editors initialized successfully");
    } catch (error) {
        console.error("Error initializing editors:", error);
        showAlert("Could not initialize code editors. Please check console for details.", "error");
        return; // Stop initialization if editors failed
    }
    
    // Language change handler
    document.getElementById('languageSelect').addEventListener('change', function() {
        var language = this.value;
        editor.session.setMode("ace/mode/" + language);
        outputEditor.session.setMode("ace/mode/" + language);
    });
    
    // Beautify button handler
    document.getElementById('beautifyButton').addEventListener('click', function() {
        try {
            var code = editor.getValue();
            if (!code.trim()) {
                showAlert("Please enter some code first", "error");
                return;
            }
            
            var language = document.getElementById('languageSelect').value;
            var indentSize = parseInt(document.getElementById('indentSize').value);
            var options = {
                indent_size: indentSize,
                indent_char: ' '
            };
            
            var beautified = "";
            
            switch (language) {
                case 'html':
                    beautified = html_beautify(code, options);
                    break;
                case 'css':
                    beautified = css_beautify(code, options);
                    break;
                case 'javascript':
                    beautified = js_beautify(code, options);
                    break;
            }
            
            outputEditor.setValue(beautified, -1);
            showAlert("Code beautified successfully!");
        } catch (error) {
            console.error("Beautify error:", error);
            showAlert("Error beautifying code: " + error.message, "error");
        }
    });
    
    // Minify button handler
    document.getElementById('minifyButton').addEventListener('click', function() {
        try {
            var code = editor.getValue();
            if (!code.trim()) {
                showAlert("Please enter some code first", "error");
                return;
            }
            
            var language = document.getElementById('languageSelect').value;
            var minified = "";
            
            switch (language) {
                case 'html':
                    minified = HTMLMinifier.minify(code, {
                        removeComments: true,
                        collapseWhitespace: true,
                        minifyJS: true,
                        minifyCSS: true
                    });
                    break;
                case 'css':
                    minified = csso.minify(code).css;
                    break;
                case 'javascript':
                    var result = Terser.minify(code);
                    if (result.error) throw result.error;
                    minified = result.code;
                    break;
            }
            
            outputEditor.setValue(minified, -1);
            showAlert("Code minified successfully!");
        } catch (error) {
            console.error("Minify error:", error);
            showAlert("Error minifying code: " + error.message, "error");
        }
    });
    
    // Copy button handler with mobile optimization
    document.getElementById('copyButton').addEventListener('click', function() {
        try {
            var text = outputEditor.getValue();
            if (!text.trim()) {
                showAlert("Nothing to copy!", "error");
                return;
            }
            
            // For mobile browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';  // Avoid scrolling to bottom
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showAlert("Copied to clipboard!");
                } else {
                    // Fallback to modern API if available
                    navigator.clipboard.writeText(text)
                        .then(() => showAlert("Copied to clipboard!"))
                        .catch(err => {
                            console.error("Copy error:", err);
                            showAlert("Error copying to clipboard", "error");
                        });
                }
            } catch (err) {
                // Try modern API as fallback
                navigator.clipboard.writeText(text)
                    .then(() => showAlert("Copied to clipboard!"))
                    .catch(err => {
                        console.error("Copy error:", err);
                        showAlert("Error copying to clipboard", "error");
                    });
            }
            
            document.body.removeChild(textArea);
        } catch (error) {
            console.error("Copy error:", error);
            showAlert("Error copying to clipboard", "error");
        }
    });
    
    // Clear button handler
    document.getElementById('clearButton').addEventListener('click', function() {
        editor.setValue("");
        outputEditor.setValue("");
        showAlert("Editors cleared!");
    });
    
    // Mobile-optimized SweetAlert notification
    function showAlert(message, type = "success") {
        const Toast = Swal.mixin({
            toast: true,
            position: isMobile ? 'bottom' : 'bottom-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            width: isMobile ? 'auto' : null,
            customClass: {
                container: isMobile ? 'my-swal-container' : '',
                popup: isMobile ? 'my-swal-popup' : '',
            }
        });
        
        Toast.fire({
            icon: type,
            title: message
        });
    }
    
    // Handle window resize to adjust editor size
    window.addEventListener('resize', function() {
        const newIsMobile = window.innerWidth < 768;
        if (newIsMobile !== isMobile) {
            // Refresh the page to reinitialize editors with new settings
            // Or you could adjust settings here without reload
            editor.setOptions({
                fontSize: newIsMobile ? "12px" : "14px",
                enableLiveAutocompletion: !newIsMobile
            });
            
            outputEditor.setOptions({
                fontSize: newIsMobile ? "12px" : "14px"
            });
        }
    });
});
</script>
@endsection