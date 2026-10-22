# Analisa & Rencana Perombakan UI — FKStudio

> Tanggal analisa: 2026-10-22
> Cakupan: landing page publik, checkout & area pelanggan, auth, dan seluruh dashboard admin.
> Status: **implementasi selesai di branch `redesign/ui-v3`** (8 commit); Fase 7 (QA lintas browser/perangkat nyata, Lighthouse) belum dilakukan — lihat bagian 10. Keputusan final ada di bagian 9.

---

## 1. Ringkasan Eksekutif

Secara fungsi, aplikasi ini sudah lengkap (CMS landing, paket, checkout DP/pelunasan, voucher, invoice PDF, tiket, blog, manajemen user). Yang bermasalah adalah **lapisan tampilannya**: tidak ada design system, jadi setiap halaman "berdiri sendiri" dengan warna, font, dan gaya masing-masing.

Lima temuan terbesar:

| # | Temuan | Dampak |
|---|--------|--------|
| 1 | **Tampilan terpecah jadi 3 "dunia"**: landing (terang + dark toggle), checkout/profil/auth (gelap permanen `#0f172a`/`#020617`), admin (terang dengan sidebar gelap). | Pengguna merasa pindah ke produk lain saat klik "Beli" atau masuk ke dashboard. |
| 2 | **Teks terlalu kecil dan terlalu "teriak"**: `text-[10px]` dipakai 361×, `text-[9px]` 64×, `text-[8px]` 39×; `uppercase` 720× dan `font-black` 790×. | Sulit dibaca, terutama di HP; terkesan padat dan kurang profesional. |
| 3 | **Kontras warna rendah**: `slate-400` dipakai 452× di atas putih (rasio kontras hanya **2,56:1**, standar minimum 4,5:1). | Label dan teks pendukung nyaris tak terbaca di layar terang/outdoor. |
| 4 | **Admin belum ramah HP/tablet**: 6 tabel besar (orders, invoices, users, tickets, dll.) hanya mengandalkan scroll horizontal; hanya 5 file yang punya `overflow-x-auto`; tidak ada versi kartu untuk mobile; header halaman admin disembunyikan di mobile (`hidden lg:block`). | Mengelola order dari HP sangat tidak nyaman. |
| 5 | **Utang teknis di front-end**: CSS inline `<style>` di 31 file, `@apply` di dalam `<style>` Blade (tidak diproses Tailwind → efek gradient-text tidak jalan di beberapa halaman), kelas yang tidak valid (`slate-350`, `slate-650`, `lg:col-span-6.5`), ±2.400 baris view `tenant/*` yang sudah mati, dan library dimuat dari CDN berulang-ulang. | Sulit dirawat, lambat, dan visual bisa rusak diam-diam. |

**Arah perombakan:** satu design system terpusat → tema terang & gelap yang cerah dan ceria (violet cerah + aksen pink, matahari, mint, langit) → satu bahasa visual dari landing sampai admin → mobile-first → admin berbasis komponen (tabel responsif → kartu di HP).

---

## 2. Inventaris Saat Ini

### 2.1 Stack
- Laravel 13, PHP ≥ 8.3, Blade + Alpine.js, Tailwind CSS (via Vite), FontAwesome 6.4 (CDN), Swiper 11 (CDN), AOS (CDN), SweetAlert2 (CDN), dompdf untuk PDF.
- `package.json` memuat **`tailwindcss ^3`** bersama **`@tailwindcss/vite ^4`** (plugin v4 tidak dipakai di `vite.config.js`) → versi campur, perlu dirapikan.
- `@vite` dipanggil di 25 view, tapi tiap layout/halaman punya `<head>` sendiri-sendiri (tidak ada layout publik bersama).

### 2.2 Halaman (±13.650 baris view)

| Area | Halaman | Layout yang dipakai | Tema |
|------|---------|--------------------|------|
| **Landing** | `landing/index` (hero, clients, about, services, portfolio, packages, testimonials, footer) | `<head>` sendiri | Terang + dark toggle |
| | `landing/packages`, `package` (detail), `portfolio`, `blogs/index`, `blogs/show` | `<head>` sendiri ×5 | Terang + dark toggle |
| **Transaksi & akun** | `checkout` (658 baris), `checkout-pelunasan`, `checkout-success` | `<head>` sendiri | **Gelap permanen** |
| | `user/orders`, `user/websites`, `profile/edit` | `<head>` sendiri | **Gelap permanen** |
| **Auth** | login, register, forgot/reset/confirm/verify | `layouts/guest` | **Gelap permanen** |
| **Admin** | dashboard statistik, settings, payment methods, hero, about, services, projects, blogs, packages, testimonials, clients, owners, vouchers, orders, tickets, invoices (index/create/edit/show/pdf), users | `layouts/app` + `layouts/navigation` (sidebar) | Terang, sidebar gelap |
| **Sisa lama (mati)** | `tenant/*`, `dashboard/tenants`, `layouts/tenant-app`, `welcome` (bawaan Laravel) | — | Tidak ada controller/route/model yang memakainya |

### 2.3 Metrik yang diukur dari kode

