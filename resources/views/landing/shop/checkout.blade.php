@php
    $id = app()->getLocale() === 'id';
    $money = fn ($v) => \App\Services\Shop\Money::format($v);
    $banks = $settings->payment_methods ?? [];
    $manualAvailable = $settings->shop_manual_payment_enabled && (count($banks) > 0 || $settings->invoice_qris);
    $cfg = [
        'subtotal' => $summary['subtotal'],
        'voucherUrl' => route('shop.checkout.voucher'),
        'ratesUrl' => route('shop.checkout.rates'),
        'csrf' => csrf_token(),
        'hasPhysical' => $summary['has_physical'],
        'addressId' => old('address_id', $addresses->first()?->id),
        'voucher' => old('voucher_code', ''),
        'payment' => old('payment', count($duitkuMethods) ? 'duitku:'.$duitkuMethods[0]['paymentMethod'] : ($manualAvailable ? 'manual' : 'duitku')),
        'locale' => app()->getLocale(),
        'freeLabel' => $id ? 'Gratis' : 'Free',
    ];
    $radio = 'flex h-full items-center gap-3 rounded-lg border-2 border-line bg-surface p-3.5 transition hover:border-edge peer-checked:border-edge peer-checked:bg-sun-100 peer-checked:shadow-hard-sm peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500 dark:peer-checked:bg-surface-2';
