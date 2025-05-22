<?php

namespace App\Providers;

use App\Services\MofhService;
use Illuminate\Support\ServiceProvider;

class MofhServiceProvider extends ServiceProvider 
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(MofhService::class, function ($app) {
            return new MofhService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}