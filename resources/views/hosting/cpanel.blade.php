@extends('layouts.master')

@section('title') 
    @lang('translation.Control_Panel') ({{ $username }})
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
                                        <i data-feather="settings" class="icon-dual-primary icon-lg"></i>
                                    </div>
                                </div>

                                {{-- Content --}}
                                <div class="mt-4 pt-2">
                                    <h4>@lang('translation.Login_to_cPanel')</h4>
                                    <p class="text-muted font-size-15 mb-4">
                                        @lang('translation.Please_wait_redirect_cPanel')
                                    </p>

                                    {{-- Auto Login Form --}}
                                    <form action="https://{{ $cpanel_url }}/login.php" 
                                          method="post" id="cpanel-login">
                                        <input type="hidden" name="uname" value="{{ $username }}">
                                        <input type="hidden" name="passwd" value="{{ $password }}">
                                        
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                                <i class="bx bx-right-arrow-alt font-size-16 align-middle me-2"></i>
                                                @lang('translation.Redirect_to_cPanel')
                                            </button>
                                        </div>
                                    </form>
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
    // Auto submit form
    const form = document.getElementById('cpanel-login');
    if (form) {
        setTimeout(() => {
            form.submit();
        }, 1000);
    }
});
</script>
@endsection