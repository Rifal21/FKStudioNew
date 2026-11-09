<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\Shop\ProductSaver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Sample digital products for the shop: one per delivery type (file, license key, link),
 * with and without variants. Contents, prices, files and keys are placeholders; edit or
 * delete them in Dashboard > Toko > Produk before switching the shop on.
 *
 * Safe to run again: products whose slug already exists are skipped, nothing is deleted.
 */
class ShopProductSeeder extends Seeder
{
    private const FILE_DISK = 'local';

    public function run(ProductSaver $saver): void
    {
        $categories = $this->categories();

        foreach ($this->products() as $data) {
            if (Product::withTrashed()->where('slug', $data['slug'])->exists()) {
                continue;
            }

            $files = $data['files'] ?? [];
            $keys = $data['keys'] ?? 0;
            $data['product_category_id'] = $categories[$data['category']]->id;
            unset($data['files'], $data['keys'], $data['category']);

            $product = $saver->save(new Product(), $data + [
                'type' => Product::DIGITAL,
                'is_active' => true,
                'max_downloads' => 5,
            ]);

            foreach ($files as $file) {
                $this->attachSampleFile($product, $file);
            }

            for ($i = 0; $i < $keys; $i++) {
                $product->licenseKeys()->create(['key' => 'DEMO-'.strtoupper(Str::random(4).'-'.Str::random(4).'-'.Str::random(4))]);
            }
        }
    }

    /** @return array<string, ProductCategory> */
    private function categories(): array
    {
        $rows = [
            'template' => ['slug' => 'template-website', 'name_id' => 'Template Website', 'name_en' => 'Website Templates', 'description_id' => 'Template siap pakai yang bisa langsung dipasang dan diubah.', 'description_en' => 'Ready-made templates you can install and customise.', 'sort_order' => 1],
            'uikit' => ['slug' => 'ui-kit-desain', 'name_id' => 'UI Kit & Desain', 'name_en' => 'UI Kits & Design', 'description_id' => 'Komponen antarmuka dan aset desain.', 'description_en' => 'Interface components and design assets.', 'sort_order' => 2],
            'tools' => ['slug' => 'plugin-tools', 'name_id' => 'Plugin & Tools', 'name_en' => 'Plugins & Tools', 'description_id' => 'Plugin dan alat bantu pengembangan dengan lisensi.', 'description_en' => 'Licensed plugins and developer tools.', 'sort_order' => 3],
            'learn' => ['slug' => 'ebook-kelas', 'name_id' => 'E-book & Kelas', 'name_en' => 'E-books & Courses', 'description_id' => 'Materi belajar untuk pemilik bisnis dan developer.', 'description_en' => 'Learning material for business owners and developers.', 'sort_order' => 4],
        ];

        return collect($rows)->map(fn ($row) => ProductCategory::firstOrCreate(['slug' => $row['slug']], $row + ['is_active' => true]))->all();
    }