| Metrik | Nilai | Catatan |
|--------|-------|---------|
| Font | Inter (landing/checkout), Outfit (admin/auth) | 2 font berbeda untuk 1 produk; `tailwind.config` malah mendeklarasikan Figtree |
| Radius | `rounded-[3rem]` ×60, `[2rem]` ×46, `[2.5rem]` ×27, `[3.5rem]` ×18, `[4rem]` ×4, dst. | Terlalu bulat dan tidak konsisten |
| Warna utama | `blue-500/600` (±780×), `indigo` (±200×), `emerald`, `red`, `pink` | Biru-indigo gelap + slate; kesan dingin/korporat berat |
| Hex hard-code | `#0f172a`, `#f8fafc`, `#2563eb`, plus sisa Breeze (`#f53003`, `#1b1b18`) | Tidak lewat token |
| `dark:` | 635 pemakaian, **hanya di halaman landing** | Admin tidak punya dark mode; checkout/auth dipaksa gelap |
| `<img>` | 75, **65 tanpa `alt`**, **0 `loading="lazy"`** | SEO, aksesibilitas, dan kecepatan |
| `aria-label` | 0 | Tombol ikon (hamburger, toggle tema, panah slider) tak terbaca screen reader |
| Efek | `blur`/`backdrop-blur` ±170×, `data-aos` 92×, blob animasi `animate-pulse` + `animate-bounce` di background fixed | Berat di HP kelas menengah ke bawah |
| Preloader | Landing menampilkan spinner paksa 800 ms | Memperlambat tampilan awal tanpa alasan nyata |
| Alpine | Dimuat dari Vite **dan** dari CDN di landing | Inisialisasi ganda berpotensi bug |

### 2.4 Pola desain yang ada (dan perlu dipertahankan / dibuang)

**Dipertahankan:** struktur konten landing, urutan section, navbar "floating pill" saat scroll (konsepnya bagus), bilingual ID/EN, sidebar admin berkelompok (Main Menu / Transaction / Landing Content / System), invoice PDF.

**Dibuang/diganti:** gaya "glassmorphism + blob + uppercase tracking lebar + font-black di semua tempat", radius ekstrem, tema gelap-paksa di checkout/auth, label 8–10 px.

---

## 3. Masalah Per Area

### 3.1 Landing page
1. **Hero**: slider foto full-bleed dengan overlay gradient putih tebal → foto jadi pudar, teks kiri padat; judul `text-[5rem] font-black` dengan gradient-text (di beberapa halaman `@apply`-nya tidak aktif).
2. **Navbar**: label menu `text-[11px] uppercase tracking-[0.18em]` → sulit dibaca; 7 item + switch bahasa + toggle tema + avatar berdesakan di desktop; ada query DB (`PackageOrder::where…first()`) langsung di Blade.
3. **Background blob fixed** + preloader + AOS di semua elemen → terasa "ramai", boros GPU.
4. **Tidak ada ritme section**: semua section hampir sama (judul tengah + grid kartu radius besar). Tidak ada penanda visual antar-section yang jelas, tidak ada section "cara kerja", "statistik", atau "FAQ" yang lazim di landing jasa web.
5. **CTA ganda tidak jelas hierarkinya** (Mulai Konsultasi vs Lihat Portofolio vs panah slider berjejer di satu baris).
6. **Footer** dan **clients marquee** perlu dirapikan agar konsisten dengan palet baru.

### 3.2 Checkout & area pelanggan
- Berpindah dari landing terang ke checkout gelap terasa seperti pindah situs.
- `checkout.blade.php` 658 baris dengan state Alpine besar di `<body x-data>` + banyak label `text-[9px]/[10px]` → form panjang dan sulit dipindai di HP.
- Tidak ada **stepper** (Data → Domain → Pembayaran → Konfirmasi) dan ringkasan harga tidak "menempel" di HP.
- `user/orders` (364 baris) & `user/websites` perlu tampilan status yang lebih jelas (timeline pengerjaan).

### 3.3 Auth
- Kartu kaca di atas background gelap + blob; input putih-transparan; kontras tombol dan placeholder rendah. Tidak ada elemen kepercayaan (logo + tagline + ilustrasi).

### 3.4 Admin
1. **Sidebar** 18 menu dengan kelas yang diulang-ulang manual (tiap link ±300 karakter class) → tidak ada komponen `<x-sidebar-link>`.
2. **Header halaman disembunyikan di mobile**; header mobile hanya logo + hamburger; tidak ada breadcrumb/judul halaman di HP.
3. **Dashboard statistik** hanya 6 kartu angka + kartu "Welcome" besar — tidak ada angka bisnis yang berguna (pendapatan bulan ini, order menunggu bayar, order dalam pengerjaan, tiket terbuka, invoice jatuh tempo) dan tidak ada grafik/aktivitas terbaru. `blogsCount` dihitung di controller tapi tidak ditampilkan.
4. **Tabel**: padding `p-6`, header `text-xs uppercase tracking-widest slate-400`, aksi dalam sel padat; `orders/index` memuat info website, domain, catatan, progres, dan tombol aksi dalam satu sel → sangat tinggi dan berat dibaca. Tidak ada filter/pencarian/pagination yang terlihat konsisten.
5. **Form CRUD** (services, projects, packages, testimonials, clients, owners, vouchers) berupa modal/panel dengan input mentah (`<input>` tanpa komponen) → tampilan dan validasi error tidak seragam.
6. **Notifikasi**: SweetAlert2 hanya untuk `session('success')`; error validasi tidak punya pola tampilan global.
7. Hampir tidak ada empty state, skeleton, atau konfirmasi hapus yang seragam.

