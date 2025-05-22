<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Account Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #f0f0f0;
        }
        .header h1 {
            color: #3f51b5;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px 0;
        }
        .credentials {
            background-color: #f1f5f9;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .credentials p {
            margin: 10px 0;
        }
        .credentials strong {
            display: inline-block;
            width: 100px;
        }
        .note {
            background-color: #fffde7;
            border-left: 4px solid #ffc107;
            padding: 10px 15px;
            margin-top: 20px;
        }
        .button {
            display: inline-block;
            background-color: #3f51b5;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 4px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $site_name }}</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $name }},</p>
            
            <p>Your account has been successfully migrated to our new system. As part of this process, your password has been reset for security reasons.</p>
            
            <div class="credentials">
                <p><strong>Username:</strong> {{ $email }}</p>
                <p><strong>Password:</strong> {{ $password }}</p>
                <p><strong>Account Type:</strong> {{ ucfirst($role) }}</p>
            </div>
            
            <p>You can now log in using the new credentials above:</p>
            
            <div style="text-align: center;">
                <a href="{{ $login_url }}" class="button">Login to Your Account</a>
            </div>
            
            <div class="note">
                <p><strong>Important:</strong> For security purposes, we recommend that you change your password immediately after logging in.</p>
            </div>
            
            <p>If you did not expect this email or have any questions, please contact our support team.</p>
            
            <p>Thank you,<br>{{ $site_name }} Team</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message, please do not reply directly to this email.</p>
            <p>&copy; {{ date('Y') }} {{ $site_name }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>