<?php

namespace App\Mail\Auth;

use Spatie\MailTemplates\TemplateMailable;

class NewDeviceLoginMail extends TemplateMailable
{
    public $name;
    public $time;
    public $ip_address;
    public $browser;
    public $device;
    public $location;

    /**
     * Create a new message instance.
     *
     * @param mixed $user
     * @param array $data
     */
    public function __construct($user = null, $data = [])
    {
        $this->name = $user ? $user->name : 'User';
        $this->time = $data['time'] ?? now()->format('Y-m-d H:i:s');
        $this->ip_address = $data['ip_address'] ?? 'Unknown';
        $this->browser = $data['browser'] ?? 'Unknown Browser';
        $this->device = $data['device'] ?? 'Unknown Device';
        $this->location = $data['location'] ?? 'Unknown Location';
    }

    /**
     * Get the default subject for the message.
     *
     * @return string
     */
    public function getDefaultSubject(): string
    {
        return 'New Login Detected on Your Account';
    }

    /**
     * Get the default content for the message.
     *
     * @return string
     */
    public function getDefaultContent(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4F46E5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f8f9fa; border: 1px solid #e9ecef; }
        .footer { text-align: center; padding: 10px; font-size: 12px; color: #6c757d; }
        .alert-box { background-color: #fff3cd; border: 1px solid #ffeeba; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #dee2e6; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #e9ecef; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Security Alert</h1>
        </div>
        <div class="content">
            <p>Hello {{ $name }},</p>
            
            <div class="alert-box">
                <strong>We detected a new login to your account from a device that you haven't used before.</strong>
            </div>
            
            <p>Here are the details of the login:</p>
            
            <table>
                <tr>
                    <th>Time</th>
                    <td>{{ $time }}</td>
                </tr>
                <tr>
                    <th>IP Address</th>
                    <td>{{ $ip_address }}</td>
                </tr>
                <tr>
                    <th>Device</th>
                    <td>{{ $device }}</td>
                </tr>
                <tr>
                    <th>Browser</th>
                    <td>{{ $browser }}</td>
                </tr>
                <tr>
                    <th>Location</th>
                    <td>{{ $location }}</td>
                </tr>
            </table>
            
            <p>If this was you, you can ignore this email. If you don't recognize this login, please change your password immediately and contact support.</p>
            
            <p>Best regards,<br>Support Team</p>
        </div>
        <div class="footer">
            <p>This is an automated message, please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
    }