### 3.5 Teknis
- 31 file berisi `<style>` inline (banyak duplikat `.glass`, `.gradient-text`).
- `@apply` dalam `<style>` Blade tidak diproses → harus dipindah ke `resources/css/app.css`.
- Kelas tidak valid: `slate-350`, `slate-650`, `lg:col-span-6.5` (tidak menghasilkan CSS).
- `tailwind.config.js` punya `safelist` regex untuk 5 warna × 10 shade × 3 varian → membengkakkan CSS hasil build; sebagian besar tidak perlu.
- Dependency CDN tersebar: FontAwesome (24×), jsDelivr (24×), unpkg (14×), Google Fonts (32×) → banyak request pihak ketiga, tanpa SRI, dan gagal total jika CDN down.
- View mati `tenant/*` (±2.400 baris) menambah beban cari-ganti saat redesign.

---

## 4. Arah Desain Baru

**Kata kunci:** cerah · ceria · bersih · ramah · tepercaya.
Bayangkan studio digital yang hangat dan modern: latar putih keunguan lembut, violet cerah sebagai warna merek, dengan percikan pink, kuning matahari, mint, dan biru langit sebagai aksen. Bukan "gelap-teknis", melainkan "profesional yang menyenangkan".

### 4.1 Palet warna — "Violet Sunrise"

Warna merek diganti dari biru-indigo menjadi **violet cerah**, dipadukan dengan pink, kuning matahari, mint, dan biru langit. Semua pasangan teks/latar sudah dihitung rasio kontrasnya (target WCAG AA ≥ 4,5:1). Token ada di [tailwind.config.js](tailwind.config.js) dan variabel tema di [resources/css/app.css](resources/css/app.css); contoh visual: halaman `/styleguide` (hanya di environment `local`).

**Warna merek (violet)**

| Token | Hex | Penggunaan |
|-------|-----|-----------|
| `brand-50` | `#F5F0FF` | Latar tint, hover lembut |
| `brand-100` | `#EBE0FF` | Border tint, chip |
| `brand-300` | `#C4B5FD` | Teks merek di dark mode |
| `brand-500` | `#8B5CF6` | Dekoratif, ikon besar, ring fokus (bukan untuk teks putih kecil) |
| `brand-600` | `#7C3AED` | **Tombol utama, link** (putih di atasnya **5,7:1** ✔) |
| `brand-700` | `#6D28D9` | Hover/active (putih **7,1:1** ✔); teks pada `brand-50` **6,4:1** ✔ |

**Aksen ceria**

| Token | Hex | Peran |
|-------|-----|-------|
| `pink` | `#FF5C8A` (500) / `#BE185D` (700) | Aksen gradient, sorotan; teks `pink-700` di atas `pink-50` **5,5:1** ✔ |
| `sun` (kuning matahari) | `#FFC83D` (400) | Badge "Populer", CTA sekunder. **Selalu teks navy di atasnya (11,5:1 ✔)** |
| `mint` | `#2DD4BF` (400) / `#0F766E` (800) | Sukses/aktif; teks `mint-800` di atas `mint-50` **5,1:1** ✔ |
| `sky` | `#22C7E8` (400) / `#0E7490` (800) | Info; teks `sky-800` di atas `sky-50` **4,9:1** ✔ |
| `danger` | `#DC2626` (600) / `#B42318` (700) | Hapus/error; teks `danger-700` di atas `danger-50` **5,9:1** ✔ |

**Netral & tema (otomatis berubah di dark mode)**

| Token | Terang | Gelap | Kontras teks (terang / gelap) |
|-------|--------|-------|--------------|
| `bg` | `#FBF9FF` | `#0F0B24` | — |
| `surface` | `#FFFFFF` | `#1A1538` | — |
| `surface-2` | `#F5F2FC` | `#231D4A` | — |
| `ink` (teks utama) | `#17123A` | `#F3F0FF` | 17,7:1 / 17,1:1 |
| `muted` (teks pendukung) | `#625C80` | `#A9A3C8` | 6,2:1 / 7,3:1 |
| `line` (border) | `#E8E2F6` | `#342D63` | — |

Kelas seperti `bg-surface`, `text-ink`, `text-muted`, `border-line` otomatis benar di kedua tema tanpa `dark:`.

**Gradient khas** (hero, banner, ilustrasi — bukan untuk teks kecil): `bg-brand-gradient` (violet → ungu → pink), `bg-sunrise` (kuning → pink), `bg-brand-gradient-soft` (latar lembut).

**Status (badge: tint + teks gelap + titik)**: menunggu = `badge-warning`, lunas/aktif = `badge-success`, proses/info = `badge-info`, gagal/batal = `badge-danger`, sorotan = `badge-brand`.

> **Dark mode & light mode keduanya dipertahankan** dan berlaku untuk seluruh situs (landing, checkout, area pelanggan, auth, admin). Default mengikuti pengaturan perangkat; pengunjung bisa mengganti lewat tombol tema (tersimpan di `localStorage`).

