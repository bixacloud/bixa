@extends('layouts.master')

@section('title') Manage Announcements @endsection

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
    .announcement-item {
        border-left: 4px solid #eee;
        padding: 15px;
        margin-bottom: 15px;
        position: relative;
        transition: all 0.3s;
    }
    
    .announcement-item:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .announcement-item .badge {
        position: absolute;
        top: 15px;
        right: 15px;
    }
    
    .announcement-item.info {
        border-color: var(--bs-info);
    }
    
    .announcement-item.success {
        border-color: var(--bs-success);
    }
    
    .announcement-item.warning {
        border-color: var(--bs-warning);
    }
    
    .announcement-item.danger {
        border-color: var(--bs-danger);
    }
    
    .announcement-controls {
        display: flex;
        gap: 5px;
        margin-top: 10px;
    }
    
    .announcement-preview {
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
        /* Card adjustments */
        .card-body {
            padding: 1rem;
        }
        
        /* Announcement item layout for mobile */
        .announcement-item {
            padding: 12px;
        }
        
        /* Header with controls layout */
        .announcement-header {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        /* Control buttons size */
        .announcement-controls .btn {
            padding: 0.3rem 0.5rem;
            font-size: 0.8rem;
        }
        
        .announcement-controls .btn i {
            font-size: 0.9rem;
        }
        
        /* Metadata layout */
        .metadata-section {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 5px;
        }
        
        .metadata-section .badge {
            margin-right: 5px;
            display: inline-block;
        }
        
        /* Froala editor height adjustment */
        .froala-editor {
            height: 200px;
            font-size: 13px;
        }
        
        /* Modal adjustments */
        .modal-body {
            padding: 1rem;
        }
        
        /* Form controls spacing */
        .form-group {
            margin-bottom: 0.75rem;
        }
        
        /* Make main add button fixed */
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
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Admin @endslot
        @slot('li_2') Notifications @endslot
        @slot('title') Announcements @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                        <h4 class="card-title mb-2">Manage Announcements</h4>
                        <div class="add-btn-wrapper">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
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

                    @if($announcements->isEmpty())
                        <div class="text-center py-4">
                            <i class="bx bx-info-circle font-size-40 text-muted"></i>
                            <h5 class="mt-3">No Announcements</h5>
                            <p class="text-muted">There are no announcements available. Add your first announcement to display on the dashboard.</p>
                        </div>
                    @else
                        <div class="announcement-list">
                            @foreach($announcements as $announcement)
                                <div class="announcement-item {{ $announcement->type }} card">
                                    <div class="announcement-header">
                                        <div>
                                            <h5 class="mb-1">
                                                <i class="{{ $announcement->icon }} me-2"></i>
                                                {{ $announcement->title }}
                                            </h5>
                                        </div>
                                        <div class="metadata-section">
                                            <small>
                                                <span class="me-2">Type: <span class="badge bg-{{ $announcement->type }}">{{ ucfirst($announcement->type) }}</span></span>
                                                <span class="me-2">Order: #{{ $announcement->display_order }}</span>
                                                @if($announcement->start_date || $announcement->end_date)
                                                    <span class="me-2">
                                                        Duration: 
                                                        {{ $announcement->start_date ? $announcement->start_date->format('M d, Y') : 'Any time' }}
                                                        -
                                                        {{ $announcement->end_date ? $announcement->end_date->format('M d, Y') : 'No end date' }}
                                                    </span>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    <div class="announcement-preview">
                                        {!! $announcement->content !!}
                                    </div>
                                    <div class="announcement-controls">
                                        <label class="toggle-switch me-2">
                                            <input type="checkbox" class="toggle-status" data-id="{{ $announcement->id }}" {{ $announcement->is_enabled ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <button type="button" class="btn btn-sm btn-info edit-btn" data-id="{{ $announcement->id }}">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $announcement->id }}">
                                            <i class="bx bx-trash"></i>
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

    <!-- Add Announcement Modal -->
    <div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAnnouncementModalLabel">Add New Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addAnnouncementForm" action="{{ route('admin.notifications.announcements.store') }}" method="POST">
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
                                <label for="display_order" class="form-label">Display Order</label>
                                <input type="number" class="form-control" id="display_order" name="display_order" value="0" min="0" required>
                                <small class="text-muted">Lower numbers appear first</small>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="icon" class="form-label">Icon (Optional)</label>
                                <input type="text" class="form-control" id="icon" name="icon" placeholder="bx bx-info-circle">
                                <small class="text-muted">BoxIcons or other icon class</small>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_enabled" name="is_enabled" checked>
                                    <label class="form-check-label" for="is_enabled">Enable Announcement</label>
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
                                <textarea id="content" name="content" class="froala-editor"></textarea>
                            </div>
                            
                            <!-- Upload progress indicator -->
                            <div id="upload-progress" class="progress mt-2">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                            <div id="upload-status" class="mt-1 small"></div>
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

    <!-- Edit Announcement Modal -->
    <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAnnouncementModalLabel">Edit Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editAnnouncementForm" method="POST">
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
                                <label for="edit_display_order" class="form-label">Display Order</label>
                                <input type="number" class="form-control" id="edit_display_order" name="display_order" min="0" required>
                                <small class="text-muted">Lower numbers appear first</small>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="edit_icon" class="form-label">Icon (Optional)</label>
                                <input type="text" class="form-control" id="edit_icon" name="icon" placeholder="bx bx-info-circle">
                                <small class="text-muted">BoxIcons or other icon class</small>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="edit_is_enabled" name="is_enabled">
                                    <label class="form-check-label" for="edit_is_enabled">Enable Announcement</label>
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
                                <textarea id="edit_content" name="content" class="froala-editor"></textarea>
                            </div>
                            
                            <!-- Upload progress indicator -->
                            <div id="edit-upload-progress" class="progress mt-2">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                            <div id="edit-upload-status" class="mt-1 small"></div>
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
    
    // Initialize elements
    const uploadProgress = document.getElementById('upload-progress');
    const progressBar = uploadProgress.querySelector('.progress-bar');
    const uploadStatus = document.getElementById('upload-status');
    
    const editUploadProgress = document.getElementById('edit-upload-progress');
    const editProgressBar = editUploadProgress.querySelector('.progress-bar');
    const editUploadStatus = document.getElementById('edit-upload-status');
    
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
    
    // Common function to initialize editor with mobile optimizations
    function initializeEditor(selector, uploadProgressEl, progressBarEl, uploadStatusEl) {
        const editorConfig = {
            // License key
            key: "1C%kZPBZ`JWSDBCQ@ZGD\\@\\JXDAAOZWJh,/.!==",
            
            // Basic configuration
            height: isMobile ? 200 : 250,
            placeholderText: 'Write your announcement content here...',
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
            
            // Events
            events: {
                'initialized': function() {
                    // Set up custom image upload handler
                    const that = this;
                    
                    // Create a function to upload images
                    function uploadImage(file, editor) {
                        // Show upload progress
                        uploadProgressEl.style.display = 'block';
                        progressBarEl.style.width = '30%';
                        uploadStatusEl.innerHTML = 'Uploading image...';
                        
                        const formData = new FormData();
                        formData.append('image', file);
                        formData.append('_token', csrfToken);
                        
                        fetch('/admin/upload-image', {
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
                            progressBarEl.style.width = '100%';
                            
                            if (data.success && data.data && data.data.url) {
                                // Success! Insert the image
                                editor.image.insert(data.data.url, null, null, editor.image.get());
                                
                                uploadStatusEl.innerHTML = '<span class="text-success"><i class="bx bx-check-circle me-1"></i> Image uploaded successfully!</span>';
                                
                                // Hide progress bar after a delay
                                setTimeout(() => {
                                    uploadProgressEl.style.display = 'none';
                                    progressBarEl.style.width = '0%';
                                    uploadStatusEl.innerHTML = '';
                                }, 3000);
                            } else {
                                // Error from server
                                uploadStatusEl.innerHTML = `<span class="text-danger"><i class="bx bx-error-circle me-1"></i> ${data.error || 'Failed to upload image'}</span>`;
                                
                                // Hide progress bar after a delay
                                setTimeout(() => {
                                    uploadProgressEl.style.display = 'none';
                                    progressBarEl.style.width = '0%';
                                }, 5000);
                            }
                        })
                        .catch(error => {
                            console.error('Image upload error:', error);
                            uploadStatusEl.innerHTML = '<span class="text-danger"><i class="bx bx-error-circle me-1"></i> Error uploading image. Please try again.</span>';
                            
                            // Hide progress bar after a delay
                            setTimeout(() => {
                                uploadProgressEl.style.display = 'none';
                                progressBarEl.style.width = '0%';
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
                        }
                    });
                },
                
                // Handle image insertion after upload
                'image.inserted': function($img) {
                    // Ensure images are responsive
                    $img.addClass('img-fluid');
                }
            }
        };
        
        return new FroalaEditor(selector, editorConfig);
    }
    
    // Initialize Froala Editor for 'Add' form
    const editor = initializeEditor('#content', uploadProgress, progressBar, uploadStatus);
    
    // Show mobile-optimized toast message
    function showToast(message, type = 'success') {
        // Use SweetAlert for better mobile compatibility
        const Toast = Swal.mixin({
            toast: true,
            position: isMobile ? 'center' : 'bottom-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
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
    
    // Variable to store the edit editor instance
    let editEditor = null;
    
    // Handle toggle switches with better mobile support
    $('.toggle-status').on('click', function(e) {
        // Prevent default to avoid accidental clicks on mobile
        e.stopPropagation();
        
        const announcementId = $(this).data('id');
        const isEnabled = $(this).prop('checked');
        const toggleSwitch = $(this);
        
        $.ajax({
            url: `{{ url('admin/notifications/announcements') }}/${announcementId}/toggle`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showToast(`Announcement has been ${response.enabled ? 'enabled' : 'disabled'}.`, 'success');
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
    
    // Mobile-optimized delete confirmation
    $('.delete-btn').on('click', function() {
        const announcementId = $(this).data('id');
        
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
                window.location.href = `{{ url('admin/notifications/announcements') }}/${announcementId}/delete`;
            }
        });
    });
    
    // Handle edit button with mobile optimizations
    $('.edit-btn').on('click', function() {
        const announcementId = $(this).data('id');
        
        // Show loading state
        if (isMobile) {
            Swal.fire({
                title: 'Loading...',
                text: 'Please wait while we fetch the announcement data.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        
        // Fetch announcement data
        $.ajax({
            url: `{{ url('admin/notifications/announcements') }}/${announcementId}/edit`,
            type: 'GET',
            success: function(response) {
                if (isMobile) {
                    Swal.close();
                }
                
                if (response.announcement) {
                    const announcement = response.announcement;
                    
                    // Set form action
                    $('#editAnnouncementForm').attr('action', `{{ url('admin/notifications/announcements') }}/${announcement.id}`);
                    
                    // Fill form fields
                    $('#edit_title').val(announcement.title);
                    $('#edit_type').val(announcement.type);
                    $('#edit_display_order').val(announcement.display_order);
                    $('#edit_icon').val(announcement.icon);
                    $('#edit_is_enabled').prop('checked', announcement.is_enabled);
                    
                    if (announcement.start_date) {
                        $('#edit_start_date').flatpickr().setDate(announcement.start_date);
                    } else {
                        $('#edit_start_date').flatpickr().clear();
                    }
                    
                    if (announcement.end_date) {
                        $('#edit_end_date').flatpickr().setDate(announcement.end_date);
                    } else {
                        $('#edit_end_date').flatpickr().clear();
                    }
                    
                    // Initialize or destroy and reinitialize editor
                    if (editEditor) {
                        editEditor.destroy();
                    }
                    
                    // Show modal
                    $('#editAnnouncementModal').modal('show');
                    
                    // Initialize the editor after a short delay to ensure the modal is visible
                    setTimeout(() => {
                        editEditor = initializeEditor('#edit_content', editUploadProgress, editProgressBar, editUploadStatus);
                        
                        // Set content to editor after initialization
                        editEditor.html.set(announcement.content);
                    }, 300);
                } else {
                    showToast('Failed to load announcement data.', 'error');
                }
            },
            error: function() {
                if (isMobile) {
                    Swal.close();
                }
                showToast('Failed to load announcement data due to a server error.', 'error');
            }
        });
    });
    
    // Form submission with improved mobile UX
    function handleFormSubmission(form, submitBtn, successMessage, formType = 'add') {
        // Store original button text
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.html('<span class="spinner-border spinner-border-sm me-1"></span> ' + (formType === 'add' ? 'Creating...' : 'Updating...'));
        submitBtn.prop('disabled', true);
        
        // Get form data for AJAX submission
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
                text: 'Failed to ' + (formType === 'add' ? 'create' : 'update') + ' announcement. Please try again.',
                confirmButtonColor: '#f46a6a'
            });
            
            // Reset button
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);
        });
    }
    
    // Handle form submission for add announcement form
    $('#addAnnouncementForm').on('submit', function(e) {
        e.preventDefault();
        
        // Make sure editor content is updated
        $('#content').val(editor.html.get());
        
        handleFormSubmission(
            this, 
            $(this).find('button[type="submit"]'), 
            'Announcement created successfully!',
            'add'
        );
    });
    
    // Handle form submission for edit announcement form
    $('#editAnnouncementForm').on('submit', function(e) {
        e.preventDefault();
        
        // Make sure editor content is updated
        $('#edit_content').val(editEditor.html.get());
        
        handleFormSubmission(
            this, 
            $(this).find('button[type="submit"]'), 
            'Announcement updated successfully!',
            'edit'
        );
    });
    
    // Add specific mobile event handlers
    if (isMobile) {
        // Close modal when keyboard appears to provide more space
        const modals = document.querySelectorAll('.modal');
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
    }
});
</script>
@endsection