@php
    $packagePrice = (float) str_replace(['Rp', '.', ','], '', $package->price);
    $checkoutConfig = [
        'packagePrice' => $packagePrice,
        'taxRate' => (float) ($settings->tax_rate ?? 11.00),
        'packageId' => $package->id,
        'businessType' => old('business_type', ''),
        'voucherUrl' => route('checkout.apply_voucher'),
        'csrf' => csrf_token(),
    ];
    $whatsapp = preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '');
    if ($whatsapp && str_starts_with($whatsapp, '0')) $whatsapp = '62' . substr($whatsapp, 1);
@endphp
<x-layouts.public :noindex="true" :title="'Checkout - ' . $package->getTranslation('name')" description="Selesaikan pesanan paket layanan FKStudio Anda.">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <div class="relative pb-32 pt-28 md:pb-24 md:pt-36" x-data="checkoutForm(@js($checkoutConfig))">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(50%_30%_at_50%_0%,rgb(30_155_229/0.12),transparent)]" aria-hidden="true"></div>

        <div class="container-page relative">
            <a href="{{ route('package.show', $package->slug) }}" class="btn-secondary btn-sm rounded-full"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Kembali ke detail paket</a>
            <h1 class="mt-6 text-3xl font-extrabold md:text-5xl">Selesaikan <span class="gradient-text">pesanan Anda</span></h1>
            <p class="mt-2 text-muted">Lengkapi data website, pilih skema dan metode pembayaran, lalu konfirmasi.</p>

            <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-3">
                {{-- Form --}}
                <div class="min-w-0 lg:col-span-2">
                    <form id="checkout-form" action="{{ route('checkout.process', $package->id) }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="payment_scheme" :value="paymentScheme">
                        <input type="hidden" name="voucher_code" :value="appliedCode">

                        @if (session('error'))
                            <div class="flex items-start gap-3 rounded-lg border border-danger-500/30 bg-danger-50 p-4 text-danger-700 dark:bg-danger-500/10 dark:text-red-300" role="alert">
                                <i class="fa-solid fa-circle-exclamation mt-0.5" aria-hidden="true"></i>
                                <div><p class="font-bold">Gagal memproses pembayaran</p><p class="text-small">{{ session('error') }}</p></div>
                            </div>
                        @endif

                        {{-- 1. Website --}}
                        <x-ui.card>
                            <h2 class="flex items-center gap-3 text-xl">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-600 text-small font-bold text-white">1</span>
                                Informasi website
                            </h2>
                            <div class="mt-6 grid gap-5 md:grid-cols-2">
                                <div>
                                    <span class="label">Nama lengkap</span>
                                    <p class="input flex items-center bg-surface-2 text-muted">{{ Auth::user()->name }}</p>
                                </div>
                                <div>
                                    <span class="label">Email</span>
                                    <p class="input flex items-center bg-surface-2 text-muted"><span class="truncate">{{ Auth::user()->email }}</span></p>
                                </div>

                                <x-ui.input name="website_name" label="Nama website / brand" placeholder="Contoh: Toko Kopi Senja" :required="true" />

                                <div>
                                    <label for="website_url" class="label">Subdomain yang diinginkan <span class="font-normal text-brand-text">(gratis)</span></label>
                                    <div @class(['flex overflow-hidden rounded-md border bg-surface focus-within:ring-2 focus-within:ring-brand-500/25', 'border-line focus-within:border-brand-500' => ! $errors->has('website_url'), 'border-danger-500' => $errors->has('website_url')])>
                                        <input id="website_url" type="text" name="website_url" placeholder="brandkamu" value="{{ old('website_url') }}" autocomplete="off" inputmode="url"
                                            oninput="this.value = this.value.toLowerCase().replace(/[\s\.]+/g, '-').replace(/[^a-z0-9\-]/g, '').replace(/-+/g, '-')"
                                            onblur="this.value = this.value.replace(/^-+|-+$/g, '')"
                                            class="min-h-[44px] w-full min-w-0 border-0 bg-transparent px-3.5 py-2.5 text-body text-ink placeholder:text-muted/70 focus:ring-0">
                                        <span class="flex shrink-0 select-none items-center border-l border-line bg-surface-2 px-3 text-small font-semibold text-muted">.fkstudio.id</span>
                                    </div>
                                    @error('website_url')<p class="field-error">{{ $message }}</p>@enderror
                                </div>

                                <div class="md:col-span-2">
                                    <x-ui.select name="business_type" label="Jenis bisnis / website" x-model="businessType" :required="true">
                                        <option value="">-- Pilih jenis --</option>
                                        @foreach (['Company Profile', 'Toko Online / E-Commerce', 'Portofolio', 'Blog / Artikel', 'Landing Page', 'Aplikasi Web', 'Lainnya'] as $type)
                                            <option value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                    </x-ui.select>
                                    <div x-show="businessType === 'Lainnya'" x-cloak x-transition class="mt-4">
                                        <x-ui.input name="custom_business_type" label="Jenis website custom Anda" placeholder="Misal: portal berita, undangan digital, platform kursus" x-bind:required="businessType === 'Lainnya'" />
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <x-ui.textarea name="client_notes" label="Brief / catatan tambahan" hint="Opsional — ceritakan kebutuhan atau referensi desain Anda." />
                                </div>
                            </div>
                        </x-ui.card>

                        {{-- 2. Payment --}}
                        <x-ui.card>
                            <h2 class="flex items-center gap-3 text-xl">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-600 text-small font-bold text-white">2</span>
                                Skema &amp; metode pembayaran
                            </h2>

                            <fieldset class="mt-6">
                                <legend class="label">Skema pembayaran</legend>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    @foreach (['full' => ['Lunas (100%)', 'Bayar penuh langsung di awal.'], 'dp' => ['Bertahap (DP 50%)', 'Mulai dengan DP 50% hari ini, sisanya menyusul.']] as $value => [$name, $desc])
                                        <button type="button" @click="paymentScheme = '{{ $value }}'" :aria-pressed="paymentScheme === '{{ $value }}'"
                                            class="rounded-lg border p-4 text-left transition"
                                            :class="paymentScheme === '{{ $value }}' ? 'border-brand-600 bg-brand-soft ring-2 ring-brand-600/30' : 'border-line bg-surface hover:border-brand-300'">
                                            <span class="flex items-center justify-between font-bold">{{ $name }}
                                                <i class="fa-solid fa-circle-check text-brand-600" x-show="paymentScheme === '{{ $value }}'" x-cloak aria-hidden="true"></i>
                                            </span>
                                            <span class="mt-1 block text-caption text-muted">{{ $desc }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </fieldset>

                            <div class="mt-8">
                                <x-site.payment-methods :settings="$settings" :duitku-methods="$duitkuMethods" />
                                @error('payment_method')<p class="field-error">{{ $message }}</p>@enderror
                            </div>
                        </x-ui.card>

                        {{-- 3. Confirm --}}
                        <x-ui.card>
                            <label class="flex cursor-pointer items-start gap-3">
                                <input type="checkbox" id="agree" x-model="agree" class="mt-1 h-5 w-5 rounded border-line text-brand-600 focus:ring-brand-500">
                                <span class="text-small text-muted">Saya telah membaca dan menyetujui <a href="#" class="font-semibold text-brand-text underline">Syarat &amp; Ketentuan</a> serta kebijakan layanan {{ $settings->site_name ?: 'FKStudio' }}.</span>
                            </label>
                            <button type="submit" :disabled="!agree" class="btn-primary btn-lg mt-6 hidden w-full lg:inline-flex">
                                Konfirmasi &amp; Pesan Sekarang <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </button>
                        </x-ui.card>
                    </form>
                </div>

                {{-- Summary --}}
                <aside class="space-y-6 lg:col-span-1">
                    <div class="card card-pad lg:sticky lg:top-28">
                        <p class="text-caption font-bold uppercase tracking-wider text-brand-text">Detail pesanan</p>
                        <h2 class="mt-2 text-xl">{{ $package->getTranslation('name') }}</h2>
                        <p class="text-muted">{{ $package->price }}</p>

                        <div class="divider my-5"></div>

                        {{-- Voucher --}}
                        <p class="label"><i class="fa-solid fa-ticket text-mint-500" aria-hidden="true"></i> Voucher diskon</p>
                        <div class="flex gap-2">
                            <input type="text" x-model="voucherCode" placeholder="Kode voucher" class="input uppercase" :disabled="appliedCode !== ''" aria-label="Kode voucher" @keydown.enter.prevent="applyVoucher()">
                            <button type="button" @click="applyVoucher()" x-show="appliedCode === ''" class="btn-primary shrink-0" :disabled="isCheckingVoucher">
                                <span x-show="!isCheckingVoucher">Pakai</span><i class="fa-solid fa-spinner animate-spin" x-show="isCheckingVoucher" x-cloak aria-hidden="true"></i>
                            </button>
                            <button type="button" @click="removeVoucher()" x-show="appliedCode !== ''" x-cloak class="btn-danger-soft shrink-0">Batal</button>
                        </div>
                        <p x-show="voucherError" x-cloak class="field-error" x-text="voucherError" role="alert"></p>
                        <p x-show="voucherSuccess" x-cloak class="mt-1.5 text-caption font-medium text-mint-800 dark:text-mint-300" x-text="voucherSuccess"></p>

                        <div class="divider my-5"></div>

                        <dl class="space-y-2 text-small">
                            <div class="flex justify-between gap-4"><dt class="text-muted">Subtotal paket</dt><dd class="font-semibold" x-text="formatCurrency(packagePrice)"></dd></div>
                            <div class="flex justify-between gap-4" x-show="discount() > 0" x-cloak><dt class="text-mint-800 dark:text-mint-300">Voucher <span class="font-mono" x-text="appliedCode"></span></dt><dd class="font-semibold text-mint-800 dark:text-mint-300" x-text="'- ' + formatCurrency(discount())"></dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-muted">PPN (<span x-text="taxRate"></span>%)</dt><dd class="font-semibold" x-text="formatCurrency(taxAmount())"></dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-muted">Skema</dt><dd class="font-semibold" x-text="paymentScheme === 'dp' ? 'DP 50%' : 'Lunas 100%'"></dd></div>
                            <div class="flex justify-between gap-4" x-show="paymentScheme === 'dp'" x-cloak><dt class="text-muted">Sisa pelunasan</dt><dd class="font-semibold text-brand-text" x-text="formatCurrency(remainingBalance())"></dd></div>
                        </dl>

                        <div class="mt-5 rounded-lg bg-brand-soft p-4">
                            <p class="text-caption font-semibold text-brand-text" x-text="paymentScheme === 'dp' ? 'DP yang dibayar hari ini' : 'Total pembayaran hari ini'"></p>
                            <p class="text-3xl font-extrabold" x-text="formatCurrency(totalPrice())"></p>
                        </div>
                    </div>

                    <div class="card card-pad">
                        <h3 class="text-lg">Butuh bantuan?</h3>
                        <p class="mt-1 text-small text-muted">Ada pertanyaan sebelum membayar? Tim kami siap membantu.</p>
                        @if ($whatsapp)
                            <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 text-small font-bold text-brand-text hover:underline"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WhatsApp support</a>
                        @endif
                    </div>
                </aside>
            </div>
        </div>

        {{-- Mobile sticky total bar --}}
        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-line bg-surface/95 p-3 pb-[calc(0.75rem+env(safe-area-inset-bottom))] shadow-pop backdrop-blur lg:hidden">
            <div class="mx-auto flex max-w-lg items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-caption text-muted" x-text="paymentScheme === 'dp' ? 'DP hari ini' : 'Total hari ini'"></p>
                    <p class="truncate text-lg font-extrabold" x-text="formatCurrency(totalPrice())"></p>
                </div>
                <button type="submit" form="checkout-form" :disabled="!agree" class="btn-primary shrink-0">Pesan sekarang</button>
            </div>
        </div>
    </div>

    <x-slot:footer><x-site.footer /></x-slot:footer>

    <x-slot:scripts>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('checkoutForm', (cfg) => ({
                    agree: false,
                    paymentScheme: 'full',
                    businessType: cfg.businessType,
                    packagePrice: cfg.packagePrice,
                    taxRate: cfg.taxRate,
                    voucherCode: '',
                    appliedCode: '',
                    discountAmount: 0,
                    voucherSuccess: '',
                    voucherError: '',
                    isCheckingVoucher: false,

                    discount() { return this.discountAmount; },
                    taxAmount() { return Math.round(Math.max(0, this.packagePrice - this.discount()) * (this.taxRate / 100)); },
                    grandTotal() { return this.packagePrice - this.discount() + this.taxAmount(); },
                    totalPrice() { return this.paymentScheme === 'dp' ? this.grandTotal() * 0.5 : this.grandTotal(); },
                    remainingBalance() { return this.grandTotal() - this.totalPrice(); },
                    formatCurrency(val) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(val); },

                    async applyVoucher() {
                        if (!this.voucherCode.trim()) {
                            this.voucherError = 'Silakan masukkan kode voucher.';
                            this.voucherSuccess = '';
                            return;
                        }
                        this.isCheckingVoucher = true;
                        this.voucherError = '';
                        this.voucherSuccess = '';
                        try {
                            const response = await fetch(cfg.voucherUrl, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': cfg.csrf, 'Accept': 'application/json' },
                                body: JSON.stringify({ voucher_code: this.voucherCode, package_id: cfg.packageId, buy_domain: '0', domain_name: '' }),
                            });
                            const result = await response.json();
                            if (result.success) {
                                this.discountAmount = result.discount_amount;
                                this.appliedCode = result.code;
                                this.voucherSuccess = result.message;
                            } else {
                                this.voucherError = result.message;
                                this.discountAmount = 0;
                                this.appliedCode = '';
                            }
                        } catch (err) {
                            this.voucherError = 'Gagal menghubungi server.';
                        } finally {
                            this.isCheckingVoucher = false;
                        }
                    },

                    removeVoucher() {
                        this.discountAmount = 0;
                        this.appliedCode = '';
                        this.voucherCode = '';
                        this.voucherSuccess = '';
                        this.voucherError = '';
                    },
                }));
            });
        </script>
    </x-slot:scripts>
</x-layouts.public>
