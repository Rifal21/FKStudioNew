<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TenantHeroSection;
use App\Models\TenantSetting;
use Illuminate\Support\Facades\Storage;

use App\Traits\NextcloudStorage;

class BuilderController extends Controller
{
    use NextcloudStorage;

    public function index()
    {
        $setting = TenantSetting::firstOrCreate([]);
        $hero = TenantHeroSection::firstOrCreate([]);
        
        // Default sections structure
        $defaults = [
            'design' => [
                'theme_color' => '#3b82f6',
                'button_style' => 'rounded-full',
                'product_cta_text' => 'Beli Sekarang',
                'card_style' => 'grid' // grid, list
            ],
            'about' => [
                'title' => '',
                'description' => '',
                'image' => ''
            ],
            'features' => [],
            'products' => [],
            'testimonials' => [],
            'footer' => [
                'address' => '',
                'email' => '',
                'phone' => '',
                'whatsapp' => '',
                'instagram' => ''
            ]
        ];

        // Deep merge defaults with existing data
        $sectionsData = array_replace_recursive($defaults, $setting->sections_data ?? []);
        $siteType = $setting->site_type;

        return view('tenant.dashboard.builder', compact('setting', 'sectionsData', 'hero', 'siteType'));
    }

    public function save(Request $request)
    {
        $setting = TenantSetting::firstOrCreate([]);
        $hero = TenantHeroSection::firstOrCreate([]);

        // Save Hero
        if ($request->has('hero')) {
            $heroData = $request->input('hero');
            
            // Handle Hero Image Base64 Upload
            if (isset($heroData['background_image_base64']) && str_starts_with($heroData['background_image_base64'], 'data:image')) {
                $heroData['background_image'] = $this->uploadBase64Image($heroData['background_image_base64'], 'hero');
                unset($heroData['background_image_base64']);
            }
            
            $hero->update($heroData);
        }

        // Save Sections
        if ($request->has('sections')) {
            $sectionsData = $request->input('sections');
            
            // Handle Base64 image uploads in sections
            if (isset($sectionsData['about']['image_base64']) && str_starts_with($sectionsData['about']['image_base64'], 'data:image')) {
                $sectionsData['about']['image'] = $this->uploadBase64Image($sectionsData['about']['image_base64'], 'about');
                unset($sectionsData['about']['image_base64']);
            }

            if (isset($sectionsData['features']) && is_array($sectionsData['features'])) {
                foreach ($sectionsData['features'] as $key => $feature) {
                    if (isset($feature['icon_base64']) && str_starts_with($feature['icon_base64'], 'data:image')) {
                        $sectionsData['features'][$key]['icon'] = $this->uploadBase64Image($feature['icon_base64'], 'features');
                        unset($sectionsData['features'][$key]['icon_base64']);
                    }
                }
            }

            if (isset($sectionsData['products']) && is_array($sectionsData['products'])) {
                foreach ($sectionsData['products'] as $key => $product) {
                    if (isset($product['image_base64']) && str_starts_with($product['image_base64'], 'data:image')) {
                        $sectionsData['products'][$key]['image'] = $this->uploadBase64Image($product['image_base64'], 'products');
                        unset($sectionsData['products'][$key]['image_base64']);
                    }
                }
            }

            $setting->update(['sections_data' => $sectionsData]);
        }

        return response()->json(['success' => true]);
    }

    private function uploadBase64Image($base64Data, $folder)
    {
        $parts = explode(',', $base64Data);
        if (count($parts) !== 2) return null;
        
        $imageType = explode(';', explode(':', $parts[0])[1])[0];
        $imageExtension = explode('/', $imageType)[1] ?? 'png';
        $imageStr = base64_decode($parts[1]);
        
        $filename = time() . '_' . uniqid() . '.' . $imageExtension;
        $tenantId = tenant('id');
        $targetFolder = "tenants/{$tenantId}/{$folder}";

        try {
            return $this->uploadRawToNextcloud($imageStr, $filename, $targetFolder);
        } catch (\Exception $e) {
            Log::error("Nextcloud Base64 Upload Failed: " . $e->getMessage());
            // Fallback local storage
            $path = 'tenant/' . $tenantId . '/' . $folder . '/' . $filename;
            Storage::disk('public')->put($path, $imageStr);
            return $path;
        }
    }
}
