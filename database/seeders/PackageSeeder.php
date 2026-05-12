<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        Package::truncate();

        Package::create([
            'name_id' => 'Starter Spark',
            'name_en' => 'Starter Spark',
            'price' => 'Rp 7.500.000',
            'description_id' => 'Solusi esensial untuk membangun kehadiran digital yang solid dan profesional.',
            'description_en' => 'Essential solution to build a solid and professional digital presence.',
            'features_id' => ['Custom UI/UX Design (Up to 5 Pages)', 'Mobile Responsive Development', 'Standard SEO Setup', 'SSL Certificate & Security', '1 Month Technical Support'],
            'features_en' => ['Custom UI/UX Design (Up to 5 Pages)', 'Mobile Responsive Development', 'Standard SEO Setup', 'SSL Certificate & Security', '1 Month Technical Support'],
            'cta_text_id' => 'Mulai Sekarang',
            'cta_text_en' => 'Get Started',
            'cta_link' => '#contact',
            'is_active' => true,
            'is_featured' => false,
            'slug' => 'starter-spark',
            'order' => 1,
        ]);

        Package::create([
            'name_id' => 'Business Growth',
            'name_en' => 'Business Growth',
            'price' => 'Rp 18.500.000',
            'description_id' => 'Paket komprehensif untuk mendominasi pasar digital dengan fitur-fitur canggih.',
            'description_en' => 'Comprehensive package to dominate the digital market with advanced features.',
            'features_id' => ['Full Ecosystem Web Design', 'Advanced SEO & Analytics', 'Content Management System (CMS)', 'Social Media Integration', '6 Months Dedicated Support', 'Email Marketing Setup'],
            'features_en' => ['Full Ecosystem Web Design', 'Advanced SEO & Analytics', 'Content Management System (CMS)', 'Social Media Integration', '6 Months Dedicated Support', 'Email Marketing Setup'],
            'cta_text_id' => 'Pilih Paket Populer',
            'cta_text_en' => 'Choose Popular Plan',
            'cta_link' => '#contact',
            'is_active' => true,
            'is_featured' => true,
            'slug' => 'business-growth',
            'order' => 2,
        ]);

        Package::create([
            'name_id' => 'Enterprise Custom',
            'name_en' => 'Enterprise Custom',
            'price' => 'By Quote',
            'description_id' => 'Solusi teknologi tingkat tinggi yang dirancang khusus untuk kebutuhan korporasi skala besar.',
            'description_en' => 'High-level technology solutions tailor-made for large-scale corporate needs.',
            'features_id' => ['Unlimited Custom Features', 'High-End Security & Firewall', '24/7 Priority Support Lane', 'Dedicated Performance Server', 'API Integration & Automations', 'Quarterly System Audit'],
            'features_en' => ['Unlimited Custom Features', 'High-End Security & Firewall', '24/7 Priority Support Lane', 'Dedicated Performance Server', 'API Integration & Automations', 'Quarterly System Audit'],
            'cta_text_id' => 'Konsultasi Gratis',
            'cta_text_en' => 'Free Consultation',
            'cta_link' => '#contact',
            'is_active' => true,
            'is_featured' => false,
            'slug' => 'enterprise-custom',
            'order' => 3,
        ]);
    }
}
