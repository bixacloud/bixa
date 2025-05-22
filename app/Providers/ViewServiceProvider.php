<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
			 $credit = '<div class="text-sm-end d-none d-sm-block">Design & Develop by <a href="https://bixa.app" class="text-decoration-underline">Bixa Cloud</a></div>';
            $view->with('encodedCredit', base64_encode($credit));
            $siteSettings = Cache::remember('site_settings', 60*24, function () {
                return [
                    'site_title' => Setting::get('site_title', config('app.name')),
                    'footer_text' => Setting::get('footer_text', '© 2025 All rights reserved.'),
                ];
            });
            
            $view->with('siteSettings', $siteSettings);
        });
    }
}