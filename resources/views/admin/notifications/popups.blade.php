@extends('layouts.master')

@section('title') Manage Popup Notifications @endsection

@section('css')
<!-- Flatpickr css -->
<link href="{{ URL::asset('build/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Sweet Alert css -->
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

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
    .popup-item {
        border-left: 4px solid #eee;
        padding: 15px;
        margin-bottom: 15px;
        position: relative;
        transition: all 0.3s;
    }
    
    .popup-item:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .popup-item.info {
        border-color: var(--bs-info);
    }
    
    .popup-item.success {
        border-color: var(--bs-success);
    }
    
    .popup-item.warning {
        border-color: var(--bs-warning);
    }
    
    .popup-item.danger {
        border-color: var(--bs-danger);
    }
    
    .popup-controls {
        display: flex;
        gap: 5px;
        margin-top: 10px;
    }
    
    .popup-preview {
        margin-top: 10px;
        padding: 10px;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        background-color: #f9f9f9;
    }
    
    /* Custom toggle switch styling */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }
    
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .toggle-switch .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
    }
    
    .toggle-switch .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    
    .toggle-switch input:checked + .slider {
        background-color: #556ee6;
    }
    
    .toggle-switch input:checked + .slider:before {
        transform: translateX(20px);
    }
    
    .popup-modal-preview {
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        margin-top: 15px;
    }
    
    .popup-modal-preview .modal-header {
        padding: 8px 15px;
    }
    
    .popup-modal-preview .modal-body {
        padding: 15px;
        max-height: 200px;
        overflow-y: auto;
    }
    
    .popup-modal-preview .modal-footer {
        padding: 8px 15px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }
    
    .dismissal-stats {
        margin-top: 10px;
        font-size: 13px;
        color: #6c757d;
    }

    /* Editor container styling */
    .editor-container {
        position: relative;
        margin-bottom: 20px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    
    /* Froala editor styling */
    .froala-editor {
        height: 250px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 14px;
    }
    
    /* Upload progress indicator */
    #upload-progress, #edit-upload-progress {
        display: none;
        margin-top: 10px;
    }
    
    #upload-progress.show, #edit-upload-progress.show {
        display: block;
    }
    
    #upload-status, #edit-upload-status {
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
    
    /* Mobile optimizations */
    @media (max-width: 767.98px) {
        .card-body {
            padding: 1rem;
        }
        
        .popup-item {
            padding: 12px;
        }
        
        .popup-header {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .popup-controls {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 8px;
        }
        
        .popup-controls .btn {
            padding: 0.3rem 0.5rem;
            font-size: 0.8rem;
        }
        
        .popup-controls .btn i {
            font-size: 0.9rem;
        }
        
        .metadata-section {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 5px;
        }
        
        .metadata-section .badge {
            display: inline-block;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        .froala-editor {
            height: 200px;
            font-size: 13px;
        }
        
        .modal-body {
            padding: 1rem;
        }
        
        .form-group {
            margin-bottom: 0.75rem;
        }
        
        .add-btn-wrapper {
            position: sticky;
            bottom: 20px;
            right: 20px;
            z-index: 99;
            text-align: right;
        }
        
        .add-btn-wrapper .btn {
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            border-radius: 5px;
            padding: 0.5rem 1rem;
        }
        
        .popup-modal-preview {
            margin-top: 10px;
        }
        
        .popup-modal-preview .modal-body {
            max-height: 150px;
        }
        
        /* Form layout optimizations for mobile */
        .row .col-md-6:first-child {
            margin-bottom: 10px;
        }
        
        /* Compact switches on mobile */
        .form-check-label {
            font-size: 0.9rem;
        }
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Admin @endslot
        @slot('li_2') Notifications @endslot
        @slot('title') Popup Notifications @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                        <h4 class="card-title mb-2">Manage Popup Notifications</h4>
                        <div class="add-btn-wrapper">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPopupModal">
                                <i class="bx bx-plus me-1"></i> Add New
                            </button>
                        </div>
                    </div>

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

                    <div id="form-messages"></div>

                    @if($popups->isEmpty())
                        <div class="text-center py-4">
                            <i class="bx bx-window-alt font-size-40 text-muted"></i>
                            <h5 class="mt-3">No Popup Notifications</h5>
                            <p class="text-muted">There are no popup notifications available. Add your first popup to display to users.</p>
                        </div>
                    @else
                        <div class="popup-list">
                            @foreach($popups as $popup)
                                <div class="popup-item {{ $popup->type }} card">
                                    <div class="popup-header">
                                        <div>
                                            <h5 class="mb-1">{{ $popup->title }}</h5>
                                        </div>
                                        <div class="metadata-section">
                                            <small>
                                                <span class="me-2">Type: <span class="badge bg-{{ $popup->type }}">{{ ucfirst($popup->type) }}</span></span>
                                                <span class="me-2">Status: <span class="badge bg-{{ $popup->is_enabled ? 'success' : 'secondary' }}">{{ $popup->is_enabled ? 'Enabled' : 'Disabled' }}</span></span>
                                                <span class="me-2">Show once: <span class="badge bg-{{ $popup->show_once ? 'info' : 'secondary' }}">{{ $popup->show_once ? 'Yes' : 'No' }}</span></span>
                                                <span class="me-2">Allow dismiss: <span class="badge bg-{{ $popup->allow_dismiss ? 'info' : 'warning' }}">{{ $popup->allow_dismiss ? 'Yes' : 'No' }}</span></span>
                                                @if($popup->start_date || $popup->end_date)
                                                    <span class="d-block mt-1">
                                                        Duration: 
                                                        {{ $popup->start_date ? $popup->start_date->format('M d, Y') : 'Any time' }}
                                                        -
                                                        {{ $popup->end_date ? $popup->end_date->format('M d, Y') : 'No end date' }}
                                                    </span>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    <div class="popup-preview">
                                        {!! $popup->content !!}
                                    </div>
                                    
                                    <div class="dismissal-stats">
                                        <i class="bx bx-info-circle"></i> 
                                        Dismissed by {{ $popup->dismissedByUsers->count() }} users 
                                        @if($popup->created_at)
                                        <span class="ms-2 text-muted">Created {{ $popup->created_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="popup-controls">
                                        <label class="toggle-switch me-2">
                                            <input type="checkbox" class="toggle-status" data-id="{{ $popup->id }}" {{ $popup->is_enabled ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <button type="button" class="btn btn-sm btn-info edit-btn" data-id="{{ $popup->id }}">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $popup->id }}">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary preview-btn" data-id="{{ $popup->id }}">
                                            <i class="bx bx-show"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add Popup Modal -->
    <div class="modal fade" id="addPopupModal" tabindex="-1" aria-labelledby="addPopupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPopupModalLabel">Add New Popup Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addPopupForm" action="{{ route('admin.notifications.popups.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-8 mb-2">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="info">Info</option>
                                    <option value="success">Success</option>
                                    <option value="warning">Warning</option>
                                    <option value="danger">Danger</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_enabled" name="is_enabled" checked>
                                    <label class="form-check-label" for="is_enabled">Enable Popup</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="allow_dismiss" name="allow_dismiss" checked>
                                    <label class="form-check-label" for="allow_dismiss">Allow Dismissal</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="show_once" name="show_once" checked>
                                    <label class="form-check-label" for="show_once">Show Only Once</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label for="start_date" class="form-label">Start Date (Optional)</label>
                                <input type="text" class="form-control flatpickr-date" id="start_date" name="start_date" placeholder="Select date">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="end_date" class="form-label">End Date (Optional)</label>
                                <input type="text" class="form-control flatpickr-date" id="end_date" name="end_date" placeholder="Select date">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <!-- Editor Container -->
                            <div class="editor-container">
                                <textarea id="content" name="content" class="froala-editor" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Popup Modal -->
    <div class="modal fade" id="editPopupModal" tabindex="-1" aria-labelledby="editPopupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPopupModalLabel">Edit Popup Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPopupForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-8 mb-2">
                                <label for="edit_title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="edit_title" name="title" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="edit_type" class="form-label">Type</label>
                                <select class="form-select" id="edit_type" name="type" required>
                                    <option value="info">Info</option>
                                    <option value="success">Success</option>
                                    <option value="warning">Warning</option>
                                    <option value="danger">Danger</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit_is_enabled" name="is_enabled">
                                    <label class="form-check-label" for="edit_is_enabled">Enable Popup</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit_allow_dismiss" name="allow_dismiss">
                                    <label class="form-check-label" for="edit_allow_dismiss">Allow Dismissal</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit_show_once" name="show_once">
                                    <label class="form-check-label" for="edit_show_once">Show Only Once</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label for="edit_start_date" class="form-label">Start Date (Optional)</label>
                                <input type="text" class="form-control flatpickr-date" id="edit_start_date" name="start_date" placeholder="Select date">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="edit_end_date" class="form-label">End Date (Optional)</label>
                                <input type="text" class="form-control flatpickr-date" id="edit_end_date" name="end_date" placeholder="Select date">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_content" class="form-label">Content</label>
                            <!-- Editor Container -->
                            <div class="editor-container">
                                <textarea id="edit_content" name="content" class="froala-editor" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Preview Popup Modal -->
    <div class="modal fade" id="previewPopupModal" tabindex="-1" aria-labelledby="previewPopupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewPopupModalLabel">Popup Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">This is how the popup will appear to users:</p>
                    
                    <div class="popup-modal-preview">
                        <div class="modal-header preview-header">
                            <h5 class="modal-title preview-title"></h5>
                            <button type="button" class="btn-close"></button>
                        </div>
                        <div class="modal-body preview-body">
                        </div>
                        <div class="modal-footer preview-footer">
                            <div class="form-check me-auto preview-dont-show" style="display: none;">
                                <input class="form-check-input" type="checkbox" id="previewDontShowAgain">
                                <label class="form-check-label" for="previewDontShowAgain">
                                    Don't show again
                                </label>
                            </div>
                            
                            <button type="button" class="btn preview-button">Close</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close Preview</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Message -->
    <div id="toast-message" class="toast-message"></div>
@endsection

@section('script')
<!-- Flatpickr js -->
<script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>

<!-- Sweet alert js -->
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

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
    // Detect mobile devices
    const isMobile = window.innerWidth < 768;
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Initialize flatpickr date pickers with mobile optimizations
    $('.flatpickr-date').flatpickr({
        dateFormat: 'Y-m-d',
        allowInput: true,
        // Mobile optimizations
        disableMobile: false, // Enable native picker on mobile
        static: isMobile // Makes the calendar not move when scrolling on mobile
    });
    
    // Configure Froala editor for better mobile experience
    const editorConfig = {
        // License key
        key: "1C%kZPBZ`JWSDBCQ@ZGD\\@\\JXDAAOZWJh,/.!==",
        
        // Basic configuration
        height: isMobile ? 200 : 250,
        placeholderText: 'Write your popup content here...',
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
        
        // Mobile-friendly toolbar
        toolbarButtons: isMobile ? 
        // Simplified for mobile
        ['bold', 'italic', '|', 'paragraphFormat', '|', 'insertImage', '|', 'html'] : 
        // Full for desktop
        ['bold', 'italic', 'underline', '|', 'paragraphFormat', 'formatOL', 'formatUL', '|', 'insertImage', '|', 'html'],
        
        // Mobile optimizations
        toolbarSticky: true,
        toolbarStickyOffset: isMobile ? 50 : 0,
        
        // Image upload configurations
        imageUpload: true,
        imageUploadURL: "{{ route('admin.upload-image') }}",
        imageUploadParams: {
            _token: csrfToken
        },
        
        // Events
        events: {
            'image.uploaded': function (response) {
                // Parse response
                try {
                    const data = JSON.parse(response);
                    if (data.success && data.data && data.data.url) {
                        return data.data.url;
                    }
                } catch (e) {
                    console.error('Error parsing image upload response:', e);
                }
                
                return false;
            },
            'image.uploadError': function (error) {
                console.error('Image upload error:', error);
                
                // Show error message
                showToast('Failed to upload image. Please try again.', 'error');
                
                return false;
            },
            'image.inserted': function($img) {
                // Ensure images are responsive
                $img.addClass('img-fluid');
            }
        }
    };
    
    // Initialize both content editors
    const editor = new FroalaEditor('#content', editorConfig);
    let editEditor = null;
    
    // Show toast message
    function showToast(message, type = 'success') {
        // Use SweetAlert for better mobile compatibility
        const Toast = Swal.mixin({
            toast: true,
            position: isMobile ? 'center' : 'bottom-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            width: isMobile ? 'auto' : undefined,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        
        Toast.fire({
            icon: type,
            title: message
        });
    }
    
    // Handle toggle switches for enabling/disabling popups
    $('.toggle-status').on('click', function(e) {
        // Prevent default to avoid accidental clicks on mobile
        e.stopPropagation();
        
        const popupId = $(this).data('id');
        const isEnabled = $(this).prop('checked');
        const toggleSwitch = $(this);
        
        $.ajax({
            url: `{{ url('admin/notifications/popups') }}/${popupId}/toggle`,
            type: 'POST',
            data: {
                _token: csrfToken
            },
            success: function(response) {
                if (response.success) {
                    showToast(`Popup has been ${response.enabled ? 'enabled' : 'disabled'}.`, 'success');
                } else {
                    // Revert toggle if failed
                    toggleSwitch.prop('checked', !isEnabled);
                    showToast('Failed to update status.', 'error');
                }
            },
            error: function() {
                // Revert toggle if failed
                toggleSwitch.prop('checked', !isEnabled);
                showToast('Failed to update status due to a server error.', 'error');
            }
        });
    });
    
    // Handle edit button click
    $('.edit-btn').on('click', function() {
        const popupId = $(this).data('id');
        
        // Show loading state on mobile
        if (isMobile) {
            Swal.fire({
                title: 'Loading...',
                text: 'Please wait while we fetch the popup data.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        
        // Fetch popup data
        $.ajax({
            url: `{{ url('admin/notifications/popups') }}/${popupId}/edit`,
            type: 'GET',
            success: function(response) {
                if (isMobile) {
                    Swal.close();
                }
                
                if (response.popup) {
                    const popup = response.popup;
                    
                    // Set form action
                    $('#editPopupForm').attr('action', `{{ url('admin/notifications/popups') }}/${popup.id}`);
                    
                    // Fill form fields
                    $('#edit_title').val(popup.title);
                    $('#edit_type').val(popup.type);
                    $('#edit_is_enabled').prop('checked', popup.is_enabled);
                    $('#edit_allow_dismiss').prop('checked', popup.allow_dismiss);
                    $('#edit_show_once').prop('checked', popup.show_once);
                    
                    if (popup.start_date) {
                        $('#edit_start_date').flatpickr().setDate(popup.start_date);
                    } else {
                        $('#edit_start_date').flatpickr().clear();
                    }
                    
                    if (popup.end_date) {
                        $('#edit_end_date').flatpickr().setDate(popup.end_date);
                    } else {
                        $('#edit_end_date').flatpickr().clear();
                    }
                    
                    // Initialize or destroy and reinitialize editor
                    if (editEditor) {
                        editEditor.destroy();
                    }
                    
                    // Show modal
                    $('#editPopupModal').modal('show');
                    
                    // Initialize the editor after a short delay to ensure the modal is visible
                    setTimeout(() => {
                        editEditor = new FroalaEditor('#edit_content', {
                            ...editorConfig,
                            events: {
                                ...editorConfig.events,
                                'initialized': function() {
                                    // Set content to editor after initialization
                                    this.html.set(popup.content);
                                }
                            }
                        });
                    }, 300);
                } else {
                    showToast('Failed to load popup data.', 'error');
                }
            },
            error: function() {
                if (isMobile) {
                    Swal.close();
                }
                showToast('Failed to load popup data due to a server error.', 'error');
            }
        });
    });
    
    // Handle delete button click with improved mobile UX
    $('.delete-btn').on('click', function() {
        const popupId = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#556ee6',
            cancelButtonColor: '#f46a6a',
            confirmButtonText: 'Yes, delete it!',
            heightAuto: false, // Better for mobile
            width: isMobile ? '85%' : '32rem'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `{{ url('admin/notifications/popups') }}/${popupId}/delete`;
            }
        });
    });
    
    // Handle preview button click with mobile optimizations
    $('.preview-btn').on('click', function() {
        const popupId = $(this).data('id');
        
        // Show loading indicator on mobile
        if (isMobile) {
            Swal.fire({
                title: 'Loading preview...',
                text: 'Please wait',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        
        // Fetch popup data for preview
        $.ajax({
            url: `{{ url('admin/notifications/popups') }}/${popupId}/edit`,
            type: 'GET',
            success: function(response) {
                if (isMobile) {
                    Swal.close();
                }
                
                if (response.popup) {
                    const popup = response.popup;
                    
                    // Set preview content
                    $('.preview-title').text(popup.title);
                    $('.preview-body').html(popup.content);
                    
                    // Set button class based on type
                    $('.preview-button').attr('class', `btn btn-${popup.type}`);
                    $('.preview-button').text('Close');
                    
                    // Show "Don't show again" checkbox if show_once is enabled
                    if (popup.show_once) {
                        $('.preview-dont-show').show();
                    } else {
                        $('.preview-dont-show').hide();
                    }
                    
                    // Show preview modal
                    $('#previewPopupModal').modal('show');
                } else {
                    showToast('Failed to load popup data for preview.', 'error');
                }
            },
            error: function() {
                if (isMobile) {
                    Swal.close();
                }
                showToast('Failed to load popup data due to a server error.', 'error');
            }
        });
    });
    
    // Optimize form submission for better UX
    function handleFormSubmission(form, submitBtn, successMessage, formType = 'add') {
        // Store original button text
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.html('<span class="spinner-border spinner-border-sm me-1"></span> ' + (formType === 'add' ? 'Creating...' : 'Updating...'));
        submitBtn.prop('disabled', true);
        
        // Get form data
        const formData = new FormData(form);
        
        // Submit the form using fetch API
        fetch(form.action, {
            method: 'POST', 
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: successMessage,
                confirmButtonColor: '#556ee6',
                timer: isMobile ? 2000 : undefined,
                timerProgressBar: isMobile ? true : false,
                showConfirmButton: !isMobile
            }).then(function() {
                // Reload the page
                window.location.reload();
            });
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Show error message
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to ' + (formType === 'add' ? 'create' : 'update') + ' popup. Please try again.',
                confirmButtonColor: '#f46a6a'
            });
            
            // Reset button
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);
        });
    }
    
    // Handle form submission for add popup form
    $('#addPopupForm').on('submit', function(e) {
        e.preventDefault();
        
        // Make sure Froala content is updated in the form
        const editorContent = editor.html.get();
        $('#content').val(editorContent);
        
        handleFormSubmission(
            this, 
            $(this).find('button[type="submit"]'), 
            'Popup created successfully!',
            'add'
        );
    });
    
    // Handle form submission for edit popup form
    $('#editPopupForm').on('submit', function(e) {
        e.preventDefault();
        
        // Make sure Froala content is updated in the form
        const editorContent = editEditor.html.get();
        $('#edit_content').val(editorContent);
        
        handleFormSubmission(
            this, 
            $(this).find('button[type="submit"]'), 
            'Popup updated successfully!',
            'edit'
        );
    });
    
    // Mobile-specific event handlers
    if (isMobile) {
        // Manage form inputs on mobile
        const inputs = document.querySelectorAll('input[type="text"], input[type="number"], textarea');
        
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                // Scroll to the input after a short delay
                setTimeout(() => {
                    input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            });
        });
        
        // Window resize handler
        window.addEventListener('resize', () => {
            // Update UI based on orientation changes
            const newIsMobile = window.innerWidth < 768;
            if (newIsMobile !== isMobile) {
                // Reload page to reinitialize with new settings
                window.location.reload();
            }
        });
        
        // Improve modal scrolling on mobile
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('shown.bs.modal', () => {
                modal.querySelector('.modal-body').scrollTop = 0;
            });
        });
    }
});
</script>
@endsection