<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        if (!$admin) {
            return;
        }

        $posts = [
            [
                'title_id' => 'Tren Desain Glassmorphism pada Antarmuka Modern 2026',
                'title_en' => 'Glassmorphism Design Trends in Modern Interfaces 2026',
                'slug' => 'glassmorphism-design-trends-modern-interfaces-2026',
                'content' => "<p>Desain antarmuka pengguna terus berkembang dengan sangat cepat. Di tahun 2026, salah satu tren yang paling mendominasi estetika premium adalah <strong>Glassmorphism</strong> (efek kaca transparan).</p><p>Glassmorphism menggabungkan elemen transparansi latar belakang, filter blur yang mendalam, batas garis tipis yang menyala (glowing borders), serta bayangan lembut untuk memberikan kesan kedalaman 3D.</p><p>Berikut beberapa tips mengimplementasikan tren ini pada website Anda:</p><ul><li>Gunakan saturasi warna latar belakang yang hidup (vibrant) agar efek blur kaca terlihat menonjol.</li><li>Buat pembatas luar (borders) yang sangat tipis semi-transparan (misalnya border-white/10) untuk memisahkan kartu dari latar belakang secara elegan.</li><li>Pertahankan kontras teks dengan menggunakan warna solid seperti putih murni atau slate-900 untuk aksesibilitas yang baik.</li></ul><p>Dengan memadukan tren visual ini, website Anda tidak hanya terasa lebih premium tetapi juga memberikan pengalaman interaksi yang futuristik dan memikat mata para pengunjung.</p>",
                'category_id' => 'Desain UI/UX',
                'category_en' => 'UI/UX Design',
                'author_name' => 'Rifal Kurniawan',
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title_id' => 'Mengapa Arsitektur Server Serverless Sangat Efisien untuk Startup',
                'title_en' => 'Why Serverless Server Architecture is Extremely Efficient for Startups',
                'slug' => 'why-serverless-architecture-efficient-startups',
                'content' => "<p>Mengelola server tradisional sering kali menjadi hambatan besar bagi startup yang ingin bergerak cepat. Di sinilah <strong>Serverless Computing</strong> (komputasi tanpa server) hadir sebagai solusi revolusioner.</p><p>Serverless memungkinkan developer untuk fokus 100% pada penulisan kode aplikasi tanpa harus memikirkan konfigurasi sistem operasi, skalabilitas kapasitas, atau patching keamanan.</p><p>Manfaat utama Serverless meliputi:</p><ul><li><strong>Biaya Berdasarkan Penggunaan Nyata (Pay-as-you-go):</strong> Anda hanya membayar ketika fungsi kode Anda dieksekusi, bukan untuk waktu idle server.</li><li><strong>Skalabilitas Instan:</strong> Infrastruktur secara otomatis membesar dari beberapa permintaan hingga jutaan akses tanpa lag.</li><li><strong>Kecepatan Rilis Produk (Time-to-market):</strong> Waktu peluncuran fitur baru berkurang secara dramatis karena manajemen server ditangani sepenuhnya oleh provider cloud.</li></ul><p>Mengadopsi serverless di tahun 2026 adalah pilihan paling strategis untuk membangun aplikasi modern yang sangat performant dengan modal awal minimum.</p>",
                'category_id' => 'Teknologi',
                'category_en' => 'Technology',
                'author_name' => 'FKStudio Dev Team',
                'is_published' => true,
                'published_at' => now()->subDay(),
            ]
        ];

        foreach ($posts as $post) {
            $post['author_id'] = $admin->id;
            BlogPost::create($post);
        }
    }
}
