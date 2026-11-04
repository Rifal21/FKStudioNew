<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Moves the stored brand to "FKStudio IT Solution" and replaces the old template SEO title. A value the admin has
     * already rewritten is left alone: each update only matches the previous default.
     */
    public function up(): void
    {
        $settings = DB::table('site_settings');

        (clone $settings)->where('site_name', 'FKStudio')->update(['site_name' => 'FKStudio IT Solution']);
        (clone $settings)->where('seo_title', 'FKStudio - Modern Creative Agency')->update(['seo_title' => 'FKStudio IT Solution - Jasa Pembuatan Website & Aplikasi']);
        (clone $settings)->where('seo_description', 'Kami membantu bisnis Anda tumbuh dengan desain dan teknologi modern.')->update(['seo_description' => 'FKStudio IT Solution: jasa pembuatan website, toko online, dan aplikasi untuk UMKM di Indonesia. Harga paket transparan, bayar DP atau lunas.']);
    }

    public function down(): void
    {
        DB::table('site_settings')->where('site_name', 'FKStudio IT Solution')->update(['site_name' => 'FKStudio']);
    }
};
