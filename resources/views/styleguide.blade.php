<x-layouts.public :noindex="true" title="Styleguide" description="Komponen dan token desain FKStudio">
    <x-slot:navbar>
        <header class="sticky top-0 z-40 border-b border-line bg-bg/85 backdrop-blur">
            <div class="container-page flex h-16 items-center justify-between">
                <span class="font-extrabold">Styleguide <span class="gradient-text">Violet Sunrise</span></span>
                <x-ui.theme-toggle />
            </div>
        </header>
    </x-slot:navbar>

    <div class="container-page space-y-16 py-12">

        {{-- Colors --}}
        <section>
            <x-ui.section-heading align="left" eyebrow="Token" title="Warna" lead="Teks gelap di atas warna cerah (kuning, mint); teks putih hanya di atas brand-600 ke atas." />
            <div class="mt-8 grid gap-6">
                @foreach ([
                    'brand' => ['bg-brand-50', 'bg-brand-100', 'bg-brand-300', 'bg-brand-400', 'bg-brand-500', 'bg-brand-600', 'bg-brand-700', 'bg-brand-800'],
                    'pink' => ['bg-pink-50', 'bg-pink-100', 'bg-pink-300', 'bg-pink-400', 'bg-pink-500', 'bg-pink-600', 'bg-pink-700'],
                    'sun' => ['bg-sun-50', 'bg-sun-100', 'bg-sun-300', 'bg-sun-400', 'bg-sun-500', 'bg-sun-700'],
                    'mint' => ['bg-mint-50', 'bg-mint-100', 'bg-mint-300', 'bg-mint-400', 'bg-mint-500', 'bg-mint-800'],
                    'sky' => ['bg-sky-50', 'bg-sky-100', 'bg-sky-300', 'bg-sky-400', 'bg-sky-500', 'bg-sky-800'],
                ] as $name => $swatches)
                    <div>
                        <p class="mb-2 text-small font-semibold capitalize">{{ $name }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($swatches as $sw)
                                <div class="w-20">
                                    <div class="h-12 rounded-md border border-line {{ $sw }}"></div>
                                    <p class="mt-1 text-caption text-muted">{{ str_replace('bg-', '', $sw) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                <div>
                    <p class="mb-2 text-small font-semibold">Semantik (berubah di dark mode)</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach (['bg-bg' => 'bg', 'bg-surface' => 'surface', 'bg-surface-2' => 'surface-2', 'bg-brand-soft' => 'brand-soft', 'bg-line' => 'line', 'bg-muted' => 'muted', 'bg-ink' => 'ink'] as $cls => $label)
                            <div class="w-20">
                                <div class="h-12 rounded-md border border-line {{ $cls }}"></div>
                                <p class="mt-1 text-caption text-muted">{{ $label }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="h-20 rounded-lg bg-brand-gradient"></div>
                    <div class="h-20 rounded-lg bg-sunrise"></div>
                    <div class="h-20 rounded-lg bg-brand-gradient-soft"></div>
                </div>
            </div>
        </section>

        {{-- Typography --}}
        <section>
            <x-ui.section-heading align="left" eyebrow="Token" title="Tipografi" lead="Hanken Grotesk. Teks terkecil 12 px." />
            <div class="mt-8 space-y-4">
                <p class="text-4xl font-extrabold md:text-6xl">Display <span class="gradient-text">Gradient</span></p>
                <h1 class="text-3xl md:text-5xl">Heading 1 — Website profesional</h1>
                <h2 class="text-2xl md:text-3xl">Heading 2 — Layanan kami</h2>
                <h3 class="text-xl">Heading 3 — Paket Bisnis</h3>
                <p>Body 16 px — Kami menghadirkan website, e-commerce, dan aplikasi yang membantu bisnis Anda tumbuh.</p>
                <p class="text-small text-muted">Small 14 px muted — teks pendukung dan keterangan.</p>
                <p class="text-caption text-muted">Caption 12 px — batas terkecil yang diizinkan.</p>
            </div>
        </section>

        {{-- Buttons --}}
        <section>
            <x-ui.section-heading align="left" eyebrow="Komponen" title="Tombol" />
            <div class="mt-8 space-y-4">
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.button>Primary</x-ui.button>
                    <x-ui.button variant="secondary">Secondary</x-ui.button>
                    <x-ui.button variant="ghost">Ghost</x-ui.button>
                    <x-ui.button variant="sun">Sun</x-ui.button>
                    <x-ui.button variant="danger">Danger</x-ui.button>
                    <x-ui.button variant="danger-soft">Danger soft</x-ui.button>
                    <x-ui.button disabled>Disabled</x-ui.button>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.button size="sm" icon="fa-plus">Small</x-ui.button>
                    <x-ui.button icon="fa-arrow-right" :icon-right="true">Medium</x-ui.button>
                    <x-ui.button size="lg" href="#" icon="fa-rocket">Large link</x-ui.button>
                    <button class="btn-icon border border-line bg-surface" aria-label="Pengaturan"><i class="fa-solid fa-gear"></i></button>
                </div>
            </div>
        </section>

        {{-- Badges --}}
        <section>
            <x-ui.section-heading align="left" eyebrow="Komponen" title="Badge status" />
            <div class="mt-8 flex flex-wrap gap-3">
                <x-ui.badge variant="warning" :dot="true">Menunggu pembayaran</x-ui.badge>
                <x-ui.badge variant="info" :dot="true">Dalam pengerjaan</x-ui.badge>
                <x-ui.badge variant="success" :dot="true">Lunas</x-ui.badge>
                <x-ui.badge variant="danger" :dot="true">Dibatalkan</x-ui.badge>
                <x-ui.badge variant="brand">Terpopuler</x-ui.badge>
                <x-ui.badge>Draft</x-ui.badge>
            </div>
        </section>

        {{-- Forms --}}
        <section>
            <x-ui.section-heading align="left" eyebrow="Komponen" title="Form" />
            <form class="mt-8 grid max-w-3xl gap-5 sm:grid-cols-2" onsubmit="return false">
                <x-ui.input name="sg_name" label="Nama lengkap" placeholder="Nama Anda" :required="true" />
                <x-ui.input name="sg_email" type="email" label="Email" hint="Kami tidak akan membagikan email Anda." />
                <x-ui.select name="sg_type" label="Jenis bisnis">
                    <option>Toko online</option>
                    <option>Company profile</option>
                    <option>Sekolah</option>
                </x-ui.select>
                <x-ui.input name="sg_phone" type="tel" inputmode="tel" label="WhatsApp" placeholder="08xxxxxxxxxx" />
                <div class="sm:col-span-2">
                    <x-ui.textarea name="sg_note" label="Catatan" hint="Ceritakan kebutuhan website Anda." />
                </div>
                <div class="flex gap-3 sm:col-span-2">
                    <x-ui.button type="submit">Kirim</x-ui.button>
                    <x-ui.button variant="secondary">Batal</x-ui.button>
                </div>
            </form>
        </section>

        {{-- Cards --}}
        <section>
            <x-ui.section-heading align="left" eyebrow="Komponen" title="Kartu & statistik" />
            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-ui.stat-card label="Pendapatan bulan ini" value="Rp 24,5 jt" icon="fa-wallet" tone="brand" trend="12% vs bulan lalu" />
                <x-ui.stat-card label="Menunggu bayar" value="7" icon="fa-hourglass-half" tone="sun" />
                <x-ui.stat-card label="Dalam pengerjaan" value="12" icon="fa-code" tone="sky" trend="3 order baru" />
                <x-ui.stat-card label="Tiket terbuka" value="2" icon="fa-headset" tone="pink" trend="1 lebih sedikit" :trend-up="false" />
            </div>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <x-ui.card hover>
                    <span class="flex h-12 w-12 items-center justify-center rounded-md bg-brand-50 text-xl text-brand-700 dark:bg-brand-500/20 dark:text-brand-300"><i class="fa-solid fa-laptop-code"></i></span>
                    <h3 class="mt-4 text-lg">Website Company Profile</h3>
                    <p class="mt-1 text-small text-muted">Tampil profesional dengan desain responsif di semua perangkat.</p>
                </x-ui.card>
                <x-ui.card hover>
                    <span class="flex h-12 w-12 items-center justify-center rounded-md bg-sun-50 text-xl text-sun-700 dark:bg-sun-400/15 dark:text-sun-300"><i class="fa-solid fa-cart-shopping"></i></span>
                    <h3 class="mt-4 text-lg">Toko Online</h3>
                    <p class="mt-1 text-small text-muted">Katalog, keranjang, dan pembayaran terintegrasi.</p>
                </x-ui.card>
                <x-ui.card>
                    <x-ui.empty-state icon="fa-folder-open" title="Belum ada data" text="Data akan muncul di sini setelah ditambahkan.">
                        <x-ui.button size="sm" icon="fa-plus">Tambah</x-ui.button>
                    </x-ui.empty-state>
                </x-ui.card>
            </div>
        </section>

        {{-- Table --}}
        <section>
            <x-ui.section-heading align="left" eyebrow="Komponen" title="Tabel" />
            <div class="table-wrap mt-8">
                <table class="table-base">
                    <thead><tr><th>Order</th><th>Pelanggan</th><th>Paket</th><th>Status</th><th class="text-right">Total</th></tr></thead>
                    <tbody>
                        <tr><td class="font-mono text-caption text-muted">#A1B2C3D4</td><td class="font-semibold">Budi Santoso</td><td>Bisnis</td><td><x-ui.badge variant="success" :dot="true">Lunas</x-ui.badge></td><td class="text-right font-semibold">Rp 3.500.000</td></tr>
                        <tr><td class="font-mono text-caption text-muted">#E5F6G7H8</td><td class="font-semibold">Siti Aminah</td><td>Starter</td><td><x-ui.badge variant="warning" :dot="true">Menunggu</x-ui.badge></td><td class="text-right font-semibold">Rp 1.200.000</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Stepper & toast --}}
        <section>
            <x-ui.section-heading align="left" eyebrow="Komponen" title="Stepper & notifikasi" />
            <div class="mt-8 space-y-6">
                <x-ui.stepper :steps="['Paket', 'Data website', 'Pembayaran', 'Selesai']" :current="2" />
                <div class="flex flex-wrap gap-3">
                    <x-ui.button variant="secondary" onclick="toast('success', 'Perubahan berhasil disimpan.')">Toast sukses</x-ui.button>
                    <x-ui.button variant="secondary" onclick="toast('error', 'Terjadi kesalahan, coba lagi.')">Toast error</x-ui.button>
                    <x-ui.button variant="secondary" onclick="toast('info', 'Order baru masuk.')">Toast info</x-ui.button>
                </div>
                <form data-confirm="Item ini akan dihapus permanen." data-confirm-title="Hapus item?" data-confirm-ok="Ya, hapus" onsubmit="event.preventDefault(); toast('success','Konfirmasi diterima');">
                    <x-ui.button type="submit" variant="danger-soft" icon="fa-trash">Uji konfirmasi hapus</x-ui.button>
                </form>
            </div>
        </section>
    </div>
</x-layouts.public>
