<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\OAuthSetting;

class OAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Load OAuth settings from database
        if (!\App::runningInConsole() && \Schema::hasTable('oauth_settings')) {
            $providers = ['google', 'facebook'];
            
            foreach ($providers as $provider) {
                $setting = OAuthSetting::where('provider', $provider)
                                     ->where('is_enabled', true)
                                     ->first();
                                     
                if ($setting) {
                    Config::set("services.{$provider}.client_id", $setting->client_id);
                    Config::set("services.{$provider}.client_secret", $setting->client_secret);
                    Config::set("services.{$provider}.redirect", url("/auth/{$provider}/callback"));
                }
            }
        }
    }
}