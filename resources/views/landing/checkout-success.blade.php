@php
    $bankName = '';
    $bankNumber = '';
    $bankOwner = '';

    if ($order->payment_method !== 'QRIS' && ! str_starts_with($order->payment_method ?? '', 'Duitku')) {
        $parts = explode(' - ', $order->payment_method);
        $bankName = $parts[0] ?? $order->payment_method;
        $bankNumber = $parts[1] ?? '';

        foreach ($settings->payment_methods ?? [] as $method) {
            if (strcasecmp($method['bank'], $bankName) === 0 && $method['number'] == $bankNumber) {
                $bankOwner = $method['name'];
                break;
            }
        }
    }

    $isPelunasanPayment = ($order->status === 'paid' && $order->payment_scheme === 'dp' && $order->final_invoice_id && $order->finalInvoice && $order->finalInvoice->status === 'Unpaid');
    $upfrontAmount = $isPelunasanPayment ? $order->remaining_balance : ($order->payment_scheme === 'dp' ? $order->dp_amount : $order->total_amount);
    $isDuitku = str_starts_with($order->payment_method ?? '', 'Duitku');
@endphp
<x-layouts.public :title="'Pesanan Diterima - ' . $order->package_name" description="Instruksi pembayaran untuk pesanan Anda.">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <div class="relative pb-16 pt-28 md:pb-24 md:pt-36">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(50%_30%_at_50%_0%,rgb(45_212_191/0.14),transparent)]" aria-hidden="true"></div>

        <div class="container-page relative max-w-5xl">
            <div class="text-center">
                <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-mint-400 text-2xl text-[#17123A] shadow-glow"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                <h1 class="mt-5 text-3xl font-extrabold md:text-5xl">Pesanan <span class="gradient-text">diterima!</span></h1>
                <p class="mx-auto mt-3 max-w-xl text-muted">Terima kasih telah mempercayai {{ $settings->site_name ?: 'FKStudio' }}. Pesanan Anda tercatat dengan ID <span class="font-mono font-semibold text-ink">{{ substr($order->id, 0, 8) }}</span>.</p>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-8 md:grid-cols-2">
                {{-- Left: steps + breakdown --}}
                <div class="min-w-0 space-y-6">
                    <x-ui.card>
                        <h2 class="text-xl">Langkah selanjutnya</h2>
                        <ol class="mt-5 space-y-5">
                            @foreach ([
                                ['Lakukan pembayaran', 'Transfer sesuai nominal ke rekening yang telah Anda pilih.'],
                                ['Unggah bukti transfer', 'Gunakan form pada halaman ini untuk mengunggah bukti pembayaran.'],
                                ['Aktivasi layanan', 'Admin akan memverifikasi pembayaran Anda dalam waktu maksimal 1x24 jam.'],
                            ] as $i => [$t, $d])
                                <li class="flex gap-4">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-600 text-small font-bold text-white">{{ $i + 1 }}</span>
                                    <div><p class="font-semibold">{{ $t }}</p><p class="text-small text-muted">{{ $d }}</p></div>
                                </li>
                            @endforeach
                        </ol>
                    </x-ui.card>

                    <x-ui.card>
                        <h2 class="text-xl">Rincian pembelian</h2>
                        <dl class="mt-4 space-y-2 text-small">
                            <div class="flex justify-between gap-4"><dt class="text-muted">Subtotal paket</dt><dd class="font-semibold">{{ $order->package_price }}</dd></div>
                            @if ($order->buy_domain && $order->domain_price)
                                <div class="flex justify-between gap-4"><dt class="text-muted">Domain ({{ $order->domain_name }})</dt><dd class="font-semibold">Rp {{ number_format($order->domain_price, 0, ',', '.') }}</dd></div>
                            @endif
                            @if ($order->discount_amount > 0)
                                <div class="flex justify-between gap-4 text-mint-800 dark:text-mint-300"><dt>Voucher ({{ $order->voucher_code }})</dt><dd class="font-semibold">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</dd></div>
                            @endif
                            <div class="flex justify-between gap-4"><dt class="text-muted">PPN ({{ $settings->tax_rate ?? 11 }}%)</dt><dd class="font-semibold">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</dd></div>
                            <div class="flex justify-between gap-4 border-t border-line pt-3 text-base"><dt class="font-bold">Grand total</dt><dd class="font-extrabold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</dd></div>
                        </dl>
                    </x-ui.card>
                </div>

                {{-- Right: payment --}}
                <div class="min-w-0">
                    @if ($isDuitku)
                        <x-ui.card class="flex flex-col items-center py-12 text-center">
                            <span class="flex h-20 w-20 items-center justify-center rounded-full bg-brand-soft text-3xl text-brand-text"><i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i></span>
                            <h2 class="mt-6 text-xl">Menunggu konfirmasi</h2>
                            <p class="mt-2 text-small text-muted">Kami sedang menunggu konfirmasi pembayaran otomatis dari Duitku. Halaman ini akan diperbarui otomatis.</p>
                        </x-ui.card>
                        <script>setTimeout(() => window.location.reload(), 5000);</script>
                    @else
                        <x-ui.card class="space-y-6">
                            <div class="rounded-lg border border-mint-400/40 bg-mint-50 p-5 dark:bg-mint-400/10">
                                <p class="text-caption font-bold text-mint-800 dark:text-mint-300">
                                    {{ $isPelunasanPayment ? 'Jumlah pembayaran pelunasan (50%)' : ($order->payment_scheme === 'dp' ? 'Jumlah down payment (DP 50%)' : 'Jumlah transfer lunas (100%)') }}
                                </p>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-3xl font-extrabold">Rp {{ number_format($upfrontAmount, 0, ',', '.') }}</span>
                                    <button type="button" class="btn-icon" aria-label="Salin jumlah transfer" x-data
                                        @click="navigator.clipboard.writeText('{{ (int) $upfrontAmount }}').then(() => toast('success', 'Jumlah transfer disalin.'))">
                                        <i class="fa-regular fa-copy" aria-hidden="true"></i>
                                    </button>
                                </div>
                                @if ($order->payment_scheme === 'dp' && ! $isPelunasanPayment)
                                    <p class="mt-2 text-caption text-muted">Sisa pelunasan <strong class="text-brand-text">Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}</strong> akan ditagihkan setelah website selesai dikerjakan.</p>
                                @endif
                            </div>

                            @if ($order->payment_method === 'QRIS' && $settings->invoice_qris)
                                <div class="flex flex-col items-center rounded-lg bg-white p-6 text-center">
                                    <p class="text-caption font-bold uppercase tracking-wider text-slate-500">Scan QRIS untuk membayar</p>
                                    <img src="{{ $settings->invoice_qris_url }}" alt="Kode QRIS pembayaran" class="mt-4 aspect-square w-full max-w-[220px] object-contain">
                                </div>
                            @elseif ($bankNumber)
                                <div class="rounded-lg border border-line bg-surface-2 p-5">
                                    <div class="flex items-center justify-between">
                                        <span class="badge-brand">Transfer bank</span>
                                        <span class="text-lg font-extrabold uppercase">{{ $bankName }}</span>
                                    </div>
                                    <p class="mt-4 text-caption font-semibold text-muted">Nomor rekening</p>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-2xl font-bold tracking-wide">{{ $bankNumber }}</span>
                                        <button type="button" class="btn-icon" aria-label="Salin nomor rekening" x-data
                                            @click="navigator.clipboard.writeText('{{ $bankNumber }}').then(() => toast('success', 'Nomor rekening disalin.'))">
                                            <i class="fa-regular fa-copy" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <p class="mt-3 text-caption font-semibold text-muted">Nama penerima</p>
                                    <p class="font-bold uppercase">{{ $bankOwner ?: '-' }}</p>
                                </div>
                            @endif

                            <form action="{{ route('checkout.confirm', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 border-t border-line pt-6">
                                @csrf
                                <h2 class="text-lg">Konfirmasi pembayaran</h2>
                                <x-ui.textarea name="message" label="Pesan (opsional)" :rows="3" placeholder="Contoh: Saya sudah transfer dari rekening atas nama…" />

                                <div x-data="{ fileName: null }">
                                    <label for="attachment" class="label">Bukti pembayaran <span class="text-danger-600" aria-hidden="true">*</span></label>
                                    <label for="attachment" class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-line bg-surface-2 px-4 py-8 text-center transition hover:border-brand-400 hover:bg-brand-soft">
                                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-brand-500" aria-hidden="true"></i>
                                        <span class="mt-2 text-small font-semibold" x-text="fileName || 'Klik untuk memilih bukti transfer'"></span>
                                        <span class="text-caption text-muted">Format gambar, maks. 2 MB</span>
                                    </label>
                                    <input id="attachment" type="file" name="attachment" accept="image/*" required class="sr-only"
                                        @change="fileName = $event.target.files[0]?.name">
                                    @error('attachment')<p class="field-error">{{ $message }}</p>@enderror
                                </div>

                                <button type="submit" class="btn-primary btn-lg w-full">Kirim konfirmasi</button>
                            </form>
                        </x-ui.card>
                    @endif
                </div>
            </div>

            <p class="mt-10 text-center"><a href="{{ route('user.orders') }}" class="text-small font-semibold text-brand-text hover:underline">Lihat pesanan saya</a> &middot; <a href="{{ route('home') }}" class="text-small font-semibold text-muted hover:text-ink">Kembali ke beranda</a></p>
        </div>
    </div>

    <x-slot:footer><x-site.footer /></x-slot:footer>
</x-layouts.public>