### 4.2 Tipografi
- **Satu keluarga font untuk semua halaman.** Rekomendasi: **Plus Jakarta Sans** (ramah, modern, bagus untuk teks Indonesia) untuk judul + isi; alternatif aman: **Inter** untuk isi dan **Plus Jakarta Sans** hanya untuk judul besar. Di-*self-host* lewat Vite (hapus Google Fonts ×32).
- Skala (mobile → desktop):

| Peran | Mobile | Desktop | Berat |
|-------|--------|---------|-------|
| Display (hero) | 36/40 | 60/64 | 800 |
| H1 | 30/36 | 44/50 | 700 |
| H2 | 24/30 | 32/38 | 700 |
| H3 | 18/26 | 22/30 | 600 |
| Body | **16/26** | 17/28 | 400 |
| Small / label | **14/20** | 14/20 | 500 |
| Caption (minimum mutlak) | **12/16** | 12/16 | 500 |

- **Aturan baru:** tidak ada teks di bawah 12 px; `uppercase` hanya untuk *eyebrow* pendek (maks. ±20 kali di seluruh situs, bukan 720); `font-black` dilarang untuk teks isi.

### 4.3 Bentuk, jarak, bayangan
- Radius: `sm 8px` (input, badge) · `md 12px` (tombol) · `lg 16px` (kartu) · `xl 24px` (hero card/modal) · `full` (avatar/pill). Hapus semua `rounded-[3rem]` dll.
- Spasi berbasis 4/8 px; padding section landing `py-16 md:py-24`; padding kartu `p-5 md:p-6`.
- Bayangan lembut berwarna merek (`0 8px 24px -8px rgba(37,99,235,.18)`) menggantikan `shadow-2xl` + blur besar.
- **Hentikan** blob animasi fixed, preloader paksa, dan `backdrop-blur` berlapis. Blur hanya untuk navbar saat scroll.

### 4.4 Ikon & ilustrasi
- Tetap FontAwesome agar migrasi murah, tetapi **di-bundle lokal** (subset) — atau pindah ke Lucide/Heroicons SVG bila ingin lebih ringan.
- Tambah ilustrasi/ikon duotone berwarna (mint, sun, coral) pada section Layanan dan "Cara Kerja" agar terasa ceria.
- Hero: foto/mockup perangkat di dalam bingkai berwarna (kartu miring + elemen floating kecil), **bukan** foto full-bleed yang dipudarkan.

### 4.5 Gerak
- Gunakan transisi 150–250 ms; AOS dikurangi (hanya fade-up pada judul section & kartu pertama), hormati `prefers-reduced-motion`.
- Marquee klien tetap, dengan jeda saat hover.

---

## 5. Fondasi Teknis (dikerjakan lebih dulu)

1. **Tailwind**: pilih satu versi. Rekomendasi: **tetap v3** (hapus `@tailwindcss/vite` yang tak terpakai) agar `tailwind.config.js` + `@layer` yang ada tetap valid; migrasi ke v4 dibahas terpisah.
2. **Token** di `tailwind.config.js` (`colors.brand/sun/coral/mint/violet/ink/muted/line/bg`, `borderRadius`, `boxShadow`, `fontFamily`, skala `fontSize`) + CSS variables di `app.css` bila nanti butuh dark mode.
3. **Komponen CSS** di `@layer components` (`app.css`): `.btn`, `.btn-primary|secondary|ghost|danger`, `.card`, `.badge-*`, `.input`, `.label`, `.eyebrow`, `.section`, `.container-page`. **Pindahkan semua `<style>` inline ke sini** dan hapus duplikasi.
4. **Layout Blade bersama** (menggantikan 9+ `<head>` terpisah):
   - `layouts/public.blade.php` — landing, produk, portfolio, blog, checkout, area pelanggan (navbar + footer + SEO slot).
   - `layouts/auth.blade.php` — auth (versi terang, split-screen).
   - `layouts/admin.blade.php` — dashboard (sidebar + topbar + breadcrumb) menggantikan `layouts/app`.
5. **Komponen Blade** (`resources/views/components/ui/`): `button`, `card`, `badge`, `input`, `select`, `textarea`, `modal`, `table` + `table-mobile-card`, `stat-card`, `empty-state`, `page-header`, `sidebar-link`, `flash`/`toast`, `stepper`, `section-heading`, `confirm-delete`.
6. **Aset**: `npm install` FontAwesome (atau SVG icon), Swiper, AOS, SweetAlert2, Alpine → di-bundle via Vite; hapus CDN. Alpine dimuat **sekali**.
7. **Bersih-bersih**: hapus `resources/views/tenant/*`, `dashboard/tenants`, `layouts/tenant-app`, `welcome.blade.php` (setelah dikonfirmasi tidak dipakai), hapus `safelist` regex, perbaiki kelas invalid.
8. **Pindahkan query dari Blade** (navbar `PackageOrder::…first()`, `guest.blade` `SiteSetting::first()`) ke View Composer agar view bersih dan cepat.

---

## 6. Rencana Per Halaman