    private function products(): array
    {
        return [
            [
                'slug' => 'template-company-profile-laravel',
                'category' => 'template',
                'name_id' => 'Template Company Profile Laravel',
                'name_en' => 'Laravel Company Profile Template',
                'short_description_id' => 'Website profil perusahaan siap pakai: responsif, dua bahasa, dan kontennya diubah dari panel admin.',
                'short_description_en' => 'A ready-made company profile site: responsive, bilingual, with content managed from an admin panel.',
                'description_id' => $this->description('id', ['Source code Laravel + Blade + Tailwind', 'Panel admin untuk hero, layanan, portofolio, dan blog', 'Dukungan dua bahasa (ID/EN)', 'Panduan instalasi langkah demi langkah', 'Pembaruan gratis 6 bulan'], 'Cocok untuk UMKM yang ingin punya website profesional tanpa mulai dari nol.'),
                'description_en' => $this->description('en', ['Laravel + Blade + Tailwind source code', 'Admin panel for hero, services, portfolio and blog', 'Bilingual support (ID/EN)', 'Step-by-step installation guide', 'Free updates for 6 months'], 'For small businesses that want a professional website without starting from scratch.'),
                'license_terms_id' => "Boleh dipakai untuk 1 website (pribadi atau klien).\nTidak boleh dijual ulang atau dibagikan sebagai template.",
                'license_terms_en' => "Use on 1 website (your own or a client's).\nMay not be resold or redistributed as a template.",
                'digital_delivery' => Product::DELIVERY_FILE,
                'is_featured' => true,
                'sort_order' => 1,
                'has_variants' => false,
                'price' => 349000,
                'compare_at_price' => 499000,
                'files' => [['name' => 'company-profile-laravel.txt', 'version' => 'v1.0']],
            ],
            [
                'slug' => 'template-toko-online-umkm',
                'category' => 'template',
                'name_id' => 'Template Toko Online UMKM',
                'name_en' => 'Small Business Online Store Template',
                'short_description_id' => 'Toko online sederhana dengan katalog, keranjang, dan checkout lewat WhatsApp.',
                'short_description_en' => 'A simple online store with catalog, cart and WhatsApp checkout.',
                'description_id' => $this->description('id', ['Katalog produk dengan kategori dan pencarian', 'Keranjang dan checkout ke WhatsApp', 'Halaman admin produk', 'Siap dipasang di shared hosting'], 'Pilih Lite untuk satu toko, Pro untuk fitur voucher dan laporan penjualan.'),
                'description_en' => $this->description('en', ['Product catalog with categories and search', 'Cart and WhatsApp checkout', 'Product admin pages', 'Runs on shared hosting'], 'Pick Lite for a single store, Pro for vouchers and sales reports.'),
                'digital_delivery' => Product::DELIVERY_FILE,
                'is_featured' => true,
                'sort_order' => 2,
                'has_variants' => true,
                'options' => [['name_id' => 'Edisi', 'name_en' => 'Edition', 'values' => [['value_id' => 'Lite', 'value_en' => 'Lite'], ['value_id' => 'Pro', 'value_en' => 'Pro']]]],
                'variants' => [
                    ['key' => 'Lite', 'price' => 499000, 'is_active' => true],
                    ['key' => 'Pro', 'price' => 899000, 'compare_at_price' => 1199000, 'is_active' => true],
                ],
                'files' => [
                    ['name' => 'toko-online-umkm.txt', 'version' => 'v1.0'],
                    ['name' => 'toko-online-umkm-modul-pro.txt', 'version' => 'v1.0', 'variant' => 'Pro'],
                ],
            ],
            [
                'slug' => 'ui-kit-dashboard-admin',
                'category' => 'uikit',
                'name_id' => 'UI Kit Dashboard Admin',
                'name_en' => 'Admin Dashboard UI Kit',
                'short_description_id' => '120+ komponen Tailwind untuk panel admin, lengkap dengan file Figma.',
                'short_description_en' => '120+ Tailwind components for admin panels, with Figma files.',
                'description_id' => $this->description('id', ['120+ komponen HTML + Tailwind', 'File Figma dengan auto-layout', 'Mode terang dan gelap', 'Grafik, tabel, form, dan halaman login'], 'Lisensi Personal untuk proyek sendiri, Komersial untuk proyek klien, Agensi untuk proyek tanpa batas.'),
                'description_en' => $this->description('en', ['120+ HTML + Tailwind components', 'Figma file with auto-layout', 'Light and dark mode', 'Charts, tables, forms and login pages'], 'Personal for your own project, Commercial for client work, Agency for unlimited projects.'),
                'license_terms_id' => "Personal: 1 proyek pribadi.\nKomersial: 1 proyek klien.\nAgensi: proyek tanpa batas, tidak boleh dijual ulang sebagai UI kit.",
                'license_terms_en' => "Personal: 1 personal project.\nCommercial: 1 client project.\nAgency: unlimited projects, may not be resold as a UI kit.",
                'digital_delivery' => Product::DELIVERY_FILE,
                'sort_order' => 3,
                'has_variants' => true,
                'options' => [['name_id' => 'Lisensi', 'name_en' => 'License', 'values' => [['value_id' => 'Personal', 'value_en' => 'Personal'], ['value_id' => 'Komersial', 'value_en' => 'Commercial'], ['value_id' => 'Agensi', 'value_en' => 'Agency']]]],
                'variants' => [
                    ['key' => 'Personal', 'price' => 150000, 'is_active' => true],
                    ['key' => 'Komersial', 'price' => 450000, 'is_active' => true],
                    ['key' => 'Agensi', 'price' => 900000, 'is_active' => true],
                ],
                'files' => [['name' => 'ui-kit-dashboard.txt', 'version' => 'v2.1']],
            ],
            [
                'slug' => 'icon-pack-umkm',
                'category' => 'uikit',
                'name_id' => 'Icon Pack UMKM (300 ikon)',
                'name_en' => 'Small Business Icon Pack (300 icons)',
                'short_description_id' => '300 ikon SVG bertema usaha: kuliner, retail, jasa, dan pembayaran.',
                'short_description_en' => '300 business-themed SVG icons: food, retail, services and payments.',
                'description_id' => $this->description('id', ['300 ikon SVG dan PNG', 'Tiga gaya: garis, isi, dan duotone', 'Bisa diubah warnanya'], 'Untuk website, media sosial, dan materi promosi.'),
                'description_en' => $this->description('en', ['300 SVG and PNG icons', 'Three styles: line, solid and duotone', 'Recolourable'], 'For websites, social media and promotional material.'),
                'digital_delivery' => Product::DELIVERY_FILE,
                'max_downloads' => null,
                'sort_order' => 4,
                'has_variants' => false,
                'price' => 59000,
                'files' => [['name' => 'icon-pack-umkm.txt', 'version' => 'v1.0']],
            ],
            [
                'slug' => 'plugin-invoice-whatsapp-laravel',
                'category' => 'tools',
                'name_id' => 'Lisensi Plugin Invoice WhatsApp untuk Laravel',
                'name_en' => 'WhatsApp Invoice Plugin License for Laravel',
                'short_description_id' => 'Kirim invoice PDF otomatis ke WhatsApp pelanggan dari aplikasi Laravel Anda.',
                'short_description_en' => 'Send PDF invoices to customers on WhatsApp straight from your Laravel app.',
                'description_id' => $this->description('id', ['Integrasi WAHA (WhatsApp HTTP API)', 'Template pesan yang bisa diubah', 'Lampiran PDF otomatis', 'Satu kunci lisensi per domain'], 'Kunci lisensi muncul di akun Anda setelah pembayaran lunas.'),
                'description_en' => $this->description('en', ['WAHA (WhatsApp HTTP API) integration', 'Editable message templates', 'Automatic PDF attachment', 'One license key per domain'], 'Your license key appears in your account once payment is confirmed.'),
                'license_terms_id' => 'Satu kunci lisensi berlaku untuk satu domain produksi.',
                'license_terms_en' => 'One license key is valid for one production domain.',
                'digital_delivery' => Product::DELIVERY_LICENSE,
                'sort_order' => 5,
                'has_variants' => false,
                'price' => 250000,
                'keys' => 10,
            ],
            [
                'slug' => 'ebook-memulai-toko-online',
                'category' => 'learn',
                'name_id' => 'E-book: Memulai Toko Online dari Nol',
                'name_en' => 'E-book: Starting an Online Store from Scratch',
                'short_description_id' => 'Panduan praktis dari memilih platform sampai pesanan pertama.',
                'short_description_en' => 'A practical guide from choosing a platform to your first order.',
                'description_id' => $this->description('id', ['Memilih platform yang sesuai', 'Foto produk dan deskripsi yang menjual', 'Pembayaran dan pengiriman', 'Promosi awal dengan anggaran kecil'], 'Format PDF, bisa dibaca di ponsel dan laptop.'),
                'description_en' => $this->description('en', ['Choosing the right platform', 'Product photos and copy that sell', 'Payments and shipping', 'Early promotion on a small budget'], 'PDF format, readable on phone and laptop.'),
                'digital_delivery' => Product::DELIVERY_FILE,
                'sort_order' => 6,
                'has_variants' => false,
                'price' => 89000,
                'compare_at_price' => 129000,
                'files' => [['name' => 'ebook-memulai-toko-online.txt', 'version' => 'Edisi 1']],
            ],
            [
                'slug' => 'kelas-online-laravel-untuk-pemula',
                'category' => 'learn',
                'name_id' => 'Kelas Online: Laravel untuk Pemula',
                'name_en' => 'Online Course: Laravel for Beginners',
                'short_description_id' => 'Belajar membuat aplikasi web dengan Laravel lewat video bertahap.',
                'short_description_en' => 'Learn to build web apps with Laravel through step-by-step videos.',
                'description_id' => $this->description('id', ['Video bertahap dari instalasi sampai deploy', 'Studi kasus aplikasi kasir sederhana', 'Akses selamanya ke materi'], 'Link akses kelas muncul di akun Anda setelah pembayaran lunas.'),
                'description_en' => $this->description('en', ['Step-by-step videos from install to deploy', 'Case study: a simple point-of-sale app', 'Lifetime access to the material'], 'The course link appears in your account once payment is confirmed.'),
                'digital_delivery' => Product::DELIVERY_LINK,
                'access_url' => 'https://example.com/kelas-laravel-pemula',
                'sort_order' => 7,
                'has_variants' => false,
                'price' => 199000,
            ],
        ];
    }

