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
            'title_id' => 'Wujudkan Ide Digital Anda Menjadi Kenyataan',
            'title_en' => 'Turn Your Digital Ideas Into Reality',
            'subtitle_id' => 'Kami adalah agensi kreatif yang berdedikasi untuk menciptakan pengalaman digital yang inovatif, fungsional, dan berorientasi pada hasil untuk brand Anda.',
            'subtitle_en' => 'We are a creative agency dedicated to creating innovative, functional, and result-oriented digital experiences for your brand.',
            'cta_text_id' => 'Mulai Proyek',
            'cta_text_en' => 'Start Project',
            'cta_link' => '#portfolio',
        ]);

        // Add some dummy slides if needed
        HeroSlide::create(['image' => 'hero-1.jpg', 'order' => 1]);
        HeroSlide::create(['image' => 'hero-2.jpg', 'order' => 2]);
    }
}
