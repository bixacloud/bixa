@extends('layouts.master')

@section('title') Base64 Encoder & Decoder @endsection

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
    
    .tool-container {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .text-area {
        width: 100%;
        min-height: 150px;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        margin-bottom: 15px;
        font-family: monospace;
    }
    
    .action-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        gap: 10px;
    }
    
    .file-upload-container {
        margin-top: 20px;
        padding: 15px;
        border: 2px dashed #ced4da;
        border-radius: 4px;
        text-align: center;
    }
    
    .file-upload-input {
        display: none;
    }
    
    .drag-drop-area {
        padding: 30px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .drag-drop-area:hover, .drag-drop-area.dragover {
        background-color: #e9ecef;
    }
    
    .nav-tabs .nav-link {
        cursor: pointer;
    }
    
    /* Copy button styling */
    .copy-btn {
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        color: #4338ca !important;
        padding: 6px 8px !important;
        transition: transform 0.2s, color 0.2s;
        border-radius: 4px;
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
    
    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
        }
        
        .action-buttons .btn {
            margin-bottom: 10px;
        }
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Tools @endslot
        @slot('title') Base64 Encoder & Decoder @endslot
    @endcomponent

    <!-- Header Section -->
    <div class="header-section text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-4 header-title">Base64 Encoder & Decoder</h1>
            <p class="lead mb-4">Encode or decode text and files using Base64 format</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="bx bx-lock me-2"></i>
                        Base64 Converter
                    </div>
                    <div class="card-body">
                        <!-- Mode Selection Tabs -->
                        <ul class="nav nav-tabs mb-3" id="conversionTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="text-tab" data-bs-toggle="tab" data-bs-target="#textConversion" type="button" role="tab">Text Conversion</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="file-tab" data-bs-toggle="tab" data-bs-target="#fileConversion" type="button" role="tab">File Conversion</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="image-tab" data-bs-toggle="tab" data-bs-target="#imageConversion" type="button" role="tab">Image Conversion</button>
                            </li>
                        </ul>
                        
                        <!-- Tab Content -->
                        <div class="tab-content" id="conversionTabContent">
                            <!-- Text Conversion Tab -->
                            <div class="tab-pane fade show active" id="textConversion" role="tabpanel">
                                <div class="tool-container">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="btn-group w-100" role="group">
                                                <button type="button" class="btn btn-primary active" id="encodeTextBtn" onclick="setTextConversionMode('encode')">Encode to Base64</button>
                                                <button type="button" class="btn btn-outline-primary" id="decodeTextBtn" onclick="setTextConversionMode('decode')">Decode from Base64</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="inputText" class="form-label" id="inputTextLabel">Input Text (to encode)</label>
                                        <textarea id="inputText" class="text-area" placeholder="Enter text to encode to Base64..."></textarea>
                                    </div>
                                    
                                    <div class="text-center mb-3">
                                        <button class="btn btn-success px-4" id="convertTextBtn" onclick="convertText()">
                                            <i class="bx bx-refresh me-1"></i> <span id="convertTextBtnLabel">Encode</span>
                                        </button>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="outputText" class="form-label" id="outputTextLabel">Output Base64</label>
                                        <textarea id="outputText" class="text-area" readonly placeholder="Base64 encoded result will appear here..."></textarea>
                                    </div>
                                    
                                    <div class="action-buttons">
                                        <button class="btn btn-primary" id="copyTextOutputBtn" onclick="copyTextOutput()">
                                            <i class="bx bx-copy me-1"></i> Copy Output
                                        </button>
                                        <button class="btn btn-secondary" onclick="clearTextAreas()">
                                            <i class="bx bx-trash me-1"></i> Clear
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- File Conversion Tab -->
                            <div class="tab-pane fade" id="fileConversion" role="tabpanel">
                                <div class="tool-container">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="btn-group w-100" role="group">
                                                <button type="button" class="btn btn-primary active" id="encodeFileBtn" onclick="setFileConversionMode('encode')">File to Base64</button>
                                                <button type="button" class="btn btn-outline-primary" id="decodeFileBtn" onclick="setFileConversionMode('decode')">Base64 to File</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- File to Base64 Section -->
                                    <div id="fileToBase64Section">
                                        <div class="file-upload-container mb-3">
                                            <div class="drag-drop-area" id="dropZone">
                                                <i class="bx bx-cloud-upload fs-1 text-primary mb-2"></i>
                                                <p>Drag & drop your file here or <label for="fileInput" class="text-primary fw-bold" style="cursor: pointer;">browse</label></p>
                                                <p class="text-muted small">Max file size: 5MB</p>
                                                <input type="file" id="fileInput" class="file-upload-input" onchange="handleFileSelect(event)">
                                            </div>
                                        </div>
                                        
                                        <div id="fileInfo" class="alert alert-info mb-3 d-none">
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-file me-2 fs-4"></i>
                                                <div>
                                                    <div id="fileName">filename.txt</div>
                                                    <div id="fileSize" class="text-muted small">0 bytes</div>
                                                </div>
                                                <button type="button" class="btn-close ms-auto" aria-label="Close" onclick="clearFileSelection()"></button>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center mb-3">
                                            <button class="btn btn-success px-4" id="convertFileBtn" onclick="convertFile()" disabled>
                                                <i class="bx bx-refresh me-1"></i> <span id="convertFileBtnLabel">Encode File</span>
                                            </button>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="fileOutputText" class="form-label">Base64 Output</label>
                                            <textarea id="fileOutputText" class="text-area" readonly placeholder="Base64 encoded file will appear here..."></textarea>
                                        </div>
                                        
                                        <div class="action-buttons">
                                            <button class="btn btn-primary" id="copyFileOutputBtn" onclick="copyFileOutput()">
                                                <i class="bx bx-copy me-1"></i> Copy Output
                                            </button>
                                            <button class="btn btn-secondary" onclick="clearFileSection()">
                                                <i class="bx bx-trash me-1"></i> Clear
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Base64 to File Section -->
                                    <div id="base64ToFileSection" class="d-none">
                                        <div class="mb-3">
                                            <label for="base64Input" class="form-label">Base64 Input</label>
                                            <textarea id="base64Input" class="text-area" placeholder="Paste Base64 encoded data here..."></textarea>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="outputFileName" class="form-label">Output Filename</label>
                                                <input type="text" class="form-control" id="outputFileName" placeholder="Enter filename with extension (e.g., file.pdf)">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="mimeType" class="form-label">MIME Type (Optional)</label>
                                                <input type="text" class="form-control" id="mimeType" placeholder="E.g., application/pdf">
                                            </div>
                                        </div>
                                        
                                        <div class="text-center mb-3">
                                            <button class="btn btn-success px-4" onclick="base64ToFile()">
                                                <i class="bx bx-download me-1"></i> Download File
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Image Conversion Tab -->
                            <div class="tab-pane fade" id="imageConversion" role="tabpanel">
                                <div class="tool-container">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="btn-group w-100" role="group">
                                                <button type="button" class="btn btn-primary active" id="imageToBase64Btn" onclick="setImageConversionMode('encode')">Image to Base64</button>
                                                <button type="button" class="btn btn-outline-primary" id="base64ToImageBtn" onclick="setImageConversionMode('decode')">Base64 to Image</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Image to Base64 Section -->
                                    <div id="imageToBase64Section">
                                        <div class="file-upload-container mb-3">
                                            <div class="drag-drop-area" id="imageDropZone">
                                                <i class="bx bx-image fs-1 text-primary mb-2"></i>
                                                <p>Drag & drop your image here or <label for="imageInput" class="text-primary fw-bold" style="cursor: pointer;">browse</label></p>
                                                <p class="text-muted small">Supported formats: JPG, PNG, GIF, WEBP (Max: 2MB)</p>
                                                <input type="file" id="imageInput" class="file-upload-input" accept="image/*" onchange="handleImageSelect(event)">
                                            </div>
                                        </div>
                                        
                                        <div id="imagePreviewContainer" class="text-center mb-3 d-none">
                                            <h6>Image Preview:</h6>
                                            <img id="imagePreview" class="img-fluid mb-2" style="max-height: 200px; border: 1px solid #ced4da; border-radius: 4px;">
                                            <div id="imageInfo" class="text-muted small">
                                                <span id="imageName">image.jpg</span> - <span id="imageSize">0 KB</span> - <span id="imageDimensions">0×0</span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="clearImageSelection()">
                                                <i class="bx bx-x"></i> Remove
                                            </button>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="includeImageHeader" checked>
                                                <label class="form-check-label" for="includeImageHeader">
                                                    Include Data URL header (e.g., data:image/jpeg;base64,)
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="text-center mb-3">
                                            <button class="btn btn-success px-4" id="convertImageBtn" onclick="convertImage()" disabled>
                                                <i class="bx bx-refresh me-1"></i> Convert to Base64
                                            </button>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="imageBase64Output" class="form-label">Base64 Output</label>
                                            <textarea id="imageBase64Output" class="text-area" readonly placeholder="Base64 encoded image will appear here..."></textarea>
                                        </div>
                                        
                                        <div class="action-buttons">
                                            <button class="btn btn-primary" id="copyImageOutputBtn" onclick="copyImageOutput()">
                                                <i class="bx bx-copy me-1"></i> Copy Output
                                            </button>
                                            <button class="btn btn-secondary" onclick="clearImageSection()">
                                                <i class="bx bx-trash me-1"></i> Clear
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Base64 to Image Section -->
                                    <div id="base64ToImageSection" class="d-none">
                                        <div class="mb-3">
                                            <label for="imageBase64Input" class="form-label">Base64 Input</label>
                                            <textarea id="imageBase64Input" class="text-area" placeholder="Paste Base64 encoded image data here..."></textarea>
                                            <div class="form-text">
                                                You can include or exclude the data URL header (data:image/jpeg;base64,)
                                            </div>
                                        </div>
                                        
                                        <div class="text-center mb-3">
                                            <button class="btn btn-success px-4" onclick="base64ToImage()">
                                                <i class="bx bx-refresh me-1"></i> Convert to Image
                                            </button>
                                        </div>
                                        
                                        <div id="base64ImagePreviewContainer" class="text-center mb-3 d-none">
                                            <h6>Image Preview:</h6>
                                            <img id="base64ImagePreview" class="img-fluid mb-2" style="max-height: 300px; border: 1px solid #ced4da; border-radius: 4px;">
                                            <div class="mt-3">
                                                <button class="btn btn-primary" onclick="downloadBase64Image()">
                                                    <i class="bx bx-download me-1"></i> Download Image
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Info Section -->
                        <div class="card bg-light mt-4">
                            <div class="card-body">
                                <h5><i class="bx bx-info-circle text-primary me-2"></i> About Base64 Encoding</h5>
                                <p>Base64 is a binary-to-text encoding scheme that represents binary data in an ASCII string format by translating it into a radix-64 representation.</p>
                                <p>Common use cases include:</p>
                                <ul>
                                    <li>Embedding binary data (images, files) in text-based formats like HTML, CSS, or JSON</li>
                                    <li>Transmitting binary data in environments that only support text</li>
                                    <li>Embedding small images directly in web pages using data URLs</li>
                                    <li>Storing binary data in text databases</li>
                                </ul>
                                <div class="alert alert-warning mt-2">
                                    <i class="bx bx-shield-quarter me-2"></i>
                                    <strong>Note:</strong> Base64 encoding is not encryption and doesn't provide any security. It's merely a way to represent binary data in ASCII format.
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

<script>
    // Global variables
    let textConversionMode = 'encode';
    let fileConversionMode = 'encode';
    let imageConversionMode = 'encode';
    let selectedFile = null;
    let selectedImage = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Set up drag and drop for files
        setupDragDrop('dropZone', 'fileInput');
        setupDragDrop('imageDropZone', 'imageInput');
    });

    /**
     * Set up drag and drop functionality
     * @param {string} dropZoneId - ID of the drop zone element
     * @param {string} inputId - ID of the file input element
     */
    function setupDragDrop(dropZoneId, inputId) {
        const dropZone = document.getElementById(dropZoneId);
        const fileInput = document.getElementById(inputId);
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            dropZone.classList.add('dragover');
        }
        
        function unhighlight() {
            dropZone.classList.remove('dragover');
        }
        
        dropZone.addEventListener('click', () => {
            fileInput.click();
        });
        
        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                fileInput.files = files;
                const event = new Event('change');
                fileInput.dispatchEvent(event);
            }
        });
    }

    /**
     * Set text conversion mode (encode/decode)
     * @param {string} mode - 'encode' or 'decode'
     */
    function setTextConversionMode(mode) {
        textConversionMode = mode;
        
        // Update button states
        if (mode === 'encode') {
            document.getElementById('encodeTextBtn').classList.add('active');
            document.getElementById('encodeTextBtn').classList.remove('btn-outline-primary');
            document.getElementById('encodeTextBtn').classList.add('btn-primary');
            
            document.getElementById('decodeTextBtn').classList.remove('active');
            document.getElementById('decodeTextBtn').classList.add('btn-outline-primary');
            document.getElementById('decodeTextBtn').classList.remove('btn-primary');
            
            document.getElementById('inputTextLabel').textContent = 'Input Text (to encode)';
            document.getElementById('outputTextLabel').textContent = 'Output Base64';
            document.getElementById('convertTextBtnLabel').textContent = 'Encode';
            document.getElementById('inputText').placeholder = 'Enter text to encode to Base64...';
            document.getElementById('outputText').placeholder = 'Base64 encoded result will appear here...';
        } else {
            document.getElementById('decodeTextBtn').classList.add('active');
            document.getElementById('decodeTextBtn').classList.remove('btn-outline-primary');
            document.getElementById('decodeTextBtn').classList.add('btn-primary');
            
            document.getElementById('encodeTextBtn').classList.remove('active');
            document.getElementById('encodeTextBtn').classList.add('btn-outline-primary');
            document.getElementById('encodeTextBtn').classList.remove('btn-primary');
            
            document.getElementById('inputTextLabel').textContent = 'Input Base64 (to decode)';
            document.getElementById('outputTextLabel').textContent = 'Output Text';
            document.getElementById('convertTextBtnLabel').textContent = 'Decode';
            document.getElementById('inputText').placeholder = 'Enter Base64 to decode...';
            document.getElementById('outputText').placeholder = 'Decoded text result will appear here...';
        }
    }
    
    /**
     * Set file conversion mode (encode/decode)
     * @param {string} mode - 'encode' or 'decode'
     */
    function setFileConversionMode(mode) {
        fileConversionMode = mode;
        
        if (mode === 'encode') {
            document.getElementById('encodeFileBtn').classList.add('active');
            document.getElementById('encodeFileBtn').classList.remove('btn-outline-primary');
            document.getElementById('encodeFileBtn').classList.add('btn-primary');
            
            document.getElementById('decodeFileBtn').classList.remove('active');
            document.getElementById('decodeFileBtn').classList.add('btn-outline-primary');
            document.getElementById('decodeFileBtn').classList.remove('btn-primary');
            
            document.getElementById('fileToBase64Section').classList.remove('d-none');
            document.getElementById('base64ToFileSection').classList.add('d-none');
        } else {
            document.getElementById('decodeFileBtn').classList.add('active');
            document.getElementById('decodeFileBtn').classList.remove('btn-outline-primary');
            document.getElementById('decodeFileBtn').classList.add('btn-primary');
            
            document.getElementById('encodeFileBtn').classList.remove('active');
            document.getElementById('encodeFileBtn').classList.add('btn-outline-primary');
            document.getElementById('encodeFileBtn').classList.remove('btn-primary');
            
            document.getElementById('fileToBase64Section').classList.add('d-none');
            document.getElementById('base64ToFileSection').classList.remove('d-none');
        }
    }
    
    /**
     * Set image conversion mode (encode/decode)
     * @param {string} mode - 'encode' or 'decode'
     */
    function setImageConversionMode(mode) {
        imageConversionMode = mode;
        
        if (mode === 'encode') {
            document.getElementById('imageToBase64Btn').classList.add('active');
            document.getElementById('imageToBase64Btn').classList.remove('btn-outline-primary');
            document.getElementById('imageToBase64Btn').classList.add('btn-primary');
            
            document.getElementById('base64ToImageBtn').classList.remove('active');
            document.getElementById('base64ToImageBtn').classList.add('btn-outline-primary');
            document.getElementById('base64ToImageBtn').classList.remove('btn-primary');
            
            document.getElementById('imageToBase64Section').classList.remove('d-none');
            document.getElementById('base64ToImageSection').classList.add('d-none');
        } else {
            document.getElementById('base64ToImageBtn').classList.add('active');
            document.getElementById('base64ToImageBtn').classList.remove('btn-outline-primary');
            document.getElementById('base64ToImageBtn').classList.add('btn-primary');
            
            document.getElementById('imageToBase64Btn').classList.remove('active');
            document.getElementById('imageToBase64Btn').classList.add('btn-outline-primary');
            document.getElementById('imageToBase64Btn').classList.remove('btn-primary');
            
            document.getElementById('imageToBase64Section').classList.add('d-none');
            document.getElementById('base64ToImageSection').classList.remove('d-none');
        }
    }
    
    /**
     * Convert text based on the current mode
     */
    function convertText() {
        const inputText = document.getElementById('inputText').value;
        if (!inputText.trim()) {
            showAlert('Please enter some text first', 'error');
            return;
        }
        
        try {
            let result = '';
            if (textConversionMode === 'encode') {
                // Encode text to Base64
                result = btoa(unescape(encodeURIComponent(inputText)));
            } else {
                // Decode Base64 to text
                result = decodeURIComponent(escape(atob(inputText.trim())));
            }
            
            document.getElementById('outputText').value = result;
            showAlert(`Text ${textConversionMode}d successfully!`);
        } catch (error) {
            showAlert(`Error ${textConversionMode}ing text: ${error.message}`, 'error');
        }
    }
    
    /**
     * Handle file selection
     */
    function handleFileSelect(event) {
        const file = event.target.files[0];
        
        if (!file) return;
        
        // Check file size (5MB limit)
        if (file.size > 5 * 1024 * 1024) {
            showAlert('File is too large. Maximum size is 5MB.', 'error');
            return;
        }
        
        selectedFile = file;
        
        // Show file info
        document.getElementById('fileInfo').classList.remove('d-none');
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = formatBytes(file.size);
        
        // Enable convert button
        document.getElementById('convertFileBtn').disabled = false;
    }
    
    /**
     * Convert file to Base64
     */
    function convertFile() {
        if (!selectedFile) {
            showAlert('Please select a file first', 'error');
            return;
        }
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const base64String = e.target.result;
            // If it's a data URL, extract just the Base64 part
            const result = base64String.split(',')[1] || base64String;
            document.getElementById('fileOutputText').value = result;
            showAlert('File encoded to Base64 successfully!');
        };
        
        reader.onerror = function() {
            showAlert('Error reading the file', 'error');
        };
        
        reader.readAsDataURL(selectedFile);
    }
    
    /**
     * Clear file selection
     */
    function clearFileSelection() {
        selectedFile = null;
        document.getElementById('fileInput').value = '';
        document.getElementById('fileInfo').classList.add('d-none');
        document.getElementById('convertFileBtn').disabled = true;
    }
    
    /**
     * Convert Base64 to file and download
     */
    function base64ToFile() {
        const base64Input = document.getElementById('base64Input').value.trim();
        const outputFileName = document.getElementById('outputFileName').value.trim();
        let mimeType = document.getElementById('mimeType').value.trim();
        
        if (!base64Input) {
            showAlert('Please enter Base64 data', 'error');
            return;
        }
        
        if (!outputFileName) {
            showAlert('Please specify a filename', 'error');
            return;
        }
        
        // If no MIME type provided, try to guess from the filename
        if (!mimeType) {
            const extension = outputFileName.split('.').pop().toLowerCase();
            mimeType = getMimeTypeFromExtension(extension);
        }
        
        try {
            // Parse input (handle with or without data URL prefix)
            let base64Data = base64Input;
            if (base64Input.includes(',')) {
                base64Data = base64Input.split(',')[1];
            }
            
            // Convert Base64 to binary
            const binaryString = atob(base64Data);
            const bytes = new Uint8Array(binaryString.length);
            for (let i = 0; i < binaryString.length; i++) {
                bytes[i] = binaryString.charCodeAt(i);
            }
            
            // Create Blob and download
            const blob = new Blob([bytes], { type: mimeType });
            const url = URL.createObjectURL(blob);
            
            const a = document.createElement('a');
            a.href = url;
            a.download = outputFileName;
            document.body.appendChild(a);
            a.click();
            
            // Clean up
            setTimeout(() => {
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 100);
            
            showAlert('File created and downloaded successfully!');
        } catch (error) {
            showAlert(`Error creating file: ${error.message}`, 'error');
        }
    }
    
    /**
     * Handle image selection
     */
    function handleImageSelect(event) {
        const file = event.target.files[0];
        
        if (!file) return;
        
        // Check if it's an image
        if (!file.type.startsWith('image/')) {
            showAlert('Selected file is not an image', 'error');
            return;
        }
        
        // Check file size (2MB limit)
        if (file.size > 2 * 1024 * 1024) {
            showAlert('Image is too large. Maximum size is 2MB.', 'error');
            return;
        }
        
        selectedImage = file;
        
        // Show image preview
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = document.getElementById('imagePreview');
            img.src = e.target.result;
            
            // Show image info once it's loaded
            img.onload = function() {
                document.getElementById('imagePreviewContainer').classList.remove('d-none');
                document.getElementById('imageName').textContent = file.name;
                document.getElementById('imageSize').textContent = formatBytes(file.size);
                document.getElementById('imageDimensions').textContent = `${img.naturalWidth}×${img.naturalHeight}`;
            };
        };
        
        reader.readAsDataURL(file);
        
        // Enable convert button
        document.getElementById('convertImageBtn').disabled = false;
    }
    
    /**
     * Clear image selection
     */
    function clearImageSelection() {
        selectedImage = null;
        document.getElementById('imageInput').value = '';
        document.getElementById('imagePreviewContainer').classList.add('d-none');
        document.getElementById('convertImageBtn').disabled = true;
    }
    
    /**
     * Convert image to Base64
     */
    function convertImage() {
        if (!selectedImage) {
            showAlert('Please select an image first', 'error');
            return;
        }
        
        const reader = new FileReader();
        const includeHeader = document.getElementById('includeImageHeader').checked;
        
        reader.onload = function(e) {
            let result = e.target.result;
            
            // Remove header if not wanted
            if (!includeHeader && result.includes(',')) {
                result = result.split(',')[1];
            }
            
            document.getElementById('imageBase64Output').value = result;
            showAlert('Image encoded to Base64 successfully!');
        };
        
        reader.onerror = function() {
            showAlert('Error reading the image', 'error');
        };
        
        reader.readAsDataURL(selectedImage);
    }
    
    /**
     * Convert Base64 to image and show preview
     */
    function base64ToImage() {
        let base64Input = document.getElementById('imageBase64Input').value.trim();
        
        if (!base64Input) {
            showAlert('Please enter Base64 image data', 'error');
            return;
        }
        
        try {
            // Check if it already has a data URL prefix
            if (!base64Input.startsWith('data:image/')) {
                // Add a generic image data URL prefix
                base64Input = `data:image/png;base64,${base64Input}`;
            }
            
            // Test if valid by creating an image
            const img = document.getElementById('base64ImagePreview');
            img.src = base64Input;
            
            // Show preview when loaded
            img.onload = function() {
                document.getElementById('base64ImagePreviewContainer').classList.remove('d-none');
                showAlert('Base64 converted to image successfully!');
            };
            
            img.onerror = function() {
                showAlert('The Base64 data is not a valid image', 'error');
            };
        } catch (error) {
            showAlert(`Error converting Base64 to image: ${error.message}`, 'error');
        }
    }
    
    /**
     * Download the Base64 image
     */
    function downloadBase64Image() {
        const imgSrc = document.getElementById('base64ImagePreview').src;
        
        if (!imgSrc || imgSrc === window.location.href) {
            showAlert('No image to download', 'error');
            return;
        }
        
        // Create a link and trigger download
        const a = document.createElement('a');
        a.href = imgSrc;
        a.download = 'image.' + getImageTypeFromDataURL(imgSrc);
        document.body.appendChild(a);
        a.click();
        
        // Clean up
        setTimeout(() => {
            document.body.removeChild(a);
        }, 100);
        
        showAlert('Image downloaded successfully!');
    }
    
    /**
     * Extract image type from a data URL
     * @param {string} dataURL - The data URL
     * @returns {string} - The image extension
     */
    function getImageTypeFromDataURL(dataURL) {
        const matches = dataURL.match(/^data:image\/([a-zA-Z0-9]+);base64,/);
        return matches ? matches[1] : 'png'; // Default to PNG if not found
    }
    
    /**
     * Get MIME type from file extension
     * @param {string} extension - File extension
     * @returns {string} - MIME type
     */
    function getMimeTypeFromExtension(extension) {
        const mimeTypes = {
            'txt': 'text/plain',
            'html': 'text/html',
            'css': 'text/css',
            'js': 'application/javascript',
            'json': 'application/json',
            'pdf': 'application/pdf',
            'zip': 'application/zip',
            'jpg': 'image/jpeg',
            'jpeg': 'image/jpeg',
            'png': 'image/png',
            'gif': 'image/gif',
            'svg': 'image/svg+xml',
            'ico': 'image/x-icon',
            'mp3': 'audio/mpeg',
            'mp4': 'video/mp4',
            'wav': 'audio/wav',
            'doc': 'application/msword',
            'docx': 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls': 'application/vnd.ms-excel',
            'xlsx': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt': 'application/vnd.ms-powerpoint',
            'pptx': 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xml': 'application/xml',
            'csv': 'text/csv'
        };
        
        return mimeTypes[extension] || 'application/octet-stream';
    }
    
    /**
     * Format bytes to human-readable format
     * @param {number} bytes - Number of bytes
     * @returns {string} - Formatted size
     */
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
    
    /**
     * Copy text output to clipboard
     */
    function copyTextOutput() {
        const text = document.getElementById('outputText').value;
        const button = document.getElementById('copyTextOutputBtn');
        copyToClipboard(text, button);
    }
    
    /**
     * Copy file output to clipboard
     */
    function copyFileOutput() {
        const text = document.getElementById('fileOutputText').value;
        const button = document.getElementById('copyFileOutputBtn');
        copyToClipboard(text, button);
    }
    
    /**
     * Copy image output to clipboard
     */
    function copyImageOutput() {
        const text = document.getElementById('imageBase64Output').value;
        const button = document.getElementById('copyImageOutputBtn');
        copyToClipboard(text, button);
    }
    
    /**
     * Clear text areas
     */
    function clearTextAreas() {
        document.getElementById('inputText').value = '';
        document.getElementById('outputText').value = '';
        showAlert('Text areas cleared');
    }
    
    /**
     * Clear file section
     */
    function clearFileSection() {
        clearFileSelection();
        document.getElementById('fileOutputText').value = '';
        showAlert('File section cleared');
    }
    
    /**
     * Clear image section
     */
    function clearImageSection() {
        clearImageSelection();
        document.getElementById('imageBase64Output').value = '';
        showAlert('Image section cleared');
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
     * Copy text to clipboard with visual feedback
     */
    function copyToClipboard(text, button = null) {
        if (!text) {
            showAlert('Nothing to copy. Text is empty.', 'error');
            return;
        }
        
        // Use modern clipboard API when available
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text)
                .then(() => {
                    showSuccessAnimation(button);
                    showAlert(`Copied to clipboard!`);
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
                showAlert(`Copied to clipboard!`);
            } else {
                showAlert('Copy failed. Please try again.', 'error');
            }
        } catch (err) {
            console.error("Fallback copy error:", err);
            showAlert('Copy failed. Please try again.', 'error');
        }
    }
</script>
@endsection