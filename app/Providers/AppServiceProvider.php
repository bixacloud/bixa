<?php
namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\View\Components\AdSlot; 
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AcmeDns::class, function ($app) {
            return new AcmeDns();
        });
		
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $systemInfo = [
            'version' => config('app.version', '1.0.0'),
            'laravel_version' => app()->version(),
        ];

        View::share('systemInfo', $systemInfo);
        Blade::component('ad-slot', AdSlot::class); 
         // Share available languages with all views
        View::composer('*', function ($view) {
            $view->with('availableLanguages', \App\Helpers\LanguageHelper::getAvailableLanguages());
        });
    }
    

    }
    