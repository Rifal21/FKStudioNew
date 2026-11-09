@php
    use App\Models\ShopOrder;
    use App\Models\ShopPayment;
    $money = fn ($v) => \App\Services\Shop\Money::format($v);
    $fmt = fn ($d) => $d?->timezone('Asia/Jakarta')->format('d/m/Y H:i');
    $address = $order->shipping_address;
    $paymentLabels = [
        ShopPayment::PENDING => ['Menunggu', 'warning'], ShopPayment::SUBMITTED => ['Perlu verifikasi', 'info'],
        ShopPayment::SUCCESS => ['Berhasil', 'success'], ShopPayment::REJECTED => ['Ditolak', 'danger'],
        ShopPayment::FAILED => ['Gagal', 'danger'], ShopPayment::EXPIRED => ['Kedaluwarsa', 'neutral'],
    ];
@endphp
<x-app-layout>
    <x-slot name="header">Pesanan {{ $order->order_number }}</x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <x-ui.page-header :title="$order->order_number" :description="'Dibuat '.$fmt($order->created_at).' WIB · '.$order->customer_name">
            <x-slot:actions>
                <x-ui.button :href="route('dashboard.shop.orders.index')" variant="secondary" icon="fa-arrow-left">Kembali</x-ui.button>
                @if ($order->invoice)
                    <x-ui.button :href="route('dashboard.invoices.show', $order->invoice->id)" variant="secondary" icon="fa-file-invoice">Invoice</x-ui.button>
                @endif
            </x-slot:actions>
        </x-ui.page-header>

        @if ($order->needs_attention)
            <div class="flex flex-wrap items-start gap-4 rounded-lg border-2 border-danger-500 bg-danger-50 p-4 text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert">
                <i class="fa-solid fa-triangle-exclamation mt-1" aria-hidden="true"></i>
                <div class="min-w-0 flex-1"><p class="font-bold">Perlu tindakan</p><p class="whitespace-pre-line text-small">{{ $order->attention_reason }}</p></div>
                <form method="POST" action="{{ route('dashboard.shop.orders.resolve', $order) }}">@csrf<button type="submit" class="btn-secondary btn-sm">Tandai selesai</button></form>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:items-start">
            <div class="min-w-0 space-y-6 lg:col-span-2">
                {{-- Payments --}}
                <x-ui.card>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-lg font-bold">Pembayaran</h2>
                        <x-ui.badge :variant="$order->paymentStatusVariant()" dot>{{ $order->paymentStatusLabel('id') }}</x-ui.badge>
                    </div>
                    @if ($order->payment_status === ShopOrder::PENDING && $order->expires_at)
                        <p class="mt-1 text-small text-muted">Kedaluwarsa otomatis {{ $fmt($order->expires_at) }} WIB bila belum dibayar.</p>
                    @endif
                    @forelse ($order->payments as $payment)
                        @php [$plabel, $pvariant] = $paymentLabels[$payment->status] ?? [$payment->status, 'neutral']; @endphp
                        <div @class(['mt-4 rounded-lg border-2 p-4', 'border-brand-600 bg-brand-soft' => $payment->status === ShopPayment::SUBMITTED, 'border-line' => $payment->status !== ShopPayment::SUBMITTED])>
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="font-semibold">{{ $payment->channel === 'manual' ? 'Transfer manual' : 'Duitku' }}{{ $payment->method_name ? ' · '.$payment->method_name : ($payment->method_code ? ' · '.$payment->method_code : '') }}</p>
                                <x-ui.badge :variant="$pvariant">{{ $plabel }}</x-ui.badge>
                            </div>
                            <dl class="mt-2 grid gap-x-6 gap-y-1 text-small sm:grid-cols-2">
                                <div><dt class="inline text-muted">Nominal:</dt> <dd class="inline font-semibold">{{ $money($payment->amount) }}</dd></div>
                                @if ($payment->merchant_order_id)<div><dt class="inline text-muted">ID Duitku:</dt> <dd class="inline font-mono">{{ $payment->merchant_order_id }}</dd></div>@endif
                                @if ($payment->reference)<div><dt class="inline text-muted">Referensi:</dt> <dd class="inline font-mono">{{ $payment->reference }}</dd></div>@endif
                                @if ($payment->sender_name)<div><dt class="inline text-muted">Pengirim:</dt> <dd class="inline">{{ $payment->sender_name }} ({{ $payment->sender_bank }})</dd></div>@endif
                                @if ($payment->submitted_at)<div><dt class="inline text-muted">Bukti dikirim:</dt> <dd class="inline">{{ $fmt($payment->submitted_at) }}</dd></div>@endif
                                @if ($payment->paid_at)<div><dt class="inline text-muted">Lunas:</dt> <dd class="inline">{{ $fmt($payment->paid_at) }}</dd></div>@endif
                                @if ($payment->verifier)<div><dt class="inline text-muted">Diverifikasi:</dt> <dd class="inline">{{ $payment->verifier->name }}, {{ $fmt($payment->verified_at) }}</dd></div>@endif
                            </dl>
                            @if ($payment->customer_note)<p class="mt-2 text-small">Catatan pelanggan: {{ $payment->customer_note }}</p>@endif
                            @if ($payment->rejection_reason)<p class="mt-2 text-small text-danger-700 dark:text-red-300">Alasan ditolak: {{ $payment->rejection_reason }}</p>@endif

                            @if ($payment->hasProof())
                                <a href="{{ route('dashboard.shop.orders.proof', [$order, $payment]) }}" target="_blank" rel="noopener" class="btn-secondary btn-sm mt-3"><i class="fa-solid fa-image" aria-hidden="true"></i> Lihat bukti transfer</a>
                            @endif

                            @if ($payment->status === ShopPayment::SUBMITTED)
                                <div class="mt-4 grid gap-3 border-t border-line pt-4 md:grid-cols-2" x-data="{ rejecting: false }">
                                    <form method="POST" action="{{ route('dashboard.shop.orders.approve', [$order, $payment]) }}" x-data @submit="if (!confirm('Pastikan dana {{ $money($payment->amount) }} sudah masuk ke rekening. Setujui?')) $event.preventDefault()">
                                        @csrf
                                        <button type="submit" class="btn-primary w-full"><i class="fa-solid fa-check" aria-hidden="true"></i> Setujui &amp; tandai lunas</button>
                                    </form>
                                    <button type="button" class="btn-danger-soft w-full" @click="rejecting = !rejecting"><i class="fa-solid fa-xmark" aria-hidden="true"></i> Tolak</button>
                                    <form method="POST" action="{{ route('dashboard.shop.orders.reject', [$order, $payment]) }}" x-show="rejecting" x-cloak class="space-y-2 md:col-span-2">
                                        @csrf
                                        <x-ui.textarea name="reason" label="Alasan penolakan (dikirim ke pelanggan)" rows="2" required placeholder="Mis. nominal tidak sesuai, bukti tidak terbaca." />
                                        <button type="submit" class="btn-danger">Kirim penolakan</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="mt-3 text-small text-muted">Belum ada percobaan pembayaran.</p>
                    @endforelse
                </x-ui.card>

                {{-- Items & digital access --}}
                <x-ui.card :padded="false">
                    <h2 class="border-b border-line px-5 py-4 text-lg font-bold">Produk</h2>
                    <ul class="divide-y divide-line">
                        @foreach ($order->items as $item)
                            <li class="p-5">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <p class="font-bold">{{ $item->product_name }}</p>
                                        <p class="text-small text-muted">{{ $item->variant_title ? $item->variant_title.' · ' : '' }}{{ $item->quantity }} × {{ $money($item->unit_price) }} · {{ $item->product_type === 'digital' ? 'Digital' : 'Fisik' }}{{ $item->sku ? ' · SKU '.$item->sku : '' }}</p>
                                    </div>
                                    <p class="font-bold">{{ $money($item->subtotal) }}</p>
                                </div>
                                @if ($item->entitlements->isNotEmpty())
                                    <ul class="mt-3 space-y-2">
                                        @foreach ($item->entitlements as $n => $entitlement)
                                            <li class="flex flex-wrap items-center justify-between gap-2 rounded-md bg-surface-2 px-3 py-2 text-small">
                                                <span>
                                                    Akses #{{ $n + 1 }}:
                                                    {{ $entitlement->download_count }}{{ $entitlement->max_downloads ? '/'.$entitlement->max_downloads : '' }} unduhan
                                                    @if ($entitlement->licenseKey) · kunci <span class="font-mono">{{ $entitlement->licenseKey->masked() }}</span>@elseif ($item->product?->digital_delivery === 'license_key') · <span class="font-semibold text-danger-700 dark:text-red-300">belum ada kunci</span>@endif
                                                    @if ($entitlement->revoked_at) · <span class="font-semibold text-danger-700 dark:text-red-300">dicabut</span>@endif
                                                </span>
                                                <span class="flex gap-1">
                                                    <form method="POST" action="{{ route('dashboard.shop.orders.entitlements.reset', [$order, $entitlement]) }}">@csrf<button type="submit" class="btn-ghost btn-sm">Reset unduhan</button></form>
                                                    <form method="POST" action="{{ route('dashboard.shop.orders.entitlements.toggle', [$order, $entitlement]) }}">@csrf<button type="submit" class="btn-ghost btn-sm">{{ $entitlement->revoked_at ? 'Pulihkan' : 'Cabut akses' }}</button></form>
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    @if ($order->isPaid() && $order->hasDigitalItems())
                        <div class="border-t border-line p-5">
                            <form method="POST" action="{{ route('dashboard.shop.orders.resend', $order) }}">@csrf<button type="submit" class="btn-secondary btn-sm"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Kirim ulang akses</button></form>
                            <p class="mt-1 text-caption text-muted">Melengkapi kunci lisensi yang kurang lalu mengirim ulang email/WhatsApp akses.</p>
                        </div>
                    @endif
                </x-ui.card>

                {{-- Shipping --}}
                @if ($order->hasPhysicalItems())
                    <x-ui.card>
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h2 class="text-lg font-bold">Pengiriman</h2>
                            @if ($order->isPaid())<x-ui.badge variant="info">{{ $order->fulfillmentStatusLabel('id') }}</x-ui.badge>@endif
                        </div>
                        @if ($address)
                            <p class="mt-2 text-small">{{ $address['recipient_name'] ?? '' }} · {{ $address['phone'] ?? '' }}<br>{{ $address['address_line'] ?? '' }}, {{ $address['district'] ?? '' }} {{ $address['city'] ?? '' }}, {{ $address['province'] ?? '' }} {{ $address['postal_code'] ?? '' }}</p>
                        @endif
                        <p class="mt-1 text-small text-muted">Layanan dipilih: {{ strtoupper((string) $order->courier_code) }} {{ $order->courier_service }} · {{ $money($order->shipping_cost) }} · berat {{ number_format($order->items->sum(fn ($i) => ($i->weight_grams ?? 0) * $i->quantity), 0, ',', '.') }} g</p>
                        @if ($order->tracking_number)
                            <p class="mt-2 text-small">Resi: <span class="font-mono font-bold">{{ $order->tracking_number }}</span> · dikirim {{ $fmt($order->shipped_at) }}</p>
                        @endif
                        @if ($order->isPaid() && in_array($order->fulfillment_status, [ShopOrder::PROCESSING, ShopOrder::SHIPPED], true))
                            <form method="POST" action="{{ route('dashboard.shop.orders.ship', $order) }}" class="mt-4 grid gap-3 md:grid-cols-4 md:items-end">
                                @csrf
                                <x-ui.input name="courier_code" label="Kurir" :value="$order->courier_code === 'flat' ? '' : $order->courier_code" required placeholder="JNE" />
                                <x-ui.input name="courier_service" label="Layanan" :value="$order->courier_service" placeholder="REG" />
                                <x-ui.input name="tracking_number" label="Nomor resi" :value="$order->tracking_number" required />
                                <button type="submit" class="btn-primary">{{ $order->tracking_number ? 'Perbarui resi' : 'Tandai dikirim' }}</button>
                            </form>
                        @endif
                        @if ($order->fulfillment_status === ShopOrder::SHIPPED)
                            <form method="POST" action="{{ route('dashboard.shop.orders.deliver', $order) }}" class="mt-3">@csrf<button type="submit" class="btn-secondary btn-sm"><i class="fa-solid fa-house-circle-check" aria-hidden="true"></i> Tandai diterima</button></form>
                        @endif
                    </x-ui.card>
                @endif
            </div>

            <div class="space-y-6 lg:sticky lg:top-6">
                {{-- Totals incl. PPN (admin only) --}}
                <x-ui.card>
                    <h2 class="text-lg font-bold">Rincian</h2>
                    <dl class="mt-3 space-y-1.5 text-small">
                        <div class="flex justify-between"><dt class="text-muted">Subtotal</dt><dd>{{ $money($order->subtotal) }}</dd></div>
                        @if ($order->discount_amount)<div class="flex justify-between"><dt class="text-muted">Voucher {{ $order->voucher_code }}</dt><dd>- {{ $money($order->discount_amount) }}</dd></div>@endif
                        @if ($order->shipping_cost)<div class="flex justify-between"><dt class="text-muted">Ongkir</dt><dd>{{ $money($order->shipping_cost) }}</dd></div>@endif
                        @if ($order->unique_code)<div class="flex justify-between"><dt class="text-muted">Kode unik</dt><dd>{{ $money($order->unique_code) }}</dd></div>@endif
                        <div class="flex justify-between border-t border-line pt-2 text-base font-extrabold"><dt>Total dibayar</dt><dd>{{ $money($order->total_amount) }}</dd></div>
                    </dl>
                    <div class="mt-4 rounded-lg border-2 border-edge bg-sun-100 p-3 text-small dark:bg-surface-2">
                        <p class="font-bold"><i class="fa-solid fa-lock" aria-hidden="true"></i> PPN (hanya admin)</p>
                        <dl class="mt-2 space-y-1">
                            <div class="flex justify-between"><dt>Nilai barang (termasuk PPN)</dt><dd>{{ $money($order->goodsAmount()) }}</dd></div>
                            <div class="flex justify-between"><dt>DPP</dt><dd>{{ $money($order->tax_base) }}</dd></div>
                            <div class="flex justify-between font-bold"><dt>PPN {{ rtrim(rtrim(number_format((float) $order->tax_rate, 2, ',', '.'), '0'), ',') }}%</dt><dd>{{ $money($order->tax_amount) }}</dd></div>
                        </dl>
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <h2 class="text-lg font-bold">Pelanggan</h2>
                    <p class="mt-2 text-small font-semibold">{{ $order->customer_name }}</p>
                    <p class="text-small text-muted">{{ $order->customer_email }}</p>
                    @if ($order->customer_phone)<p class="text-small text-muted">{{ $order->customer_phone }}</p>@endif
                    @if ($order->notes)<p class="mt-2 rounded-md bg-surface-2 p-2 text-small">Catatan: {{ $order->notes }}</p>@endif
                </x-ui.card>

                <x-ui.card>
                    <h2 class="text-lg font-bold">Catatan admin</h2>
                    <form method="POST" action="{{ route('dashboard.shop.orders.notes', $order) }}" class="mt-3 space-y-2">
                        @csrf @method('PATCH')
                        <textarea name="admin_notes" rows="3" class="input" aria-label="Catatan admin">{{ $order->admin_notes }}</textarea>
                        <button type="submit" class="btn-secondary btn-sm">Simpan catatan</button>
                    </form>
                </x-ui.card>

                @if ($order->isOpen())
                    <form method="POST" action="{{ route('dashboard.shop.orders.cancel', $order) }}" x-data @submit="if (!confirm('Batalkan pesanan dan kembalikan stok?')) $event.preventDefault()">
                        @csrf
                        <button type="submit" class="btn-danger-soft w-full">Batalkan pesanan</button>
                    </form>
                @endif

                @if ($order->isPaid())
                    <x-ui.card x-data="{ open: false }">
                        <button type="button" class="flex w-full items-center justify-between text-left font-bold" @click="open = !open" :aria-expanded="open">Catat refund <i class="fa-solid fa-chevron-down text-small" :class="open && 'rotate-180'" aria-hidden="true"></i></button>
                        <form method="POST" action="{{ route('dashboard.shop.orders.refund', $order) }}" x-show="open" x-cloak class="mt-3 space-y-2 text-small">
                            @csrf
                            <p class="text-muted">Kembalikan dana dulu lewat dashboard Duitku atau transfer bank, lalu catat di sini. Akses produk digital akan dicabut.</p>
                            <label class="flex items-center gap-2"><input type="checkbox" name="restock" value="1" class="h-5 w-5 rounded border-line text-brand-600"> Kembalikan stok</label>
                            <label class="flex items-center gap-2"><input type="checkbox" name="confirm" value="1" required class="h-5 w-5 rounded border-line text-brand-600"> Dana sudah dikembalikan</label>
                            <button type="submit" class="btn-danger">Catat refund</button>
                        </form>
                    </x-ui.card>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
