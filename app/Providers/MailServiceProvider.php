<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;  
use Illuminate\Auth\Notifications\ResetPassword;
use App\Mail\Auth\VerifyEmailMail;
use App\Mail\Auth\ResetPasswordMail;

class MailServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Override verify email
        VerifyEmail::toMailUsing(function($notifiable, $url) {
            return (new VerifyEmailMail($notifiable, $url))
                ->to($notifiable->email);
        });

        // Override reset password
        ResetPassword::toMailUsing(function($notifiable, $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
            
            return (new ResetPasswordMail($notifiable, $url))
                ->to($notifiable->email);
        });
    }
}