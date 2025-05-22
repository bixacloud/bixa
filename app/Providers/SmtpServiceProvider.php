<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\SmtpSetting;

class SmtpServiceProvider extends ServiceProvider
{
    public function boot()
    {
        try {
            $smtp = SmtpSetting::where('status', true)->first();
            
            if ($smtp) {
                config([
                    'mail.mailers.smtp.host' => $smtp->hostname,
                    'mail.mailers.smtp.port' => $smtp->port,
                    'mail.mailers.smtp.username' => $smtp->username,
                    'mail.mailers.smtp.password' => $smtp->password,
                    'mail.mailers.smtp.encryption' => $smtp->encryption === 'none' ? null : $smtp->encryption,
                    'mail.from.address' => $smtp->from_email,
                    'mail.from.name' => $smtp->from_name
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error loading SMTP settings: ' . $e->getMessage());
        }
    }
}