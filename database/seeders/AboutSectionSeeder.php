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
            'title_id' => 'Ahli Pembuatan Website & Aplikasi',
            'title_en' => 'Experts in Web & App Development',
            'description_id' => 'FKStudio adalah mitra pengembang digital yang berfokus pada jasa pembuatan website premium, landing page interaktif, dan aplikasi mobile berkinerja tinggi. Kami menggabungkan estetika UI/UX modern dengan performa teknologi mutakhir untuk mempercepat pertumbuhan bisnis Anda di era digital.',
            'description_en' => 'FKStudio is a digital development partner focusing on premium custom website development, interactive landing pages, and high-performance mobile applications. We blend modern UI/UX aesthetics with cutting-edge tech stack to accelerate your business growth.',
            'vision_id' => 'Menjadi agensi pengembang website dan aplikasi terpercaya yang menghadirkan solusi teknologi paling andal, asyik, dan mudah digunakan untuk semua kalangan bisnis.',
            'vision_en' => 'To be a trusted web and mobile application development agency delivering the most reliable, interactive, and user-friendly technology solutions for all business scales.',
            'mission_id' => "Menghadirkan website custom dan aplikasi mobile berkualitas premium dengan arsitektur kode bersih.\nMendampingi transformasi digital UMKM dan perusahaan korporat secara transparan dan solutif.\nMengoptimalkan performa kecepatan, keamanan, dan fungsionalitas di setiap produk yang dibuat.\nMemberikan layanan konsultasi IT dan pemeliharaan sistem jangka panjang yang responsif.",
            'mission_en' => "Delivering premium custom websites and mobile apps with clean, maintainable codebases.\nSupporting the digital transformation of SMEs and corporations with transparent solutions.\nOptimizing speed performance, security, and responsive features in every built product.\nProviding responsive IT consulting and long-term proactive system maintenance.",
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
