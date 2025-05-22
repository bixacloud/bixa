<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FroalaLicenseService;

class FroalaLicenseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FroalaLicenseService::class, function ($app) {
            return new FroalaLicenseService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}