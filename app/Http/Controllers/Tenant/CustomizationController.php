<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TenantHeroSection;
use App\Models\TenantSetting;

use App\Traits\NextcloudStorage;

class CustomizationController extends Controller
{
    use NextcloudStorage;

    public function editHero()
    {
        $hero = TenantHeroSection::firstOrCreate([]);
        return view('tenant.dashboard.hero-edit', compact('hero'));
    }

    public function updateHero(Request $request)
    {
        $request->validate([
            'headline' => 'nullable|string|max:255',
            'subheadline' => 'nullable|string',
            'cta_text' => 'nullable|string|max:50',
            'cta_link' => 'nullable|string|max:255',
            'background_image' => 'nullable|image|max:5120', // 5MB max
        ]);

        $hero = TenantHeroSection::firstOrCreate([]);

        $data = $request->only(['headline', 'subheadline', 'cta_text', 'cta_link']);

        if ($request->hasFile('background_image')) {
            if ($hero->background_image) {
                $this->deleteFromNextcloud($hero->background_image);
            }
            $tenantId = tenant('id');
            $data['background_image'] = $this->uploadToNextcloud($request->file('background_image'), "tenants/{$tenantId}/hero");
        }

        $hero->update($data);

        return redirect()->route('tenant.customize.hero')->with('success', 'Hero section updated successfully!');
    }
}