@endphp
<x-layouts.public :noindex="true" :title="'Checkout'">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <div class="pb-32 pt-28 md:pb-24 md:pt-36" x-data="shopCheckout(@js($cfg))">
        <div class="container-page max-w-6xl">
            <a href="{{ route('cart.index') }}" class="btn-secondary btn-sm rounded-full"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> {{ $id ? 'Kembali ke keranjang' : 'Back to cart' }}</a>
            <h1 class="mt-6 font-display text-4xl font-extrabold tracking-tight md:text-5xl">Checkout</h1>

            @if (session('error') || $errors->any())
                <div class="mt-6 flex items-start gap-3 rounded-lg border-2 border-danger-500 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert">
                    <i class="fa-solid fa-circle-exclamation mt-0.5" aria-hidden="true"></i>
                    <div>
                        @if (session('error'))<p class="font-semibold">{{ session('error') }}</p>@endif
                        @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                </div>
            @endif

            <form id="checkout-form" method="POST" action="{{ route('shop.checkout.process') }}" class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3" @submit="submitting = true">
                @csrf
                <input type="hidden" name="voucher_code" :value="appliedCode">

                <div class="min-w-0 space-y-6 lg:col-span-2">
                    {{-- Contact --}}
                    <x-ui.card>
                        <h2 class="flex items-center gap-3 text-xl"><span class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-edge bg-sun-400 text-small font-extrabold text-navy">1</span>{{ $id ? 'Data pembeli' : 'Buyer details' }}</h2>
                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            <div><span class="label">{{ $id ? 'Nama' : 'Name' }}</span><p class="input flex items-center bg-surface-2 text-muted">{{ auth()->user()->name }}</p></div>
                            <div><span class="label">Email</span><p class="input flex items-center bg-surface-2 text-muted"><span class="truncate">{{ auth()->user()->email }}</span></p></div>
                            <x-ui.input name="customer_phone" type="tel" :label="$id ? 'Nomor WhatsApp (opsional)' : 'WhatsApp number (optional)'" :value="old('customer_phone')" placeholder="0812-3456-7890" :hint="$id ? 'Untuk notifikasi pembayaran dan akses produk.' : 'For payment and access notifications.'" />
                            <x-ui.input name="notes" :label="$id ? 'Catatan (opsional)' : 'Notes (optional)'" :value="old('notes')" />
                        </div>
                    </x-ui.card>

                    {{-- Shipping (physical only) --}}
                    @if ($summary['has_physical'])
                        <x-ui.card>
                            <h2 class="flex items-center gap-3 text-xl"><span class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-edge bg-sun-400 text-small font-extrabold text-navy">2</span>{{ $id ? 'Pengiriman' : 'Shipping' }}</h2>
                            @if ($addresses->isEmpty())
                                <p class="mt-4 text-small text-muted">{{ $id ? 'Belum ada alamat pengiriman.' : 'No shipping address yet.' }}</p>
                                <a href="{{ route('user.addresses.index', ['return' => 'checkout']) }}" class="btn-secondary mt-3"><i class="fa-solid fa-plus" aria-hidden="true"></i> {{ $id ? 'Tambah alamat' : 'Add address' }}</a>
                            @else
                                <fieldset class="mt-5">
                                    <legend class="label">{{ $id ? 'Alamat' : 'Address' }}</legend>
                                    <div class="grid gap-3 md:grid-cols-2">
                                        @foreach ($addresses as $address)
                                            <label class="block cursor-pointer">
                                                <input type="radio" name="address_id" value="{{ $address->id }}" x-model="addressId" @change="loadRates()" class="peer sr-only">
                                                <span class="{{ $radio }} items-start">
                                                    <span class="min-w-0 text-small">
                                                        <span class="block font-bold">{{ $address->label ?: $address->recipient_name }}</span>
                                                        <span class="block text-muted">{{ $address->recipient_name }} · {{ $address->phone }}</span>
                                                        <span class="block text-muted">{{ $address->address_line }}, {{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <a href="{{ route('user.addresses.index', ['return' => 'checkout']) }}" class="mt-3 inline-block text-small font-semibold text-brand-text hover:underline">{{ $id ? 'Kelola alamat' : 'Manage addresses' }}</a>
                                </fieldset>
                                <fieldset class="mt-6">
                                    <legend class="label">{{ $id ? 'Layanan pengiriman' : 'Shipping service' }}</legend>
                                    <p x-show="loadingRates" class="text-small text-muted"><i class="fa-solid fa-spinner animate-spin" aria-hidden="true"></i> {{ $id ? 'Menghitung ongkir…' : 'Calculating shipping…' }}</p>
                                    <p x-show="!loadingRates && rates.length === 0" x-cloak class="text-small text-muted">{{ $id ? 'Belum ada layanan pengiriman untuk alamat ini. Hubungi kami.' : 'No shipping service for this address yet. Please contact us.' }}</p>
                                    <div class="grid gap-3 md:grid-cols-2">
                                        <template x-for="rate in rates" :key="rate.code + ':' + rate.service">
                                            <label class="block cursor-pointer">
                                                <input type="radio" name="shipping_option" :value="rate.code + ':' + rate.service" x-model="shippingOption" class="peer sr-only">
                                                <span class="{{ $radio }} justify-between">
                                                    <span class="text-small font-bold" x-text="rate.label + (rate.etd ? ' (' + rate.etd + ')' : '')"></span>
                                                    <span class="text-small font-extrabold" x-text="money(rate.cost)"></span>
                                                </span>
                                            </label>
                                        </template>
                                    </div>
                                </fieldset>
                            @endif
                        </x-ui.card>
                    @endif

                    {{-- Nothing to pay: a 100% voucher or a price of 0 --}}
                    <x-ui.card x-show="isFree" x-cloak>
                        <h2 class="flex items-center gap-3 text-xl"><span class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-edge bg-mint-400 text-small font-extrabold text-navy"><i class="fa-solid fa-gift" aria-hidden="true"></i></span>{{ $id ? 'Tidak perlu membayar' : 'Nothing to pay' }}</h2>
                        <p class="mt-4 text-small text-muted">{{ $id ? 'Total pesanan Anda Rp 0. Setelah menekan tombol di bawah, produk langsung tersedia di akun Anda tanpa pembayaran.' : 'Your order total is Rp 0. Once you place it, your products are available in your account right away, with no payment.' }}</p>
                    </x-ui.card>

                    {{-- Payment --}}
                    <x-ui.card x-show="!isFree">
                        <h2 class="flex items-center gap-3 text-xl"><span class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-edge bg-sun-400 text-small font-extrabold text-navy">{{ $summary['has_physical'] ? 3 : 2 }}</span>{{ $id ? 'Metode pembayaran' : 'Payment method' }}</h2>

                        <h3 class="mt-6 text-small font-bold">{{ $id ? 'Otomatis (langsung terverifikasi)' : 'Automatic (verified instantly)' }}</h3>
                        <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @forelse ($duitkuMethods as $method)
                                <label class="block cursor-pointer">
                                    <input type="radio" name="payment" value="duitku:{{ $method['paymentMethod'] }}" x-model="payment" class="peer sr-only">
                                    <span class="{{ $radio }} flex-col justify-center text-center">
                                        <span class="flex h-9 w-full items-center justify-center rounded bg-white p-1.5"><img src="{{ $method['paymentImage'] }}" alt="" loading="lazy" class="max-h-full max-w-full object-contain"></span>
                                        <span class="line-clamp-2 text-caption font-semibold">{{ $method['paymentName'] }}</span>
                                    </span>
                                </label>
                            @empty
                                <label class="col-span-2 block cursor-pointer sm:col-span-3">
                                    <input type="radio" name="payment" value="duitku" x-model="payment" class="peer sr-only">
                                    <span class="{{ $radio }}">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-brand-50 text-brand-700 dark:bg-brand-500/20 dark:text-brand-300"><i class="fa-solid fa-wallet" aria-hidden="true"></i></span>
                                        <span class="text-small"><span class="block font-bold">{{ $id ? 'Bayar online' : 'Pay online' }}</span><span class="block text-muted">Virtual Account, QRIS, e-wallet</span></span>
                                    </span>
                                </label>
                            @endforelse
                        </div>

                        @if ($manualAvailable)
                            <h3 class="mt-6 text-small font-bold">{{ $id ? 'Manual (diverifikasi admin)' : 'Manual (verified by our team)' }}</h3>
                            <label class="mt-3 block cursor-pointer">
                                <input type="radio" name="payment" value="manual" x-model="payment" class="peer sr-only">
                                <span class="{{ $radio }}">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-sun-100 text-sun-800 dark:bg-sun-400/15 dark:text-sun-300"><i class="fa-solid fa-building-columns" aria-hidden="true"></i></span>
                                    <span class="min-w-0 text-small">
                                        <span class="block font-bold">{{ $id ? 'Transfer bank' : 'Bank transfer' }}{{ $settings->invoice_qris ? ' / QRIS' : '' }}</span>
                                        <span class="block text-muted">{{ collect($banks)->pluck('bank')->filter()->map(fn ($b) => strtoupper($b))->implode(', ') }}{{ $id ? ' — unggah bukti setelah transfer' : ' — upload your receipt after paying' }}</span>
                                    </span>
                                </span>
                            </label>
                        @endif
                    </x-ui.card>
                </div>

                {{-- Summary --}}
                <aside>
                    <div class="rounded-xl border-2 border-edge bg-surface p-5 shadow-hard lg:sticky lg:top-28">
                        <h2 class="text-lg font-bold">{{ $id ? 'Pesanan Anda' : 'Your order' }}</h2>
                        <ul class="mt-4 space-y-3 text-small">
                            @foreach ($summary['lines'] as $line)
                                <li class="flex justify-between gap-3">
                                    <span class="min-w-0">
                                        <span class="block font-semibold">{{ $line->product->getTranslation('name') }}</span>
                                        <span class="block text-muted">{{ $line->variant->label() ? $line->variant->label().' · ' : '' }}{{ $line->quantity }} × {{ $money($line->unit_price) }}</span>
                                    </span>
                                    <span class="shrink-0 font-semibold">{{ $money($line->subtotal) }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="divider my-4"></div>
                        <p class="label"><i class="fa-solid fa-ticket text-mint-600" aria-hidden="true"></i> {{ $id ? 'Voucher' : 'Voucher' }}</p>
                        <div class="flex gap-2">
                            <input type="text" x-model="voucherCode" class="input uppercase" :disabled="appliedCode !== ''" placeholder="{{ $id ? 'Kode voucher' : 'Voucher code' }}" aria-label="{{ $id ? 'Kode voucher' : 'Voucher code' }}" @keydown.enter.prevent="applyVoucher()">
                            <button type="button" class="btn-secondary shrink-0" x-show="appliedCode === ''" @click="applyVoucher()" :disabled="checkingVoucher">{{ $id ? 'Pakai' : 'Apply' }}</button>
                            <button type="button" class="btn-danger-soft shrink-0" x-show="appliedCode !== ''" x-cloak @click="removeVoucher()">{{ $id ? 'Batal' : 'Remove' }}</button>
                        </div>
                        <p x-show="voucherError" x-cloak class="field-error" x-text="voucherError" role="alert"></p>

                        <dl class="mt-4 space-y-2 text-small">
                            <div class="flex justify-between gap-4"><dt class="text-muted">Subtotal</dt><dd class="font-semibold" x-text="money(subtotal)"></dd></div>
                            <div class="flex justify-between gap-4" x-show="discount > 0" x-cloak><dt class="text-mint-800 dark:text-mint-300">Voucher <span class="font-mono" x-text="appliedCode"></span></dt><dd class="font-semibold text-mint-800 dark:text-mint-300" x-text="'- ' + money(discount)"></dd></div>
                            @if ($summary['has_physical'])
                                <div class="flex justify-between gap-4"><dt class="text-muted">{{ $id ? 'Ongkir' : 'Shipping' }}</dt><dd class="font-semibold" x-text="shippingCost === null ? '-' : money(shippingCost)"></dd></div>
                            @endif
                            @if ($settings->shop_manual_unique_code)
                                <div class="flex justify-between gap-4" x-show="payment === 'manual' && !isFree" x-cloak><dt class="text-muted">{{ $id ? 'Kode unik' : 'Unique code' }}</dt><dd class="text-muted">{{ $id ? '+ Rp1–999, tampil setelah pesan' : '+ Rp1–999, shown after ordering' }}</dd></div>
                            @endif
                        </dl>
                        <div class="mt-4 rounded-lg border-2 border-edge bg-sun-100 p-4 dark:bg-surface-2">
                            <p class="text-caption font-bold">{{ $id ? 'Total bayar' : 'Total' }}</p>
                            <p class="text-3xl font-extrabold" x-text="isFree ? cfg.freeLabel : money(total)">{{ $money($summary['subtotal']) }}</p>
                        </div>

                        <label class="mt-5 flex cursor-pointer items-start gap-3 text-small">
                            <input type="checkbox" name="agree" value="1" x-model="agree" class="mt-0.5 h-5 w-5 rounded border-line text-brand-600 focus:ring-brand-500">
                            <span class="text-muted">{{ $id ? 'Saya setuju dengan syarat & ketentuan serta ketentuan lisensi produk.' : 'I agree to the terms and the product license terms.' }}</span>
                        </label>
                        <button type="submit" class="btn-primary btn-lg mt-5 hidden w-full lg:inline-flex" :disabled="!canSubmit">
                            <span x-show="!submitting" x-text="isFree ? '{{ $id ? 'Dapatkan sekarang' : 'Get it now' }}' : '{{ $id ? 'Buat pesanan & bayar' : 'Place order & pay' }}'">{{ $id ? 'Buat pesanan & bayar' : 'Place order & pay' }}</span>
                            <span x-show="submitting" x-cloak><i class="fa-solid fa-spinner animate-spin" aria-hidden="true"></i> {{ $id ? 'Memproses…' : 'Processing…' }}</span>
                        </button>
                    </div>
                </aside>
            </form>
        </div>

        <div class="fixed inset-x-0 bottom-0 z-40 border-t-2 border-edge bg-surface/95 p-3 pb-[calc(0.75rem+env(safe-area-inset-bottom))] backdrop-blur lg:hidden">
            <div class="mx-auto flex max-w-lg items-center justify-between gap-3">
                <div class="min-w-0"><p class="text-caption text-muted">Total</p><p class="truncate text-lg font-extrabold" x-text="isFree ? cfg.freeLabel : money(total)"></p></div>
                <button type="submit" form="checkout-form" class="btn-primary shrink-0" :disabled="!canSubmit" x-text="isFree ? '{{ $id ? 'Dapatkan' : 'Get it' }}' : '{{ $id ? 'Bayar' : 'Pay' }}'">{{ $id ? 'Bayar' : 'Pay' }}</button>
            </div>
        </div>
    </div>

    <x-slot:footer><x-site.footer /></x-slot:footer>
    <x-slot:scripts>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('shopCheckout', (cfg) => ({
                    subtotal: cfg.subtotal,
                    payment: cfg.payment,
                    agree: false,
                    submitting: false,
                    voucherCode: cfg.voucher,
                    appliedCode: '',
                    discount: 0,
                    voucherError: '',
                    checkingVoucher: false,
                    addressId: cfg.addressId,
                    rates: [],
                    shippingOption: null,
                    loadingRates: false,
                    cfg: cfg,
                    init() {
                        if (cfg.voucher) this.applyVoucher();
                        if (cfg.hasPhysical && this.addressId) this.loadRates();
                    },
                    get shippingCost() {
                        if (!cfg.hasPhysical) return 0;
                        const rate = this.rates.find(r => r.code + ':' + r.service === this.shippingOption);
                        return rate ? rate.cost : null;
                    },
                    get total() { return Math.max(0, this.subtotal - this.discount) + (this.shippingCost || 0); },
                    // Nothing to pay: no payment method is needed. A physical cart only counts once its shipping fee is known.
                    get isFree() { return this.total === 0 && (!cfg.hasPhysical || this.shippingCost !== null); },
                    get canSubmit() { return this.agree && !this.submitting && (this.isFree || !!this.payment) && (!cfg.hasPhysical || this.shippingCost !== null); },
                    money(v) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(v || 0); },
                    async post(url, body) {
                        const res = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrf }, body: JSON.stringify(body) });
                        return res.json();
                    },
                    async applyVoucher() {
                        if (!this.voucherCode.trim()) return;
                        this.checkingVoucher = true; this.voucherError = '';
                        try {
                            const data = await this.post(cfg.voucherUrl, { voucher_code: this.voucherCode });
                            if (data.success) { this.appliedCode = data.code; this.discount = data.discount; }
                            else { this.voucherError = data.message || 'Voucher tidak valid.'; }
                        } catch (e) { this.voucherError = cfg.locale === 'id' ? 'Gagal memeriksa voucher.' : 'Could not check the voucher.'; }
                        this.checkingVoucher = false;
                    },
                    removeVoucher() { this.appliedCode = ''; this.discount = 0; this.voucherCode = ''; },
                    async loadRates() {
                        this.loadingRates = true; this.rates = []; this.shippingOption = null;
                        try {
                            const data = await this.post(cfg.ratesUrl, { address_id: this.addressId });
                            this.rates = data.options || [];
                            if (this.rates.length) this.shippingOption = this.rates[0].code + ':' + this.rates[0].service;
                        } catch (e) { this.rates = []; }
                        this.loadingRates = false;
                    },
                }));
            });
        </script>
    </x-slot:scripts>
</x-layouts.public>
