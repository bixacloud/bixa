@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.Register')
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
        
        .register-container {
            background: white;
            border-radius: 8px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .register-title {
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
        
        .btn-register {
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
        
        .btn-register:hover {
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
        
        .login-text {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-top: 15px;
        }
        
        .login-link {
            color: #a74cf2;
            font-weight: 500;
            text-decoration: none;
        }
        
        .login-link:hover {
            text-decoration: underline;
        }
        
        .terms-text {
            font-size: 13px;
            color: #777;
            margin-bottom: 20px;
        }
        
        .terms-link {
            color: #a74cf2;
            text-decoration: none;
        }
        
        .terms-link:hover {
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
    </style>
@endsection

@section('body')
<body>
@endsection

@section('content')
<div class="gradient-background">
    <div class="register-container">
        <div class="text-center mb-4">
            <h1 class="register-title d-inline-flex align-items-center">
                <img src="{{ URL::asset('build/images/logo-sm.svg') }}" alt="" height="30" class="me-2">
                @lang('translation.Sign_Up')
            </h1>
        </div>

        <form method="POST" action="{{ route('register') }}" class="needs-validation">
            @csrf
            <div class="form-group">
                <label for="email" class="form-label">@lang('translation.Email')</label>
                <div class="input-group">
                    <i class="input-icon bx bx-envelope"></i>
                    <input id="email" type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           name="email" value="{{ old('email') }}" 
                           placeholder="@lang('translation.Enter_your_email')" required autocomplete="email">
                </div>
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="name" class="form-label">@lang('translation.Username')</label>
                <div class="input-group">
                    <i class="input-icon bx bx-user"></i>
                    <input id="name" type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           name="name" value="{{ old('name') }}" 
                           placeholder="@lang('translation.Choose_a_username')" required autocomplete="name">
                </div>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">@lang('translation.Password')</label>
                <div class="input-group">
                    <i class="input-icon bx bx-lock-alt"></i>
                    <input id="password" type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           name="password" placeholder="@lang('translation.Create_a_password')" 
                           required autocomplete="new-password">
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="@lang('translation.Toggle_password_visibility')">
                        <i class="bx bx-hide"></i>
                    </button>
                </div>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">@lang('translation.Confirm_Password')</label>
                <div class="input-group">
                    <i class="input-icon bx bx-lock-alt"></i>
                    <input id="password_confirmation" type="password" 
                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                           name="password_confirmation" placeholder="@lang('translation.Confirm_your_password')" 
                           required autocomplete="new-password">
                    <button type="button" class="password-toggle" id="toggleConfirmPassword" aria-label="@lang('translation.Toggle_password_visibility')">
                        <i class="bx bx-hide"></i>
                    </button>
                </div>
                @error('password_confirmation')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <p class="terms-text">
                @lang('translation.By_registering_you_agree') <a href="#" class="terms-link">@lang('translation.Terms_of_Use')</a>
            </p>

            <button type="submit" class="btn-register">@lang('translation.SIGN_UP')</button>
        </form>

        <div class="divider">
            <span class="divider-text">@lang('translation.Or_Sign_Up_Using')</span>
        </div>

        <div class="social-buttons">
            <a href="{{ route('social.login', 'facebook') }}" class="social-button facebook">
                <i class="bx bxl-facebook"></i>
            </a>
            <a href="{{ route('social.login', 'twitter') }}" class="social-button twitter">
                <i class="bx bxl-twitter"></i>
            </a>
            <a href="{{ route('social.login', 'google') }}" class="social-button google">
                <i class="bx bxl-google"></i>
            </a>
        </div>

        <p class="login-text">
            @lang('translation.Already_have_an_account') <a href="{{ route('login') }}" class="login-link">@lang('translation.Login')</a>
        </p>
        
        <div class="text-center mt-5 text-muted">
            <p class="mb-0">© <script>document.write(new Date().getFullYear())</script> BIXA. @lang('translation.Crafted_with') <i class="bx bx-heart text-danger"></i></p>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/js/pages/validation.init.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility functions
        function setupPasswordToggle(toggleId, passwordId) {
            const toggle = document.getElementById(toggleId);
            const password = document.getElementById(passwordId);
            
            if (toggle && password) {
                toggle.addEventListener('click', function() {
                    // Toggle the type attribute
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    
                    // Toggle the icon
                    toggle.querySelector('i').classList.toggle('bx-hide');
                    toggle.querySelector('i').classList.toggle('bx-show');
                });
            }
        }
        
        // Setup toggles for both password fields
        setupPasswordToggle('togglePassword', 'password');
        setupPasswordToggle('toggleConfirmPassword', 'password_confirmation');
    });
</script>
@endsection