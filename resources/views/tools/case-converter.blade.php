@extends('layouts.master')

@section('title') Case Converter Tool @endsection

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
    }
    
    .case-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .case-button {
        padding: 15px;
        text-align: center;
        cursor: pointer;
        background-color: #e9ecef;
        border: none;
        border-radius: 4px;
        transition: all 0.3s;
    }
    
    .case-button:hover {
        background-color: #dee2e6;
    }
    
    .action-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        gap: 10px;
    }
    
    @media (max-width: 768px) {
        .case-buttons {
            grid-template-columns: 1fr;
        }
        
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
        @slot('title') Case Converter @endslot
    @endcomponent

    <!-- Header Section -->
    <div class="header-section text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-4 header-title">Case Converter Tool</h1>
            <p class="lead mb-4">Convert your text to different case formats with just one click</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="bx bx-text me-2"></i>
                        Text Case Converter
                    </div>
                    <div class="card-body">
                        <div class="tool-container">
                            <textarea id="inputText" class="text-area" placeholder="Enter your text here to convert to different cases..."></textarea>
                            
                            <div class="case-buttons">
                                <button class="case-button" onclick="convertCase('sentence')">Sentence case</button>
                                <button class="case-button" onclick="convertCase('lower')">lower case</button>
                                <button class="case-button" onclick="convertCase('upper')">UPPER CASE</button>
                                <button class="case-button" onclick="convertCase('capitalized')">Capitalized Case</button>
                                <button class="case-button" onclick="convertCase('alternating')">aLtErNaTiNg cAsE</button>
                                <button class="case-button" onclick="convertCase('title')">Title Case</button>
                                <button class="case-button" onclick="convertCase('inverse')">InVeRsE CaSe</button>
                                <button class="case-button" onclick="downloadText()">Download Text</button>
                            </div>
                            
                            <div class="action-buttons">
                                <button class="btn btn-primary" onclick="copyToClipboard()">
                                    <i class="bx bx-copy me-1"></i> Copy to Clipboard
                                </button>
                                <button class="btn btn-secondary" onclick="clearText()">
                                    <i class="bx bx-trash me-1"></i> Clear
                                </button>
                            </div>
                        </div>
                        
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5><i class="bx bx-info-circle text-primary me-2"></i> About Case Converter</h5>
                                <p>This tool allows you to quickly convert text between different case formats:</p>
                                <ul>
                                    <li><strong>Sentence case:</strong> Capitalizes the first letter of each sentence.</li>
                                    <li><strong>lower case:</strong> Converts all text to lowercase.</li>
                                    <li><strong>UPPER CASE:</strong> Converts all text to uppercase.</li>
                                    <li><strong>Capitalized Case:</strong> Capitalizes the first letter of each word.</li>
                                    <li><strong>aLtErNaTiNg cAsE:</strong> Alternates between lowercase and uppercase letters.</li>
                                    <li><strong>Title Case:</strong> Capitalizes words except for articles, conjunctions, and prepositions.</li>
                                    <li><strong>InVeRsE CaSe:</strong> Inverts the case of each letter.</li>
                                </ul>
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
    /**
     * Display a SweetAlert notification
     * @param {string} message - The message to display
     * @param {string} type - The type of notification (success or error)
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
     * Copy text to clipboard
     */
    function copyToClipboard() {
        const textArea = document.getElementById('inputText');
        const text = textArea.value;
        
        if (!text) {
            showAlert('Nothing to copy!', 'error');
            return;
        }
        
        // Use modern Clipboard API when available
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text)
                .then(() => {
                    showAlert('Copied to clipboard!');
                })
                .catch(err => {
                    fallbackCopyToClipboard(text);
                });
        } else {
            // Fallback for older browsers
            fallbackCopyToClipboard(text);
        }
    }
    
    /**
     * Fallback method for copying to clipboard
     * @param {string} text - Text to copy
     */
    function fallbackCopyToClipboard(text) {
        try {
            // Create temporary element
            const textarea = document.createElement('textarea');
            
            // Set attributes and styling
            textarea.value = text;
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'fixed';
            textarea.style.top = '0';
            textarea.style.left = '0';
            textarea.style.opacity = '0';
            textarea.style.pointerEvents = 'none';
            
            // Add to DOM
            document.body.appendChild(textarea);
            
            // Select and copy
            textarea.select();
            textarea.setSelectionRange(0, 99999);
            const successful = document.execCommand('copy');
            
            // Log result
            if (successful) {
                showAlert('Copied to clipboard!');
            } else {
                showAlert('Copy failed. Please try again.', 'error');
            }
            
            // Clean up
            document.body.removeChild(textarea);
        } catch (err) {
            showAlert('Copy failed. Please try again.', 'error');
        }
    }
    
    /**
     * Clear the text area
     */
    function clearText() {
        document.getElementById('inputText').value = '';
        showAlert('Text cleared');
    }
    
    /**
     * Convert text to specified case
     * @param {string} caseType - The type of case conversion
     */
    function convertCase(caseType) {
        const textArea = document.getElementById('inputText');
        let text = textArea.value;
        
        if (!text) {
            showAlert('Please enter some text first', 'error');
            return;
        }
        
        switch (caseType) {
            case 'sentence':
                // Split by sentence ending punctuation followed by space or end of string
                text = text.toLowerCase().replace(/(^\s*\w|[.!?]\s*\w)/g, match => {
                    return match.toUpperCase();
                });
                showAlert('Text converted to sentence case!');
                break;
                
            case 'lower':
                text = text.toLowerCase();
                showAlert('Text converted to lowercase!');
                break;
                
            case 'upper':
                text = text.toUpperCase();
                showAlert('Text converted to UPPERCASE!');
                break;
                
            case 'capitalized':
                text = text.replace(/\w\S*/g, word => {
                    return word.charAt(0).toUpperCase() + word.substr(1).toLowerCase();
                });
                showAlert('Text converted to Capitalized Case!');
                break;
                
            case 'alternating':
                text = Array.from(text).map((char, index) => {
                    return index % 2 === 0 ? char.toLowerCase() : char.toUpperCase();
                }).join('');
                showAlert('Text converted to aLtErNaTiNg cAsE!');
                break;
                
            case 'title':
                // List of words to not capitalize in title case
                const minorWords = ['a', 'an', 'the', 'and', 'but', 'or', 'for', 'nor', 'on', 'at', 'to', 'from', 'by', 'in', 'of'];
                
                text = text.toLowerCase().replace(/\w\S*/g, (word, index, fullString) => {
                    // Always capitalize the first and last word
                    if (index === 0 || index === fullString.length - 1) {
                        return word.charAt(0).toUpperCase() + word.substr(1);
                    }
                    
                    // Don't capitalize minor words
                    if (minorWords.includes(word)) {
                        return word;
                    }
                    
                    // Capitalize other words
                    return word.charAt(0).toUpperCase() + word.substr(1);
                });
                showAlert('Text converted to Title Case!');
                break;
                
            case 'inverse':
                text = Array.from(text).map(char => {
                    return char === char.toUpperCase() ? char.toLowerCase() : char.toUpperCase();
                }).join('');
                showAlert('Text converted to InVeRsE CaSe!');
                break;
        }
        
        textArea.value = text;
    }
    
    /**
     * Download text as a file
     */
    function downloadText() {
        const textArea = document.getElementById('inputText');
        const text = textArea.value;
        
        if (!text) {
            showAlert('Nothing to download!', 'error');
            return;
        }
        
        // Create blob with text content
        const blob = new Blob([text], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        
        // Create a download link and click it
        const a = document.createElement('a');
        a.href = url;
        a.download = 'converted-text.txt';
        document.body.appendChild(a);
        a.click();
        
        // Clean up
        setTimeout(() => {
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }, 100);
        
        showAlert('Text downloaded successfully!');
    }
</script>
@endsection