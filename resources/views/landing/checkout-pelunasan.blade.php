<x-layouts.public :title="'Pelunasan - ' . $order->package_name" description="Selesaikan pelunasan proyek website Anda.">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <div class="relative pb-32 pt-28 md:pb-24 md:pt-36" x-data="{ agree: false }">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(50%_30%_at_50%_0%,rgb(30_155_229/0.12),transparent)]" aria-hidden="true"></div>

        <div class="container-page relative">
            <a href="{{ route('user.orders') }}" class="btn-secondary btn-sm rounded-full"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Kembali ke Pesanan Saya</a>
            <h1 class="mt-6 text-3xl font-extrabold md:text-5xl">Pelunasan <span class="gradient-text">proyek website</span></h1>

            <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="min-w-0 lg:col-span-2">
                    <form id="pelunasan-form" action="{{ route('checkout.pelunasan.process', $order->id) }}" method="POST" class="space-y-6">
                        @csrf

                        @if (session('error'))
                            <div class="flex items-start gap-3 rounded-lg border border-danger-500/30 bg-danger-50 p-4 text-danger-700 dark:bg-danger-500/10 dark:text-red-300" role="alert">
                                <i class="fa-solid fa-circle-exclamation mt-0.5" aria-hidden="true"></i>
                                <div><p class="font-bold">Gagal memproses pembayaran</p><p class="text-small">{{ session('error') }}</p></div>
                            </div>
                        @endif

                        <x-ui.card>
                            <h2 class="text-xl">Metode pembayaran pelunasan</h2>
                            <p class="mt-1 text-small text-muted">Pilih metode pembayaran untuk melunasi sisa tagihan.</p>
                            <div class="mt-6">
                                <x-site.payment-methods :settings="$settings" :duitku-methods="$duitkuMethods" />
                                @error('payment_method')<p class="field-error">{{ $message }}</p>@enderror
                            </div>
                        </x-ui.card>

                        <x-ui.card>
                            <label class="flex cursor-pointer items-start gap-3">
                                <input type="checkbox" x-model="agree" class="mt-1 h-5 w-5 rounded border-line text-brand-600 focus:ring-brand-500">
                                <span class="text-small text-muted">Saya menyatakan setuju untuk melanjutkan pembayaran pelunasan sisa 50% untuk pengerjaan proyek website ini.</span>
                            </label>
                            <button type="submit" :disabled="!agree" class="btn-primary btn-lg mt-6 hidden w-full lg:inline-flex">Bayar Pelunasan Sekarang <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                        </x-ui.card>
                    </form>
                </div>

                <aside class="space-y-6">
                    <div class="card card-pad lg:sticky lg:top-28">
                        <p class="text-caption font-bold uppercase tracking-wider text-brand-text">Ringkasan invoice</p>
                        <h2 class="mt-2 text-xl">{{ $order->package_name }}</h2>
                        <p class="text-small text-muted">Brand: <strong class="text-ink">{{ $order->website_name }}</strong></p>

                        <dl class="mt-5 space-y-2 border-t border-line pt-5 text-small">
                            <div class="flex justify-between gap-4"><dt class="text-muted">Grand total proyek</dt><dd class="font-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-mint-800 dark:text-mint-300">DP (50%) terbayar</dt><dd class="font-semibold text-mint-800 dark:text-mint-300">- Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</dd></div>
                        </dl>

                        <div class="mt-5 rounded-lg bg-brand-soft p-4">
                            <p class="text-caption font-semibold text-brand-text">Tagihan pelunasan (50%)</p>
                            <p class="text-3xl font-extrabold">Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="card card-pad">
                        <h3 class="flex items-center gap-2 text-lg"><i class="fa-solid fa-bolt text-sun-500" aria-hidden="true"></i> Konfirmasi instan</h3>
                        <p class="mt-1 text-small text-muted">Pilih <strong>Pembayaran Otomatis</strong> via Duitku agar pelunasan terkonfirmasi langsung, tanpa perlu mengunggah bukti pembayaran.</p>
                    </div>
                </aside>
            </div>
        </div>

        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-line bg-surface/95 p-3 pb-[calc(0.75rem+env(safe-area-inset-bottom))] shadow-pop backdrop-blur lg:hidden">
            <div class="mx-auto flex max-w-lg items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-caption text-muted">Tagihan pelunasan</p>
                    <p class="truncate text-lg font-extrabold">Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}</p>
                </div>
                <button type="submit" form="pelunasan-form" :disabled="!agree" class="btn-primary shrink-0">Bayar sekarang</button>
            </div>
        </div>
    </div>

    <x-slot:footer><x-site.footer /></x-slot:footer>
</x-layouts.public>