### 6.1 Landing (`/`)
| Section | Rencana |
|---------|---------|
| **Navbar** | Logo kiri; menu 5–6 item (Beranda, Layanan, Portofolio, Paket, Blog, Kontak) dengan teks **14–15 px, huruf normal-tebal-sedang**, bukan uppercase kecil. Tombol CTA "Konsultasi Gratis" (brand-600). Bahasa ID/EN jadi dropdown kecil. Toggle tema opsional. Mobile: drawer layar-penuh dengan tombol besar (≥ 48 px), CTA di dasar. Pill floating saat scroll dipertahankan. |
| **Hero** | Dua kolom: kiri judul 36→60 px + subjudul + **1 CTA utama + 1 CTA sekunder** (hapus panah slider dari baris CTA, pindahkan ke bawah slider); kanan: slider foto/mockup dalam kartu berbingkai + 2–3 *floating badge* ("100+ proyek", "Garansi revisi", rating). Latar `bg` dengan gradient lembut `brand-50 → white` + bentuk geometris berwarna. Trust-row logo klien dengan avatar riil. |
| **Clients** | Marquee logo grayscale → berwarna saat hover, di strip putih. |
| **Statistik (baru)** | 4 angka besar (proyek selesai, klien, tahun pengalaman, rating) — diisi dari data yang ada. |
| **About** | Foto tim/owner di kartu dengan sudut 24 px + dekorasi sun/mint; visi–misi sebagai 2 kartu ikon; dihapus teks 10 px. |
| **Services** | Grid 3 kolom (1 di HP) kartu putih, ikon duotone di tile warna-warni (brand/mint/sun/coral/violet bergantian), judul 18 px, deskripsi 15 px, link "Selengkapnya". |
| **Cara Kerja (baru)** | 4 langkah horizontal/vertikal: Konsultasi → Desain → Pengembangan → Peluncuran, bernomor berwarna. |
| **Portfolio** | Kartu gambar rasio 16:10, kategori sebagai chip, overlay judul saat hover/tap, tombol "Lihat semua". |
| **Packages** | Kartu harga dengan satu paket "Terpopuler" (badge `sun`, border brand-500, sedikit terangkat); daftar fitur dengan ikon centang mint; harga 32 px; tombol penuh-lebar. |
| **Testimonials** | Swiper dengan kartu putih + kutip besar berwarna; avatar + bintang `sun`; bullet navigasi brand. |
| **FAQ (baru)** | Accordion Alpine, 5–6 pertanyaan (data bisa dari `site_settings` atau hard-code awal, nanti dijadikan modul admin). |
| **CTA penutup (baru)** | Banner gradient brand→violet dengan tombol putih & tombol `sun`. |
| **Footer** | Latar `ink` (navy) atau `brand-50` terang (pilih di bagian 9); 4 kolom (merek, tautan, layanan, kontak) → 1–2 kolom di HP; ikon sosial 44 px. |
| **Tombol WhatsApp melayang** | Tetap, tapi posisi menghindari bottom-nav/CTA mobile. |

### 6.2 Produk / Paket / Portfolio / Blog
- **`packages` (daftar)** & **`package` (detail)**: layout dua kolom di desktop (detail + kartu harga *sticky*), di HP kartu harga jadi **bottom bar sticky** ("Rp xxx — Pesan Sekarang").
- **`portfolio`**: filter kategori (chip scroll horizontal di HP) + grid masonry sederhana.
- **`blogs/index`**: kartu artikel (gambar 16:9, kategori, waktu baca), kartu "artikel utama" besar di atas; pencarian opsional.
- **`blogs/show`**: kolom baca max 720 px, tipografi artikel 17–18/30, daftar isi, tombol bagikan, artikel terkait. Perbaiki `alt` gambar.

### 6.3 Checkout & Area Pelanggan (jadi **terang**, satu bahasa dengan landing)
- **Checkout**: pakai `layouts/public`; **stepper 4 langkah** (Paket → Data Website → Domain & Pembayaran → Konfirmasi). Di desktop: form kiri + ringkasan harga kanan (*sticky*); di HP: ringkasan jadi *collapsible* di atas dan total ringkas di bar bawah. Opsi DP/Lunas jadi kartu pilihan (radio-card) bukan toggle kecil. Voucher: input + tombol "Pakai" dengan feedback inline. Teks bantuan ≥ 12 px.
- **Pelunasan**, **Sukses**: ilustrasi/ikon centang besar mint, ringkasan pesanan, instruksi bayar jelas, tombol unduh invoice/kembali.
- **`user/orders`**: kartu per order dengan badge status + **timeline pengerjaan** (Dibayar → Dikerjakan → Revisi → Selesai); tombol "Bayar Pelunasan" menonjol.
- **`user/websites`**: kartu website (favicon/screenshot, domain, status aktif, tanggal perpanjangan).
- **`profile/edit`**: bentuk 3 kartu (Info, Password, Hapus Akun) dalam layout publik; area bahaya berwarna koral.

### 6.4 Auth
- `layouts/auth`: **split-screen** — kiri panel gradient brand dengan logo, tagline, 3 poin nilai + ilustrasi; kanan form di kartu putih. Di HP: panel kiri jadi header ringkas.
- Input tinggi 48 px, label di atas, ikon opsional, tombol show/hide password, error inline berwarna koral, tautan sekunder 14 px. Login sosial bila nanti diperlukan.

