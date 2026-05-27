<?php

namespace Database\Seeders;

use App\Models\HeroSection;
use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class HeroSectionSeeder extends Seeder
{
    public function run(): void
    {
        HeroSection::truncate();
        HeroSection::create([
            'title_id' => 'Transformasi Bisnis Lewat Website & Aplikasi Premium',
            'title_en' => 'Elevate Your Business With Premium Web & Apps',
            'subtitle_id' => 'Kami menghadirkan jasa pembuatan website custom, platform e-commerce, dan aplikasi mobile berkinerja tinggi yang dirancang khusus untuk mempercepat pertumbuhan bisnis Anda.',
            'subtitle_en' => 'We deliver premium custom website development, robust e-commerce platforms, and high-performance mobile applications tailored to accelerate your business growth.',
            'cta_text_id' => 'Mulai Konsultasi',
            'cta_text_en' => 'Start Consultation',
            'cta_link' => '/products',
        ]);

        // Add dummy slides
        HeroSlide::create(['image' => 'hero-1.jpg', 'order' => 1]);
        HeroSlide::create(['image' => 'hero-2.jpg', 'order' => 2]);
    }
}
