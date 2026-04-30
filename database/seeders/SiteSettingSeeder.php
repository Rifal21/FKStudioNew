<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::create([
            'site_name' => 'FKStudio',
            'footer_text_id' => '© 2024 FKStudio. Semua hak dilindungi.',
            'footer_text_en' => '© 2024 FKStudio. All rights reserved.',
            'seo_title' => 'FKStudio - Modern Creative Agency',
            'seo_description' => 'Kami membantu bisnis Anda tumbuh dengan desain dan teknologi modern.',
            'contact_email' => 'hello@fkstudio.com',
            'contact_phone' => '+62 812 3456 7890',
            'contact_address' => 'Jakarta, Indonesia',
        ]);
    }
}
