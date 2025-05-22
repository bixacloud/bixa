@extends('layouts.master')

@section('title') @lang('translation.Web_File_Manager') @endsection

@section('css')
<link href="{{ URL::asset('/build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    /* Main styling */
    .clipboard-badge {
        margin-left: 10px;
        align-self: center;
    }
    .selected-row {
        background-color: rgba(0, 123, 255, 0.1) !important;
    }
    .root-directory-alert {
        border-left: 4px solid #17a2b8;
    }
    
    /* Drag & Drop Upload */
    .upload-dropzone {
        border: 2px dashed #ccc;
        border-radius: 6px;
        background-color: #f9f9f9;
        transition: all 0.3s ease;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        margin-bottom: 15px;
    }
    
    .upload-dropzone.highlight {
        border-color: #6366f1;
        background-color: #f1f5f9;
    }
    
    .upload-dropzone i {
        font-size: 2rem;
        color: #6c757d;
        margin-bottom: 10px;
    }
    
    .upload-progress-item {
        margin-bottom: 8px;
    }
    
    .progress {
        height: 6px;
        margin-top: 5px;
    }
    
    #upload-progress-container {
        max-height: 200px;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.Hosting') @endslot
        @slot('li_2') 
            <a href="{{ route('hosting.view', $account->username) }}">{{ $account->label }}</a>
        @endslot
        @slot('title') @lang('translation.Web_File_Manager') @endslot
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

                    @if(isset($isRootDirectory) && $isRootDirectory)
                        <div class="alert alert-info root-directory-alert">
                            <i class="bx bx-info-circle me-1"></i> @lang('translation.Root_directory_message')
                        </div>
                    @endif

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <a href="{{ route('hosting.view', $account->username) }}" class="btn btn-light">
                            <i class="bx bx-arrow-back"></i> @lang('translation.Back_to_Account')
                        </a>
                        
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="bx bx-upload"></i> @lang('translation.Upload')
                        </button>
                        
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDirModal">
                            <i class="bx bx-folder-plus"></i> @lang('translation.New_Folder')
                        </button>
                        
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFileModal">
                            <i class="bx bx-file-plus"></i> @lang('translation.New_File')
                        </button>
                        
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="clipboardOperationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-clipboard"></i> @lang('translation.Clipboard')
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="clipboardOperationsDropdown">
                                <li>
                                    <a class="dropdown-item disabled" href="#" id="copySelectedBtn">
                                        <i class="bx bx-copy"></i> @lang('translation.Copy_Selected')
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item disabled" href="#" id="cutSelectedBtn">
                                        <i class="bx bx-cut"></i> @lang('translation.Cut_Selected')
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item disabled" href="#" id="pasteBtn">
                                        <i class="bx bx-paste"></i> @lang('translation.Paste_Here')
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item disabled" href="#" id="clearClipboardBtn">
                                        <i class="bx bx-trash"></i> @lang('translation.Clear_Clipboard')
                                    </a>
                                </li>
                            </ul>
                        </div>
                        
                        @if(isset($settings) && $settings->allow_zip_operations)
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-warning dropdown-toggle" type="button" id="zipOperationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-archive"></i> @lang('translation.Zip_Operations')
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="zipOperationsDropdown">
                                <li>
                                    <a class="dropdown-item" href="#" id="zipSelectedBtn">
                                        <i class="bx bx-archive-in"></i> @lang('translation.Zip_Selected')
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" id="extractZipBtn">
                                        <i class="bx bx-archive-out"></i> @lang('translation.Extract_Zip')
                                    </a>
                                </li>
                            </ul>
                        </div>
                        @endif
                        
                        <button type="button" class="btn btn-danger" id="deleteSelectedBtn" disabled>
                            <i class="bx bx-trash"></i> @lang('translation.Delete_Selected')
                        </button>
                        
                        <span class="badge bg-info clipboard-badge d-none" id="clipboard-badge"></span>
                    </div>
					<!-- Breadcrumbs -->
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb bg-light p-2">
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

                    <!-- File listing -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th width="30">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </div>
                                    </th>
                                    <th>@lang('translation.Name')</th>
                                    <th>@lang('translation.Size')</th>
                                    <th>@lang('translation.Last_Modified')</th>
                                    <th>@lang('translation.Permissions')</th>
                                    <th>@lang('translation.Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($listing) == 0)
                                    <tr>
                                        <td colspan="6" class="text-center">@lang('translation.No_files_found')</td>
                                    </tr>
                                @else
                                    @if($currentPath != '/')
                                        <tr>
                                            <td></td>
                                            <td>
                                                <a href="{{ route('webftp.index', ['username' => $account->username, 'path' => dirname($currentPath)]) }}" class="text-decoration-none">
                                                    <i class="bx bx-arrow-back text-primary"></i> ..
                                                </a>
                                            </td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                    @endif
                                    
                                    @foreach($listing as $item)
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input item-checkbox" 
                                                        data-path="{{ $item['path'] }}" 
                                                        data-is-dir="{{ $item['is_dir'] ? 'true' : 'false' }}"
                                                        data-name="{{ $item['name'] }}">
                                                </div>
                                            </td>
                                            <td>
                                                @if($item['is_dir'])
                                                    <a href="{{ route('webftp.index', ['username' => $account->username, 'path' => $item['path']]) }}" class="text-decoration-none">
                                                        <i class="bx bx-{{ $item['icon'] }} text-warning"></i> {{ $item['name'] }}
                                                    </a>
                                                @elseif(in_array($item['type'], ['html', 'php', 'css', 'javascript', 'json', 'text', 'markdown', 'xml', 'sql']))
                                                    <a href="{{ route('webftp.edit', ['username' => $account->username, 'path' => $item['path']]) }}" class="text-decoration-none">
                                                        <i class="bx bx-{{ $item['icon'] }} text-primary"></i> {{ $item['name'] }}
                                                    </a>
                                                @else
                                                    <span>
                                                        <i class="bx bx-{{ $item['icon'] }} text-secondary"></i> {{ $item['name'] }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $item['formatted_size'] }}</td>
                                            <td>{{ date('Y-m-d H:i', $item['timestamp']) }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark font-monospace">{{ $item['permissions'] }}</span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bx bx-dots-horizontal-rounded"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @if($item['is_dir'])
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('webftp.index', ['username' => $account->username, 'path' => $item['path']]) }}">
                                                                    <i class="bx bx-folder-open"></i> @lang('translation.Open')
                                                                </a>
                                                            </li>
                                                        @elseif(in_array($item['type'], ['html', 'php', 'css', 'javascript', 'json', 'text', 'markdown', 'xml', 'sql']))
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('webftp.edit', ['username' => $account->username, 'path' => $item['path']]) }}">
                                                                    <i class="bx bx-edit"></i> @lang('translation.Edit')
                                                                </a>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <a class="dropdown-item rename-item" href="#" 
                                                                data-path="{{ $item['path'] }}" 
                                                                data-name="{{ $item['name'] }}">
                                                                <i class="bx bx-rename"></i> @lang('translation.Rename')
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('webftp.download', ['username' => $account->username, 'path' => $item['path']]) }}">
                                                                <i class="bx bx-download"></i> @lang('translation.Download')
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item chmod-item" href="#" 
                                                                data-path="{{ $item['path'] }}" 
                                                                data-is-dir="{{ $item['is_dir'] ? 'true' : 'false' }}"
                                                                data-name="{{ $item['name'] }}" 
                                                                data-permissions="{{ $item['permissions'] }}">
                                                                <i class="bx bx-lock-open"></i> @lang('translation.Change_Permissions')
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item copy-item" href="#"
                                                                data-path="{{ $item['path'] }}"
                                                                data-is-dir="{{ $item['is_dir'] ? 'true' : 'false' }}"
                                                                data-name="{{ $item['name'] }}">
                                                                <i class="bx bx-copy"></i> @lang('translation.Copy')
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item cut-item" href="#"
                                                                data-path="{{ $item['path'] }}"
                                                                data-is-dir="{{ $item['is_dir'] ? 'true' : 'false' }}"
                                                                data-name="{{ $item['name'] }}">
                                                                <i class="bx bx-cut"></i> @lang('translation.Cut')
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item delete-item text-danger" href="#" 
                                                                data-path="{{ $item['path'] }}" 
                                                                data-is-dir="{{ $item['is_dir'] ? 'true' : 'false' }}"
                                                                data-name="{{ $item['name'] }}">
                                                                <i class="bx bx-trash"></i> @lang('translation.Delete')
                                                            </a>
                                                        </li>
                                                        @if(!$item['is_dir'] && $item['type'] == 'archive' && isset($settings) && $settings->allow_zip_operations)
                                                            <li>
                                                                <a class="dropdown-item extract-zip" href="#" 
                                                                    data-path="{{ $item['path'] }}"
                                                                    data-name="{{ $item['name'] }}">
                                                                    <i class="bx bx-archive-out"></i> @lang('translation.Extract')
                                                                </a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<!-- Upload Modal with Drag & Drop -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">@lang('translation.Upload_Files')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Drag & Drop Zone -->
                    <div id="upload-dropzone" class="upload-dropzone">
                        <i class="bx bx-upload"></i>
                        <p class="mb-0">@lang('translation.Drag_drop_files')</p>
                        <p class="text-muted small">@lang('translation.Max_file_size'): {{ isset($settings) ? $settings->max_upload_size : 10 }}MB</p>
                        <input type="file" id="file-input" name="files[]" style="display: none;" multiple>
                    </div>
                    
                    <!-- Upload Progress -->
                    <div id="upload-progress-container" style="display: none;">
                        <h6 class="mb-2">@lang('translation.Upload_Progress')</h6>
                        <div id="upload-progress-list"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('translation.Close')</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Directory Modal -->
    <div class="modal fade" id="createDirModal" tabindex="-1" aria-labelledby="createDirModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('webftp.createDirectory', ['username' => $account->username, 'path' => $currentPath]) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createDirModalLabel">@lang('translation.Create_New_Directory')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">@lang('translation.Directory_Name')</label>
                            <input type="text" class="form-control" id="name" name="name" required 
                                pattern="[a-zA-Z0-9_\-\.]+" title="@lang('translation.Only_allowed_chars')">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('translation.Cancel')</button>
                        <button type="submit" class="btn btn-primary">@lang('translation.Create')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create File Modal -->
    <div class="modal fade" id="createFileModal" tabindex="-1" aria-labelledby="createFileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('webftp.createFile', ['username' => $account->username, 'path' => $currentPath]) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createFileModalLabel">@lang('translation.Create_New_File')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">@lang('translation.File_Name')</label>
                            <input type="text" class="form-control" id="name" name="name" required 
                                pattern="[a-zA-Z0-9_\-\.]+" title="@lang('translation.Only_allowed_chars')">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('translation.Cancel')</button>
                        <button type="submit" class="btn btn-primary">@lang('translation.Create')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rename Modal -->
    <div class="modal fade" id="renameModal" tabindex="-1" aria-labelledby="renameModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="renameForm" method="POST">
                    @csrf
                    <input type="hidden" name="path" id="renamePath">
                    <div class="modal-header">
                        <h5 class="modal-title" id="renameModalLabel">@lang('translation.Rename_Item')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">@lang('translation.New_Name')</label>
                            <input type="text" class="form-control" id="renameName" name="name" required 
                                pattern="[a-zA-Z0-9_\-\.]+" title="@lang('translation.Only_allowed_chars')">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('translation.Cancel')</button>
                        <button type="submit" class="btn btn-primary">@lang('translation.Rename')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
	<!-- Extract Zip Modal -->
    <div class="modal fade" id="extractZipModal" tabindex="-1" aria-labelledby="extractZipModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('webftp.unzipFile', ['username' => $account->username, 'path' => $currentPath]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="zip_file" id="zipFilePath">
                    <div class="modal-header">
                        <h5 class="modal-title" id="extractZipModalLabel">@lang('translation.Extract_Zip_File')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <p>@lang('translation.Extract') <strong id="zipFileName"></strong> @lang('translation.to'):</p>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="extract_to" id="extractCurrent" value="{{ $currentPath }}" checked>
                                <label class="form-check-label" for="extractCurrent">
                                    @lang('translation.Current_Directory')
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="extract_to" id="extractNew" value="">
                                <label class="form-check-label" for="extractNew">
                                    @lang('translation.New_Folder_Auto_Created')
                                </label>
                            </div>
                            <div class="mt-2" id="newFolderInput" style="display: none;">
                                <input type="text" class="form-control" id="newFolderName" placeholder="@lang('translation.New_folder_name')">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('translation.Cancel')</button>
                        <button type="submit" class="btn btn-primary">@lang('translation.Extract')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Zip Selected Modal -->
    <div class="modal fade" id="zipSelectedModal" tabindex="-1" aria-labelledby="zipSelectedModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('webftp.zipFiles', ['username' => $account->username, 'path' => $currentPath]) }}" method="POST">
                    @csrf
                    <div id="selectedItemsContainer"></div>
                    <div class="modal-header">
                        <h5 class="modal-title" id="zipSelectedModalLabel">@lang('translation.Create_Zip_Archive')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="zip_name" class="form-label">@lang('translation.Zip_File_Name')</label>
                            <input type="text" class="form-control" id="zip_name" name="zip_name" required 
                                pattern="[a-zA-Z0-9_\-\.]+\.zip" title="@lang('translation.Filename_must_end_with_zip')">
                            <small class="text-muted">@lang('translation.Filename_must_end_with_zip')</small>
                        </div>
                        <div class="mb-3">
                            <p>@lang('translation.Selected_items_to_zip'):</p>
                            <ul id="selectedItemsList"></ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('translation.Cancel')</button>
                        <button type="submit" class="btn btn-primary">@lang('translation.Create_Zip')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Chmod Modal -->
    <div class="modal fade" id="chmodModal" tabindex="-1" aria-labelledby="chmodModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chmodModalLabel">@lang('translation.Change_Permissions')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="chmodPath">
                    <input type="hidden" id="chmodIsDir">
                    
                    <div class="mb-3">
                        <h6 id="chmodItemName"></h6>
                    </div>

                    <div class="mb-3">
                        <label for="permissionsInput" class="form-label">@lang('translation.Permissions_Octal')</label>
                        <input type="text" class="form-control" id="permissionsInput" placeholder="@lang('translation.eg_755')">
                    </div>
                    
                    <div class="mb-3">
                        <div class="row">
                            <div class="col">
                                <b>@lang('translation.Owner')</b>
                                <div class="form-check">
                                    <input class="form-check-input perm-checkbox" type="checkbox" id="ownerRead" data-value="400">
                                    <label class="form-check-label" for="ownerRead">@lang('translation.Read')</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input perm-checkbox" type="checkbox" id="ownerWrite" data-value="200">
                                    <label class="form-check-label" for="ownerWrite">@lang('translation.Write')</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input perm-checkbox" type="checkbox" id="ownerExec" data-value="100">
                                    <label class="form-check-label" for="ownerExec">@lang('translation.Execute')</label>
                                </div>
                            </div>
                            <div class="col">
                                <b>@lang('translation.Group')</b>
                                <div class="form-check">
                                    <input class="form-check-input perm-checkbox" type="checkbox" id="groupRead" data-value="40">
                                    <label class="form-check-label" for="groupRead">@lang('translation.Read')</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input perm-checkbox" type="checkbox" id="groupWrite" data-value="20">
                                    <label class="form-check-label" for="groupWrite">@lang('translation.Write')</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input perm-checkbox" type="checkbox" id="groupExec" data-value="10">
                                    <label class="form-check-label" for="groupExec">@lang('translation.Execute')</label>
                                </div>
                            </div>
                            <div class="col">
                                <b>@lang('translation.Others')</b>
                                <div class="form-check">
                                    <input class="form-check-input perm-checkbox" type="checkbox" id="otherRead" data-value="4">
                                    <label class="form-check-label" for="otherRead">@lang('translation.Read')</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input perm-checkbox" type="checkbox" id="otherWrite" data-value="2">
                                    <label class="form-check-label" for="otherWrite">@lang('translation.Write')</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input perm-checkbox" type="checkbox" id="otherExec" data-value="1">
                                    <label class="form-check-label" for="otherExec">@lang('translation.Execute')</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3" id="recursiveContainer">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="recursiveCheckbox">
                            <label class="form-check-label" for="recursiveCheckbox">
                                @lang('translation.Apply_recursively')
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('translation.Cancel')</button>
                    <button type="button" class="btn btn-primary" id="chmodApplyBtn">@lang('translation.Apply_Changes')</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script src="{{ URL::asset('/build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all checkbox
        const selectAllCheckbox = document.getElementById('selectAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
        
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
                updateRowSelection(checkbox);
            });
            updateDeleteSelectedButton();
            updateClipboardButtons();
        });
        
        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateDeleteSelectedButton();
                updateClipboardButtons();
                updateRowSelection(this);
                
                // Update "Select All" checkbox state
                let allChecked = true;
                itemCheckboxes.forEach(cb => {
                    if (!cb.checked) allChecked = false;
                });
                selectAllCheckbox.checked = allChecked;
            });
        });
        
        function updateRowSelection(checkbox) {
            const row = checkbox.closest('tr');
            if (checkbox.checked) {
                row.classList.add('selected-row');
            } else {
                row.classList.remove('selected-row');
            }
        }
        
        function updateDeleteSelectedButton() {
            let anyChecked = false;
            itemCheckboxes.forEach(checkbox => {
                if (checkbox.checked) anyChecked = true;
            });
            
            deleteSelectedBtn.disabled = !anyChecked;
        }
        
        // Delete item
        const deleteButtons = document.querySelectorAll('.delete-item');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const path = this.dataset.path;
                const isDir = this.dataset.isDir === 'true';
                const name = this.dataset.name;
                
                Swal.fire({
                    title: '@lang("translation.Delete_Item")',
                    text: `@lang("translation.Are_you_sure_delete") ${name}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '@lang("translation.Yes_delete_it")',
                    cancelButtonText: '@lang("translation.No_cancel")',
                    confirmButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteItem(path, isDir);
                    }
                });
            });
        });
        
        function deleteItem(path, isDir) {
            fetch('{{ route('webftp.delete', ['username' => $account->username]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    path: path,
                    is_dir: isDir
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '@lang("translation.Deleted")',
                        text: data.message,
                        icon: 'success'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: '@lang("translation.Error")',
                        text: data.message,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: '@lang("translation.Error")',
                    text: '@lang("translation.An_error_deleting_item")',
                    icon: 'error'
                });
            });
        }
        
        // Delete selected items
        deleteSelectedBtn.addEventListener('click', function() {
            const selectedItems = [];
            
            itemCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedItems.push({
                        path: checkbox.dataset.path,
                        isDir: checkbox.dataset.isDir === 'true',
                        name: checkbox.dataset.name
                    });
                }
            });
            
            if (selectedItems.length === 0) return;
            
            Swal.fire({
                title: '@lang("translation.Delete_Selected_Items")',
                text: `@lang("translation.Are_you_sure_delete_selected") ${selectedItems.length} @lang("translation.selected_items")?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '@lang("translation.Yes_delete_them")',
                cancelButtonText: '@lang("translation.No_cancel")',
                confirmButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    let deletePromises = [];
                    
                    selectedItems.forEach(item => {
                        const promise = fetch('{{ route('webftp.delete', ['username' => $account->username]) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                path: item.path,
                                is_dir: item.isDir
                            })
                        }).then(response => response.json());
                        
                        deletePromises.push(promise);
                    });
                    
                    Promise.all(deletePromises)
                        .then(() => {
                            Swal.fire({
                                title: '@lang("translation.Deleted")',
                                text: '@lang("translation.Selected_items_deleted")',
                                icon: 'success'
                            }).then(() => {
                                window.location.reload();
                            });
                        })
                        .catch(() => {
                            Swal.fire({
                                title: '@lang("translation.Error")',
                                text: '@lang("translation.An_error_deleting_some_items")',
                                icon: 'error'
                            }).then(() => {
                                window.location.reload();
                            });
                        });
                }
            });
        });
        
        // Rename item
        const renameButtons = document.querySelectorAll('.rename-item');
        const renameModal = document.getElementById('renameModal');
        const renameForm = document.getElementById('renameForm');
        const renamePath = document.getElementById('renamePath');
        const renameName = document.getElementById('renameName');
        
        renameButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const path = this.dataset.path;
                const name = this.dataset.name;
                
                renamePath.value = path;
                renameName.value = name;
                
                const modal = new bootstrap.Modal(renameModal);
                modal.show();
            });
        });
        
        renameForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch('{{ route('webftp.rename', ['username' => $account->username]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    path: renamePath.value,
                    name: renameName.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '@lang("translation.Renamed")',
                        text: data.message,
                        icon: 'success'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: '@lang("translation.Error")',
                        text: data.message,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: '@lang("translation.Error")',
                    text: '@lang("translation.An_error_renaming_item")',
                    icon: 'error'
                });
            });
        });
        
        // Extract zip
        const extractZipButtons = document.querySelectorAll('.extract-zip');
        const extractZipModal = document.getElementById('extractZipModal');
        const zipFilePath = document.getElementById('zipFilePath');
        const zipFileName = document.getElementById('zipFileName');
        const extractNew = document.getElementById('extractNew');
        const extractCurrent = document.getElementById('extractCurrent');
        const newFolderInput = document.getElementById('newFolderInput');
        const newFolderName = document.getElementById('newFolderName');
        
        extractZipButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const path = this.dataset.path;
                const name = this.dataset.name;
                
                zipFilePath.value = path;
                zipFileName.textContent = name;
                
                // Reset form
                extractCurrent.checked = true;
                newFolderInput.style.display = 'none';
                newFolderName.value = name.replace(/\.zip$/, '');
                
                const modal = new bootstrap.Modal(extractZipModal);
                modal.show();
            });
        });
        
        extractNew.addEventListener('change', function() {
            if (this.checked) {
                newFolderInput.style.display = 'block';
                extractNew.value = '{{ $currentPath }}/' + newFolderName.value;
            }
        });
        
        extractCurrent.addEventListener('change', function() {
            if (this.checked) {
                newFolderInput.style.display = 'none';
            }
        });
        
        newFolderName.addEventListener('input', function() {
            extractNew.value = '{{ $currentPath }}/' + this.value;
        });
        
        // Zip selected items
        const zipSelectedBtn = document.getElementById('zipSelectedBtn');
        const zipSelectedModal = document.getElementById('zipSelectedModal');
        const selectedItemsList = document.getElementById('selectedItemsList');
        const selectedItemsContainer = document.getElementById('selectedItemsContainer');
        const zipNameInput = document.getElementById('zip_name');
        
        if (zipSelectedBtn) {
            zipSelectedBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const selectedItems = [];
                
                itemCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        selectedItems.push({
                            path: checkbox.dataset.path,
                            name: checkbox.dataset.name
                        });
                    }
                });
                
                if (selectedItems.length === 0) {
                    Swal.fire({
                        title: '@lang("translation.No_Items_Selected")',
                        text: '@lang("translation.Please_select_item_to_zip")',
                        icon: 'warning'
                    });
                    return;
                }
                
                // Clear previous list and container
                selectedItemsList.innerHTML = '';
                selectedItemsContainer.innerHTML = '';
                
                // Fill the list and hidden inputs
                selectedItems.forEach((item, index) => {
                    const li = document.createElement('li');
                    li.textContent = item.name;
                    selectedItemsList.appendChild(li);
                    
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `items[${index}]`;
                    input.value = item.path;
                    selectedItemsContainer.appendChild(input);
                });
                
                // Set default zip name
                const timestamp = new Date().toISOString().replace(/[-:.]/g, '').substring(0, 14);
                zipNameInput.value = `archive_${timestamp}.zip`;
                
                const modal = new bootstrap.Modal(zipSelectedModal);
                modal.show();
            });
        }
        
        // Extracting dialog
        const extractZipBtn = document.getElementById('extractZipBtn');
        
        if (extractZipBtn) {
            extractZipBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const zipFiles = [];
                
                itemCheckboxes.forEach(checkbox => {
                    if (checkbox.checked && checkbox.dataset.name.endsWith('.zip')) {
                        zipFiles.push({
                            path: checkbox.dataset.path,
                            name: checkbox.dataset.name
                        });
                    }
                });
                
                if (zipFiles.length === 0) {
                    Swal.fire({
                        title: '@lang("translation.No_Zip_Files_Selected")',
                        text: '@lang("translation.Please_select_zip_to_extract")',
                        icon: 'warning'
                    });
                    return;
                }
                
                if (zipFiles.length > 1) {
                    Swal.fire({
                        title: '@lang("translation.Multiple_Zip_Files")',
                        text: '@lang("translation.Please_select_one_zip")',
                        icon: 'warning'
                    });
                    return;
                }
                
                zipFilePath.value = zipFiles[0].path;
                zipFileName.textContent = zipFiles[0].name;
                
                // Reset form
                extractCurrent.checked = true;
                newFolderInput.style.display = 'none';
                newFolderName.value = zipFiles[0].name.replace(/\.zip$/, '');
                
                const modal = new bootstrap.Modal(extractZipModal);
                modal.show();
            });
        }
		// Chmod functionality
        const chmodButtons = document.querySelectorAll('.chmod-item');
        const chmodModal = document.getElementById('chmodModal');
        const chmodPath = document.getElementById('chmodPath');
        const chmodIsDir = document.getElementById('chmodIsDir');
        const chmodItemName = document.getElementById('chmodItemName');
        const permissionsInput = document.getElementById('permissionsInput');
        const recursiveContainer = document.getElementById('recursiveContainer');
        const recursiveCheckbox = document.getElementById('recursiveCheckbox');
        const chmodApplyBtn = document.getElementById('chmodApplyBtn');
        const permCheckboxes = document.querySelectorAll('.perm-checkbox');

        // Add event listeners to chmod buttons
        chmodButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const path = this.dataset.path;
                const isDir = this.dataset.isDir === 'true';
                const name = this.dataset.name;
                const permissions = this.dataset.permissions;
                
                // Set modal values
                chmodPath.value = path;
                chmodIsDir.value = isDir;
                chmodItemName.textContent = name;
                
                // Parse current permissions
                parsePermissions(permissions);
                
                // Show/hide recursive option based on whether it's a directory
                recursiveContainer.style.display = isDir ? 'block' : 'none';
                recursiveCheckbox.checked = false;
                
                // Show the modal
                const modal = new bootstrap.Modal(chmodModal);
                modal.show();
            });
        });

        // Parse permissions string to checkboxes and input
        function parsePermissions(permString) {
            // Calculate octal value from permission string (e.g., -rwxr-xr--)
            let ownerValue = 0;
            if (permString[1] === 'r') ownerValue += 4;
            if (permString[2] === 'w') ownerValue += 2;
            if (permString[3] === 'x') ownerValue += 1;
            
            let groupValue = 0;
            if (permString[4] === 'r') groupValue += 4;
            if (permString[5] === 'w') groupValue += 2;
            if (permString[6] === 'x') groupValue += 1;
            
            let otherValue = 0;
            if (permString[7] === 'r') otherValue += 4;
            if (permString[8] === 'w') otherValue += 2;
            if (permString[9] === 'x') otherValue += 1;
            
            // Combine into three-digit octal format
            const octalValue = ownerValue * 100 + groupValue * 10 + otherValue;
            
            // Update input field
            permissionsInput.value = octalValue.toString().padStart(3, '0');
            
            // Update checkboxes
            updateCheckboxesFromOctal(octalValue);
        }

        // Update checkboxes based on octal value
        function updateCheckboxesFromOctal(octalValue) {
            // Instead of binary conversion, directly check each permission bit
            // Owner permissions (first digit of octal)
            const ownerBits = Math.floor(octalValue / 100);
            document.getElementById('ownerRead').checked = (ownerBits & 4) === 4;  // Check if bit 4 is set
            document.getElementById('ownerWrite').checked = (ownerBits & 2) === 2; // Check if bit 2 is set
            document.getElementById('ownerExec').checked = (ownerBits & 1) === 1;  // Check if bit 1 is set
            
            // Group permissions (second digit of octal)
            const groupBits = Math.floor((octalValue % 100) / 10);
            document.getElementById('groupRead').checked = (groupBits & 4) === 4;
            document.getElementById('groupWrite').checked = (groupBits & 2) === 2;
            document.getElementById('groupExec').checked = (groupBits & 1) === 1;
            
            // Others permissions (third digit of octal)
            const otherBits = octalValue % 10;
            document.getElementById('otherRead').checked = (otherBits & 4) === 4;
            document.getElementById('otherWrite').checked = (otherBits & 2) === 2;
            document.getElementById('otherExec').checked = (otherBits & 1) === 1;
        }

        // Update octal input when checkboxes are changed
        permCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Calculate octal value directly from checkbox states
                let ownerValue = 0;
                if (document.getElementById('ownerRead').checked) ownerValue += 4;
                if (document.getElementById('ownerWrite').checked) ownerValue += 2;
                if (document.getElementById('ownerExec').checked) ownerValue += 1;
                
                let groupValue = 0;
                if (document.getElementById('groupRead').checked) groupValue += 4;
                if (document.getElementById('groupWrite').checked) groupValue += 2;
                if (document.getElementById('groupExec').checked) groupValue += 1;
                
                let otherValue = 0;
                if (document.getElementById('otherRead').checked) otherValue += 4;
                if (document.getElementById('otherWrite').checked) otherValue += 2;
                if (document.getElementById('otherExec').checked) otherValue += 1;
                
                // Combine into standard octal format
                const octalValue = ownerValue * 100 + groupValue * 10 + otherValue;
                permissionsInput.value = octalValue.toString().padStart(3, '0');
            });
        });

        // Update checkboxes when octal input is changed
        permissionsInput.addEventListener('input', function() {
            const octalValue = parseInt(this.value, 8);
            if (!isNaN(octalValue) && octalValue >= 0 && octalValue <= 777) {
                updateCheckboxesFromOctal(octalValue);
            }
        });

        // Apply chmod changes
        chmodApplyBtn.addEventListener('click', function() {
            const permissions = permissionsInput.value;
            const path = chmodPath.value;
            const isRecursive = recursiveCheckbox.checked;
            
            // Validate permissions
            const permRegex = /^[0-7]{3}$/;
            if (!permRegex.test(permissions)) {
                Swal.fire({
                    title: '@lang("translation.Invalid_Permissions")',
                    text: '@lang("translation.Permissions_must_be_octal")',
                    icon: 'error'
                });
                return;
            }
            
            // Send AJAX request
            fetch('{{ route('webftp.chmod', ['username' => $account->username]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    path: path,
                    permissions: parseInt(permissions),
                    recursive: isRecursive
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '@lang("translation.Permissions_Changed")',
                        text: data.message,
                        icon: 'success'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: '@lang("translation.Error")',
                        text: data.message,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: '@lang("translation.Error")',
                    text: '@lang("translation.An_error_changing_permissions")',
                    icon: 'error'
                });
            });
            
            // Close the modal
            const modal = bootstrap.Modal.getInstance(chmodModal);
            modal.hide();
        });
        
        // Clipboard operations
        const clipboardBadge = document.getElementById('clipboard-badge');
        const copySelectedBtn = document.getElementById('copySelectedBtn');
        const cutSelectedBtn = document.getElementById('cutSelectedBtn');
        const pasteBtn = document.getElementById('pasteBtn');
        const clearClipboardBtn = document.getElementById('clearClipboardBtn');
        
        // Individual file/directory copy/cut buttons
        const copyItemButtons = document.querySelectorAll('.copy-item');
        const cutItemButtons = document.querySelectorAll('.cut-item');
        
        // Function to update clipboard buttons state
        function updateClipboardButtons() {
            const anyChecked = Array.from(itemCheckboxes).some(checkbox => checkbox.checked);
            
            copySelectedBtn.classList.toggle('disabled', !anyChecked);
            cutSelectedBtn.classList.toggle('disabled', !anyChecked);
        }
        
        // Function to check clipboard status
        function checkClipboardStatus() {
            fetch('{{ route('webftp.clipboard.status', ['username' => $account->username]) }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.has_items) {
                    clipboardBadge.textContent = data.items_count + ' @lang("translation.item(s)_in_clipboard") (' + data.action + ')';
                    clipboardBadge.classList.remove('d-none');
                    
                    pasteBtn.classList.remove('disabled');
                    clearClipboardBtn.classList.remove('disabled');
                } else {
                    clipboardBadge.classList.add('d-none');
                    pasteBtn.classList.add('disabled');
                    clearClipboardBtn.classList.add('disabled');
                }
            })
            .catch(error => {
                console.error('@lang("translation.Error_checking_clipboard_status"):', error);
            });
        }
        
        // Update initial state
        updateClipboardButtons();
        checkClipboardStatus();
		// Individual item copy
        copyItemButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const path = this.dataset.path;
                const name = this.dataset.name;
                
                fetch('{{ route('webftp.clipboard.copy', ['username' => $account->username]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        items: [path],
                        current_path: '{{ $currentPath }}'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '@lang("translation.Copied")',
                            text: `${name} @lang("translation.copied_to_clipboard")`,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        
                        checkClipboardStatus();
                    } else {
                        Swal.fire({
                            title: '@lang("translation.Error")',
                            text: data.message,
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: '@lang("translation.Error")',
                        text: '@lang("translation.An_error_copying_item")',
                        icon: 'error'
                    });
                });
            });
        });
        
        // Individual item cut
        cutItemButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const path = this.dataset.path;
                const name = this.dataset.name;
                
                fetch('{{ route('webftp.clipboard.cut', ['username' => $account->username]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        items: [path],
                        current_path: '{{ $currentPath }}'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '@lang("translation.Cut")',
                            text: `${name} @lang("translation.cut_to_clipboard")`,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        
                        checkClipboardStatus();
                    } else {
                        Swal.fire({
                            title: '@lang("translation.Error")',
                            text: data.message,
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: '@lang("translation.Error")',
                        text: '@lang("translation.An_error_cutting_item")',
                        icon: 'error'
                    });
                });
            });
        });
        
        // Copy selected items
        copySelectedBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (this.classList.contains('disabled')) return;
            
            const selectedItems = [];
            
            itemCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedItems.push(checkbox.dataset.path);
                }
            });
            
            if (selectedItems.length === 0) {
                Swal.fire({
                    title: '@lang("translation.No_Items_Selected")',
                    text: '@lang("translation.Please_select_item_to_copy")',
                    icon: 'warning'
                });
                return;
            }
            
            fetch('{{ route('webftp.clipboard.copy', ['username' => $account->username]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    items: selectedItems,
                    current_path: '{{ $currentPath }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '@lang("translation.Copied")',
                        text: data.message,
                        icon: 'success'
                    });
                    
                    checkClipboardStatus();
                    
                    // Uncheck all checkboxes
                    selectAllCheckbox.checked = false;
                    itemCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                        updateRowSelection(checkbox);
                    });
                    updateDeleteSelectedButton();
                    updateClipboardButtons();
                } else {
                    Swal.fire({
                        title: '@lang("translation.Error")',
                        text: data.message,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: '@lang("translation.Error")',
                    text: '@lang("translation.An_error_copying_items")',
                    icon: 'error'
                });
            });
        });
        
        // Cut selected items
        cutSelectedBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (this.classList.contains('disabled')) return;
            
            const selectedItems = [];
            
            itemCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedItems.push(checkbox.dataset.path);
                }
            });
            
            if (selectedItems.length === 0) {
                Swal.fire({
                    title: '@lang("translation.No_Items_Selected")',
                    text: '@lang("translation.Please_select_item_to_cut")',
                    icon: 'warning'
                });
                return;
            }
            
            fetch('{{ route('webftp.clipboard.cut', ['username' => $account->username]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    items: selectedItems,
                    current_path: '{{ $currentPath }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '@lang("translation.Cut")',
                        text: data.message,
                        icon: 'success'
                    });
                    
                    checkClipboardStatus();
                    
                    // Uncheck all checkboxes
                    selectAllCheckbox.checked = false;
                    itemCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                        updateRowSelection(checkbox);
                    });
                    updateDeleteSelectedButton();
                    updateClipboardButtons();
                } else {
                    Swal.fire({
                        title: '@lang("translation.Error")',
                        text: data.message,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: '@lang("translation.Error")',
                    text: '@lang("translation.An_error_cutting_items")',
                    icon: 'error'
                });
            });
        });
        
        // Paste items
        pasteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (this.classList.contains('disabled')) return;
            
            Swal.fire({
                title: '@lang("translation.Paste_Items")',
                text: '@lang("translation.Are_you_sure_paste_here")',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '@lang("translation.Yes_paste_here")',
                cancelButtonText: '@lang("translation.Cancel")'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: '@lang("translation.Pasting")',
                        text: '@lang("translation.Please_wait_processing")',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    fetch('{{ route('webftp.clipboard.paste', ['username' => $account->username]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            destination_path: '{{ $currentPath }}'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let successCount = 0;
                            let failCount = 0;
                            let resultMessage = '';
                            
                            data.processed_items.forEach(item => {
                                if (item.success) {
                                    successCount++;
                                    resultMessage += `<p class="text-success"><i class="bx bx-check"></i> ${item.name}: ${item.message}</p>`;
                                } else {
                                    failCount++;
                                    resultMessage += `<p class="text-danger"><i class="bx bx-x"></i> ${item.name}: ${item.message}</p>`;
                                }
                            });
                            
                            Swal.fire({
                                title: '@lang("translation.Paste_Complete")',
                                html: `<p>${data.message}</p><div style="max-height: 200px; overflow-y: auto;">${resultMessage}</div>`,
                                icon: successCount > 0 ? 'success' : 'error'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: '@lang("translation.Error")',
                                text: data.message,
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: '@lang("translation.Error")',
                            text: '@lang("translation.An_error_pasting_items")',
                            icon: 'error'
                        });
                    });
                }
            });
        });
        
        // Clear clipboard
        clearClipboardBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (this.classList.contains('disabled')) return;
            
            fetch('{{ route('webftp.clipboard.clear', ['username' => $account->username]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    clipboardBadge.classList.add('d-none');
                    pasteBtn.classList.add('disabled');
                    clearClipboardBtn.classList.add('disabled');
                    
                    Swal.fire({
                        title: '@lang("translation.Clipboard_Cleared")',
                        text: data.message,
                        icon: 'success'
                    });
                } else {
                    Swal.fire({
                        title: '@lang("translation.Error")',
                        text: data.message,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: '@lang("translation.Error")',
                    text: '@lang("translation.An_error_clearing_clipboard")',
                    icon: 'error'
                });
            });
        });
		// Drag & Drop Upload
        const dropzone = document.getElementById('upload-dropzone');
        const fileInput = document.getElementById('file-input');
        const uploadProgressContainer = document.getElementById('upload-progress-container');
        const uploadProgressList = document.getElementById('upload-progress-list');
        const uploadModal = document.getElementById('uploadModal');
        
        // Handle clicking on dropzone
        dropzone.addEventListener('click', function(e) {
            fileInput.click();
        });
        
        // Handle file selection via input
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
            this.value = ''; // Reset input for reuse
        });
        
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, preventDefaults, false);
        });
        
        // Highlight dropzone when dragging over it
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, unhighlight, false);
        });
        
        // Handle dropped files
        dropzone.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        });
        
        // Also enable drag & drop on entire document
        ['dragenter', 'dragover', 'drop'].forEach(eventName => {
            document.addEventListener(eventName, function(e) {
                if (e.target !== dropzone && !dropzone.contains(e.target)) {
                    preventDefaults(e);
                    
                    if (eventName === 'drop') {
                        // Show modal and process files
                        const modal = new bootstrap.Modal(uploadModal);
                        modal.show();
                        
                        // Process the dropped files after modal is visible
                        setTimeout(() => {
                            handleFiles(e.dataTransfer.files);
                        }, 300);
                    }
                }
            });
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        function highlight() {
            dropzone.classList.add('highlight');
        }
        
        function unhighlight() {
            dropzone.classList.remove('highlight');
        }
        
        // Process files for upload
        function handleFiles(files) {
            if (files.length === 0) return;
            
            // Show progress container
            uploadProgressContainer.style.display = 'block';
            
            // Clear previous uploads
            uploadProgressList.innerHTML = '';
            
            // Process each file
            Array.from(files).forEach(uploadFile);
        }
        
        // Upload a single file
        function uploadFile(file) {
            // Skip folders
            if (!file.type && file.size % 4096 === 0) {
                const errorItem = document.createElement('div');
                errorItem.className = 'alert alert-danger mb-2';
                errorItem.textContent = '@lang("translation.Folders_not_supported")';
                uploadProgressList.appendChild(errorItem);
                return;
            }
            
            // Create progress item
            const progressItem = document.createElement('div');
            progressItem.className = 'mb-3';
            
            const maxSizeMB = {{ isset($settings) ? $settings->max_upload_size : 10 }};
            const maxSizeBytes = maxSizeMB * 1024 * 1024;
            
            // Check file size
            if (file.size > maxSizeBytes) {
                progressItem.innerHTML = `
                    <div class="d-flex justify-content-between mb-1">
                        <span title="${file.name}">${truncateFilename(file.name, 25)}</span>
                        <span class="badge bg-danger">@lang("translation.Too_Large")</span>
                    </div>
                    <div class="small text-danger">@lang("translation.File_exceeds_max_size") (${maxSizeMB}MB)</div>
                `;
                uploadProgressList.appendChild(progressItem);
                return;
            }
            
            progressItem.innerHTML = `
                <div class="d-flex justify-content-between mb-1">
                    <span title="${file.name}">${truncateFilename(file.name, 25)}</span>
                    <span class="upload-percent">0%</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            `;
            
            uploadProgressList.appendChild(progressItem);
            
            // Prepare form data
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');
            
            // Make AJAX request
            const xhr = new XMLHttpRequest();
            
            // Progress tracking
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    progressItem.querySelector('.progress-bar').style.width = percentComplete + '%';
                    progressItem.querySelector('.progress-bar').setAttribute('aria-valuenow', percentComplete);
                    progressItem.querySelector('.upload-percent').textContent = percentComplete + '%';
                }
            });
            
            // Handle errors
            xhr.addEventListener('error', function() {
                progressItem.querySelector('.upload-percent').innerHTML = '<span class="badge bg-danger">@lang("translation.Error")</span>';
            });
            
            // Handle completion
            xhr.addEventListener('load', function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            progressItem.querySelector('.upload-percent').innerHTML = '<span class="badge bg-success">@lang("translation.Complete")</span>';
                        } else {
                            progressItem.querySelector('.upload-percent').innerHTML = '<span class="badge bg-danger">@lang("translation.Error")</span>';
                            progressItem.innerHTML += `<div class="small text-danger">${response.message || '@lang("translation.Error_uploading_file")'}</div>`;
                        }
                    } catch (e) {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            // Success but couldn't parse JSON - might be a redirect
                            progressItem.querySelector('.upload-percent').innerHTML = '<span class="badge bg-success">@lang("translation.Complete")</span>';
                            
                            // Delay reload to allow user to see the completion
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            progressItem.querySelector('.upload-percent').innerHTML = '<span class="badge bg-danger">@lang("translation.Error")</span>';
                            progressItem.innerHTML += `<div class="small text-danger">@lang("translation.Error_processing_response")</div>`;
                        }
                    }
                } else {
                    progressItem.querySelector('.upload-percent').innerHTML = '<span class="badge bg-danger">@lang("translation.Error")</span>';
                    progressItem.innerHTML += `<div class="small text-danger">@lang("translation.HTTP_Error"): ${xhr.status}</div>`;
                }
            });
            
            // Open and send the request
            xhr.open('POST', '{{ route('webftp.upload', ['username' => $account->username, 'path' => $currentPath]) }}', true);
            xhr.send(formData);
        }
        
        // Helper function to truncate filename
        function truncateFilename(filename, maxLength) {
            if (filename.length <= maxLength) {
                return filename;
            }
            
            const extension = filename.includes('.') 
                ? filename.substring(filename.lastIndexOf('.')) 
                : '';
                
            const nameWithoutExt = filename.substring(0, filename.length - extension.length);
            
            const truncatedName = nameWithoutExt.substring(0, maxLength - extension.length - 3) + '...';
            return truncatedName + extension;
        }
    });
</script>
@endsection