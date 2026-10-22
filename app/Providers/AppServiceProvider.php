<?php

namespace App\Providers;

use App\Models\PackageOrder;
use App\Models\SiteSetting;
use App\Models\Ticket;
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
        View::composer(['components.layouts.*', 'components.site.*'], function ($view) {
            static $site = null;

            $site ??= SiteSetting::first() ?: new SiteSetting();

            $view->with('site', $site);
        });

        // Admin shell: counters shown as badges in the sidebar / bottom nav.
        View::composer(['layouts.navigation', 'layouts.app'], function ($view) {
            $view->with('navCounts', [
                'orders' => PackageOrder::where('status', 'pending')->count(),
                'tickets' => Ticket::whereIn('status', ['open', 'in_progress'])->count(),
            ]);
        });

        // Navbar: show "Website Saya" only for customers with a finished website.
        View::composer('components.site.navbar', function ($view) {
            $hasWebsite = auth()->check() && PackageOrder::where('user_id', auth()->id())
                ->where('work_status', 'completed')
                ->whereNotNull('website_url')
                ->exists();

            $view->with('hasWebsite', $hasWebsite);
        });
    }
}
