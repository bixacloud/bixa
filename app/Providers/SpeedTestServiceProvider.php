<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ServerSideSpeedTestService;
use App\Services\PingHelperService;
use App\Services\BandwidthTestService;

class SpeedTestServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register PingHelperService
        $this->app->singleton(PingHelperService::class, function ($app) {
            return new PingHelperService();
        });
        
        // Register BandwidthTestService
        $this->app->singleton(BandwidthTestService::class, function ($app) {
            return new BandwidthTestService();
        });
        
        // Register ServerSideSpeedTestService with dependencies
        $this->app->singleton(ServerSideSpeedTestService::class, function ($app) {
            return new ServerSideSpeedTestService(
                $app->make(BandwidthTestService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}