@extends('layouts.master')

@section('title') @lang('translation.Two_Factor_Authentication') @endsection

@section('content')
    <div class="auth-bg-basic d-flex align-items-center min-vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card mt-4">
                        <div class="card-body p-4">
                            <div class="text-center mt-2">
                                <h5 class="text-primary">@lang('translation.Two_Factor_Authentication')</h5>
                                <p class="text-muted">@lang('translation.Enter_code_from_app')</p>
                            </div>
                            
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            <div class="p-2 mt-4">
                                <form action="{{ route('2fa.validate.post') }}" method="POST">
                                    @csrf
                                    
                                    <div class="mb-3">
                                        <label class="form-label" for="one_time_password">@lang('translation.Verification_Code')</label>
                                        <input type="text" class="form-control" id="one_time_password" name="one_time_password" 
                                            placeholder="@lang('translation.Enter_6_digit_code')" required>
                                    </div>
                                    
                                    <div class="mt-3 text-center">
                                        <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">
                                            @lang('translation.Verify')
                                        </button>
                                    </div>
                                    
                                    <div class="mt-4 text-center">
                                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-link">
                                                @lang('translation.Logout_and_login_again')
                                            </button>
                                        </form>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection