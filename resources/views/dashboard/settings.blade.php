<x-app-layout>
    <x-slot name="header">Pengaturan Situs</x-slot>

    @php
        $socials = $settings->social_links ?? [];

        // Tab yang memuat error validasi dibuka lebih dulu.
        $initialTab = 'umum';
        if ($errors->has('tax_rate')) {
            $initialTab = 'invoice';
        }
        if ($errors->has('site_name')) {
            $initialTab = 'umum';
        }

        $tabs = [
            'umum' => ['Umum', 'fa-sliders'],
            'seo' => ['SEO', 'fa-magnifying-glass-chart'],
            'kontak' => ['Kontak & Sosial', 'fa-address-book'],
            'invoice' => ['Invoice & Pajak', 'fa-file-invoice-dollar'],
        ];

        $uploads = [
            'site_logo' => ['label' => 'Logo situs', 'hint' => 'PNG/SVG disarankan', 'icon' => 'fa-image', 'url' => $settings->site_logo ? $settings->logo_url : null, 'pad' => 'p-1'],
            'site_favicon' => ['label' => 'Favicon', 'hint' => 'ICO/PNG (32x32)', 'icon' => 'fa-icons', 'url' => $settings->site_favicon ? $settings->favicon_url : null, 'pad' => 'p-3'],
            'og_image' => ['label' => 'Gambar OG', 'hint' => 'Media sosial (1200x630)', 'icon' => 'fa-share-nodes', 'url' => $settings->og_image ? $settings->og_image_url : null, 'pad' => 'p-1'],
            'invoice_logo' => ['label' => 'Logo invoice', 'hint' => 'Untuk kop surat', 'icon' => 'fa-file-image', 'url' => $settings->invoice_logo ? $settings->invoice_logo_url : null, 'pad' => 'p-1'],
            'invoice_signature' => ['label' => 'Tanda tangan', 'hint' => 'Gambar tanda tangan', 'icon' => 'fa-signature', 'url' => $settings->invoice_signature ? $settings->invoice_signature_url : null, 'pad' => 'p-1'],
        ];
    @endphp

    <form action="{{ route('dashboard.settings.update') }}" method="POST" enctype="multipart/form-data"
        x-data="{ tab: '{{ $initialTab }}' }">
        @csrf

        <div class="mx-auto max-w-5xl space-y-6">
            <x-ui.page-header title="Pengaturan Situs" description="Atur identitas, SEO, kontak, dan invoice. Semua tab disimpan sekaligus." />

            @if ($errors->any())
                <div class="rounded-lg border border-danger-500 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-danger-100" role="alert">
                    <p class="font-semibold"><i class="fa-solid fa-circle-exclamation mr-1" aria-hidden="true"></i> Periksa kembali isian berikut:</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Tabs --}}
            <div class="custom-scrollbar -mx-4 overflow-x-auto px-4 lg:mx-0 lg:px-0">
                <div role="tablist" aria-label="Kelompok pengaturan" class="flex w-max min-w-full gap-1 rounded-lg border border-line bg-surface p-1">
                    @foreach ($tabs as $key => [$tabLabel, $tabIcon])
                        <button type="button" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}"
                            @click="tab = '{{ $key }}'" :aria-selected="tab === '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'bg-brand-soft text-brand-text' : 'text-muted hover:bg-surface-2'"
                            class="inline-flex min-h-[44px] flex-1 items-center justify-center gap-2 whitespace-nowrap rounded-md px-4 text-small font-semibold transition">
                            <i class="fa-solid {{ $tabIcon }}" aria-hidden="true"></i> {{ $tabLabel }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Tab: Umum --}}
            <div id="panel-umum" role="tabpanel" aria-labelledby="tab-umum" x-show="tab === 'umum'" @if ($initialTab !== 'umum') x-cloak @endif class="space-y-6">
                <x-ui.card>
                    <h2 class="text-xl font-bold">Identitas situs</h2>
                    <p class="mt-1 text-small text-muted">Nama dan logo yang tampil di situs publik.</p>

                    <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <x-ui.input name="site_name" label="Nama situs" :value="$settings->site_name" required />
                        </div>
                        @foreach (collect($uploads)->only(['site_logo', 'site_favicon']) as $uName => $u)
                            <div x-data="{ preview: null }" class="flex items-center gap-4 rounded-lg border border-line bg-surface-2 p-4">
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-md border border-line bg-surface">
                                    <img x-show="preview" x-cloak :src="preview" alt="Pratinjau {{ $u['label'] }}" class="h-full w-full object-contain {{ $u['pad'] }}">
                                    <div x-show="!preview" class="flex h-full w-full items-center justify-center">
                                        @if ($u['url'])
                                            <img src="{{ $u['url'] }}" alt="{{ $u['label'] }} saat ini" class="h-full w-full object-contain {{ $u['pad'] }}">
                                        @else
                                            <i class="fa-solid {{ $u['icon'] }} text-2xl text-muted" aria-hidden="true"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-small font-semibold">{{ $u['label'] }}</p>
                                    <p class="text-caption text-muted">{{ $u['hint'] }}</p>
                                    <label class="btn btn-secondary btn-sm mt-2 cursor-pointer focus-within:ring-2 focus-within:ring-brand-500/40">
                                        <i class="fa-solid fa-camera" aria-hidden="true"></i> Ganti
                                        <input type="file" name="{{ $uName }}" accept="image/*" class="sr-only"
                                            @change="const f = $event.target.files[0]; if (f) preview = URL.createObjectURL(f)">
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <h2 class="text-xl font-bold">Teks footer</h2>
                    <p class="mt-1 text-small text-muted">Ditampilkan di bagian bawah situs sesuai bahasa pengunjung.</p>
                    <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                        <x-ui.textarea name="footer_text_id" label="Teks footer (Indonesia)" rows="3" :value="$settings->footer_text_id" />
                        <x-ui.textarea name="footer_text_en" label="Teks footer (English)" rows="3" :value="$settings->footer_text_en" />
                    </div>
                </x-ui.card>
            </div>

            {{-- Tab: SEO --}}
            <div id="panel-seo" role="tabpanel" aria-labelledby="tab-seo" x-show="tab === 'seo'" x-cloak class="space-y-6">
                <x-ui.card>
                    <h2 class="text-xl font-bold">Mesin pencari</h2>
                    <p class="mt-1 text-small text-muted">Informasi yang dibaca Google dan media sosial saat situs dibagikan.</p>

                    <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                        <x-ui.input name="seo_title" label="Judul meta SEO" :value="$settings->seo_title" />
                        <x-ui.input name="seo_keywords" label="Kata kunci SEO" :value="$settings->seo_keywords" placeholder="creative, agency, portfolio, modern" hint="Pisahkan dengan koma." />
                        <div class="md:col-span-2">
                            <x-ui.textarea name="seo_description" label="Deskripsi SEO" rows="3" :value="$settings->seo_description" />
                        </div>
                        <div class="md:col-span-2">
                            <x-ui.input name="google_console_verification" label="Verifikasi Google Search Console" :value="$settings->google_console_verification" placeholder="google-site-verification-code" />
                        </div>
                        <div class="md:col-span-2">
                            <x-ui.input name="bing_verification" label="Verifikasi Bing Webmaster Tools" :value="$settings->bing_verification" placeholder="kode msvalidate.01" hint="Bing juga dipakai ChatGPT dan beberapa asisten AI untuk mencari." />
                        </div>
                        @foreach (collect($uploads)->only(['og_image']) as $uName => $u)
                            <div x-data="{ preview: null }" class="flex items-center gap-4 rounded-lg border border-line bg-surface-2 p-4 md:col-span-2">
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-md border border-line bg-surface">
                                    <img x-show="preview" x-cloak :src="preview" alt="Pratinjau {{ $u['label'] }}" class="h-full w-full object-contain {{ $u['pad'] }}">
                                    <div x-show="!preview" class="flex h-full w-full items-center justify-center">
                                        @if ($u['url'])
                                            <img src="{{ $u['url'] }}" alt="{{ $u['label'] }} saat ini" class="h-full w-full object-contain {{ $u['pad'] }}">
                                        @else
                                            <i class="fa-solid {{ $u['icon'] }} text-2xl text-muted" aria-hidden="true"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-small font-semibold">{{ $u['label'] }}</p>
                                    <p class="text-caption text-muted">{{ $u['hint'] }}</p>
                                    <label class="btn btn-secondary btn-sm mt-2 cursor-pointer focus-within:ring-2 focus-within:ring-brand-500/40">
                                        <i class="fa-solid fa-camera" aria-hidden="true"></i> Ganti
                                        <input type="file" name="{{ $uName }}" accept="image/*" class="sr-only"
                                            @change="const f = $event.target.files[0]; if (f) preview = URL.createObjectURL(f)">
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
                <x-ui.card>
                    <h2 class="text-xl font-bold">Alamat bisnis untuk Google dan AI</h2>
                    <p class="mt-1 text-small text-muted">Isi hanya data yang benar. Kalau alamat jalan dan kota kosong, tidak ada data lokasi yang dipublikasikan.</p>

                    <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <x-ui.input name="biz_street" label="Alamat jalan" :value="$settings->biz_street" placeholder="Jl. Contoh No. 10" />
                        </div>
                        <x-ui.input name="biz_city" label="Kota" :value="$settings->biz_city" placeholder="Bandung" />
                        <x-ui.input name="biz_region" label="Provinsi" :value="$settings->biz_region" placeholder="Jawa Barat" />
                        <x-ui.input name="biz_postal_code" label="Kode pos" :value="$settings->biz_postal_code" maxlength="10" />
                        <x-ui.input name="biz_opening_hours" label="Jam buka" :value="$settings->biz_opening_hours" placeholder="Mo-Fr 09:00-17:00" hint="Format Google: Mo-Fr 09:00-17:00 (Mo Tu We Th Fr Sa Su)." />
                        <div class="md:col-span-2">
                            <x-ui.input name="biz_price_range" label="Kisaran harga" :value="$settings->biz_price_range" placeholder="Rp 7.500.000 - Rp 18.500.000" />
                        </div>
                    </div>
                </x-ui.card>
            </div>

            {{-- Tab: Kontak & Sosial --}}
            <div id="panel-kontak" role="tabpanel" aria-labelledby="tab-kontak" x-show="tab === 'kontak'" x-cloak class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <x-ui.card>
                    <h2 class="text-xl font-bold">Kontak bisnis</h2>
                    <p class="mt-1 text-small text-muted">Dipakai di footer dan halaman kontak.</p>
                    <div class="mt-6 space-y-5">
                        <x-ui.input name="contact_email" type="email" label="Email bisnis" :value="$settings->contact_email" />
                        <x-ui.input name="contact_phone" label="Nomor telepon" :value="$settings->contact_phone" />
                        <x-ui.input name="contact_address" label="Alamat kantor" :value="$settings->contact_address" />
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <h2 class="text-xl font-bold">Media sosial</h2>
                    <p class="mt-1 text-small text-muted">Tempel tautan profil lengkap. Kosongkan untuk menyembunyikan.</p>
                    <div class="mt-6 space-y-5">
                        @foreach (['instagram' => ['fa-instagram', 'Instagram'], 'facebook' => ['fa-facebook-f', 'Facebook'], 'twitter' => ['fa-x-twitter', 'X (Twitter)'], 'linkedin' => ['fa-linkedin-in', 'LinkedIn']] as $key => [$icon, $socialLabel])
                            <div>
                                <label for="social_{{ $key }}" class="label">{{ $socialLabel }}</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-muted">
                                        <i class="fa-brands {{ $icon }}" aria-hidden="true"></i>
                                    </span>
                                    <input type="text" id="social_{{ $key }}" name="social[{{ $key }}]" value="{{ old('social.' . $key, $socials[$key] ?? '') }}"
                                        class="input pl-11" placeholder="https://{{ $key }}.com/...">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            </div>

            {{-- Tab: Invoice & Pajak --}}
            <div id="panel-invoice" role="tabpanel" aria-labelledby="tab-invoice" x-show="tab === 'invoice'" @if ($initialTab !== 'invoice') x-cloak @endif class="space-y-6">
                <x-ui.card>
                    <h2 class="text-xl font-bold">Data perusahaan di invoice</h2>
                    <p class="mt-1 text-small text-muted">Tampil pada kop dan tanda tangan invoice.</p>

                    <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                        <x-ui.input name="invoice_company_name" label="Nama perusahaan di invoice" :value="$settings->invoice_company_name" />
                        <x-ui.input name="tax_rate" type="number" step="0.01" min="0" max="100" label="Tarif pajak PPN (%)" :value="$settings->tax_rate ?? 11.00" placeholder="Contoh: 11.00" required />
                        <x-ui.input name="invoice_signer_name" label="Nama penanda tangan" :value="$settings->invoice_signer_name" placeholder="Contoh: Rifal Kurniawan" />
                        <x-ui.input name="invoice_signer_title" label="Jabatan penanda tangan" :value="$settings->invoice_signer_title" placeholder="Contoh: Authorized Representative" />
                        <div class="md:col-span-2">
                            <x-ui.textarea name="invoice_company_address" label="Alamat perusahaan di invoice" rows="3" :value="$settings->invoice_company_address" />
                        </div>
                        @foreach (collect($uploads)->only(['invoice_logo', 'invoice_signature']) as $uName => $u)
                            <div x-data="{ preview: null }" class="flex items-center gap-4 rounded-lg border border-line bg-surface-2 p-4">
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-md border border-line bg-surface">
                                    <img x-show="preview" x-cloak :src="preview" alt="Pratinjau {{ $u['label'] }}" class="h-full w-full object-contain {{ $u['pad'] }}">
                                    <div x-show="!preview" class="flex h-full w-full items-center justify-center">
                                        @if ($u['url'])
                                            <img src="{{ $u['url'] }}" alt="{{ $u['label'] }} saat ini" class="h-full w-full object-contain {{ $u['pad'] }}">
                                        @else
                                            <i class="fa-solid {{ $u['icon'] }} text-2xl text-muted" aria-hidden="true"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-small font-semibold">{{ $u['label'] }}</p>
                                    <p class="text-caption text-muted">{{ $u['hint'] }}</p>
                                    <label class="btn btn-secondary btn-sm mt-2 cursor-pointer focus-within:ring-2 focus-within:ring-brand-500/40">
                                        <i class="fa-solid fa-camera" aria-hidden="true"></i> Ganti
                                        <input type="file" name="{{ $uName }}" accept="image/*" class="sr-only"
                                            @change="const f = $event.target.files[0]; if (f) preview = URL.createObjectURL(f)">
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>

                <x-ui.card class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-brand-soft text-brand-text">
                            <i class="fa-solid fa-credit-card" aria-hidden="true"></i>
                        </span>
                        <div>
                            <h3 class="text-body font-bold">Metode pembayaran manual</h3>
                            <p class="text-small text-muted">Kelola rekening bank dan kode QRIS untuk checkout.</p>
                        </div>
                    </div>
                    <x-ui.button variant="secondary" :href="route('dashboard.payment_methods.edit')" icon="fa-arrow-right" :icon-right="true" class="shrink-0">Kelola pembayaran</x-ui.button>
                </x-ui.card>

                <x-ui.card class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-brand-soft text-brand-text">
                            <i class="fa-brands fa-whatsapp text-xl" aria-hidden="true"></i>
                        </span>
                        <div>
                            <h3 class="text-body font-bold">WhatsApp untuk invoice</h3>
                            <p class="text-small text-muted">Sambungkan sesi WAHA agar invoice langganan terkirim otomatis.</p>
                        </div>
                    </div>
                    <x-ui.button variant="secondary" :href="route('dashboard.whatsapp.edit')" icon="fa-arrow-right" :icon-right="true" class="shrink-0">Atur WhatsApp</x-ui.button>
                </x-ui.card>
            </div>
        </div>

        {{-- Sticky save bar --}}
        <div class="sticky bottom-[calc(57px+env(safe-area-inset-bottom))] z-20 -mx-4 mt-6 border-t border-line bg-surface/90 px-4 py-3 backdrop-blur lg:bottom-0 lg:-mx-8 lg:px-8">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-4">
                <p class="hidden text-small text-muted sm:block">Perubahan di semua tab disimpan bersamaan.</p>
                <x-ui.button type="submit" icon="fa-floppy-disk" class="w-full sm:w-auto">Simpan perubahan</x-ui.button>
            </div>
        </div>
    </form>
</x-app-layout>
