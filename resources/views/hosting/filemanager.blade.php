@extends('layouts.master')

@section('title') 
    @lang('translation.File_Manager') @lang('translation.Login') ({{ $username }})
@endsection

@section('content')
    <div class="auth-page">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-4">
                    <div class="text-center">
                        <div class="card">
                            <div class="card-body p-4">
                                {{-- Icon --}}
                                <div class="avatar-lg mx-auto">
                                    <div class="avatar-title rounded-circle bg-light">
                                        <i data-feather="folder" class="icon-dual-primary icon-lg"></i>
                                    </div>
                                </div>
                                
                                {{-- Content --}}
                                <div class="mt-4 pt-2">
                                    <h4>@lang('translation.File_Manager_Access')</h4>
                                    <p class="text-muted font-size-15 mb-4">
                                        @lang('translation.Please_wait_redirect_filemanager')
                                    </p>

                                    {{-- Redirect Button --}}
                                    <div class="d-grid">
                                        <button type="button" id="redirect-btn" 
                                                class="btn btn-primary waves-effect waves-light">
                                            <i class="bx bx-right-arrow-alt font-size-16 align-middle me-2"></i>
                                            @lang('translation.Access_File_Manager')
                                        </button>
                                    </div>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configure FileManager access
    const config = {
        't': 'ftp',
        'c': {
            'v': 1,
            'p': '{{ $password }}',
            'i': '{{ $dir }}'
        }
    };
    
    const encodedConfig = btoa(JSON.stringify(config));
    const fileManagerUrl = 'https://filemanager.ai/new/#/c/ftpupload.net/{{ $username }}/' + encodedConfig;

    // Handle redirect button click
    const redirectBtn = document.getElementById('redirect-btn');
    if (redirectBtn) {
        redirectBtn.addEventListener('click', function() {
            window.location.href = fileManagerUrl;
        });
    }

    // Auto redirect after delay
    setTimeout(() => {
        window.location.href = fileManagerUrl;
    }, 1500);
});
</script>
@endsection