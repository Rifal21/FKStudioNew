<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TenantSetting;

class OnboardingController extends Controller
{
    public function index()
    {
        return view('tenant.onboarding.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'site_type' => 'required|in:branding,sales',
        ]);

        $setting = TenantSetting::firstOrCreate([]);
        $setting->update([
            'site_type' => $request->site_type,
            'is_onboarded' => true,
        ]);

        return redirect()->route('tenant.dashboard');
    }
}