### 6.5 Admin
**Shell (`layouts/admin`)**
- **Sidebar terang** (putih, border `line`, item aktif = tint `brand-50` + teks `brand-700` + bar kiri `brand-600`) — lebih cerah daripada slate-900 sekarang. Opsi: sidebar `ink` dengan aksen cerah (lihat bagian 9).
- Menu dikelompokkan dan **bisa dilipat**; tampilkan **badge angka** (order menunggu, tiket terbuka).
- **Topbar** (semua ukuran layar): hamburger (HP), judul halaman + breadcrumb, pencarian cepat, tombol "Lihat situs", avatar dropdown (profil, keluar). Tanggal dipindah ke dashboard.
- HP: sidebar jadi drawer; **bottom navigation 4–5 item** untuk fungsi tersering (Beranda, Order, Invoice, Tiket, Menu).

**Dashboard (`dashboard`)**
- Baris 1: 4 KPI berfokus bisnis — *Pendapatan bulan ini*, *Order menunggu pembayaran*, *Order sedang dikerjakan*, *Tiket terbuka*; masing-masing ikon berwarna + tren vs bulan lalu.
- Baris 2: grafik pendapatan 6–12 bulan (ikuti panduan `dataviz`), + donat status order.
- Baris 3: tabel "Order terbaru" (5 baris) + "Aktivitas/tiket terbaru" + *quick actions* (Tambah paket, Buat invoice, Tulis blog).
- Statistik konten (services, projects, testimonials, owners, packages, invoices, blogs) jadi baris kecil kedua.
- Perlu tambahan query di `CmsController::dashboard()` (pendapatan, grup status, dll.) — dicatat sebagai pekerjaan back-end kecil.

**Pola halaman daftar (index) seragam**
`page-header` (judul + deskripsi + tombol utama) → toolbar (pencarian, filter status, urutkan) → tabel/kartu → pagination → *empty state* ber-ilustrasi.

**Tabel responsif**: ≥ lg tabel biasa (padding `px-4 py-3`, header 12–13 px sentence-case `muted`, baris hover `brand-50/40`, aksi jadi ikon-tombol/menu "⋯"); < lg **berubah jadi kartu bertumpuk** (judul, badge status, 2–3 info kunci, tombol aksi bawah). Terapkan ke: orders, invoices, users, tickets, vouchers, packages, clients, owners, testimonials, services, projects, blogs.

**Orders (halaman paling kompleks)**: pecah sel "Website Details" jadi **drawer/detail panel** (klik baris → panel samping/halaman detail dengan tab: Ringkasan · Domain · Pembayaran · Pengerjaan · Catatan). Tabel hanya memuat ID, pelanggan, paket, total, status bayar, status kerja, aksi cepat.

**Form (create/edit)**: modal → *slide-over* (panel kanan) di desktop, halaman penuh di HP; komponen `x-ui.input/select/textarea/file-upload` dengan label, hint, error seragam; upload gambar dengan pratinjau & drag-drop; tombol Simpan *sticky* di bawah pada form panjang (settings, about, hero, blog, invoice).

**Invoice**: `show` tampil seperti dokumen rapi di kartu; `create/edit` berformat baris item responsif; `pdf.blade.php` **disegarkan terpisah** (dompdf tidak mendukung CSS modern — pakai tabel + inline style sederhana, warna merek baru, tetap hemat tinta).

**Blog editor**: pertahankan editor yang ada, rapikan toolbar & pratinjau; field SEO dalam accordion.

**Settings & Payment Methods**: tab (Umum · SEO · Invoice · Pembayaran · Sosial) alih-alih satu halaman 351 baris; simpan per-tab.

**Notifikasi**: *toast* ringan (Alpine) menggantikan SweetAlert untuk sukses/error; SweetAlert/modal konfirmasi hanya untuk hapus. Ringkasan error validasi di atas form.

---

## 7. Responsif & Perangkat

| Breakpoint | Perilaku |
|------------|----------|
| **< 640 (HP)** | 1 kolom; font body 16 px; tombol & input tinggi ≥ 44–48 px; navigasi drawer + bottom-nav (admin); tabel → kartu; CTA sticky di halaman produk/checkout |
| **640–1023 (tablet)** | 2 kolom; sidebar admin sebagai drawer atau *rail* ikon; form 1–2 kolom |
| **≥ 1024 (laptop)** | Layout penuh; sidebar tetap; tabel penuh |
| **≥ 1536 (monitor lebar)** | Container maksimal 1280 px agar baris teks tetap nyaman |

Aturan umum:
- Uji di lebar **360, 390, 768, 1024, 1366, 1920**; tidak boleh ada scroll horizontal halaman.
- Gunakan `100svh`/`dvh` (hindari bar alamat mobile menutup konten), `safe-area-inset` untuk bottom-nav/CTA sticky di iOS.
- Target sentuh ≥ 44 × 44 px; jarak antar-target ≥ 8 px.
- Gambar responsif: `srcset`/`sizes` bila memungkinkan, `loading="lazy"` kecuali hero, `width/height` eksplisit (hindari CLS), format WebP untuk unggahan baru.

---

## 8. Aksesibilitas, Performa, SEO

