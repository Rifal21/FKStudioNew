<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        Project::truncate();
        
        $projects = [
            [
                'title_id' => 'Gragaz Pharmacy V2',
                'title_en' => 'Gragaz Pharmacy V2',
                'description_id' => 'Transformasi platform farmasi digital dengan sistem inventory real-time dan pengalaman belanja modern.',
                'description_en' => 'Transformation of a digital pharmacy platform with real-time inventory and modern shopping experience.',
                'category_id' => 'Web Ecosystem',
                'category_en' => 'Web Ecosystem',
                'url' => 'https://gragaz.com', // Placeholder
                'is_featured' => true,
                'order' => 1,
            ],
            [
                'title_id' => 'Mora Fashion Hub',
                'title_en' => 'Mora Fashion Hub',
                'description_id' => 'Marketplace fashion eksklusif dengan fokus pada visual storytelling dan kenyamanan transaksi.',
                'description_en' => 'Exclusive fashion marketplace focused on visual storytelling and transaction convenience.',
                'category_id' => 'E-Commerce',
                'category_en' => 'E-Commerce',
                'url' => 'https://mora.io', // Placeholder
                'is_featured' => true,
                'order' => 2,
            ],
            [
                'title_id' => 'Zenith Fintech App',
                'title_en' => 'Zenith Fintech App',
                'description_id' => 'Aplikasi manajemen keuangan pribadi dengan dashboard analitik yang intuitif dan aman.',
                'description_en' => 'Personal finance management app with intuitive and secure analytics dashboard.',
                'category_id' => 'Mobile App',
                'category_en' => 'Mobile App',
                'image' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=1200&q=80',
                'is_featured' => true,
                'order' => 3,
            ],
            [
                'title_id' => 'Nebula Creative Agency',
                'title_en' => 'Nebula Creative Agency',
                'description_id' => 'Website portfolio interaktif untuk agensi kreatif global dengan animasi yang memukau.',
                'description_en' => 'Interactive portfolio website for a global creative agency with stunning animations.',
                'category_id' => 'Creative Portfolio',
                'category_en' => 'Creative Portfolio',
                'url' => 'https://nebula.design', // Placeholder
                'is_featured' => true,
                'order' => 4,
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
