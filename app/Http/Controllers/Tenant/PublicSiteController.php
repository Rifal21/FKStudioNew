<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TenantSetting;
use App\Models\TenantHeroSection;

class PublicSiteController extends Controller
{
    public function index()
    {
        $setting = TenantSetting::first();
        $brandingName = tenant('branding_name') ?? 'FKStudio';

        // If not onboarded or no site type selected, show the default "Coming Soon" page
        if (!$setting || !$setting->is_onboarded || !$setting->site_type) {
            return view('tenant.landing', compact('brandingName'));
        }

        $hero = TenantHeroSection::firstOrCreate([]);

        if ($setting->site_type === 'branding') {
            return view('tenant.templates.branding.index', compact('brandingName', 'setting', 'hero'));
        }

        if ($setting->site_type === 'sales') {
            $products = \App\Models\Product::where('is_active', true)->orderBy('created_at', 'desc')->get();
            return view('tenant.templates.sales.index', compact('brandingName', 'setting', 'hero', 'products'));
        }

        return view('tenant.landing', compact('brandingName'));
    }
}
