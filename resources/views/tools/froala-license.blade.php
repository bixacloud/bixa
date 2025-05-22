@extends('layouts.master')

@section('title') Froala License Generator @endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.21.0/dist/sweetalert2.min.css">
<!-- Froala Editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/plugins/char_counter.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/themes/royal.min.css">
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
    
    .feature-badge {
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 50px;
        padding: 0.5rem 1rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        display: inline-flex;
        align-items: center;
    }
    
    .feature-badge i {
        color: #4ade80;
        margin-right: 0.5rem;
    }
    
    .code-block {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        padding: 1rem;
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    pre {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.9rem;
        margin: 0;
        padding: 0;
        white-space: pre-wrap;
        word-break: break-all;
        padding-right: 40px;
    }
    
    .license-key-display {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 12px;
        position: relative;
        font-family: monospace;
        word-break: break-all;
        margin-bottom: 15px;
        overflow: hidden;
        line-height: 1.5;
        padding-right: 40px;
    }
    
    /* Updated copy button style to match file 1 */
    .copy-btn {
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        color: #4338ca !important;
        padding: 6px 8px !important;
        transition: transform 0.2s, color 0.2s;
        border-radius: 4px;
        position: absolute;
        top: 8px;
        right: 8px;
        cursor: pointer;
        z-index: 5;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .copy-btn:hover {
        color: #312e81 !important;
        transform: scale(1.1);
        background-color: rgba(99, 102, 241, 0.1) !important;
    }
    
    .copy-btn:focus, 
    .copy-btn:active {
        outline: none !important;
        box-shadow: none !important;
    }
    
    .copy-btn i {
        font-size: 18px;
    }
    
    /* Animation for copy success */
    @keyframes copiedAnimation {
        0% { transform: scale(1); color: #4338ca; }
        50% { transform: scale(1.2); color: #10b981; }
        100% { transform: scale(1); color: #4338ca; }
    }
    
    .copied-animation {
        animation: copiedAnimation 0.8s ease;
    }
    
    .editor-container {
        position: relative;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        overflow: hidden;
    }
    
    #froalaEditor {
        height: 350px;
    }
    
    .plugin-container {
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 10px;
        margin-bottom: 10px;
    }

    .plugin-container:hover {
        background-color: #f8f9fa;
    }
    
    .plugin-title {
        font-weight: 600;
        margin-bottom: 4px;
    }
    
    .plugin-description {
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .step-number {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 40px;
        height: 40px;
        background-color: #4338ca;
        color: white;
        border-radius: 50%;
        margin-right: 1rem;
        font-size: 1.25rem;
        font-weight: bold;
    }
    
    .step-title {
        font-weight: 600;
        font-size: 1.25rem;
        display: inline-block;
        vertical-align: middle;
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Tools @endslot
        @slot('title') Froala License Generator @endslot
    @endcomponent

    <!-- Header Section -->
    <div class="header-section text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-4 header-title">Froala Editor License Generator</h1>
            <p class="lead mb-4">Create custom license keys for Froala WYSIWYG HTML Editor with all features unlocked</p>
            <div class="d-flex flex-wrap justify-content-center">
                <span class="feature-badge">
                    <i class="bx bx-check-circle"></i> Custom Owner Names
                </span>
                <span class="feature-badge">
                    <i class="bx bx-check-circle"></i> All Features Unlocked
                </span>
                <span class="feature-badge">
                    <i class="bx bx-check-circle"></i> Third-Party Integrations
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Generator Column -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <div>
                        <i class="bx bx-key me-2"></i>
                        License Generator
                    </div>
                    <span class="badge bg-success">Free</span>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bx bx-error-circle me-2"></i>
                            {{ $errors->first() }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form id="licenseForm" action="{{ route('tools.froala-license.generate') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Owner Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', 'BIXA') }}" required>
                            <div class="form-text">Your name will be embedded in the license</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="year" class="form-label">Expiration Year</label>
                            <input type="number" class="form-control" id="year" name="year" value="{{ old('year', '2099') }}" min="2025" required>
                            <div class="form-text">Year when the license will expire</div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Third-Party Plugins</label>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input plugin-checkbox" type="checkbox" id="code_mirror" name="plugins[]" value="code_mirror">
                                        <label class="form-check-label" for="code_mirror">
                                            Code Mirror
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input plugin-checkbox" type="checkbox" id="font_awesome" name="plugins[]" value="font_awesome">
                                        <label class="form-check-label" for="font_awesome">
                                            Font Awesome
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input plugin-checkbox" type="checkbox" id="embedly" name="plugins[]" value="embedly">
                                        <label class="form-check-label" for="embedly">
                                            Embed.ly
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input plugin-checkbox" type="checkbox" id="tui_image_editor" name="plugins[]" value="tui_image_editor">
                                        <label class="form-check-label" for="tui_image_editor">
                                            TUI Image Editor
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input plugin-checkbox" type="checkbox" id="tribute" name="plugins[]" value="tribute">
                                        <label class="form-check-label" for="tribute">
                                            Tribute.js
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input plugin-checkbox" type="checkbox" id="spell_checker" name="plugins[]" value="spell_checker">
                                        <label class="form-check-label" for="spell_checker">
                                            WProofreader
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="errorAlert" class="alert alert-danger d-none">
                            <i class="bx bx-error-circle me-2"></i>
                            <span id="errorMessage"></span>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-cog me-2"></i> Generate License Key
                        </button>
                    </form>

                    <!-- License Key Result -->
                    <div id="licenseResult" class="{{ session('license_key') ? '' : 'd-none' }} mt-4">
                        <h5 class="d-flex align-items-center mb-3">
                            <i class="bx bx-key text-primary me-2"></i>
                            Generated License Key
                        </h5>
                        
                        <div class="license-key-display">
                            <span id="licenseOutput">{{ session('license_key') }}</span>
                            <button id="copyLicenseBtn" class="copy-btn" title="Copy to clipboard" type="button">
                                <i class="bx bx-copy"></i>
                            </button>
                        </div>
                        
                        <div class="alert alert-success">
                            <div class="d-flex">
                                <i class="bx bx-check-circle mt-1 me-2"></i>
                                <span>This license key will unlock all Froala Editor features</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Implementation Guide -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="bx bx-book me-2"></i>
                    Implementation Guide
                </div>
                <div class="card-body">
                    <!-- Basic Implementation Guide -->
                    <div id="basicGuide">
                        <div class="mb-4">
                            <div class="mb-3">
                                <div class="step-number">1</div>
                                <div class="step-title">Include Froala Editor Files</div>
                            </div>
                            <div class="code-block">
                                <pre id="step1Code">&lt;!-- Include Froala CSS files --&gt;
&lt;link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css" rel="stylesheet"&gt;

&lt;!-- Include Froala JS file --&gt;
&lt;script src="https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js"&gt;&lt;/script&gt;</pre>
                                <button class="copy-btn" data-target="step1Code" type="button">
                                    <i class="bx bx-copy"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Third-Party Plugin Files -->
                        <div id="pluginGuide" class="d-none mb-4">
                            <!-- Will be populated with JavaScript -->
                        </div>
                        
                        <div class="mb-4">
                            <div class="mb-3">
                                <div class="step-number" id="containerStep">2</div>
                                <div class="step-title">Add Editor Container</div>
                            </div>
                            <div class="code-block">
                                <pre id="step2Code">&lt;div id="editor"&gt;&lt;/div&gt;</pre>
                                <button class="copy-btn" data-target="step2Code" type="button">
                                    <i class="bx bx-copy"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="mb-3">
                                <div class="step-number" id="initializeStep">3</div>
                                <div class="step-title">Initialize With License Key</div>
                            </div>
                            <div class="code-block">
                                <pre id="implementationCode">// Initialize Froala Editor
new FroalaEditor('#editor', {
  key: "{{ session('license_key') ?: 'YOUR_LICENSE_KEY' }}",
  attribution: false, // hide "Powered by Froala"
  charCounterCount: true // enable word counting
});</pre>
                                <button class="copy-btn" data-target="implementationCode" type="button">
                                    <i class="bx bx-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info d-flex">
                        <i class="bx bx-bulb me-3 fs-5"></i>
                        <div>
                            <strong>Tip:</strong> 
                            <span>To display the word counter, ensure the <code>charCounterCount: true</code> option is included in the initialization config.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Preview Column -->
        <div class="col-lg-6">
            <!-- Preview Editor -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="bx bx-show me-2"></i>
                    Froala Editor Preview
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">This is a live preview of the Froala Editor with your license key:</p>
                    
                    <div class="editor-container mb-3">
                        <div id="froalaEditor">
                            <h3>Froala WYSIWYG Editor</h3>
                            <p>Please click 'Generate License Key' to reload the editor and dismiss the 'Unlicensed copy of Froala Editor. Use it legally by purchasing a license' notice.</p>
                            <p>Try editing this text to test the editor's features!</p>
                        </div>
                    </div>
                    
                    <div id="editorInfo" class="card bg-light">
                        <div class="card-body">
                            <h6><i class="bx bx-info-circle text-primary me-2"></i> Editor Information:</h6>
                            <ul class="mb-0" id="enabledFeaturesList">
                                <li>Rich text formatting</li>
                                <li>Image and media embedding</li>
                                <li>Word and character counting</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Plugin Information -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="bx bx-extension me-2"></i>
                    Third-Party Plugins
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Froala Editor supports these powerful third-party integrations:</p>
                    
                    <div class="accordion" id="pluginAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#codeMirrorInfo">
                                    Code Mirror
                                </button>
                            </h2>
                            <div id="codeMirrorInfo" class="accordion-collapse collapse show" data-bs-parent="#pluginAccordion">
                                <div class="accordion-body">
                                    <p>Enhance HTML code view with syntax highlighting and intelligent indentation. Makes editing HTML much easier with coloring for different code elements.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tuiInfo">
                                    TUI Image Editor
                                </button>
                            </h2>
                            <div id="tuiInfo" class="accordion-collapse collapse" data-bs-parent="#pluginAccordion">
                                <div class="accordion-body">
                                    <p>Advanced image editing directly within the editor, including cropping, rotating, adding text, and much more.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#embedlyInfo">
                                    Embed.ly
                                </button>
                            </h2>
                            <div id="embedlyInfo" class="accordion-collapse collapse" data-bs-parent="#pluginAccordion">
                                <div class="accordion-body">
                                    <p>Embed content from over 500 providers, including YouTube, Vimeo, Twitter, and many other services.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tributeInfo">
                                    Tribute.js
                                </button>
                            </h2>
                            <div id="tributeInfo" class="accordion-collapse collapse" data-bs-parent="#pluginAccordion">
                                <div class="accordion-body">
                                    <p>Add @mentions and autocomplete functionality to your editor. Great for collaborative content creation.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#spellcheckerInfo">
                                    WProofreader
                                </button>
                            </h2>
                            <div id="spellcheckerInfo" class="accordion-collapse collapse" data-bs-parent="#pluginAccordion">
                                <div class="accordion-body">
                                    <p>Advanced spelling and grammar checking directly in your editor. Supports multiple languages and custom dictionaries.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.21.0/dist/sweetalert2.all.min.js"></script>


<!-- Froala Editor JS -->
<script src="https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Global variables
    let editor = null; // Variable to store the editor instance
    let generatedLicense = {{ session('license_key') ? 'true' : 'false' }}; // Flag to track if a license has been generated
    
    /**
     * Copy text to clipboard with visual feedback
     */
    function copyToClipboard(text, button = null) {
        // Use modern clipboard API when available
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text)
                .then(() => {
                    showSuccessAnimation(button);
                    showAlert(`Successfully copied`);
                })
                .catch(err => {
                    console.error("Copy failed:", err);
                    fallbackCopyMethod(text, button);
                });
        } else {
            // Fallback for older browsers
            fallbackCopyMethod(text, button);
        }
    }
    
    /**
     * Fallback copy method for browsers without clipboard API
     */
    function fallbackCopyMethod(text, button) {
        try {
            // Create temporary element
            const textarea = document.createElement('textarea');
            
            // Set its value and attributes
            textarea.value = text;
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            textarea.style.left = '-9999px';
            
            // Add to DOM, select and copy
            document.body.appendChild(textarea);
            textarea.select();
            const success = document.execCommand('copy');
            
            // Remove element
            document.body.removeChild(textarea);
            
            // Show feedback
            if (success) {
                showSuccessAnimation(button);
                showAlert(`Copied: ${text}`);
            } else {
                showAlert('Copy failed. Please try again.', 'error');
            }
        } catch (err) {
            console.error("Fallback copy error:", err);
            showAlert('Copy failed. Please try again.', 'error');
        }
    }
    
    /**
     * Show success animation for a button
     */
    function showSuccessAnimation(button) {
        if (!button) return;
        
        // Store original content
        const originalContent = button.innerHTML;
        
        // Show checkmark icon
        button.innerHTML = '<i class="bx bx-check"></i>';
        
        // Add animation class
        button.classList.add('copied-animation');
        
        // Reset after animation
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.classList.remove('copied-animation');
        }, 800);
    }
    
    /**
     * Display a SweetAlert notification
     */
    function showAlert(message, type = 'success') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        
        Toast.fire({
            icon: type,
            title: message
        });
    }
    
    /**
     * Get list of selected plugins
     * @returns {Array} - List of selected plugin IDs
     */
    function getPluginConfigs() {
        const selectedPlugins = [];
        document.querySelectorAll('.plugin-checkbox:checked').forEach(function(checkbox) {
            selectedPlugins.push(checkbox.id);
        });
        return selectedPlugins;
    }
    
    /**
     * Update plugin guide in implementation steps
     * @param {string} licenseKey - The license key to use in examples
     */
    function updatePluginGuide(licenseKey) {
        const selectedPlugins = getPluginConfigs();
        
        if (selectedPlugins.length > 0) {
            // Update step numbers
            document.getElementById('containerStep').textContent = '3';
            document.getElementById('initializeStep').textContent = '4';
            
            // Show plugin guide section
            document.getElementById('pluginGuide').classList.remove('d-none');
            
            // Build HTML for the plugin guide
            let pluginGuideHtml = `
                <div class="mb-3">
                    <div class="step-number">2</div>
                    <div class="step-title">Add Third-Party Plugin Files</div>
                </div>
                <div class="code-block">
                    <pre id="pluginFilesCode">`;
            
            // Add Code Mirror files if selected
            if (selectedPlugins.includes('code_mirror')) {
                pluginGuideHtml += `&lt;!-- Code Mirror CSS file --&gt;
&lt;link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.18/codemirror.min.css"&gt;

&lt;!-- Code Mirror JS files --&gt;
&lt;script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.18/codemirror.min.js"&gt;&lt;/script&gt;
&lt;script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.18/mode/xml/xml.min.js"&gt;&lt;/script&gt;

&lt;!-- Include Froala Code View plugin --&gt;
&lt;script type="text/javascript" src="https://cdn.jsdelivr.net/npm/froala-editor@latest/js/third_party/image_tui.min.js"&gt;&lt;/script&gt;`;
            }
            
            // Add TUI Image Editor files if selected
            if (selectedPlugins.includes('tui_image_editor')) {
                if (selectedPlugins.includes('code_mirror')) pluginGuideHtml += '\n\n';
                
                pluginGuideHtml += `&lt;!-- Include TUI CSS --&gt;
&lt;link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tui-image-editor@3.15.2/dist/tui-image-editor.css"&gt;
&lt;link rel="stylesheet" href="https://uicdn.toast.com/tui-color-picker/v2.2.7/tui-color-picker.css"&gt;

&lt;!-- Include TUI JS --&gt;
&lt;script type="text/javascript" src="https://cdn.jsdelivr.net/npm/tui-code-snippet@1.5.2/dist/tui-code-snippet.min.js"&gt;&lt;/script&gt;
&lt;script type="text/javascript" src="https://cdn.jsdelivr.net/npm/tui-color-picker@2.2.7/dist/tui-color-picker.min.js"&gt;&lt;/script&gt;
&lt;script type="text/javascript" src="https://cdn.jsdelivr.net/npm/tui-image-editor@3.15.2/dist/tui-image-editor.min.js"&gt;&lt;/script&gt;

&lt;!-- Include TUI plugin for Froala --&gt;
&lt;script type="text/javascript" src="https://cdn.jsdelivr.net/npm/froala-editor@latest/js/third_party/image_tui.min.js"&gt;&lt;/script&gt;`;
            }
            
            // Close the plugin guide HTML
            pluginGuideHtml += `</pre>
                    <button class="copy-btn" data-target="pluginFilesCode" type="button">
                        <i class="bx bx-copy"></i>
                    </button>
                </div>
            `;
            
            // Update the plugin guide in the DOM
            document.getElementById('pluginGuide').innerHTML = pluginGuideHtml;
            
            // Only update implementation code if a license has been generated
            if (generatedLicense) {
                updateImplementationCode(licenseKey);
            }
            
        } else {
            // Reset step numbers for basic guide
            document.getElementById('containerStep').textContent = '2';
            document.getElementById('initializeStep').textContent = '3';
            
            // Hide the plugin guide if no plugins are selected
            document.getElementById('pluginGuide').classList.add('d-none');
            
            // Reset implementation code to basic version if a license has been generated
            if (generatedLicense) {
                updateImplementationCode(document.getElementById('licenseOutput').textContent);
            }
        }
    }
    
    /**
     * Update implementation code with license key
     * @param {string} licenseKey - The license key to use in the code
     */
    function updateImplementationCode(licenseKey) {
        // Create implementation code with the correct license key
        const implementationCode = `// Initialize Froala Editor
new FroalaEditor('#editor', {
  key: "${licenseKey}",
  attribution: false, // hide "Powered by Froala"
  charCounterCount: true // enable word counting
});`;
        
        // Update the implementation code element
        document.getElementById('implementationCode').textContent = implementationCode;
    }
    
    /**
     * Update features list based on selected plugins
     */
    function updateFeaturesList() {
        // Base features always available
        const features = [
            'Rich text formatting',
            'Image and media embedding',
            'Word and character counting'
        ];
        
        // Get selected plugins
        const selectedPlugins = getPluginConfigs();
        
        // Add plugin-specific features to the list
        if (selectedPlugins.includes('code_mirror')) {
            features.push('HTML code view with syntax highlighting');
        }
        
        if (selectedPlugins.includes('tui_image_editor')) {
            features.push('Advanced image editing');
        }
        
        if (selectedPlugins.includes('font_awesome')) {
            features.push('Font Awesome 5 icons');
        }
        
        if (selectedPlugins.includes('embedly')) {
            features.push('Embed.ly content integration');
        }
        
        if (selectedPlugins.includes('tribute')) {
            features.push('@mentions and autocomplete');
        }
        
        if (selectedPlugins.includes('spell_checker')) {
            features.push('Spelling and grammar checking');
        }
        
        // Update the features list in the DOM
        let featuresHtml = '';
        for (const feature of features) {
            featuresHtml += `<li>${feature}</li>`;
        }
        document.getElementById('enabledFeaturesList').innerHTML = featuresHtml;
    }
    
    /**
     * Initialize Froala Editor
     */
    function initializeEditor() {
        // Destroy previous editor instance if it exists
        if (editor) {
            editor.destroy();
        }
        
        // Base configuration - use generated license key if available
        const config = {
            key: generatedLicense ? document.getElementById('licenseOutput').textContent : "YOUR_LICENSE_KEY",
            attribution: false,
            charCounterCount: true,
            charCounterMax: -1,
            placeholderText: 'Start writing your content here...',
            toolbarButtons: [
                'bold', 'italic', 'underline', 'paragraphFormat', 'align', 
                'formatOL', 'formatUL', 'insertLink', 'insertImage', 
                'insertTable', 'html'
            ],
            // Fix for HTML mode issues
            htmlAllowedTags: ['.*'],
            htmlAllowedAttrs: ['.*'],
            htmlRemoveTags: ['script'],
            codeViewKeepActiveButtons: ['html']
        };
        
        // Create new editor instance
        editor = new FroalaEditor('#froalaEditor', config);
    }
    
    // Setup all copy buttons with proper event handling
    function setupCopyButtons() {
        // Get all copy buttons
        const copyButtons = document.querySelectorAll('.copy-btn');
        
        // Add event listener to each button
        copyButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Prevent default action
                e.preventDefault();
                e.stopPropagation();
                
                // Get the data value
                const targetId = this.getAttribute('data-target');
                let valueToCopy = '';
                
                // Check if this is a code block copy button
                if (targetId) {
                    valueToCopy = document.getElementById(targetId).textContent;
                } 
                // Check if this is the license key copy button
                else if (this.id === 'copyLicenseBtn') {
                    valueToCopy = document.getElementById('licenseOutput').textContent;
                }
                
                // Copy to clipboard
                copyToClipboard(valueToCopy, this);
                
                // Prevent default action
                return false;
            });
        });
    }
    
    // Initialize editor with default configuration
    initializeEditor();
    
    // Setup copy buttons on page load
    setupCopyButtons();
    
    // Handle plugin checkbox changes
    document.querySelectorAll('.plugin-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            // Update features list in the UI
            updateFeaturesList();
            
            // Update plugin guide
            updatePluginGuide(document.getElementById('licenseOutput').textContent);
            
            // Reinitialize the demo editor with selected plugins
            initializeEditor();
            
            // Setup copy buttons again after DOM updates
            setTimeout(setupCopyButtons, 100);
        });
    });
    
    // Handle form submission with AJAX
    document.getElementById('licenseForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        const name = formData.get('name').trim();
        const year = formData.get('year').trim();
        
        // Validate input (basic client-side validation)
        if (!name) {
            document.getElementById('errorAlert').classList.remove('d-none');
            document.getElementById('errorMessage').textContent = 'Please enter a name!';
            return;
        }
        
        if (!year || !/^\d{4}$/.test(year) || parseInt(year) < 2025) {
            document.getElementById('errorAlert').classList.remove('d-none');
            document.getElementById('errorMessage').textContent = 'Year must be a 4-digit number greater than or equal to 2025!';
            return;
        }
        
        // Hide error alert
        document.getElementById('errorAlert').classList.add('d-none');
        
        // Show loading indicator
        const submitButton = this.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="bx bx-loader bx-spin me-2"></i> Generating...';
        submitButton.disabled = true;
        
        // Send AJAX request
        fetch('{{ route('tools.froala-license.generate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                name: name,
                year: year
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Get license key from response
                const licenseKey = data.license;
                
                // Set the generated license flag to true
                generatedLicense = true;
                
                // Update UI with license key
                document.getElementById('licenseOutput').textContent = licenseKey;
                document.getElementById('licenseResult').classList.remove('d-none');
                
                // Get selected plugins
                const selectedPlugins = getPluginConfigs();
                
                // Update plugin guide and features list
                updatePluginGuide(licenseKey);
                updateFeaturesList();
                
                // Update implementation code with the license key
                updateImplementationCode(licenseKey);
                
                // Properly delay the editor reinitialization to fix HTML mode issues
                setTimeout(() => {
                    try {
                        initializeEditor();
                    } catch (error) {
                        console.error("Error initializing editor:", error);
                        // If there's an error, try again after 1 second
                        setTimeout(initializeEditor, 1000);
                    }
                }, 500);
                
                // Setup copy buttons again after DOM updates
                setTimeout(setupCopyButtons, 100);
                
                // Show success message
                showAlert(data.message || 'License key generated successfully!');
            } else {
                // Show error message
                document.getElementById('errorAlert').classList.remove('d-none');
                document.getElementById('errorMessage').textContent = data.error || 'Failed to generate license key.';
            }
        })
        .catch(error => {
            // Handle error
            document.getElementById('errorAlert').classList.remove('d-none');
            document.getElementById('errorMessage').textContent = 'Connection error: Could not connect to server. Please try again.';
        })
        .finally(() => {
            // Reset submit button
            submitButton.innerHTML = originalButtonText;
            submitButton.disabled = false;
        });
    });
});
</script>
@endsection