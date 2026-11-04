<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::create([
            'site_name' => 'FKStudio IT Solution',
            'footer_text_id' => '© 2024 FKStudio IT Solution. Semua hak dilindungi.',
            'footer_text_en' => '© 2024 FKStudio IT Solution. All rights reserved.',
            'seo_title' => 'FKStudio IT Solution - Jasa Pembuatan Website & Aplikasi UMKM',
            'seo_description' => 'FKStudio IT Solution: jasa pembuatan website, toko online, dan aplikasi untuk UMKM di Indonesia. Harga paket transparan, bayar DP atau lunas.',
            'contact_email' => 'hello@fkstudio.com',
            'contact_phone' => '+62 812 3456 7890',
            'contact_address' => 'Jakarta, Indonesia',
            'tax_rate' => 11.00,
        ]);
    }
}
