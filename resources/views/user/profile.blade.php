@extends('layouts.master')

@section('title') @lang('translation.Profile') @endsection

@section('css')
<!-- Ace Editor CSS -->
<style>
    /* Signature Editor/Preview Layout */
    .signature-container {
        display: flex;
        flex-direction: column;
        width: 100%;
        margin-bottom: 30px;
    }

    .signature-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    /* Editor container */
    #editor-container {
        position: relative;
        height: 300px;
        border: 1px solid #ced4da;
        border-radius: 0 0 0.25rem 0.25rem;
        margin-bottom: 20px;
    }
    
    /* Editor */
    #signature-editor {
        width: 100%;
        height: 100%;
        font-size: 14px;
    }

    /* Editor toolbar */
    .editor-toolbar {
        padding: 8px;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: 0.25rem 0.25rem 0 0;
        border-bottom: none;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    
    /* Button groups */
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

    .signature-preview-container {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }

    .preview-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #495057;
    }

    .signature-preview {
        border: 1px solid #e2e8f0;
        padding: 15px;
        border-radius: 5px;
        background-color: #f8fafc;
        min-height: 100px;
        margin-bottom: 10px;
    }
    
    .separator {
        margin: 15px 0;
        border-top: 1px solid #e2e8f0;
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
    
    /* Upload progress indicator */
    #upload-progress {
        display: none;
    }
    
    #upload-progress.show {
        display: block;
    }
    
    /* Make toolbar scrollable on small screens */
    @media (max-width: 767.98px) {
        .editor-toolbar {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 8px;
        }
        
        #editor-container {
            height: 250px;
        }
    }
    
    /* Responsive YouTube Embeds */
    .responsive-embed-container, .youtube-embed-container {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
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

    /* Fix mobile-specific styles */
    @media (max-width: 767.98px) {
        .card-body iframe[src*="youtube.com/embed"],
        .card-body iframe[src*="youtu.be"],
        .card-body iframe[src*="youtube-nocookie.com/embed"] {
            width: 100% !important;
            height: auto !important;
            aspect-ratio: 16/9;
        }
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
    
    /* Make sure signature preview displays correctly */
    #signature-preview {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    
    #signature-preview * {
        max-width: 100%;
    }
    
    /* Make sure iframes in preview display correctly */
    #signature-preview iframe {
        max-width: 100%;
    }
    
    /* Make sure styles in preview are preserved */
    #signature-preview [style] {
        all: revert;
    }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') @lang('translation.Home') @endslot
