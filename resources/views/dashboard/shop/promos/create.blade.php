@php
    $checked = old('formats', ['instagram', 'whatsapp', 'banner', 'hashtags']);
    $icon = fn ($i) => (str_contains($i, 'fa-brands') ? '' : 'fa-solid ').$i;
@endphp
<x-app-layout>
    <x-slot name="header">Buat Konten Promosi</x-slot>

    <div class="mx-auto max-w-4xl space-y-6">
        <x-ui.page-header title="Buat konten promosi" description="Claude menulis dari data produk. Harga dan link diisi sistem, bukan ditebak AI.">
            <x-slot:actions>
                <x-ui.button :href="route('dashboard.shop.promos.index')" variant="secondary" icon="fa-arrow-left">Kembali</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        @unless ($configured)
            <div class="rounded-lg border border-warning-500/40 bg-warning-50 p-4 text-small text-warning-700 dark:bg-warning-500/15 dark:text-yellow-200" role="alert">
                <i class="fa-solid fa-key" aria-hidden="true"></i> API key Claude belum diatur (<code>ANTHROPIC_API_KEY</code> di <code>.env</code>).
            </div>
        @endunless

        @if ($errors->any())
            <div class="rounded-lg border border-danger-500/40 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert">
                <p class="font-semibold"><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i> Belum bisa dibuat:</p>
                <ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('dashboard.shop.promos.store') }}" x-data="{ busy: false }" @submit="busy = true" class="space-y-6">
            @csrf

            <x-ui.card>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="label" for="product_id">Produk <span class="text-danger-600">*</span></label>
                        <select id="product_id" name="product_id" class="select" required>
                            <option value="">Pilih produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected(old('product_id', $selected) === $product->id)>{{ $product->name_id }}{{ $product->is_active ? '' : ' (nonaktif)' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label" for="goal">Tujuan</label>
                        <select id="goal" name="goal" class="select">
                            @foreach ($goals as $key => $goal)
                                <option value="{{ $key }}" @selected(old('goal', 'launch') === $key)>{{ $goal['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label" for="tone">Nada</label>
                        <select id="tone" name="tone" class="select">
                            @foreach ($tones as $key => $tone)
                                <option value="{{ $key }}" @selected(old('tone', 'casual') === $key)>{{ $tone['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <fieldset>
                        <legend class="label">Bahasa</legend>
                        <div class="flex gap-5 pt-1 text-small">
                            <label class="flex items-center gap-2"><input type="radio" name="locale" value="id" class="h-5 w-5 border-line text-brand-600" @checked(old('locale', 'id') === 'id')> Indonesia</label>
                            <label class="flex items-center gap-2"><input type="radio" name="locale" value="en" class="h-5 w-5 border-line text-brand-600" @checked(old('locale') === 'en')> English</label>
                        </div>
                    </fieldset>

                    <div>
                        <label class="label" for="voucher_code">Kode voucher (opsional)</label>
                        <select id="voucher_code" name="voucher_code" class="select">
                            <option value="">Tanpa voucher</option>
                            @foreach ($vouchers as $voucher)
                                <option value="{{ $voucher->code }}" @selected(old('voucher_code') === $voucher->code)>{{ $voucher->code }} · {{ $voucher->type === 'percent' ? rtrim(rtrim((string) $voucher->value, '0'), '.').'%' : \App\Services\Shop\Money::format($voucher->value) }}</option>
                            @endforeach
                        </select>
                        <p class="hint">Hanya voucher aktif yang berlaku untuk toko.</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <fieldset>
                    <legend class="text-lg font-bold">Format konten</legend>
                    <p class="mt-1 text-small text-muted">Makin sedikit format, makin cepat hasilnya.</p>
                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach ($formats as $key => $format)
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line bg-surface-2 p-3 text-small font-semibold has-[:checked]:border-brand-500">
                                <input type="checkbox" name="formats[]" value="{{ $key }}" class="h-5 w-5 rounded border-line text-brand-600" @checked(in_array($key, $checked, true))>
                                <i class="{{ $icon($format['icon']) }} w-5 text-center text-brand-500" aria-hidden="true"></i>
                                {{ $format['label'] }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            </x-ui.card>

            <x-ui.card>
                <label class="label" for="notes">Catatan untuk Claude (opsional)</label>
                <textarea id="notes" name="notes" rows="4" maxlength="1000" class="input" placeholder="Contoh: sasar ibu muda; momen tahun ajaran baru; promo sampai 30 November; jangan pakai emoji.">{{ old('notes') }}</textarea>
                <p class="hint">Tulis tanggal atau momen di sini kalau perlu. Claude tidak boleh mengarang batas waktu sendiri.</p>
            </x-ui.card>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary" :disabled="busy" :aria-busy="busy">
                    <i class="fa-solid" :class="busy ? 'fa-spinner fa-spin' : 'fa-wand-magic-sparkles'" aria-hidden="true"></i>
                    <span x-text="busy ? 'Claude sedang menulis…' : 'Buat konten'">Buat konten</span>
                </button>
                <p class="text-caption text-muted" x-show="busy" x-cloak>Biasanya 10–30 detik. Jangan tutup halaman ini.</p>
            </div>
        </form>
    </div>
</x-app-layout>
