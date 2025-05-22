@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.Login')
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
        
        .forgot-link {
            text-align: right;
            display: block;
            font-size: 14px;
            color: #777;
            text-decoration: none;
            margin-top: 10px;
        }
        
        .forgot-link:hover {
            color: #a74cf2;
        }
        
        .btn-login {
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
        
        .btn-login:hover {
            opacity: 0.9;
        }
        
        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }
        
        .divider:before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #eee;
        }
        
        .divider-text {
            display: inline-block;
            padding: 0 15px;
            background: white;
            position: relative;
            color: #999;
            font-size: 14px;
        }
        
        .social-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .social-button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s;
            color: white;
            text-decoration: none;
        }
        
        .social-button:hover {
            transform: scale(1.1);
        }
        
        .facebook {
            background: #3b5998;
        }
        
        .twitter {
            background: #1da1f2;
        }
        
        .google {
            background: #dd4b39;
        }
        
        .signup-text {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-top: 15px;
        }
        
        .signup-link {
            color: #a74cf2;
            font-weight: 500;
            text-decoration: none;
        }
        
        .signup-link:hover {
            text-decoration: underline;
        }
        
        .btn-signup {
            background: none;
            border: 1px solid #ddd;
            color: #555;
            padding: 10px;
            width: 100%;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 500;
            margin-top: 10px;
            transition: all 0.3s;
        }
        
        .btn-signup:hover {
            border-color: #a74cf2;
            color: #a74cf2;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .remember-me input {
            margin-right: 8px;
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
                @lang('translation.Login')
            </h1>
        </div>
        
        @if(session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form id="formAuthentication" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email" class="form-label">@lang('translation.Username')</label>
                <div class="input-group">
                    <i class="input-icon bx bx-user"></i>
                    <input type="text" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}"
                           placeholder="@lang('translation.Type_your_username')" autofocus>
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
                           placeholder="@lang('translation.Type_your_password')">
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="@lang('translation.Toggle_password_visibility')">
                        <i class="bx bx-hide"></i>
                    </button>
                </div>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                <a href="{{ route('password.request') }}" class="forgot-link">@lang('translation.Forgot_password')</a>
            </div>

            <div class="remember-me">
                <input class="form-check-input" type="checkbox" name="remember" 
                       id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    @lang('translation.Remember_Me')
                </label>
            </div>

            <button type="submit" class="btn-login">@lang('translation.LOGIN')</button>
        </form>

        <div class="divider">
            <span class="divider-text">@lang('translation.Sign_in_with')</span>
        </div>

        <div class="social-bullets">
            <ul class="list-unstyled d-flex justify-content-center">
                <li class="mx-2">
                    <a href="{{ route('social.login', 'facebook') }}" class="social-button facebook">
                        <i class="bx bxl-facebook"></i>
                    </a>
                </li>
                <li class="mx-2">
                    <a href="{{ route('social.login', 'twitter') }}" class="social-button twitter">
                        <i class="bx bxl-twitter"></i>
                    </a>
                </li>
                <li class="mx-2">
                    <a href="{{ route('social.login', 'google') }}" class="social-button google">
                        <i class="bx bxl-google"></i>
                    </a>
                </li>
            </ul>
        </div>

        <div class="text-center mt-4">
            <p class="mb-0">@lang('translation.Dont_have_account') <a href="{{ route('register') }}" class="signup-link fw-medium">@lang('translation.Signup_now')</a></p>
        </div>
        
        <div class="text-center mt-5 text-muted">
            <p class="mb-0">© <script>document.write(new Date().getFullYear())</script> BIXA. @lang('translation.Crafted_with') <i class="bx bx-heart text-danger"></i></p>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/js/auth.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>
@endsection