    private function description(string $locale, array $points, string $closing): string
    {
        $heading = $locale === 'id' ? 'Yang Anda dapatkan' : 'What you get';
        $items = collect($points)->map(fn ($p) => '<li>'.e($p).'</li>')->implode('');

        return "<h2>{$heading}</h2><ul>{$items}</ul><p>".e($closing).'</p>';
    }

    /** Placeholder download so the buy-and-download flow can be tried end to end. */
    private function attachSampleFile(Product $product, array $file): void
    {
        $variant = isset($file['variant']) ? $product->variants->firstWhere('title_id', $file['variant']) : null;
        $path = 'shop/files/seed/'.$product->slug.'/'.$file['name'];

        Storage::disk(self::FILE_DISK)->put($path, implode("\n", [
            'FILE CONTOH — '.$product->name_id.($variant ? ' ('.$variant->title_id.')' : ''),
            '',
            'File ini dibuat oleh ShopProductSeeder untuk menguji alur beli dan unduh.',
            'Ganti dengan file produk yang sebenarnya di Dashboard > Toko > Produk sebelum toko diaktifkan.',
        ]));

        $product->files()->create([
            'product_variant_id' => $variant?->id,
            'disk' => self::FILE_DISK,
            'path' => $path,
            'original_name' => $file['name'],
            'mime' => 'text/plain',
            'size' => Storage::disk(self::FILE_DISK)->size($path),
            'version_label' => $file['version'] ?? null,
        ]);
    }
}