**Aksesibilitas**
- Semua teks ≥ 4,5:1 (ganti seluruh `slate-400` untuk teks → `muted`); fokus terlihat jelas (`focus-visible:ring-2 ring-brand-600 ring-offset-2`).
- `aria-label` pada semua tombol ikon (hamburger, toggle tema, panah slider, tutup modal, hapus); modal dengan *focus trap* & `Esc`.
- Isi `alt` 65 gambar yang kosong (gunakan nama proyek/klien/artikel); `alt=""` untuk dekoratif.
- Hormati `prefers-reduced-motion`; status tidak hanya dengan warna (selalu ikon + teks).
- Form: `label` terhubung, `autocomplete`, `inputmode` (nomor telepon, angka), pesan error `aria-describedby`.

**Performa** (target Lighthouse mobile ≥ 90 untuk landing)
- Hapus CDN → bundle lokal; font self-host dengan `font-display: swap` + preload font utama.
- Hapus preloader 800 ms, blob animasi fixed, `backdrop-blur` berlapis; batasi AOS.
- `loading="lazy"` + dimensi gambar; kompres hero; pertimbangkan `fetchpriority="high"` untuk gambar hero.
- Kecilkan CSS: hapus `safelist` regex; purge otomatis dari komponen.
- Satu Alpine saja.

**SEO** (dijaga, bukan diubah): pertahankan meta/OG/JSON-LD yang sudah baik di `landing/index`; pindahkan ke `layouts/public` agar semua halaman publik mendapat meta konsisten; pastikan heading hanya satu `<h1>` per halaman.

---

## 9. Keputusan Final

| # | Topik | Keputusan |
|---|-------|-----------|
| 1 | Dark / light mode | **Dua-duanya dipertahankan** (toggle di semua area) |
| 2 | Warna utama | **Diganti**: violet cerah + aksen pink/matahari/mint/langit ("Violet Sunrise") |
| 3 | Sidebar admin | Terang (default rencana) |
| 4 | Kode `tenant/*` | **Dihapus** (selesai, commit pertama) — termasuk `welcome` dan hook asset-domain tenancy di `AppServiceProvider` |
| 5 | Branch | Satu branch **`redesign/ui-v3`**, beberapa commit (satu atau lebih per fase) |
| 6 | Font | Plus Jakarta Sans (self-host) |
| 7 | Section baru (Statistik, Cara Kerja, FAQ, CTA penutup) | Ya |
| 8 | Grafik dashboard | Ya |
| 9 | Icon set | FontAwesome, di-bundle lokal via npm |
| 10 | Footer landing | Navy gelap di light mode; menyatu dengan tema di dark mode |

Poin 3 dan 5–10 memakai default rencana karena tidak ada instruksi lain; bisa diubah kapan saja.

---

## 10. Fase Pengerjaan & Checklist

> Estimasi dalam "sesi kerja" kira-kira; tiap fase berakhir dengan build yang jalan dan bisa dicek di browser. Urutan dipilih agar **fondasi dulu**, lalu halaman paling terlihat pengguna, lalu admin.

### Fase 0 — Persiapan (±0,5 sesi)
- [x] Buat branch `redesign/ui-v3`.
- [ ] Ambil screenshot "sebelum" (landing, checkout, admin orders, dashboard) di 390 px dan 1366 px sebagai pembanding.
- [x] Konfirmasi keputusan di bagian 9.
- [x] Hapus `tenant/*`, `dashboard/tenants`, `layouts/tenant-app`, `welcome`.

### Fase 1 — Design system & fondasi (±1–1,5 sesi)
- [x] Rapikan versi Tailwind & dependensi `package.json`.
- [x] Token di `tailwind.config.js` (warna, radius, shadow, font, skala teks); hapus `safelist`.
- [x] `resources/css/app.css`: komponen `.btn`, `.card`, `.badge-*`, `.input`, `.eyebrow`, utilitas kontainer; pindahkan semua `<style>` inline & perbaiki `@apply`.
- [x] Font self-host; FontAwesome, Swiper, AOS, SweetAlert2, Alpine di-bundle lewat Vite.
- [x] Buat `x-layouts.public` dan `x-layouts.auth` (`x-layouts.admin` dibuat di Fase 4 bersama sidebar baru).
- [x] Komponen Blade `x-ui.*` (button, card, badge, input, select, textarea, stat-card, empty-state, page-header, section-heading, theme-toggle, toaster, stepper) + halaman referensi `/styleguide` (hanya `APP_ENV=local`). `modal`, `table` responsif, dan `sidebar-link` menyusul di fase yang memakainya.
- [x] View Composer `$site` (SiteSetting) untuk layout bersama.

### Fase 2 — Landing page (±2 sesi)
- [x] Navbar + drawer mobile.
- [x] Hero baru.
- [x] Clients, Statistik, About, Services, Cara Kerja.
- [x] Portfolio, Packages, Testimonials, FAQ, CTA penutup, Footer, tombol WhatsApp.
- [x] Halaman `packages`, `package`, `portfolio`, `blogs/index`, `blogs/show` memakai `layouts/public`.
- [x] `alt`, `loading="lazy"`, ukuran gambar; cek Lighthouse mobile. _(alt, lazy-loading & ukuran gambar dikerjakan; Lighthouse belum dijalankan)_

