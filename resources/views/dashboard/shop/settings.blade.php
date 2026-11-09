@php
    $banks = $settings->payment_methods ?? [];
    $toggle = fn ($name, $label, $hint) => ['name' => $name, 'label' => $label, 'hint' => $hint];
@endphp
<x-app-layout>
    <x-slot name="header">Pengaturan Toko</x-slot>

    <div class="mx-auto max-w-4xl space-y-6">
        <x-ui.page-header title="Pengaturan toko" description="Aktifkan toko, atur PPN, transfer manual, dan pengiriman." />

        @if ($errors->any())
            <div class="rounded-lg border border-danger-500/40 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('dashboard.shop.settings.update') }}" class="space-y-6">
            @csrf

            <x-ui.card>
                <h2 class="text-lg font-bold">Status toko</h2>
                <div class="mt-4 space-y-4">
                    @foreach ([
                        $toggle('shop_enabled', 'Toko aktif', 'Menu "Toko" tampil dan pengunjung bisa berbelanja. Saat nonaktif, hanya admin yang bisa melihat toko (untuk pratinjau).'),
                        $toggle('shop_physical_enabled', 'Jual produk fisik', 'Produk fisik tampil dan bisa dibeli. Pastikan tarif ongkir sudah diatur di bawah.'),
                    ] as $t)
                        <label class="flex items-start gap-3">
                            <input type="checkbox" name="{{ $t['name'] }}" value="1" @checked(old($t['name'], $settings->{$t['name']})) class="mt-0.5 h-5 w-5 rounded border-line text-brand-600">
                            <span><span class="block font-semibold">{{ $t['label'] }}</span><span class="block text-small text-muted">{{ $t['hint'] }}</span></span>
                        </label>
                    @endforeach
                </div>
            </x-ui.card>

            <x-ui.card>
                <h2 class="text-lg font-bold">PPN</h2>
                <p class="mt-1 text-small text-muted">Harga produk sudah termasuk PPN. Sistem memisahkan DPP dan PPN di setiap pesanan untuk laporan admin; pelanggan tidak melihat baris PPN.</p>
                <div class="mt-4 max-w-xs"><x-ui.input name="shop_tax_rate" type="number" step="0.01" min="0" max="100" label="Tarif PPN (%)" :value="$settings->shop_tax_rate" required hint="Berlaku untuk pesanan baru." /></div>
            </x-ui.card>

            <x-ui.card>
                <h2 class="text-lg font-bold">Pembayaran</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <x-ui.input name="shop_order_expiry_minutes" type="number" min="15" label="Batas bayar Duitku (menit)" :value="$settings->shop_order_expiry_minutes" required hint="1440 = 24 jam. Lewat batas ini pesanan kedaluwarsa dan stok kembali." />
                    <x-ui.input name="shop_manual_expiry_minutes" type="number" min="15" label="Batas bayar transfer manual (menit)" :value="$settings->shop_manual_expiry_minutes" required hint="Pesanan yang buktinya sedang diperiksa tidak kedaluwarsa." />
                </div>
                <div class="mt-5 space-y-4">
                    @foreach ([
                        $toggle('shop_manual_payment_enabled', 'Terima transfer manual', 'Pelanggan bisa transfer ke rekening/QRIS di Metode pembayaran lalu mengunggah bukti.'),
                        $toggle('shop_manual_unique_code', 'Tambahkan kode unik (Rp1–999)', 'Memudahkan mencocokkan mutasi rekening. Kode unik tidak dikenai PPN.'),
                    ] as $t)
                        <label class="flex items-start gap-3">
                            <input type="checkbox" name="{{ $t['name'] }}" value="1" @checked(old($t['name'], $settings->{$t['name']})) class="mt-0.5 h-5 w-5 rounded border-line text-brand-600">
                            <span><span class="block font-semibold">{{ $t['label'] }}</span><span class="block text-small text-muted">{{ $t['hint'] }}</span></span>
                        </label>
                    @endforeach
                </div>
                <p class="mt-4 text-small">
                    Rekening aktif: <strong>{{ count($banks) ? collect($banks)->map(fn ($b) => strtoupper($b['bank'] ?? '').' '.($b['number'] ?? ''))->implode(', ') : 'belum ada' }}</strong>{{ $settings->invoice_qris ? ' + QRIS' : '' }}.
                    <a href="{{ route('dashboard.payment_methods.edit') }}" class="font-semibold text-brand-text hover:underline">Atur di Metode pembayaran</a>
                </p>
                <div class="mt-4 max-w-sm"><x-ui.input name="shop_admin_phone" type="tel" label="WhatsApp admin untuk notifikasi" :value="$settings->shop_admin_phone" placeholder="0812-3456-7890" hint="Menerima kabar pesanan lunas, bukti transfer baru, dan pesanan yang perlu tindakan." /></div>
            </x-ui.card>

            <x-ui.card>
                <h2 class="text-lg font-bold">Pengiriman (produk fisik)</h2>
                <p class="mt-1 text-small text-muted">Saat ini memakai tarif flat. Integrasi API ongkir (RajaOngkir/Biteship) bisa ditambahkan kemudian tanpa mengubah alur checkout.</p>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <x-ui.input name="shop_flat_shipping_cost" type="number" min="0" label="Tarif flat per pesanan (Rp)" :value="$settings->shop_flat_shipping_cost" hint="Kosong = pengiriman belum tersedia." />
                    <x-ui.input name="shop_origin_area_id" label="ID area asal (untuk API ongkir)" :value="$settings->shop_origin_area_id" />
                    <div class="md:col-span-2"><x-ui.textarea name="shop_origin_address" label="Alamat asal pengiriman" rows="2" :value="$settings->shop_origin_address" /></div>
                </div>
            </x-ui.card>

            <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Simpan pengaturan</button>
        </form>
    </div>
</x-app-layout>
