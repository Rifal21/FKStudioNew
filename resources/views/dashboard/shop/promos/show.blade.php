@php
    $icon = fn ($i) => (str_contains($i, 'fa-brands') ? '' : 'fa-solid ').$i;
    $rows = ['text' => 6, 'pair' => 5, 'lines' => 4];
    $fieldLabels = ['headline' => 'Headline', 'subheadline' => 'Subheadline', 'subject' => 'Subjek', 'body' => 'Isi email'];
@endphp
<x-app-layout>
    <x-slot name="header">Konten Promosi</x-slot>

    <div class="mx-auto max-w-4xl space-y-6"
        x-data="{
            copied: null,
            copy(key, card) {
                const sep = card.dataset.sep ?? '\n\n';
                let text = [...card.querySelectorAll('textarea')].map(f => f.value.trim()).join(sep);
                if (sep === ' ') text = text.replace(/\s*\n+\s*/g, ' ');
                navigator.clipboard.writeText(text).then(() => { this.copied = key; setTimeout(() => this.copied = null, 1500); });
            }
        }">
        <x-ui.page-header :title="$promo->product?->name_id ?? 'Produk dihapus'" :description="(\App\Services\Promo\PromoWriter::GOALS[$promo->goal]['label'] ?? $promo->goal).' · '.(\App\Services\Promo\PromoWriter::TONES[$promo->tone]['label'] ?? $promo->tone).' · '.strtoupper($promo->locale)">
            <x-slot:actions>
                <x-ui.button :href="route('dashboard.shop.promos.index')" variant="secondary" icon="fa-arrow-left">Semua konten</x-ui.button>
                @if ($promo->product)
                    <x-ui.button :href="route('dashboard.shop.promos.create', ['product' => $promo->product_id])" variant="secondary" icon="fa-plus">Konten baru</x-ui.button>
                @endif
            </x-slot:actions>
        </x-ui.page-header>

        @if (! empty($promo->warnings))
            <div class="rounded-lg border border-warning-500/40 bg-warning-50 p-4 text-small text-warning-700 dark:bg-warning-500/15 dark:text-yellow-200" role="alert">
                <p class="font-semibold"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Periksa sebelum dipakai</p>
                <ul class="mt-1 list-inside list-disc">@foreach ($promo->warnings as $warning)<li>{{ $warning }}</li>@endforeach</ul>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-danger-500/40 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert">
                <ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('dashboard.shop.promos.update', $promo) }}" class="space-y-6">
            @csrf @method('PATCH')

            @foreach ($promo->formats as $key)
                @php
                    $format = $formats[$key] ?? null;
                    $value = $promo->content[$key] ?? null;
                    $sep = $key === 'hashtags' ? ' ' : (($format['kind'] ?? null) === 'lines' ? "\n" : "\n\n");
                @endphp
                @continue(! $format)
                <x-ui.card data-card :data-sep="$sep">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-lg font-bold"><i class="{{ $icon($format['icon']) }} mr-1 text-brand-500" aria-hidden="true"></i> {{ $format['label'] }}</h2>
                        <button type="button" class="btn-secondary btn-sm" @click="copy('{{ $key }}', $el.closest('[data-card]'))">
                            <i class="fa-regular" :class="copied === '{{ $key }}' ? 'fa-circle-check' : 'fa-copy'" aria-hidden="true"></i>
                            <span x-text="copied === '{{ $key }}' ? 'Tersalin' : 'Salin'">Salin</span>
                        </button>
                    </div>

                    <div class="mt-4 space-y-3">
                        @if ($format['kind'] === 'pair')
                            @foreach (array_keys($format['schema']['properties']) as $field)
                                <div>
                                    <label class="label" for="c-{{ $key }}-{{ $field }}">{{ $fieldLabels[$field] ?? $field }}</label>
                                    <textarea id="c-{{ $key }}-{{ $field }}" name="content[{{ $key }}][{{ $field }}]" rows="{{ in_array($field, ['body'], true) ? 7 : 2 }}" class="input">{{ $value[$field] ?? '' }}</textarea>
                                </div>
                            @endforeach
                        @else
                            <textarea name="content[{{ $key }}]" rows="{{ $rows[$format['kind']] ?? 4 }}" class="input" aria-label="{{ $format['label'] }}">{{ \App\Services\Promo\PromoFormats::forForm($key, $value) }}</textarea>
                            @if ($format['kind'] === 'lines')<p class="hint">Satu baris untuk satu item.</p>@endif
                        @endif
                    </div>
                </x-ui.card>
            @endforeach

            <div class="sticky bottom-0 z-10 -mx-1 flex flex-wrap items-center gap-3 rounded-lg border border-line bg-surface p-3 shadow-sm">
                <button type="submit" name="status" value="draft" class="btn-secondary"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Simpan draf</button>
                <button type="submit" name="status" value="final" class="btn-primary"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Tandai final</button>
                <p class="ml-auto text-caption text-muted">
                    Status: <strong>{{ $promo->status === 'final' ? 'Final' : 'Draf' }}</strong>
                    · {{ $promo->created_at->translatedFormat('j M Y, H:i') }}
                    @if ($promo->model) · {{ $promo->model }} · {{ number_format($promo->input_tokens + $promo->output_tokens) }} token @endif
                </p>
            </div>
        </form>

        @if ($promo->product)
            <x-ui.card>
                <h2 class="text-lg font-bold">Buat versi lain</h2>
                <p class="mt-1 text-small text-muted">Claude membaca teks sekarang, jadi Anda bisa minta perubahan. Hasilnya disimpan sebagai konten baru; yang ini tetap ada.</p>
                <form method="POST" action="{{ route('dashboard.shop.promos.regenerate', $promo) }}" x-data="{ busy: false }" @submit="busy = true" class="mt-4 flex flex-col gap-3 sm:flex-row">
                    @csrf
                    <input type="text" name="instruction" maxlength="500" class="input" placeholder="Contoh: lebih singkat, tambahkan ajakan beli sekarang, kurangi emoji" aria-label="Instruksi perubahan">
                    <button type="submit" class="btn-primary shrink-0" :disabled="busy">
                        <i class="fa-solid" :class="busy ? 'fa-spinner fa-spin' : 'fa-rotate'" aria-hidden="true"></i>
                        <span x-text="busy ? 'Menulis ulang…' : 'Tulis ulang'">Tulis ulang</span>
                    </button>
                </form>
            </x-ui.card>
        @endif

        <details class="text-small text-muted">
            <summary class="cursor-pointer font-semibold">Pengaturan yang dipakai</summary>
            <dl class="mt-2 grid grid-cols-[auto_1fr] gap-x-4 gap-y-1">
                <dt>Voucher</dt><dd>{{ $promo->voucher_code ?: '—' }}</dd>
                <dt>Catatan</dt><dd>{{ $promo->notes ?: '—' }}</dd>
                <dt>Dibuat oleh</dt><dd>{{ $promo->author?->name ?: '—' }}</dd>
            </dl>
        </details>
    </div>
</x-app-layout>
