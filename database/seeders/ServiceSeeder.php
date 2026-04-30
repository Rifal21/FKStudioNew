<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::truncate();
        
        $services = [
            [
                'title_id' => 'UI/UX Design Specialist',
                'title_en' => 'UI/UX Design Specialist',
                'description_id' => 'Kami merancang antarmuka yang tidak hanya indah secara visual, tetapi juga memberikan pengalaman pengguna yang intuitif dan konversi tinggi.',
                'description_en' => 'We design interfaces that are not only visually stunning but also provide intuitive user experiences and high conversion rates.',
                'icon' => 'fa-solid fa-bezier-curve',
                'order' => 1,
            ],
            [
                'title_id' => 'Web & App Development',
                'title_en' => 'Web & App Development',
                'description_id' => 'Membangun ekosistem digital yang kuat dengan teknologi terbaru, performa secepat kilat, dan skalabilitas untuk masa depan bisnis Anda.',
                'description_en' => 'Building robust digital ecosystems with the latest technology, lightning-fast performance, and scalability for your future business.',
                'icon' => 'fa-solid fa-code',
                'order' => 2,
            ],
            [
                'title_id' => 'Brand Identity Strategy',
                'title_en' => 'Brand Identity Strategy',
                'description_id' => 'Mendefinisikan esensi brand Anda melalui identitas visual yang ikonik, mulai dari logo hingga pedoman gaya hidup yang konsisten.',
                'description_en' => 'Defining your brand essence through iconic visual identities, from logos to consistent lifestyle guidelines.',
                'icon' => 'fa-solid fa-gem',
                'order' => 3,
            ],
            [
                'title_id' => 'Digital Marketing Growth',
                'title_en' => 'Digital Marketing Growth',
                'description_id' => 'Mengakselerasi pertumbuhan bisnis Anda melalui strategi pemasaran digital yang terukur, SEO, dan kampanye media sosial yang berdampak.',
                'description_en' => 'Accelerating your business growth through measurable digital marketing strategies, SEO, and impactful social media campaigns.',
                'icon' => 'fa-solid fa-chart-line',
                'order' => 4,
            ],
            [
                'title_id' => 'Creative Content Production',
                'title_en' => 'Creative Content Production',
                'description_id' => 'Menciptakan narasi visual yang kuat melalui videografi, fotografi, dan desain konten yang mampu menggerakkan emosi audiens Anda.',
                'description_en' => 'Creating powerful visual narratives through videography, photography, and content design that can move your audience\'s emotions.',
                'icon' => 'fa-solid fa-camera-retro',
                'order' => 5,
            ],
            [
                'title_id' => 'IT Consultant & Solutions',
                'title_en' => 'IT Consultant & Solutions',
                'description_id' => 'Memberikan saran strategis untuk transformasi digital Anda, memastikan infrastruktur teknologi Anda siap menghadapi tantangan masa depan.',
                'description_en' => 'Providing strategic advice for your digital transformation, ensuring your technology infrastructure is ready for future challenges.',
                'icon' => 'fa-solid fa-lightbulb',
                'order' => 6,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
