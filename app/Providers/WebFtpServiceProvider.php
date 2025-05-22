<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\WebFtpSetting;
use View;

class WebFtpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Share WebFTP settings with all views
        View::composer('*', function ($view) {
            try {
                $webFtpSettings = WebFtpSetting::getSettings();
                $view->with('webFtpSettings', $webFtpSettings);
            } catch (\Exception $e) {
                $view->with('webFtpSettings', null);
            }
        });
    }
}