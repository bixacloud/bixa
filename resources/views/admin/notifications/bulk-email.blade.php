@extends('layouts.master')

@section('title') Email Notifications @endsection

@section('css')
<!-- Froala Editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css" rel="stylesheet">
<!-- Font Awesome for Froala icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<!-- CodeMirror CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/theme/monokai.min.css">
<!-- Froala Code View Plugin CSS -->
<link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/plugins/code_view.min.css" rel="stylesheet">

<!-- Sweet Alert css -->
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    .email-preview {
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        padding: 15px;
        margin-top: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        background-color: #fff;
    }
    
    .email-preview-header {
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }
    
    .email-preview-body {
        min-height: 150px;
    }
    
    .smtp-settings-card {
        height: 100%;
    }
    
    .smtp-settings-status {
        font-size: 14px;
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 4px;
    }
    
    .smtp-settings-status.enabled {
        background-color: rgba(10, 179, 156, 0.18);
        color: #0ab39c;
    }
    
    .smtp-settings-status.disabled {
        background-color: rgba(244, 106, 106, 0.18);
        color: #f46a6a;
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
        height: 300px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 14px;
    }
    
    /* Upload progress indicator */
    #upload-progress {
        display: none;
        margin-top: 10px;
    }
    
    #upload-progress.show {
        display: block;
    }
    
    #upload-status {
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
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Admin @endslot
        @slot('li_2') Notifications @endslot
        @slot('title') Email Notifications @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-4">
            <div class="card smtp-settings-card">
                <div class="card-body">
                    <h4 class="card-title mb-4">SMTP Settings Status</h4>
                    
                    @if($smtpSettings && $smtpSettings->status)
                        <div class="smtp-settings-status enabled">
                            <i class="bx bx-check-circle me-1"></i> SMTP is configured and enabled
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <tbody>
                                    <tr>
                                        <th scope="row">Host</th>
                                        <td>{{ $smtpSettings->hostname }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Port</th>
                                        <td>{{ $smtpSettings->port }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Username</th>
                                        <td>{{ $smtpSettings->username }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">From Email</th>
                                        <td>{{ $smtpSettings->from_email }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">From Name</th>
                                        <td>{{ $smtpSettings->from_name }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Encryption</th>
                                        <td>{{ strtoupper($smtpSettings->encryption) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('admin.settings.smtp') }}" class="btn btn-primary w-100">
                                <i class="bx bx-cog me-1"></i> Manage SMTP Settings
                            </a>
                        </div>
                    @else
                        <div class="smtp-settings-status disabled">
                            <i class="bx bx-error-circle me-1"></i> SMTP is not configured or disabled
                        </div>
                        
                        <p class="text-muted">
                            You need to configure SMTP settings before sending email notifications.
                        </p>
                        
                        <div class="mt-4">
                            <a href="{{ route('admin.settings.smtp') }}" class="btn btn-primary w-100">
                                <i class="bx bx-cog me-1"></i> Configure SMTP Settings
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Send Email Notification</h4>
                    
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
                    
                    @if(!$smtpSettings || !$smtpSettings->status)
                        <div class="alert alert-warning" role="alert">
                            <h5 class="alert-heading">SMTP Not Configured</h5>
                            <p>You need to configure and enable SMTP settings before sending email notifications.</p>
                            <hr>
                            <p class="mb-0">Go to <a href="{{ route('admin.settings.smtp') }}" class="alert-link">SMTP Settings</a> to configure your email server.</p>
                        </div>
                    @else
                        <form action="{{ route('admin.notifications.bulk-email.send') }}" method="POST" id="emailForm">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Email Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="content" class="form-label">Email Content</label>
                                <!-- Editor Container -->
                                <div class="editor-container">
                                    <textarea id="content" name="content" class="froala-editor"></textarea>
                                </div>
                                
                                <!-- Upload progress indicator -->
                                <div id="upload-progress" class="progress mt-2">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                </div>
                                <div id="upload-status" class="mt-1 small"></div>
                                
                                <small class="text-muted">This email will be sent to all {{ $userCount }} users.</small>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="test_email" name="test_email">
                                <label class="form-check-label" for="test_email">
                                    Send test email to me first ({{ auth()->user()->email }})
                                </label>
                            </div>
                            
                            <div class="text-end">
                                <button type="button" id="previewBtn" class="btn btn-secondary me-2">
                                    <i class="bx bx-show me-1"></i> Preview
                                </button>
                                <button type="submit" class="btn btn-primary" id="sendEmailBtn">
                                    <i class="bx bx-envelope me-1"></i> 
                                    <span id="submitBtnText">Send Email Notification</span>
                                </button>
                            </div>
                        </form>
                        
                        <div class="email-preview d-none" id="emailPreview">
                            <div class="email-preview-header">
                                <h5 class="mb-1">Email Preview</h5>
                                <div class="text-muted">
                                    <small>Subject: <span id="previewSubject"></span></small>
                                </div>
                            </div>
                            <div class="email-preview-body" id="previewContent">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast Message -->
    <div id="toast-message" class="toast-message"></div>
@endsection

@section('script')
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

<!-- Sweet alert js -->
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize elements
    const uploadProgress = document.getElementById('upload-progress');
    const progressBar = uploadProgress.querySelector('.progress-bar');
    const uploadStatus = document.getElementById('upload-status');
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Initialize Froala Editor
    const editor = new FroalaEditor('#content', {
        // License key
        key: "1C%kZPBZ`JWSDBCQ@ZGD\\@\\JXDAAOZWJh,/.!==",
        
        // Basic configuration
        height: 300,
        placeholderText: 'Write your email content here...',
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
        
        // Simplified toolbar with only basic formatting, HTML mode and image upload
        toolbarButtons: [
            'bold', 'italic', 'underline', '|',
            'paragraphFormat', 'formatOL', 'formatUL', '|',
            'insertImage', '|',  
            'html'
        ],
        
        // Disable default image upload to use our custom handler
        imageUpload: false,
        
        // Events
        events: {
            'initialized': function() {
                // Set up custom image upload handler
                const that = this;
                
                // Create a function to upload images
                function uploadImage(file, editor) {
                    // Show upload progress
                    uploadProgress.style.display = 'block';
                    progressBar.style.width = '30%';
                    uploadStatus.innerHTML = 'Uploading image...';
                    
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
                        progressBar.style.width = '100%';
                        
                        if (data.success && data.data && data.data.url) {
                            // Success! Insert the image
                            editor.image.insert(data.data.url, null, null, editor.image.get());
                            
                            uploadStatus.innerHTML = '<span class="text-success"><i class="bx bx-check-circle me-1"></i> Image uploaded successfully!</span>';
                            
                            // Hide progress bar after a delay
                            setTimeout(() => {
                                uploadProgress.style.display = 'none';
                                progressBar.style.width = '0%';
                                uploadStatus.innerHTML = '';
                            }, 3000);
                        } else {
                            // Error from server
                            uploadStatus.innerHTML = `<span class="text-danger"><i class="bx bx-error-circle me-1"></i> ${data.error || 'Failed to upload image'}</span>`;
                            
                            // Hide progress bar after a delay
                            setTimeout(() => {
                                uploadProgress.style.display = 'none';
                                progressBar.style.width = '0%';
                            }, 5000);
                        }
                    })
                    .catch(error => {
                        console.error('Image upload error:', error);
                        uploadStatus.innerHTML = '<span class="text-danger"><i class="bx bx-error-circle me-1"></i> Error uploading image. Please try again.</span>';
                        
                        // Hide progress bar after a delay
                        setTimeout(() => {
                            uploadProgress.style.display = 'none';
                            progressBar.style.width = '0%';
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
    });
    
    // Show toast message
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast-message');
        toast.textContent = message;
        
        if (type === 'error') {
            toast.classList.add('error');
        } else {
            toast.classList.remove('error');
        }
        
        toast.classList.add('show');
        
        // Auto hide after 3 seconds
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
    
    // Show/hide preview
    $('#previewBtn').on('click', function() {
        const subject = $('#subject').val();
        const content = editor.html.get();
        
        if (!subject || !content) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Information',
                text: 'Please enter both subject and content before previewing.'
            });
            return;
        }
        
        $('#previewSubject').text(subject);
        $('#previewContent').html(content);
        $('#emailPreview').removeClass('d-none');
        
        // Scroll to preview
        $('html, body').animate({
            scrollTop: $('#emailPreview').offset().top - 20
        }, 500);
    });
    
    // Handle test email checkbox
    $('#test_email').on('change', function() {
        const isChecked = $(this).prop('checked');
        
        if (isChecked) {
            $('#submitBtnText').text('Send Test Email');
        } else {
            $('#submitBtnText').text('Send Email Notification');
        }
    });
    
    // Form submission confirmation
    $('#emailForm').on('submit', function(e) {
        e.preventDefault();
        
        // Make sure Froala content is updated
        const editorContent = editor.html.get();
        $('#content').val(editorContent);
        
        const form = this;
        const isTestEmail = $('#test_email').prop('checked');
        
        if (!isTestEmail) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will send an email to all {{ $userCount }} users. This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#556ee6',
                cancelButtonColor: '#f46a6a',
                confirmButtonText: 'Yes, send it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    $('#sendEmailBtn').prop('disabled', true);
                    $('#submitBtnText').html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Sending...');
                    
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Email notifications queued successfully!'
                        }).then(function() {
                            // Reset form
                            form.reset();
                            editor.html.set('');
                            $('#submitBtnText').text('Send Email Notification');
                            $('#sendEmailBtn').prop('disabled', false);
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to send email notifications. Please try again.'
                        });
                        
                        // Reset button
                        $('#submitBtnText').text('Send Email Notification');
                        $('#sendEmailBtn').prop('disabled', false);
                    });
                }
            });
        } else {
            // For test email
            // Show loading state
            $('#sendEmailBtn').prop('disabled', true);
            $('#submitBtnText').html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Sending Test...');
            
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
                Swal.fire({
                    icon: 'success',
                    title: 'Test Email Sent',
                    text: 'Test email has been sent to your email address.'
                });
                
                // Reset button
                $('#submitBtnText').text('Send Test Email');
                $('#sendEmailBtn').prop('disabled', false);
            })
            .catch(error => {
                console.error('Error:', error);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to send test email. Please try again.'
                });
                
                // Reset button
                $('#submitBtnText').text('Send Test Email');
                $('#sendEmailBtn').prop('disabled', false);
            });
        }
    });
});
</script>
@endsection