<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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

        // Fix Vite assets for multi-tenancy
        $centralDomains = config('tenancy.central_domains', ['localhost', 'fkstudio.id']);
        $currentHost = request()->getHost();
        
        if (!in_array($currentHost, $centralDomains)) {
            // Use the first central domain for assets
            $assetDomain = $centralDomains[0];
            $scheme = config('app.env') === 'local' ? 'http' : 'https';
            $port = request()->getPort() == 8000 ? ':8000' : '';
            
            \Illuminate\Support\Facades\Vite::useAssetUrl($scheme . '://' . $assetDomain . $port);
        }
    }
}