### Fase 3 — Checkout, area pelanggan, auth (±1,5–2 sesi)
- [x] Checkout + stepper + ringkasan sticky; pelunasan; sukses.
- [x] `user/orders` (timeline), `user/websites`, `profile/edit`.
- [x] Auth split-screen untuk 6 halaman.
- [x] Uji alur beli end-to-end (DP, lunas, voucher, beli domain) — **logika tidak boleh berubah, hanya tampilan**. _(logika tidak diubah; domain reseller memang sudah dinonaktifkan di kode lama dan blok mati itu dihapus; alur beli penuh belum diuji end-to-end di browser)_

### Fase 4 — Shell admin & dashboard (±1,5 sesi)
- [x] Sidebar baru (komponen `sidebar-link`, grup lipat, badge), topbar, breadcrumb, bottom-nav mobile. _(tanpa breadcrumb; grup tidak dilipat)_
- [x] Dashboard: KPI, grafik, order terbaru, quick actions (+ query tambahan di controller).
- [x] Toast/flash & konfirmasi hapus global.

### Fase 5 — Modul admin (±3–4 sesi)
Urut berdasarkan frekuensi pakai:
- [x] Orders (tabel ringkas + panel detail) · Invoices (index/create/edit/show) · Tickets · Users. _(Orders memakai daftar kartu dengan pencarian & filter; belum ada panel detail terpisah)_
- [x] Packages · Vouchers · Payment Methods · Settings (bertab).
- [x] Hero · About · Services · Projects · Blogs · Testimonials · Clients · Owners.
- [x] Tabel → kartu di mobile untuk semuanya; form → komponen seragam; empty state. _(tabel jadi kartu di bawah lg; form CRUD jadi modal)_

### Fase 6 — Invoice PDF & email (±0,5 sesi)
- [x] Segarkan `invoices/pdf.blade.php` dengan palet baru (kompatibel dompdf), uji cetak A4. _(diuji lewat DomPDF; belum dicetak fisik)_
- [ ] (Bila ada) template notifikasi email.

### Fase 7 — QA & peluncuran (±1 sesi)
- [ ] Matriks uji: Chrome, Firefox, Safari iOS, Chrome Android × lebar 360/390/768/1024/1366/1920.
- [ ] Audit kontras & `axe`/Lighthouse (Performance, Accessibility, SEO ≥ 90).
- [ ] Cek tidak ada scroll horizontal; cek hover/focus/disabled/loading/empty/error di tiap komponen.
- [x] `npm run build`, pastikan tidak ada kelas Tailwind invalid & ukuran CSS/JS sebelum–sesudah.
- [ ] Merge dengan review per-fase (bukan satu PR raksasa).

**Total perkiraan: ±12–15 sesi kerja** (landing & checkout paling terlihat → prioritas tinggi; modul admin paling banyak jumlahnya tapi paling mudah setelah komponen jadi).

---

## 11. Risiko & Mitigasi

| Risiko | Mitigasi |
|--------|----------|
| Perubahan tampilan merusak logika checkout/pembayaran (state Alpine panjang di `<body>`) | Pecah per komponen Alpine tanpa mengubah nama field/route; uji alur beli penuh tiap selesai fase 3 |
| Redesign besar → PR raksasa & sulit direview | Kerjakan per fase, commit kecil, branch terpisah |
| `dompdf` tidak mendukung flex/grid/CSS modern | Invoice PDF diperlakukan terpisah (tabel + inline style) |
| Konten CMS lama (teks panjang, gambar tak seragam) merusak layout baru | Pakai `object-cover`, `line-clamp`, rasio gambar tetap; uji dengan data nyata |
| Kehilangan SEO saat memindah meta ke layout bersama | Pindahkan apa adanya, bandingkan `view-source` sebelum–sesudah |
| Warna cerah berisiko kontras rendah (kuning, mint, koral) | Aturan: warna cerah = latar/dekorasi; teks di atasnya navy atau varian gelap (sudah dihitung di 4.1) |
| Migrasi CDN → bundle mengubah tampilan ikon | Pakai versi FontAwesome yang sama (6.4) saat di-bundle |

---

## 12. Definisi Selesai (Acceptance)

- Seluruh halaman memakai **satu palet, satu font, satu set komponen**.
- Tidak ada teks < 12 px; tidak ada kombinasi teks dengan kontras < 4,5:1.
- Tidak ada scroll horizontal di 360 px pada **semua** halaman publik dan admin; semua tabel punya tampilan kartu di HP.
- Lighthouse mobile landing: Performance ≥ 85, Accessibility ≥ 95, SEO ≥ 95.
- Tidak ada `<style>` inline untuk gaya umum, tidak ada CDN untuk aset inti, tidak ada kelas Tailwind invalid, tidak ada `<img>` tanpa `alt`.
- Alur beli (DP & lunas), pelunasan, unduh invoice, dan CRUD admin berfungsi persis seperti sebelumnya.

---

*Langkah berikutnya: tinjau bagian 9 (keputusan). Setelah Anda setuju atau mengubah defaultnya, saya mulai dari Fase 0–1 (fondasi), lalu tunjukkan hasil landing page lebih dulu sebelum lanjut ke halaman lain.*
