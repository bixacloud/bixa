@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.Confirm_Password')
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
        
        .confirm-container {
            background: white;
            border-radius: 8px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .confirm-title {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
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
            display: block;
            text-align: right;
            font-size: 14px;
            color: #777;
            text-decoration: none;
            margin-top: 5px;
        }
        
        .forgot-link:hover {
            color: #a74cf2;
        }
        
        .btn-confirm {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4cc1ef 0%, #a74cf2 100%);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin: 10px 0 20px;
            transition: opacity 0.3s;
        }
        
        .btn-confirm:hover {
            opacity: 0.9;
        }
        
        .login-link {
            color: #a74cf2;
            font-weight: 500;
            text-decoration: none;
        }
        
        .login-link:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
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
    <div class="confirm-container">
        <div class="text-center mb-4">
            <h1 class="confirm-title d-inline-flex align-items-center">
                <img src="{{ URL::asset('build/images/logo-sm.svg') }}" alt="" height="30" class="me-2">
                @lang('translation.Confirm_Password')
            </h1>
        </div>
        
        <div class="alert alert-info text-center">
            @lang('translation.Please_confirm_password_before_continuing')
        </div>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf
            <div class="form-group">
                <label for="password" class="form-label">@lang('translation.Password')</label>
                <div class="input-group">
                    <i class="input-icon bx bx-lock-alt"></i>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                        id="password" name="password" placeholder="@lang('translation.Enter_your_password')">
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="@lang('translation.Toggle_password_visibility')">
                        <i class="bx bx-hide"></i>
                    </button>
                </div>
                @error('password')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">
                        @lang('translation.Forgot_password')
                    </a>
                @endif
            </div>

            <button type="submit" class="btn-confirm">@lang('translation.CONFIRM_PASSWORD')</button>
        </form>

        <div class="text-center">
            <p>@lang('translation.Remember_password') <a href="{{ route('login') }}" class="login-link">@lang('translation.Back_to_login')</a></p>
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