@slot('title') @lang('translation.Profile') @endslot
@endcomponent

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">@lang('translation.Personal_Information')</h4>
                    <button type="button" class="btn btn-primary waves-effect waves-light" 
                            onclick="editProfile()">
                        <i data-feather="edit-2" class="icon-sm me-1"></i> @lang('translation.Edit_Profile')
                    </button>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

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

                <!-- View Profile Info -->
                <div id="viewProfile">
                    <div class="row">
                        <div class="col-lg-3 col-md-4">
                            <div class="text-center mb-4">
                                <img src="{{ 'https://www.gravatar.com/avatar/' . md5(strtolower(trim(auth()->user()->email))) . '?s=200&d=mp' }}" 
                                     alt="user-image" 
                                     class="rounded-circle avatar-xl img-thumbnail">
                                <div class="mt-3">
                                    <p class="text-muted mb-1">
                                        @lang('translation.Profile_picture_managed_by') <a href="https://gravatar.com" target="_blank">Gravatar</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9 col-md-8">
                            <div class="table-responsive">
                                <table class="table table-nowrap mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" style="width: 30%;">@lang('translation.Full_Name'):</th>
                                            <td>{{ auth()->user()->name }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">@lang('translation.Email'):</th>
                                            <td>{{ auth()->user()->email }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">@lang('translation.Role'):</th>
                                            <td>
                                                @if(auth()->user()->role === 'admin')
                                                    <span class="badge bg-danger">@lang('translation.Administrator')</span>
                                                @elseif(auth()->user()->role === 'support')
                                                    <span class="badge bg-success">@lang('translation.Support_Staff')</span>
                                                @else
                                                    <span class="badge bg-info">@lang('translation.User')</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">@lang('translation.Joined_Date'):</th>
                                            <td>{{ auth()->user()->created_at->format('d M Y') }}</td>
                                        </tr>
                                        
                                        @if(in_array(auth()->user()->role, ['admin', 'support']))
                                        <tr>
                                            <th scope="row">@lang('translation.Signature'):</th>
                                            <td>
                                                @if(auth()->user()->signature)
                                                    <div class="border p-3 rounded">
                                                        {!! auth()->user()->signature !!}
                                                    </div>
                                                @else
                                                    <span class="text-muted">@lang('translation.No_signature_set')</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Profile Form -->
                <div id="editProfile" style="display: none;">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label for="name" class="col-sm-3 col-form-label">@lang('translation.Name')</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', Auth::user()->name) }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">@lang('translation.Email')</label>
                            <div class="col-sm-9">
                                <p class="form-control-plaintext">{{ Auth::user()->email }}</p>
                                <small class="text-muted">@lang('translation.Email_cannot_be_changed')</small>
                            </div>
                        </div>

                        @if(in_array(auth()->user()->role, ['admin', 'support']))
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="col-form-label">@lang('translation.Signature')</label>
                            </div>
                            <div class="col-sm-9">
                                <!-- Signature Container (Custom Layout) -->
                                <div class="signature-container">
                                    <div class="signature-label">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="insert-default-signature">
                                            @lang('translation.Insert_Default_Signature')
                                        </button>
                                    </div>
                                    
                                    <!-- Editor Section -->
                                    <div class="signature-editor-container">
                                        <!-- Editor Toolbar -->
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
                                            <button type="button" id="imageBtn" class="toolbar-button" title="@lang('translation.Insert_Image')">
                                                <i class="bx bx-image"></i> @lang('translation.Image')
                                            </button>
                                            <button type="button" id="youtubeBtn" class="toolbar-button" title="@lang('translation.Insert_YouTube')">
                                                <i class="bx bxl-youtube" style="color: #FF0000;"></i> YouTube
                                            </button>
                                            
                                            <!-- Format and theme controls -->
                                            <button type="button" id="formatBtn" class="toolbar-button" title="@lang('translation.Format_HTML')">
                                                <i class="bx bx-code-block"></i> @lang('translation.Format')
                                            </button>
                                            <button type="button" id="themeBtn" class="toolbar-button" title="@lang('translation.Toggle_Theme')">
                                                <i class="bx bx-moon"></i> @lang('translation.Theme')
                                            </button>
                                        </div>
                                        
                                        <!-- Ace Editor Container -->
                                        <div id="editor-container">
                                            <div id="signature-editor"></div>
                                        </div>
                                        
                                        <!-- Hidden input to store editor content -->
                                        <textarea id="signature" name="signature" class="d-none @error('signature') is-invalid @enderror">{{ old('signature', Auth::user()->signature) }}</textarea>
                                        
                                        <!-- Upload progress indicator -->
                                        <div id="upload-progress" class="progress mt-2">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        
                                        <!-- Upload status message -->
                                        <div id="upload-status" class="mt-2 small"></div>
                                        
                                        <small class="text-muted mt-2 d-block">@lang('translation.Signature_will_be_added')</small>
                                        
                                        @error('signature')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                    <!-- Preview Section -->
                                    <div class="signature-preview-container">
                                        <h5 class="preview-title">@lang('translation.Signature_Preview')</h5>
                                        <div class="signature-preview">
                                            <div id="signature-preview">
                                                {!! Auth::user()->signature !!}
                                            </div>
                                        </div>
                                        <small class="text-muted">@lang('translation.Signature_appearance')</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row justify-content-end mt-4">
                            <div class="col-sm-9">
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        <i data-feather="save" class="icon-sm me-1"></i> @lang('translation.Save_Changes')
                                    </button>
                                    <button type="button" class="btn btn-secondary waves-effect" 
                                            onclick="cancelEdit()">
                                        <i data-feather="x" class="icon-sm me-1"></i> @lang('translation.Cancel')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">@lang('translation.Change_Password')</h4>

                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <label for="current_password" class="col-sm-3 col-form-label">@lang('translation.Current_Password')</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            @error('current_password')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="password" class="col-sm-3 col-form-label">@lang('translation.New_Password')</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password" name="password" required>
                            @error('password')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="password_confirmation" class="col-sm-3 col-form-label">@lang('translation.Confirm_Password')</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password_confirmation" 
                                name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="row justify-content-end">
                        <div class="col-sm-9">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i data-feather="key" class="icon-sm me-1"></i> @lang('translation.Change_Password')
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Two-Factor Authentication Card -->
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">@lang('translation.Two_Factor_Authentication')</h4>

                <div class="row">
                    <div class="col-lg-9">
                        <p class="card-title-desc">
                            @lang('translation.2FA_description')
                        </p>
                        
                        @if(auth()->user()->google2fa_secret)
                            <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                                <i data-feather="shield" class="me-2"></i>
                                <div>
                                    <strong>@lang('translation.Enabled'):</strong> @lang('translation.2FA_enabled_message')
                                </div>
                            </div>

                            <button type="button" class="btn btn-danger waves-effect waves-light" 
                                    data-bs-toggle="modal" data-bs-target="#disable2faModal">
                                <i data-feather="shield-off" class="icon-sm me-1"></i> @lang('translation.Disable_2FA')
                            </button>
                        @else
                            <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                                <i data-feather="alert-triangle" class="me-2"></i>
                                <div>
                                    <strong>@lang('translation.Not_Enabled'):</strong> @lang('translation.2FA_not_enabled_message')
                                </div>
                            </div>

                            <a href="{{ route('2fa.setup') }}" class="btn btn-primary waves-effect waves-light">
                                <i data-feather="shield" class="icon-sm me-1"></i> @lang('translation.Enable_2FA')
                            </a>
                        @endif
                    </div>
                    <div class="col-lg-3 text-center">
                        <div class="p-3 border rounded">
                            <i data-feather="smartphone" style="width: 64px; height: 64px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Disable 2FA Modal -->
<div class="modal fade" id="disable2faModal" tabindex="-1" aria-labelledby="disable2faModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="disable2faModalLabel">@lang('translation.Disable_2FA')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('2fa.disable') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i data-feather="alert-triangle" class="me-2"></i>
                        <div>
                            <strong>@lang('translation.Warning'):</strong> @lang('translation.Disable_2FA_warning')
                        </div>
                    </div>
                    <p>@lang('translation.Enter_password_to_confirm'):</p>
                    <div class="mb-3">
                        <label for="password" class="form-label">@lang('translation.Password')</label>
                        <input type="password" class="form-control" id="disable2fa_password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('translation.Cancel')</button>
                    <button type="submit" class="btn btn-danger">@lang('translation.Disable')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast-notification"></div>
@endsection

@section('script')
<!-- Ace Editor and Extensions -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/ace.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/ext-language_tools.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.6/ext-beautify.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.15.1/beautify-html.min.js"></script>

<script>
// Global variables
var editor = null;
var isDarkMode = false;
var uploadProgress = null;
var progressBar = null;
var uploadStatus = null;

// Function to toggle profile display
function editProfile() {
    document.getElementById('viewProfile').style.display = 'none';
    document.getElementById('editProfile').style.display = 'block';
}

function cancelEdit() {
    document.getElementById('viewProfile').style.display = 'block';
    document.getElementById('editProfile').style.display = 'none';
}

// Function to update signature preview
function updateSignaturePreview(html) {
    const signaturePreview = document.getElementById('signature-preview');
    if (signaturePreview) {
        signaturePreview.innerHTML = html || '';
    }
}

// Function to update content from editor to hidden field and preview
function updateContent() {
    if (!editor) return;
    
    const html = editor.getValue();
    const signatureField = document.getElementById('signature');
    
    if (signatureField) {
        signatureField.value = html;
    }
    
    // Update the preview
    updateSignaturePreview(html);
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

// Format HTML function
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
        
        // Show success toast
        showToast('@lang("translation.Code_formatted_successfully")');
    } catch (err) {
        console.error('Error formatting HTML:', err);
        showToast('@lang("translation.Error_formatting_code")');
    }
}

// Function to insert default signature
function insertDefaultSignature() {
    if (!editor) {
        console.error("Editor not initialized");
        return false;
    }
    
    const userName = "{{ Auth::user()->name }}";
    const appName = "{{ config('app.name') }}";
    const isAdmin = "{{ Auth::user()->role === 'admin' ? 'true' : 'false' }}";
    
    const roleColor = isAdmin === 'true' ? "#d63939" : "#198754";
    const roleTitle = isAdmin === 'true' ? "@lang('translation.Administrator')" : "@lang('translation.Support_Staff')";
    
    const defaultSignature = `<div style="font-family: Arial, sans-serif; color: #333;">
        <p><strong>${userName}</strong><br>
        <span style="color: ${roleColor};">${roleTitle}</span><br>
        ${appName} @lang('translation.Support_Team')</p>
        
        <p style="font-size: 12px; color: #666;">
            @lang('translation.Need_further_assistance')
        </p>
    </div>`;
    
    // Clear existing content and insert the new signature
    editor.setValue(defaultSignature);
    
    // Update the hidden textarea
    const signatureField = document.getElementById('signature');
    if (signatureField) {
        signatureField.value = defaultSignature;
    }
    
    // Update the preview
    updateSignaturePreview(defaultSignature);
    
    console.log("Default signature inserted");
    return true;
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
    </div><p><br></p>`;
}

// Insert YouTube embed
function insertYoutubeEmbed() {
    // Prompt user for YouTube URL
    const url = prompt('@lang("translation.Enter_YouTube_URL"):');
    if (!url) return;
    
    // Parse URL to get video ID
    const videoId = parseYoutubeUrl(url);
    if (!videoId) {
        alert('@lang("translation.Invalid_YouTube_URL")');
        return;
    }
    
    // Get current cursor position
    const cursorPos = editor.getCursorPosition();
    
    // Insert YouTube embed HTML at cursor position
    editor.session.insert(cursorPos, createYoutubeEmbed(videoId));
    
    // Update content and preview
    updateContent();
    
    // Show success message
    if (uploadStatus) {
        uploadStatus.innerHTML = '<span class="text-success"><i class="bx bx-check-circle me-1"></i> @lang("translation.YouTube_embedded_successfully")</span>';
        setTimeout(() => { uploadStatus.innerHTML = ''; }, 3000);
    }
}

// Insert link function
function insertLink() {
    // Get selected text
    const selectedText = editor.getSelectedText();
    
    // Prompt for link text if none selected
    const linkText = selectedText || prompt('@lang("translation.Enter_link_text"):', '@lang("translation.Link_Text")');
    if (!linkText) return;
    
    // Prompt for URL
    const url = prompt('@lang("translation.Enter_URL"):', 'https://');
    if (!url) return;
    
    // Create HTML for link
    const html = `<a href="${url}">${linkText}</a>`;
    
    // If text was selected, replace it; otherwise insert at cursor
    if (selectedText) {
        // Replace selected text with link
        const range = editor.getSelectionRange();
        editor.session.replace(range, html);
    } else {
        // Insert at cursor position
        const cursorPos = editor.getCursorPosition();
        editor.session.insert(cursorPos, html);
    }
    
    // Update hidden input and preview
    updateContent();
}

// Upload image to server
function uploadImageToServer(file) {
    // Show upload progress
    if (uploadProgress) {
        uploadProgress.style.display = 'block';
        progressBar.style.width = '30%';
        uploadStatus.innerHTML = '<span class="text-primary"><i class="bx bx-loader-alt bx-spin me-1"></i> @lang("translation.Uploading_image")</span>';
    }
    
    // Create FormData
    const formData = new FormData();
    formData.append('image', file);
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Send request
    fetch('/upload/image', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        if (progressBar) progressBar.style.width = '60%';
        return response.json();
    })
    .then(data => {
        if (progressBar) progressBar.style.width = '100%';
        
        if (data.success) {
            // Insert image HTML at cursor position
            const imageUrl = data.data.url;
            const html = `<img src="${imageUrl}" alt="Signature Image" style="max-width: 100%; height: auto;">`;
            
            const cursorPos = editor.getCursorPosition();
            editor.session.insert(cursorPos, html);
            
            // Update hidden input and preview
            updateContent();
            
            // Show success message
            if (uploadStatus) {
                uploadStatus.innerHTML = '<span class="text-success"><i class="bx bx-check-circle me-1"></i> @lang("translation.Image_uploaded_successfully")</span>';
                
                // Hide progress bar after a delay
                setTimeout(() => {
                    uploadProgress.style.display = 'none';
                    progressBar.style.width = '0%';
                }, 2000);
                
                // Clear status message after a delay
                setTimeout(() => {
                    uploadStatus.innerHTML = '';
                }, 5000);
            }
        } else {
            // Handle error
            if (uploadStatus) {
                uploadStatus.innerHTML = '<span class="text-danger"><i class="bx bx-error-circle me-1"></i> ' + (data.error || '@lang("translation.Failed_to_upload_image")') + '</span>';
                
                // Hide progress bar after a delay
                setTimeout(() => {
                    uploadProgress.style.display = 'none';
                    progressBar.style.width = '0%';
                }, 2000);
            }
        }
    })
    .catch(error => {
        if (progressBar) progressBar.style.width = '100%';
        console.error('Upload error:', error);
        
        if (uploadStatus) {
            uploadStatus.innerHTML = '<span class="text-danger"><i class="bx bx-error-circle me-1"></i> @lang("translation.Error_uploading_image")</span>';
            
            // Hide progress bar after a delay
            setTimeout(() => {
                uploadProgress.style.display = 'none';
                progressBar.style.width = '0%';
            }, 2000);
        }
    });
}

// Handle text formatting buttons
function setupFormattingButtons() {
    // Bold
    document.getElementById('boldBtn').addEventListener('click', function() {
        const selectedText = editor.getSelectedText();
        if (!selectedText) return;
        
        const html = `<strong>${selectedText}</strong>`;
        const range = editor.getSelectionRange();
        editor.session.replace(range, html);
        
        // Update content and preview
        updateContent();
    });
    
    // Italic
    document.getElementById('italicBtn').addEventListener('click', function() {
        const selectedText = editor.getSelectedText();
        if (!selectedText) return;
        
        const html = `<em>${selectedText}</em>`;
        const range = editor.getSelectionRange();
        editor.session.replace(range, html);
        
        // Update content and preview
        updateContent();
    });
    
    // Underline
    document.getElementById('underlineBtn').addEventListener('click', function() {
        const selectedText = editor.getSelectedText();
        if (!selectedText) return;
        
        const html = `<u>${selectedText}</u>`;
        const range = editor.getSelectionRange();
        editor.session.replace(range, html);
        
        // Update content and preview
        updateContent();
    });
    
    // Alignment buttons
    const alignBtns = [
        {id: 'alignLeftBtn', align: 'left'},
        {id: 'alignCenterBtn', align: 'center'},
        {id: 'alignRightBtn', align: 'right'}
    ];
    
    alignBtns.forEach(function(btn) {
        document.getElementById(btn.id).addEventListener('click', function() {
            const selectedText = editor.getSelectedText();
            if (!selectedText) return;
            
            const html = `<div style="text-align: ${btn.align};">${selectedText}</div>`;
            const range = editor.getSelectionRange();
            editor.session.replace(range, html);
            
            // Update content and preview
            updateContent();
        });
    });
    
    // Link button
    document.getElementById('linkBtn').addEventListener('click', function() {
        insertLink();
    });
    
    // Image button
    document.getElementById('imageBtn').addEventListener('click', function() {
        // Create file input element
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*';
        fileInput.style.display = 'none';
        document.body.appendChild(fileInput);
        
        // Handle file selection
        fileInput.onchange = function() {
            if (fileInput.files && fileInput.files[0]) {
                uploadImageToServer(fileInput.files[0]);
            }
            document.body.removeChild(fileInput);
        };
        
        // Trigger file selection dialog
        fileInput.click();
    });
    
    // YouTube button
    document.getElementById('youtubeBtn').addEventListener('click', function() {
        insertYoutubeEmbed();
    });
    
    // Format button
    document.getElementById('formatBtn').addEventListener('click', function() {
        formatHtml();
    });
    
    // Theme button
    document.getElementById('themeBtn').addEventListener('click', function() {
        isDarkMode = !isDarkMode;
        editor.setTheme(isDarkMode ? "ace/theme/monokai" : "ace/theme/chrome");
        
        // Update button icon
        this.innerHTML = isDarkMode ? 
            '<i class="bx bx-sun"></i> @lang("translation.Theme")' : 
            '<i class="bx bx-moon"></i> @lang("translation.Theme")';
    });
}

// Handle paste events
function setupPasteHandler() {
    // Access the editor's textarea element
    const editorElement = document.querySelector('.ace_text-input');
    if (!editorElement) return;
    
    // Add paste event listener to the document
    document.addEventListener('paste', function(e) {
        // Only handle paste if editor has focus
        if (!editor.isFocused()) return;
        
        // Check for image data
        if (e.clipboardData && e.clipboardData.items) {
            const items = e.clipboardData.items;
            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    e.preventDefault();
                    const file = items[i].getAsFile();
                    if (file) {
                        uploadImageToServer(file);
                    }
                    return;
                }
            }
        }
        
        // Check for YouTube URL
        const text = e.clipboardData.getData('text/plain');
        if (text && (text.includes('youtube.com/watch') || text.includes('youtu.be/'))) {
            const videoId = parseYoutubeUrl(text);
            if (videoId) {
                e.preventDefault();
                
                // Insert at cursor position
                const cursorPos = editor.getCursorPosition();
                editor.session.insert(cursorPos, createYoutubeEmbed(videoId));
                
                // Update content and preview
                updateContent();
                
                // Show success message
                if (uploadStatus) {
                    uploadStatus.innerHTML = '<span class="text-success"><i class="bx bx-check-circle me-1"></i> @lang("translation.YouTube_embedded")</span>';
                    setTimeout(() => { uploadStatus.innerHTML = ''; }, 3000);
                }
                
                return;
            }
        }
    }, true);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM loaded, initializing editor");
    
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Set up upload progress elements
    uploadProgress = document.getElementById('upload-progress');
    if (uploadProgress) {
        progressBar = uploadProgress.querySelector('.progress-bar');
        uploadStatus = document.getElementById('upload-status');
    }
    
    // Initialize Ace Editor
    const editorElement = document.getElementById('signature-editor');
    if (editorElement) {
        // Create the editor
        editor = ace.edit("signature-editor");
        
        // Configure editor
        editor.setTheme("ace/theme/chrome");
        editor.session.setMode("ace/mode/html");
        editor.setOptions({
            enableBasicAutocompletion: true,
            enableLiveAutocompletion: true,
            enableSnippets: true,
            fontSize: "14px",
            showPrintMargin: false,
            highlightActiveLine: true,
            wrap: true
        });
        
        // Get initial content from hidden field
        const signatureField = document.getElementById('signature');
        if (signatureField && signatureField.value) {
            editor.setValue(signatureField.value, -1);
            
            // Update preview initially
            updateSignaturePreview(signatureField.value);
        }
        
        // Setup event listeners for content changes
        editor.on('change', function() {
            updateContent();
        });
        
        // Setup default signature button
        const insertDefaultBtn = document.getElementById('insert-default-signature');
        if (insertDefaultBtn) {
            insertDefaultBtn.addEventListener('click', function(e) {
                e.preventDefault();
                insertDefaultSignature();
            });
        }
        
        // Setup formatting buttons
        setupFormattingButtons();
        
        // Setup paste handler
        setupPasteHandler();
        
        // Handle form submission
        const form = editorElement.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                updateContent();
            });
        }
    }
});
</script>
@endsection