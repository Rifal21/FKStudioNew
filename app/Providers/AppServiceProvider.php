<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        // Site-wide settings (logo, contact, social, SEO) for the shared layouts.
        View::composer('components.layouts.*', function ($view) {
            static $site = null;

            $site ??= SiteSetting::first() ?: new SiteSetting();

            $view->with('site', $site);
        });
    }
}
