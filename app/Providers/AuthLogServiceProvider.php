<?php
namespace App\Providers;

use App\Models\AuthLogSettings;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema; 

class AuthLogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            // Set the custom notification templates
            Config::set('authentication-log.notifications.new-device.template', \App\Notifications\CustomNewDeviceNotification::class);
            Config::set('authentication-log.notifications.failed-login.template', \App\Notifications\CustomFailedLoginNotification::class);
            
            // Apply database settings if table exists
            if (Schema::hasTable('auth_log_settings')) {
                $settings = AuthLogSettings::getSettings();
                
                // Override default config with database settings
                Config::set('authentication-log.notifications.new-device.enabled', $settings->new_device_notification);
                Config::set('authentication-log.notifications.failed-login.enabled', $settings->failed_login_notification);
                Config::set('authentication-log.notifications.new-device.location', $settings->location_tracking);
                Config::set('authentication-log.notifications.failed-login.location', $settings->location_tracking);
            }
        } catch (\Exception $e) {
            report($e);
        }
    }
}