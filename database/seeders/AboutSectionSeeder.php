<?php

namespace Database\Seeders;

use App\Models\AboutSection;
use App\Models\AboutSlide;
use Illuminate\Database\Seeder;

class AboutSectionSeeder extends Seeder
{
    public function run(): void
    {
        AboutSection::truncate();
        AboutSection::create([
            'title_id' => 'Kami Menciptakan Masa Depan Digital',
            'title_en' => 'We Creating Digital Future',
            'description_id' => 'FKStudio bukan sekadar agensi kreatif; kami adalah mitra strategis Anda dalam menavigasi kompleksitas dunia digital. Kami menggabungkan estetika desain dengan kecanggihan teknologi untuk menciptakan solusi yang tidak hanya indah dipandang, tetapi juga memberikan hasil nyata bagi pertumbuhan bisnis Anda.',
            'description_en' => 'FKStudio is not just a creative agency; we are your strategic partner in navigating the complexities of the digital world. We combine design aesthetics with technological sophistication to create solutions that are not only beautiful to look at, but also deliver tangible results for your business growth.',
            'vision_id' => 'Menjadi pusat inovasi digital global yang mendefinisikan ulang cara brand berinteraksi dengan audiens melalui pengalaman visual yang emosional dan teknologi yang inklusif.',
            'vision_en' => 'To be a global digital innovation hub that redefines how brands interact with audiences through emotional visual experiences and inclusive technology.',
            'mission_id' => "Mengutamakan kualitas estetik dan fungsional dalam setiap produk digital yang diciptakan.\nMemberdayakan bisnis melalui strategi digital yang terukur dan berorientasi pada hasil.\nMendorong batas-batas kreativitas melalui riset mendalam dan teknologi terbaru.\nMembangun ekosistem kolaborasi yang transparan dan berkelanjutan dengan setiap mitra.",
            'mission_en' => "Prioritizing aesthetic and functional quality in every digital product created.\nEmpowering businesses through measurable and result-oriented digital strategies.\nPushing the boundaries of creativity through in-depth research and the latest technology.\nBuilding a transparent and sustainable collaboration ecosystem with every partner.",
            'stats' => [
                ['label_id' => 'Tahun Pengalaman', 'label_en' => 'Years Experience', 'value' => '5+'],
                ['label_id' => 'Proyek Selesai', 'label_en' => 'Projects Completed', 'value' => '100+'],
                ['label_id' => 'Klien Senang', 'label_en' => 'Happy Clients', 'value' => '50+'],
            ],
        ]);

        AboutSlide::create(['image' => 'about-1.jpg']);
        AboutSlide::create(['image' => 'about-2.jpg']);
    }
}
