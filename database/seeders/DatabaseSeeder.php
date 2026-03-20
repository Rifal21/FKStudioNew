<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin Profile
        \App\Models\User::factory()->create([
            'name' => 'Admin FKStudio',
            'email' => 'rifal@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('falkur21'),
        ]);

        // Site Settings
        \App\Models\SiteSetting::create([
            'site_name' => 'FKStudio',
            'footer_text_id' => '© 2024 FKStudio. Semua hak dilindungi.',
            'footer_text_en' => '© 2024 FKStudio. All rights reserved.',
            'seo_title' => 'FKStudio - Modern Creative Agency',
            'seo_description' => 'Kami membantu bisnis Anda tumbuh dengan desain dan teknologi modern.',
            'contact_email' => 'hello@fkstudio.com',
            'contact_phone' => '+62 812 3456 7890',
            'contact_address' => 'Jakarta, Indonesia',
        ]);

        // Hero
        \App\Models\HeroSection::create([
            'title_id' => 'Solusi Digital Kreatif untuk Masa Depan',
            'title_en' => 'Creative Digital Solutions for the Future',
            'subtitle_id' => 'Kami membangun pengalaman digital yang luar biasa untuk membantu bisnis Anda berkembang.',
            'subtitle_en' => 'We build extraordinary digital experiences to help your business grow.',
            'cta_text_id' => 'Mulai Proyek',
            'cta_text_en' => 'Start Project',
            'cta_link' => '#contact',
        ]);

        // About
        \App\Models\AboutSection::create([
            'title_id' => 'Tentang Kami',
            'title_en' => 'About Us',
            'description_id' => 'FKStudio adalah agensi kreatif yang fokus pada inovasi dan kualitas. Kami percaya bahwa setiap brand memiliki cerita unik untuk diceritakan.',
            'description_en' => 'FKStudio is a creative agency focused on innovation and quality. We believe every brand has a unique story to tell.',
            'stats' => json_encode([
                ['label_id' => 'Tahun Pengalaman', 'label_en' => 'Years Experience', 'value' => '5+'],
                ['label_id' => 'Proyek Selesai', 'label_en' => 'Projects Completed', 'value' => '100+'],
                ['label_id' => 'Klien Senang', 'label_en' => 'Happy Clients', 'value' => '50+'],
            ]),
        ]);

        // Services
        \App\Models\Service::create([
            'title_id' => 'Desain UI/UX',
            'title_en' => 'UI/UX Design',
            'description_id' => 'Menciptakan antarmuka yang indah dan intuitif.',
            'description_en' => 'Creating beautiful and intuitive interfaces.',
            'icon' => 'swatch',
            'order' => 1,
        ]);
        \App\Models\Service::create([
            'title_id' => 'Pengembangan Web',
            'title_en' => 'Web Development',
            'description_id' => 'Membangun website modern yang cepat dan responsif.',
            'description_en' => 'Building modern, fast, and responsive websites.',
            'icon' => 'code-bracket',
            'order' => 2,
        ]);
        \App\Models\Service::create([
            'title_id' => 'Branding',
            'title_en' => 'Branding',
            'description_id' => 'Membangun identitas brand yang kuat dan ikonik.',
            'description_en' => 'Building strong and iconic brand identities.',
            'icon' => 'rocket-launch',
            'order' => 3,
        ]);

        // Projects
        \App\Models\Project::create([
            'title_id' => 'Platform E-Commerce',
            'title_en' => 'E-Commerce Platform',
            'description_id' => 'Solusi belanja online yang skalabel.',
            'description_en' => 'Scalable online shopping solutions.',
            'category_id' => 'Web',
            'category_en' => 'Web',
            'is_featured' => true,
        ]);

        // Testimonials
        \App\Models\Testimonial::create([
            'name' => 'John Doe',
            'role_id' => 'CEO Teknologi',
            'role_en' => 'Tech CEO',
            'content_id' => 'Luar biasa! Mereka sangat mengerti kebutuhan bisnis kami.',
            'content_en' => 'Amazing! They really understand our business needs.',
        ]);
    }
}
