@extends('layouts.master-without-nav')

@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.Recover_Password')
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
        
        .reset-container {
            background: white;
            border-radius: 8px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .reset-title {
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
            padding: 12px 15px;
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
        
        .btn-reset {
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, #4cc1ef 0%, #a74cf2 100%);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin: 10px 0 20px;
            transition: opacity 0.3s;
            text-transform: uppercase;
        }
        
        .btn-reset:hover {
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
        
        .info-box {
            background-color: #d6ebf9;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 25px;
            color: #0c5460;
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
    <div class="reset-container">
        <div class="text-center mb-4">
            <h1 class="reset-title d-inline-flex align-items-center">
                <img src="{{ URL::asset('build/images/logo-sm.svg') }}" alt="" height="30" class="me-2">
                @lang('translation.Reset_Password')
            </h1>
        </div>
        
        <div class="info-box">
            @lang('translation.Enter_email_for_instructions')
        </div>

        @if (session('status'))
            <div class="alert alert-success text-center" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="email" class="form-label">@lang('translation.Email')</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                    id="email" name="email" value="{{ old('email') }}"
                    placeholder="@lang('translation.Enter_your_email_address')" autofocus>
                @error('email')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <button type="submit" class="btn-reset">@lang('translation.Send_Reset_Link')</button>
        </form>

        <div class="text-center">
            <p>@lang('translation.Remembered_password') <a href="{{ route('login') }}" class="login-link">@lang('translation.Back_to_login')</a></p>
        </div>
        
        <div class="text-center mt-5 text-muted">
            <p class="mb-0">© <script>document.write(new Date().getFullYear())</script> BIXA. @lang('translation.Crafted_with') <i class="bx bx-heart text-danger"></i></p>
        </div>
    </div>
</div>
@endsection