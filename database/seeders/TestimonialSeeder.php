<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        Testimonial::truncate();
        
        $testimonials = [
            [
                'name' => 'Budi Santoso',
                'role_id' => 'CEO of Gragaz Group',
                'role_en' => 'CEO of Gragaz Group',
                'content_id' => 'FKStudio benar-benar mengubah cara kami berbisnis secara digital. Sistem yang mereka bangun sangat efisien dan mudah digunakan oleh tim kami.',
                'content_en' => 'FKStudio completely changed how we do business digitally. The system they built is extremely efficient and easy for our team to use.',
                'rating' => 5,
            ],
            [
                'name' => 'Sarah Wijaya',
                'role_id' => 'Creative Director at Mora Fashion',
                'role_en' => 'Creative Director at Mora Fashion',
                'content_id' => 'Kerja sama yang luar biasa! Mereka mampu menerjemahkan visi kreatif kami menjadi sebuah karya digital yang estetik dan fungsional.',
                'content_en' => 'Incredible collaboration! They were able to translate our creative vision into an aesthetic and functional digital masterpiece.',
                'rating' => 5,
            ],
            [
                'name' => 'Michael Chen',
                'role_id' => 'Founder of Zenith Fintech',
                'role_en' => 'Founder of Zenith Fintech',
                'content_id' => 'Keamanan dan performa adalah prioritas kami, dan FKStudio memberikan solusi yang melampaui ekspektasi. Sangat direkomendasikan.',
                'content_en' => 'Security and performance are our priorities, and FKStudio delivered solutions that exceeded expectations. Highly recommended.',
                'rating' => 5,
            ],
            [
                'name' => 'Linda Permata',
                'role_id' => 'Head of Marketing at Global Edu',
                'role_en' => 'Head of Marketing at Global Edu',
                'content_id' => 'Tim yang sangat responsif dan ahli di bidangnya. Strategi pemasaran digital mereka memberikan dampak instan pada pertumbuhan traffic kami.',
                'content_en' => 'A very responsive team and experts in their field. Their digital marketing strategy had an instant impact on our traffic growth.',
                'rating' => 5,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}
