@php
    use App\Models\ShopOrder;
    use App\Models\ShopPayment;
    $id = app()->getLocale() === 'id';
    $money = fn ($v) => \App\Services\Shop\Money::format($v);
    $banks = $settings->payment_methods ?? [];
    $deadline = $order->expires_at?->timezone('Asia/Jakarta')->locale(app()->getLocale())->translatedFormat('d F Y, H:i').' WIB';
    $pendingDuitku = $order->payments->first(fn ($p) => $p->channel === ShopOrder::CHANNEL_DUITKU && $p->status === ShopPayment::PENDING && $p->payment_url);
    $rejected = $order->payments->first(fn ($p) => $p->status === ShopPayment::REJECTED);
    $isManual = $order->payment_channel === ShopOrder::CHANNEL_MANUAL;
    $address = $order->shipping_address;
    $polling = $returnedFromGateway && $order->payment_status === ShopOrder::PENDING;
@endphp
<x-layouts.public :noindex="true" :title="($id ? 'Pesanan ' : 'Order ').$order->order_number">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <div class="pb-16 pt-28 md:pb-24 md:pt-36">
        <div class="container-page max-w-5xl">
            <a href="{{ route('user.purchases') }}" class="btn-secondary btn-sm rounded-full"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> {{ $id ? 'Semua pembelian' : 'All purchases' }}</a>

            <header class="mt-6 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-small font-semibold text-muted">{{ $order->created_at->timezone('Asia/Jakarta')->locale(app()->getLocale())->translatedFormat('d F Y, H:i') }} WIB</p>
                    <h1 class="mt-1 font-mono text-3xl font-extrabold md:text-4xl">{{ $order->order_number }}</h1>
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-ui.badge :variant="$order->paymentStatusVariant()" :dot="true">{{ $order->paymentStatusLabel() }}</x-ui.badge>
                    @if ($order->isPaid() && $order->hasPhysicalItems())
                        <x-ui.badge variant="info">{{ $order->fulfillmentStatusLabel() }}</x-ui.badge>
                    @endif
                </div>
            </header>

            @if (session('success') || session('error'))
                <div @class(['mt-6 flex items-start gap-3 rounded-lg border-2 border-edge p-4 text-small font-semibold', 'bg-mint-50 text-mint-800 dark:bg-mint-400/15 dark:text-mint-300' => session('success'), 'bg-danger-50 text-danger-700 dark:bg-danger-500/15 dark:text-red-300' => session('error')]) role="status">
                    <i class="fa-solid {{ session('success') ? 'fa-circle-check' : 'fa-circle-exclamation' }} mt-0.5" aria-hidden="true"></i>
                    <span>{{ session('success') ?? session('error') }}</span>
                </div>
            @endif

            <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="min-w-0 space-y-6 lg:col-span-2">
                    {{-- Status / next step --}}
                    @if ($order->isPaid())
                        <section class="rounded-xl border-2 border-edge bg-mint-50 p-6 shadow-hard-sm dark:bg-mint-400/10">
                            <h2 class="flex items-center gap-2 text-xl font-extrabold"><i class="fa-solid fa-circle-check text-mint-600" aria-hidden="true"></i> {{ $id ? 'Pembayaran diterima' : 'Payment received' }}</h2>
                            @if ($order->hasDigitalItems())
                                <p class="mt-2 text-small">{{ $id ? 'Produk digital Anda sudah siap diunduh atau diakses.' : 'Your digital products are ready to download or open.' }}</p>
                                <a href="{{ route('user.downloads') }}" class="btn-primary mt-4"><i class="fa-solid fa-download" aria-hidden="true"></i> {{ $id ? 'Buka Produk Digital Saya' : 'Open My Digital Products' }}</a>
                            @endif
                            @if ($order->hasPhysicalItems())
                                <div class="mt-4 rounded-lg border-2 border-edge bg-surface p-4 text-small">
                                    @if ($order->tracking_number)
                                        <p class="font-bold">{{ $id ? 'Dikirim via' : 'Shipped via' }} {{ strtoupper($order->courier_code) }} {{ $order->courier_service }}</p>
                                        <p class="mt-1">{{ $id ? 'Nomor resi' : 'Tracking number' }}: <span class="font-mono font-bold">{{ $order->tracking_number }}</span></p>
                                    @else
                                        <p>{{ $id ? 'Barang sedang kami siapkan. Nomor resi akan muncul di sini setelah paket dikirim.' : 'We are preparing your items. The tracking number appears here once shipped.' }}</p>
                                    @endif
                                </div>
                            @endif
                        </section>
                    @elseif ($order->payment_status === ShopOrder::AWAITING_VERIFICATION)
                        <section class="rounded-xl border-2 border-edge bg-brand-100 p-6 shadow-hard-sm dark:bg-surface-2">
                            <h2 class="flex items-center gap-2 text-xl font-extrabold"><i class="fa-solid fa-hourglass-half text-brand-600" aria-hidden="true"></i> {{ $id ? 'Bukti transfer sedang diperiksa' : 'Your receipt is being checked' }}</h2>
                            <p class="mt-2 text-small">{{ $id ? 'Kami akan memverifikasi pembayaran Anda secepatnya, biasanya di jam kerja. Anda akan mendapat notifikasi setelah disetujui.' : 'We will verify your payment as soon as possible, usually during business hours. You will be notified once approved.' }}</p>
                        </section>
                    @elseif (in_array($order->payment_status, [ShopOrder::EXPIRED, ShopOrder::CANCELLED, ShopOrder::REFUNDED], true))
                        <section class="rounded-xl border-2 border-edge bg-surface-2 p-6">
                            <h2 class="text-xl font-extrabold">{{ $order->paymentStatusLabel() }}</h2>
                            <p class="mt-2 text-small text-muted">
                                @if ($order->payment_status === ShopOrder::EXPIRED)
                                    {{ $id ? 'Batas waktu pembayaran sudah lewat. Silakan pesan ulang dari toko.' : 'The payment deadline has passed. Please order again from the shop.' }}
                                @elseif ($order->payment_status === ShopOrder::REFUNDED)
                                    {{ $id ? 'Dana pesanan ini sudah dikembalikan.' : 'This order has been refunded.' }}
                                @else
                                    {{ $id ? 'Pesanan ini dibatalkan.' : 'This order was cancelled.' }}
                                @endif
                            </p>
                            <a href="{{ route('shop.index') }}" class="btn-secondary mt-4">{{ $id ? 'Kembali ke toko' : 'Back to shop' }}</a>
                        </section>
                    @else
                        {{-- Pending --}}
                        @if ($polling)
                            <section class="rounded-xl border-2 border-edge bg-sun-100 p-6 dark:bg-surface-2"
                                x-data="{ tries: 0, done: false, async check() { try { const r = await fetch('{{ route('shop.orders.status', $order) }}', { headers: { Accept: 'application/json' } }); const d = await r.json(); if (d.paid) { this.done = true; window.location.reload(); } } catch (e) {} if (++this.tries < 12 && !this.done) setTimeout(() => this.check(), 5000); } }"
                                x-init="check()" aria-live="polite">
                                <h2 class="flex items-center gap-2 text-lg font-extrabold"><i class="fa-solid fa-spinner animate-spin" aria-hidden="true" x-show="tries < 12"></i> {{ $id ? 'Menunggu konfirmasi pembayaran…' : 'Waiting for payment confirmation…' }}</h2>
                                <p class="mt-2 text-small" x-show="tries < 12">{{ $id ? 'Halaman ini diperbarui otomatis begitu pembayaran terkonfirmasi.' : 'This page updates automatically once the payment is confirmed.' }}</p>
                                <p class="mt-2 text-small" x-show="tries >= 12" x-cloak>{{ $id ? 'Belum ada konfirmasi. Jika Anda sudah membayar, tunggu beberapa menit lalu muat ulang halaman ini.' : 'No confirmation yet. If you have paid, wait a few minutes and reload this page.' }}</p>
                            </section>
                        @endif

                        <section class="rounded-xl border-2 border-edge bg-surface p-6 shadow-hard-sm">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <h2 class="text-xl font-extrabold">{{ $id ? 'Selesaikan pembayaran' : 'Complete your payment' }}</h2>
                                    @if ($deadline)
                                        <p class="mt-1 text-small text-muted">{{ $id ? 'Bayar sebelum' : 'Pay before' }} <strong class="text-ink">{{ $deadline }}</strong></p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-caption font-bold text-muted">{{ $id ? 'Total bayar' : 'Amount due' }}</p>
                                    <p class="text-3xl font-extrabold">{{ $money($order->total_amount) }}</p>
                                </div>
                            </div>

                            @if ($isManual)
                                @if ($rejected && ! $order->payments->contains('status', ShopPayment::SUBMITTED))
                                    <div class="mt-5 rounded-lg border-2 border-danger-500 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert">
                                        <p class="font-bold">{{ $id ? 'Bukti sebelumnya ditolak' : 'Your previous receipt was rejected' }}</p>
                                        <p class="mt-1">{{ $rejected->rejection_reason }}</p>
                                    </div>
                                @endif

                                <div class="mt-5 rounded-lg border-2 border-edge bg-sun-100 p-4 dark:bg-surface-2" x-data="{ copied: '' }">
                                    <p class="text-small font-semibold">{{ $id ? 'Transfer tepat sebesar' : 'Transfer exactly' }}</p>
                                    <div class="mt-1 flex flex-wrap items-center gap-3">
                                        <span class="text-2xl font-extrabold">{{ $money($order->total_amount) }}</span>
                                        <button type="button" class="btn-secondary btn-sm" @click="navigator.clipboard.writeText('{{ $order->total_amount }}'); copied = 'amount'">
                                            <i class="fa-regular fa-copy" aria-hidden="true"></i> <span x-text="copied === 'amount' ? '{{ $id ? 'Tersalin' : 'Copied' }}' : '{{ $id ? 'Salin' : 'Copy' }}'"></span>
                                        </button>
                                    </div>
                                    @if ($order->unique_code)
                                        <p class="mt-1 text-caption">{{ $id ? 'Termasuk kode unik' : 'Includes unique code' }} {{ $money($order->unique_code) }} {{ $id ? 'agar pembayaran mudah dicocokkan.' : 'so we can match your payment.' }}</p>
                                    @endif

                                    <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                                        @foreach ($banks as $index => $bank)
                                            <li class="rounded-lg border-2 border-edge bg-surface p-3">
                                                <p class="text-caption font-bold uppercase text-muted">{{ $bank['bank'] ?? '' }}</p>
                                                <p class="font-mono text-lg font-extrabold">{{ $bank['number'] ?? '' }}</p>
                                                <p class="text-caption">a.n. {{ $bank['name'] ?? '' }}</p>
                                                <button type="button" class="mt-2 text-caption font-bold text-brand-text hover:underline" @click="navigator.clipboard.writeText('{{ preg_replace('/\D/', '', $bank['number'] ?? '') }}'); copied = 'bank{{ $index }}'">
                                                    <span x-text="copied === 'bank{{ $index }}' ? '{{ $id ? 'Nomor tersalin' : 'Number copied' }}' : '{{ $id ? 'Salin nomor rekening' : 'Copy account number' }}'"></span>
                                                </button>
                                            </li>
                                        @endforeach
                                        @if ($settings->invoice_qris)
                                            <li class="rounded-lg border-2 border-edge bg-surface p-3 sm:col-span-2">
                                                <p class="text-caption font-bold uppercase text-muted">QRIS</p>
                                                <img src="{{ $settings->invoice_qris_url }}" alt="{{ $id ? 'Kode QRIS pembayaran' : 'Payment QRIS code' }}" class="mt-2 w-full max-w-[240px] rounded bg-white p-2">
                                            </li>
                                        @endif
                                    </ul>
                                </div>

                                <form method="POST" action="{{ route('shop.orders.proof', $order) }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                                    @csrf
                                    <h3 class="text-lg font-bold">{{ $id ? 'Sudah transfer? Unggah buktinya' : 'Paid? Upload your receipt' }}</h3>
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <x-ui.input name="sender_name" :label="$id ? 'Nama pemilik rekening pengirim' : 'Sender account name'" :value="old('sender_name', auth()->user()->name)" required />
                                        <x-ui.input name="sender_bank" :label="$id ? 'Bank / e-wallet pengirim' : 'Sender bank / e-wallet'" :value="old('sender_bank')" required placeholder="BCA, Mandiri, DANA…" />
                                        <x-ui.select name="method_name" :label="$id ? 'Ditransfer ke' : 'Paid to'">
                                            @foreach ($banks as $bank)
                                                <option value="{{ ($bank['bank'] ?? '').' - '.($bank['number'] ?? '') }}" @selected(old('method_name') === ($bank['bank'] ?? '').' - '.($bank['number'] ?? ''))>{{ strtoupper($bank['bank'] ?? '') }} {{ $bank['number'] ?? '' }}</option>
                                            @endforeach
                                            @if ($settings->invoice_qris)<option value="QRIS" @selected(old('method_name') === 'QRIS')>QRIS</option>@endif
                                        </x-ui.select>
                                        <div>
                                            <label for="proof" class="label">{{ $id ? 'Foto / PDF bukti transfer' : 'Receipt photo / PDF' }} <span class="text-danger-600" aria-hidden="true">*</span></label>
                                            <input id="proof" type="file" name="proof" accept="image/jpeg,image/png,image/webp,application/pdf" required class="block w-full text-small file:mr-3 file:rounded-md file:border-2 file:border-edge file:bg-sun-400 file:px-3 file:py-2 file:font-bold file:text-navy">
                                            <p class="hint">JPG, PNG, WEBP, PDF · maks. 2 MB</p>
                                            @error('proof')<p class="field-error">{{ $message }}</p>@enderror
                                        </div>
                                    </div>
                                    <x-ui.textarea name="customer_note" rows="2" :label="$id ? 'Catatan (opsional)' : 'Note (optional)'" :value="old('customer_note')" />
                                    <button type="submit" class="btn-primary"><i class="fa-solid fa-upload" aria-hidden="true"></i> {{ $id ? 'Kirim bukti transfer' : 'Send receipt' }}</button>
                                </form>
                            @else
                                @if ($pendingDuitku)
                                    <a href="{{ $pendingDuitku->payment_url }}" class="btn-primary btn-lg mt-6 w-full sm:w-auto">{{ $id ? 'Lanjutkan pembayaran' : 'Continue to payment' }} <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                                    <p class="mt-2 text-caption text-muted">{{ $id ? 'Link pembayaran tidak bisa dibuka atau metodenya ingin diganti? Pilih metode di bawah.' : 'Payment link not working or want another method? Choose below.' }}</p>
                                @endif
                            @endif

                            @if ($order->canPay())
                                <details class="mt-6 rounded-lg border-2 border-line p-4" @if (! $pendingDuitku && ! $isManual) open @endif>
                                    <summary class="cursor-pointer text-small font-bold">{{ $id ? 'Ganti metode pembayaran' : 'Change payment method' }}</summary>
                                    <form method="POST" action="{{ route('shop.orders.pay', $order) }}" class="mt-4 space-y-3">
                                        @csrf
                                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                            @forelse ($duitkuMethods as $method)
                                                <label class="block cursor-pointer">
                                                    <input type="radio" name="payment" value="duitku:{{ $method['paymentMethod'] }}" class="peer sr-only" required>
                                                    <span class="flex h-full flex-col items-center gap-2 rounded-lg border-2 border-line bg-surface p-3 text-center transition hover:border-edge peer-checked:border-edge peer-checked:bg-sun-100 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500 dark:peer-checked:bg-surface-2">
                                                        <span class="flex h-8 w-full items-center justify-center rounded bg-white p-1"><img src="{{ $method['paymentImage'] }}" alt="" class="max-h-full max-w-full object-contain"></span>
                                                        <span class="text-caption font-semibold">{{ $method['paymentName'] }}</span>
                                                    </span>
                                                </label>
                                            @empty
                                                <label class="block cursor-pointer">
                                                    <input type="radio" name="payment" value="duitku" class="peer sr-only" required>
                                                    <span class="flex h-full items-center justify-center rounded-lg border-2 border-line bg-surface p-3 text-caption font-bold peer-checked:border-edge peer-checked:bg-sun-100 dark:peer-checked:bg-surface-2">{{ $id ? 'Bayar online' : 'Pay online' }}</span>
                                                </label>
                                            @endforelse
                                            @if ($settings->shop_manual_payment_enabled && ! $isManual)
                                                <label class="block cursor-pointer">
                                                    <input type="radio" name="payment" value="manual" class="peer sr-only" required>
                                                    <span class="flex h-full items-center justify-center rounded-lg border-2 border-line bg-surface p-3 text-center text-caption font-bold peer-checked:border-edge peer-checked:bg-sun-100 dark:peer-checked:bg-surface-2">{{ $id ? 'Transfer bank / QRIS' : 'Bank transfer / QRIS' }}</span>
                                                </label>
                                            @endif
                                        </div>
                                        <button type="submit" class="btn-secondary">{{ $id ? 'Bayar dengan metode ini' : 'Pay with this method' }}</button>
                                    </form>
                                </details>
                            @endif
                        </section>
                    @endif

                    {{-- Items --}}
                    <section class="rounded-xl border-2 border-edge bg-surface">
                        <h2 class="border-b-2 border-edge px-5 py-4 text-lg font-bold">{{ $id ? 'Produk' : 'Items' }}</h2>
                        <ul class="divide-y divide-line">
                            @foreach ($order->items as $item)
                                @php $cover = $item->product?->images->first(); @endphp
                                <li class="flex gap-4 p-5">
                                    <div class="h-16 w-16 shrink-0 overflow-hidden rounded-lg border-2 border-edge bg-sun-100 dark:bg-surface-2">
                                        @if ($cover)<img src="{{ $cover->media_url }}" alt="" class="h-full w-full object-cover">@endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold">{{ $item->product_name }}</p>
                                        <p class="text-small text-muted">
                                            {{ $item->variant_title ? $item->variant_title.' · ' : '' }}{{ $item->quantity }} × {{ $money($item->unit_price) }}
                                            · {{ $item->product_type === 'digital' ? ($id ? 'Digital' : 'Digital') : ($id ? 'Fisik' : 'Physical') }}
                                        </p>
                                    </div>
                                    <p class="shrink-0 font-bold">{{ $money($item->subtotal) }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                </div>

                {{-- Totals --}}
                <aside class="space-y-6">
                    <div class="rounded-xl border-2 border-edge bg-surface p-5 shadow-hard lg:sticky lg:top-28">
                        <h2 class="text-lg font-bold">{{ $id ? 'Rincian' : 'Breakdown' }}</h2>
                        <dl class="mt-4 space-y-2 text-small">
                            <div class="flex justify-between gap-4"><dt class="text-muted">Subtotal</dt><dd class="font-semibold">{{ $money($order->subtotal) }}</dd></div>
                            @if ($order->discount_amount)
                                <div class="flex justify-between gap-4"><dt class="text-mint-800 dark:text-mint-300">Voucher {{ $order->voucher_code }}</dt><dd class="font-semibold text-mint-800 dark:text-mint-300">- {{ $money($order->discount_amount) }}</dd></div>
                            @endif
                            @if ($order->shipping_cost)
                                <div class="flex justify-between gap-4"><dt class="text-muted">{{ $id ? 'Ongkir' : 'Shipping' }}</dt><dd class="font-semibold">{{ $money($order->shipping_cost) }}</dd></div>
                            @endif
                            @if ($order->unique_code)
                                <div class="flex justify-between gap-4"><dt class="text-muted">{{ $id ? 'Kode unik' : 'Unique code' }}</dt><dd class="font-semibold">{{ $money($order->unique_code) }}</dd></div>
                            @endif
                        </dl>
                        <div class="divider my-4"></div>
                        <div class="flex items-end justify-between"><span class="text-small font-semibold">Total</span><span class="text-2xl font-extrabold">{{ $money($order->total_amount) }}</span></div>

                        @if ($address)
                            <div class="divider my-4"></div>
                            <h3 class="text-small font-bold">{{ $id ? 'Dikirim ke' : 'Ship to' }}</h3>
                            <p class="mt-1 text-small text-muted">{{ $address['recipient_name'] ?? '' }} · {{ $address['phone'] ?? '' }}<br>{{ $address['address_line'] ?? '' }}, {{ $address['city'] ?? '' }}, {{ $address['province'] ?? '' }} {{ $address['postal_code'] ?? '' }}</p>
                        @endif

                        <div class="mt-5 flex flex-wrap gap-2">
                            @if ($order->invoice)
                                <a href="{{ route('shop.orders.invoice', $order) }}" target="_blank" rel="noopener" class="btn-secondary btn-sm"><i class="fa-solid fa-file-invoice" aria-hidden="true"></i> Invoice</a>
                            @endif
                            @if ($order->payment_status === ShopOrder::PENDING)
                                <form method="POST" action="{{ route('shop.orders.cancel', $order) }}" x-data @submit="if (!confirm('{{ $id ? 'Batalkan pesanan ini?' : 'Cancel this order?' }}')) $event.preventDefault()">
                                    @csrf
                                    <button type="submit" class="btn-danger-soft btn-sm">{{ $id ? 'Batalkan pesanan' : 'Cancel order' }}</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <x-slot:footer><x-site.footer /></x-slot:footer>
</x-layouts.public>
