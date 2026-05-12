<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SeoController extends Controller
{
    public function sitemap()
    {
        $urls = [
            [
                'loc' => route('home'),
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'loc' => route('package.index'),
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ],
        ];

        // Add individual package pages
        $packages = \App\Models\Package::where('is_active', true)->get();
        foreach ($packages as $package) {
            if ($package->slug) {
                $urls[] = [
                    'loc' => route('package.show', $package->slug),
                    'lastmod' => $package->updated_at->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.8',
                ];
            }
        }

        $xml = view('seo.sitemap', compact('urls'))->render();

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots()
    {
        $settings = SiteSetting::first();
        $sitemapUrl = route('sitemap');

        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /dashboard/\n";
        $content .= "Disallow: /login\n";
        $content .= "Disallow: /register\n";
        $content .= "\nSitemap: {$sitemapUrl}";

        return Response::make($content, 200, ['Content-Type' => 'text/plain']);
    }
}
