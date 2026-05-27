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
                'title_id' => 'Pembuatan Website Custom',
                'title_en' => 'Custom Website Development',
                'description_id' => 'Bikin landing page, profil perusahaan, e-commerce, atau dashboard custom dengan desain eksklusif, responsif di HP, dan SEO-friendly.',
                'description_en' => 'Build high-converting landing pages, corporate profiles, e-commerce, or custom dashboards with premium mobile-first designs and advanced SEO.',
                'icon' => 'fa-solid fa-laptop-code',
                'order' => 1,
            ],
            [
                'title_id' => 'Aplikasi Mobile (Android & iOS)',
                'title_en' => 'Mobile App Development',
                'description_id' => 'Pengembangan aplikasi mobile modern dengan performa super lancar, animasi smooth, dan ketersediaan di Google Play & App Store.',
                'description_en' => 'Developing high-performance Android & iOS mobile apps using modern tech stack, smooth gestures, and publishing to Play Store & App Store.',
                'icon' => 'fa-solid fa-mobile-screen-button',
                'order' => 2,
            ],
            [
                'title_id' => 'Sistem Web & Aplikasi SaaS',
                'title_en' => 'Web Systems & SaaS Platforms',
                'description_id' => 'Solusi sistem ERP, CRM, manajemen inventori, dan otomasi internal yang mempermudah operasional harian perusahaan Anda.',
                'description_en' => 'Automated internal business workflows including custom CRM, ERP, inventory management systems, and robust SaaS solutions.',
                'icon' => 'fa-solid fa-network-wired',
                'order' => 3,
            ],
            [
                'title_id' => 'Desain UI/UX Interaktif',
                'title_en' => 'Premium UI/UX Design',
                'description_id' => 'Merancang arsitektur visual produk digital Anda dengan fokus kenyamanan pengguna, alur navigasi yang asyik, dan konversi tinggi.',
                'description_en' => 'Mapping out interactive user journeys, intuitive mockups, and modern prototype interfaces focused on customer satisfaction and conversions.',
                'icon' => 'fa-solid fa-bezier-curve',
                'order' => 4,
            ],
            [
                'title_id' => 'Integrasi API & Payment Gateway',
                'title_en' => 'API & Payment Integration',
                'description_id' => 'Hubungkan website dan aplikasimu ke sistem pembayaran otomatis, ekspedisi kurir, notifikasi WhatsApp, dan layanan pihak ketiga lainnya.',
                'description_en' => 'Connecting your website and mobile apps to automated payment gateways, logistics, SMS/WhatsApp gateways, and third-party APIs.',
                'icon' => 'fa-solid fa-link',
                'order' => 5,
            ],
            [
                'title_id' => 'Konsultasi IT & Support',
                'title_en' => 'IT Consulting & Maintenance',
                'description_id' => 'Layanan setup server cloud, optimasi database, audit keamanan, serta pemeliharaan berkala agar website Anda selalu online 24/7.',
                'description_en' => 'Cloud server deployment, database optimization, periodic security audits, and proactive maintenance to keep your systems online 24/7.',
                'icon' => 'fa-solid fa-gears',
                'order' => 6,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
