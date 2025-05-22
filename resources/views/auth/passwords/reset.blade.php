@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.Reset_Password')
@endsection

@section('css')
    <!-- Boxicons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .gradient-background {
            background: linear-gradient(135deg, #4cc1ef 0%, #a74cf2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 8px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .login-title {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            border-color: #a74cf2;
            outline: none;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        /* Password Toggle Button */
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        
        .password-toggle:hover {
            color: #a74cf2;
        }
        
        .btn-reset {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4cc1ef 0%, #a74cf2 100%);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            margin: 10px 0 20px;
            transition: opacity 0.3s;
        }
        
        .btn-reset:hover {
            opacity: 0.9;
        }
        
        .signup-text {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-top: 15px;
        }
        
        .signin-link {
            color: #a74cf2;
            font-weight: 500;
            text-decoration: none;
        }
        
        .signin-link:hover {
            text-decoration: underline;
        }
        
        /* Error message styling */
        .alert-danger {
            background-color: #ffe6e6;
            color: #d85252;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
        
        .invalid-feedback {
            color: #d85252;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }
        
        .reset-text {
            color: #666;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
@endsection

@section('body')
<body>
@endsection

@section('content')
<div class="gradient-background">
    <div class="login-container">
        <div class="text-center mb-4">
            <h1 class="login-title d-inline-flex align-items-center">
                <img src="{{ URL::asset('build/images/logo-sm.svg') }}" alt="" height="30" class="me-2">
                @lang('translation.Reset_Password')
            </h1>
        </div>
        
        <p class="reset-text">@lang('translation.Reset_Password_Instructions')</p>
        
        @if(session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form class="custom-form" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="form-group">
                <label for="email" class="form-label">@lang('translation.Email')</label>
                <div class="input-group">
                    <i class="input-icon bx bx-envelope"></i>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ $email ?? old('email') }}" 
                           placeholder="@lang('translation.Enter_your_email')" readonly>
                </div>
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">@lang('translation.Password')</label>
                <div class="input-group">
                    <i class="input-icon bx bx-lock-alt"></i>
                    <input type="password" id="password" 
                           class="form-control @error('password') is-invalid @enderror"
                           name="password" 
                           placeholder="@lang('translation.Create_a_password')">
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="@lang('translation.Toggle_password_visibility')">
                        <i class="bx bx-hide"></i>
                    </button>
                </div>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password-confirm" class="form-label">@lang('translation.Confirm_Password')</label>
                <div class="input-group">
                    <i class="input-icon bx bx-lock-alt"></i>
                    <input type="password" id="password-confirm" 
                           class="form-control"
                           name="password_confirmation" 
                           placeholder="@lang('translation.Confirm_your_password')">
                    <button type="button" class="password-toggle" id="toggleConfirmPassword" aria-label="@lang('translation.Toggle_password_visibility')">
                        <i class="bx bx-hide"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-reset">@lang('translation.CONFIRM_PASSWORD')</button>
        </form>

        <div class="text-center mt-4">
            <p class="mb-0">@lang('translation.Remembered_password') <a href="{{ url('login') }}" class="signin-link fw-medium">@lang('translation.Back_to_login')</a></p>
        </div>
        
        <div class="text-center mt-5 text-muted">
            <p class="mb-0">© <script>document.write(new Date().getFullYear())</script> BIXA. @lang('translation.Crafted_with') <i class="bx bx-heart text-danger"></i></p>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password toggle
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                // Toggle the icon
                togglePassword.querySelector('i').classList.toggle('bx-hide');
                togglePassword.querySelector('i').classList.toggle('bx-show');
            });
        }
        
        // Confirm password toggle
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const passwordConfirm = document.getElementById('password-confirm');
        
        if (toggleConfirmPassword && passwordConfirm) {
            toggleConfirmPassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirm.setAttribute('type', type);
                
                // Toggle the icon
                toggleConfirmPassword.querySelector('i').classList.toggle('bx-hide');
                toggleConfirmPassword.querySelector('i').classList.toggle('bx-show');
            });
        }
    });
</script>
@endsection