@extends('layouts.master')

@section('title') WebFTP Settings @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Settings @endslot
        @slot('title') WebFTP Settings @endslot
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

                    <form action="{{ route('admin.webftp.settings.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <h5 class="font-size-14 mb-3">General Settings</h5>
                                    <div class="form-check form-switch form-switch-lg mb-3">
                                        <input type="checkbox" class="form-check-input" id="enabled" name="enabled" 
                                            @if($settings->enabled) checked @endif>
                                        <label class="form-check-label" for="enabled">Enable WebFTP</label>
                                        <p class="text-muted small">When disabled, users will be redirected to the external file manager.</p>
                                    </div>
                                    
                                    <div class="form-check form-switch form-switch-lg mb-3">
                                        <input type="checkbox" class="form-check-input" id="use_external_service" name="use_external_service" 
                                            @if($settings->use_external_service) checked @endif>
                                        <label class="form-check-label" for="use_external_service">Use External Service as Fallback</label>
                                        <p class="text-muted small">Use external file manager service if WebFTP encounters errors.</p>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h5 class="font-size-14 mb-3">File Handling Settings</h5>
                                    <div class="mb-3">
                                        <label class="form-label">Maximum Upload Size (MB)</label>
                                        <input type="number" class="form-control" name="max_upload_size" value="{{ $settings->max_upload_size }}" 
                                            min="1" max="100">
                                        <p class="text-muted small">Maximum file size for uploads in megabytes (1-100MB).</p>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="allow_zip_operations" name="allow_zip_operations" 
                                            @if($settings->allow_zip_operations) checked @endif>
                                        <label class="form-check-label" for="allow_zip_operations">Allow Zip Operations</label>
                                        <p class="text-muted small">Allow users to zip and unzip files.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <h5 class="font-size-14 mb-3">Editor Appearance</h5>
                                    <div class="mb-3">
                                        <label class="form-label">Editor Theme</label>
                                        <select class="form-select" name="editor_theme">
                                            <option value="monokai" @if($settings->editor_theme == 'monokai') selected @endif>Monokai</option>
                                            <option value="github" @if($settings->editor_theme == 'github') selected @endif>GitHub</option>
                                            <option value="tomorrow" @if($settings->editor_theme == 'tomorrow') selected @endif>Tomorrow</option>
                                            <option value="kuroir" @if($settings->editor_theme == 'kuroir') selected @endif>Kuroir</option>
                                            <option value="twilight" @if($settings->editor_theme == 'twilight') selected @endif>Twilight</option>
                                            <option value="xcode" @if($settings->editor_theme == 'xcode') selected @endif>XCode</option>
                                            <option value="textmate" @if($settings->editor_theme == 'textmate') selected @endif>TextMate</option>
                                            <option value="solarized_dark" @if($settings->editor_theme == 'solarized_dark') selected @endif>Solarized Dark</option>
                                            <option value="solarized_light" @if($settings->editor_theme == 'solarized_light') selected @endif>Solarized Light</option>
                                        </select>
                                        <p class="text-muted small">Choose the color theme for the code editor.</p>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h5 class="font-size-14 mb-3">Editor Features</h5>
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0">Code Editor Features</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-check mb-3">
                                                <input type="checkbox" class="form-check-input" id="code_beautify" name="code_beautify" 
                                                    @if($settings->code_beautify) checked @endif>
                                                <label class="form-check-label" for="code_beautify">Code Beautify</label>
                                                <p class="text-muted small">Automatically format and indent code.</p>
                                            </div>
                                            
                                            <div class="form-check mb-3">
                                                <input type="checkbox" class="form-check-input" id="code_suggestion" name="code_suggestion" 
                                                    @if($settings->code_suggestion) checked @endif>
                                                <label class="form-check-label" for="code_suggestion">Code Suggestion</label>
                                                <p class="text-muted small">Show code suggestions as you type.</p>
                                            </div>
                                            
                                            <div class="form-check mb-3">
                                                <input type="checkbox" class="form-check-input" id="auto_complete" name="auto_complete" 
                                                    @if($settings->auto_complete) checked @endif>
                                                <label class="form-check-label" for="auto_complete">Auto Complete</label>
                                                <p class="text-muted small">Enable code auto-completion.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Update Settings</button>
                                <a href="{{ route('admin.mofh.settings') }}" class="btn btn-secondary waves-effect ms-1">Back to MOFH Settings</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">WebFTP Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h5 class="font-size-14 mb-3">About WebFTP</h5>
                                <p>The WebFTP feature provides a browser-based file manager and code editor for your hosting customers. It allows them to manage files directly without using third-party FTP clients.</p>
                            </div>
                            
                            <div class="mb-4">
                                <h5 class="font-size-14 mb-3">Server Requirements</h5>
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        PHP FTP Extension
                                        <span class="badge bg-{{ extension_loaded('ftp') ? 'success' : 'danger' }} rounded-pill">
                                            {{ extension_loaded('ftp') ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        PHP ZipArchive
                                        <span class="badge bg-{{ class_exists('ZipArchive') ? 'success' : 'danger' }} rounded-pill">
                                            {{ class_exists('ZipArchive') ? 'Available' : 'Unavailable' }}
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Temporary Directory
                                        <span class="badge bg-{{ is_writable(storage_path('app/temp')) ? 'success' : 'danger' }} rounded-pill">
                                            {{ is_writable(storage_path('app/temp')) ? 'Writable' : 'Not Writable' }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h5 class="font-size-14 mb-3">Features</h5>
                                <ul>
                                    <li>Browse and manage files and directories</li>
                                    <li>Upload and download files</li>
                                    <li>Create, edit, rename, and delete files</li>
                                    <li>Ace code editor with syntax highlighting</li>
                                    <li>Code beautification and auto-completion</li>
                                    <li>Zip and unzip operations</li>
                                </ul>
                            </div>
                            
                            <div class="mb-4">
                                <h5 class="font-size-14 mb-3">Troubleshooting</h5>
                                <div class="alert alert-info">
                                    <p class="mb-2"><strong>Connection Issues:</strong> Make sure your server can connect to ftpupload.net on port 21.</p>
                                    <p class="mb-2"><strong>Timeout Errors:</strong> Consider increasing PHP max_execution_time for large files.</p>
                                    <p class="mb-0"><strong>Out of Memory:</strong> Adjust PHP memory_limit if needed for large operations.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

