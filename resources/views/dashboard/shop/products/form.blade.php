@php
    use App\Models\Product;
    $editing = $product->exists;
    $default = $product->variants->firstWhere('is_default', true) ?? ($product->has_variants ? null : $product->variants->first());

    $optionsInit = old('options', $editing && $product->has_variants
        ? $product->options->map(fn ($o) => [
            'name_id' => $o->name_id,
            'name_en' => $o->name_en,
            'values' => $o->values->map(fn ($v) => ['value_id' => $v->value_id, 'value_en' => $v->value_en])->values()->all(),
        ])->values()->all()
        : [['name_id' => '', 'name_en' => '', 'values' => [['value_id' => '', 'value_en' => '']]]]);

    if (old('variants')) {
        $rowsInit = collect(old('variants'))->keyBy('key')->map(fn ($r) => $r + ['is_active' => ! empty($r['is_active'])])->all();
    } else {
        $rowsInit = $product->has_variants
            ? $product->variants->mapWithKeys(fn ($v) => [
                $product->options->map(fn ($o) => $v->optionValues->firstWhere('product_option_id', $o->id)?->value_id)->implode('|') => [
                    'id' => $v->id, 'price' => $v->price, 'compare_at_price' => $v->compare_at_price, 'sku' => $v->sku,
                    'stock' => $v->stock, 'weight_grams' => $v->weight_grams, 'is_active' => $v->is_active, 'image_id' => $v->product_image_id,
                ],
            ])->all()
            : [];
    }

    $images = $product->images->map(fn ($i) => ['id' => $i->id, 'url' => $i->media_url, 'alt_id' => $i->alt_id, 'alt_en' => $i->alt_en])->values();

    $state = [
        'type' => old('type', $product->type ?? Product::DIGITAL),
        'delivery' => old('digital_delivery', $product->digital_delivery ?? Product::DELIVERY_FILE),
        'hasVariants' => (bool) old('has_variants', $product->has_variants),
        'unlimited' => (bool) old('unlimited_downloads', $editing && $product->max_downloads === null),
        'options' => $optionsInit,
        'rows' => (object) $rowsInit,
        'images' => $images,
        'thumbnail' => $images->first()['id'] ?? null,
        'deleted' => [],
    ];
    $tab = fn ($label, $id) => '<button type="button" @click="lang = \''.$id.'\'" :class="lang === \''.$id.'\' ? \'bg-sun-400 text-navy\' : \'text-muted hover:text-ink\'" class="rounded-full px-3 py-1 text-caption font-bold">'.$label.'</button>';
@endphp
<x-app-layout>
    <x-slot name="header">{{ $editing ? 'Ubah Produk' : 'Produk Baru' }}</x-slot>

    @push('head')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    @endpush

    <div class="mx-auto max-w-7xl space-y-6">
        <x-ui.page-header :title="$editing ? $product->name_id : 'Produk baru'" :description="$editing ? 'Harga sudah termasuk PPN. Perubahan langsung berlaku di toko.' : 'Isi info produk, harga, dan file produk digital, lalu simpan.'">
            <x-slot:actions>
                <x-ui.button :href="route('dashboard.shop.products.index')" variant="secondary" icon="fa-arrow-left">Kembali</x-ui.button>
                @if ($editing)
                    <x-ui.button :href="route('dashboard.shop.products.show', $product)" variant="secondary" icon="fa-eye" target="_blank">Pratinjau</x-ui.button>
                @endif
                @if ($editing && $product->is_active)
                    <x-ui.button :href="route('shop.show', $product->slug)" variant="secondary" icon="fa-arrow-up-right-from-square" target="_blank">Lihat di toko</x-ui.button>
                @endif
            </x-slot:actions>
        </x-ui.page-header>

        @if ($errors->any())
            <div class="rounded-lg border border-danger-500/40 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert">
                <p class="font-semibold"><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i> Periksa kembali isian Anda:</p>
                <ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" enctype="multipart/form-data" id="product-form"
            action="{{ $editing ? route('dashboard.shop.products.update', $product) : route('dashboard.shop.products.store') }}"
            x-data="productForm(@js($state))" @submit="beforeSubmit($event)" @invalid.capture="reportInvalid($event)">
            @csrf
            @if ($editing) @method('PATCH') @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:items-start">
                <div class="min-w-0 space-y-6 lg:col-span-2">
                    {{-- Basic info --}}
                    <x-ui.card x-data="{ lang: 'id' }" @show-lang.window="lang = $event.detail">
                        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                            <h2 class="text-lg font-bold">Info produk</h2>
                            <div class="flex rounded-full border border-line bg-surface-2 p-0.5" role="group" aria-label="Bahasa">{!! $tab('Indonesia', 'id') !!}{!! $tab('English', 'en') !!}</div>
                        </div>
                        <div class="space-y-4" x-show="lang === 'id'">
                            <x-ui.input name="name_id" label="Nama produk" :value="$product->name_id" required />
                            <x-ui.textarea name="short_description_id" label="Deskripsi singkat" rows="2" :value="$product->short_description_id" hint="Tampil di kartu produk dan di bawah judul. Maks. 500 karakter." />
                            <div><label class="label" for="description_id">Deskripsi lengkap</label><textarea id="description_id" name="description_id" class="summernote">{{ old('description_id', $product->description_id) }}</textarea></div>
                            <x-ui.textarea name="license_terms_id" label="Ketentuan lisensi (opsional)" rows="3" :value="$product->license_terms_id" hint="Mis. boleh dipakai untuk 1 proyek komersial, tidak boleh dijual ulang." />
                        </div>
                        <div class="space-y-4" x-show="lang === 'en'" x-cloak>
                            <x-ui.input name="name_en" label="Product name" :value="$product->name_en" hint="Kosong = memakai nama Indonesia." />
                            <x-ui.textarea name="short_description_en" label="Short description" rows="2" :value="$product->short_description_en" />
                            <div><label class="label" for="description_en">Full description</label><textarea id="description_en" name="description_en" class="summernote">{{ old('description_en', $product->description_en) }}</textarea></div>
                            <x-ui.textarea name="license_terms_en" label="License terms (optional)" rows="3" :value="$product->license_terms_en" />
                        </div>
                    </x-ui.card>

                    {{-- Images --}}
                    <x-ui.card>
                        <div class="mb-4 flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <h2 class="text-lg font-bold">Gambar produk</h2>
                                <p class="text-small text-muted">Pilih beberapa gambar sekaligus, lalu klik <i class="fa-regular fa-star" aria-hidden="true"></i> untuk menjadikannya thumbnail (tampil di kartu produk dan sebagai gambar utama). Seret untuk mengatur urutan galeri. Maks. 4 MB per gambar, 10 gambar per simpan.</p>
                            </div>
                            <span class="badge-neutral shrink-0" x-text="(images.length + newImages.length) + ' gambar'"></span>
                        </div>
                        <input type="hidden" name="image_order" :value="images.map(i => i.id).join(',')">
                        <input type="hidden" name="thumbnail" :value="thumbnail">
                        <template x-for="id in deleted" :key="id"><input type="hidden" name="delete_images[]" :value="id"></template>

                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4" x-sortable="moveImage" x-show="images.length">
                            <template x-for="(image, index) in images" :key="image.id">
                                <div class="rounded-lg border-2 bg-surface-2 p-2 transition" :class="thumbnail === image.id ? 'border-edge ring-2 ring-sun-400' : 'border-line'" data-sortable-item>
                                    <div class="relative aspect-square overflow-hidden rounded-md">
                                        <img :src="image.url" alt="" class="h-full w-full object-cover">
                                        <span x-show="thumbnail === image.id" class="absolute left-1 top-1 rounded bg-sun-400 px-1.5 text-[11px] font-bold text-navy"><i class="fa-solid fa-star" aria-hidden="true"></i> Thumbnail</span>
                                    </div>
                                    <input type="text" :name="'image_alt_id[' + image.id + ']'" x-model="image.alt_id" class="input mt-2 py-1 text-caption" placeholder="Alt teks (ID)" aria-label="Alt teks gambar">
                                    <input type="text" :name="'image_alt_en[' + image.id + ']'" x-model="image.alt_en" class="input mt-1 py-1 text-caption" placeholder="Alt text (EN)" aria-label="Alt text">
                                    <div class="mt-1 flex items-center justify-between">
                                        <button type="button" data-drag-handle class="btn-icon cursor-grab touch-none" aria-label="Seret untuk mengubah urutan"><i class="fa-solid fa-grip-vertical" aria-hidden="true"></i></button>
                                        <button type="button" class="btn-icon" :class="thumbnail === image.id ? 'text-sun-600' : ''" @click="setThumbnail(image.id)" :aria-pressed="thumbnail === image.id" aria-label="Jadikan thumbnail" title="Jadikan thumbnail"><i :class="thumbnail === image.id ? 'fa-solid' : 'fa-regular'" class="fa-star" aria-hidden="true"></i></button>
                                        <button type="button" class="btn-icon text-danger-700 dark:text-red-300" @click="removeImage(image.id)" aria-label="Hapus gambar" title="Hapus gambar"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div x-show="newImages.length" x-cloak class="mt-4">
                            <p class="label">Gambar baru (diunggah saat disimpan)</p>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                <template x-for="(image, index) in newImages" :key="image.key">
                                    <div class="rounded-lg border-2 border-dashed bg-surface-2 p-2 transition" :class="thumbnail === 'new:' + index ? 'border-edge ring-2 ring-sun-400' : 'border-line'">
                                        <div class="relative aspect-square overflow-hidden rounded-md">
                                            <img :src="image.url" alt="" class="h-full w-full object-cover">
                                            <span x-show="thumbnail === 'new:' + index" class="absolute left-1 top-1 rounded bg-sun-400 px-1.5 text-[11px] font-bold text-navy"><i class="fa-solid fa-star" aria-hidden="true"></i> Thumbnail</span>
                                        </div>
                                        <p class="mt-1 truncate text-caption text-muted" x-text="image.name"></p>
                                        <div class="mt-1 flex items-center justify-between">
                                            <button type="button" class="btn-icon" :class="thumbnail === 'new:' + index ? 'text-sun-600' : ''" @click="setThumbnail('new:' + index)" :aria-pressed="thumbnail === 'new:' + index" aria-label="Jadikan thumbnail" title="Jadikan thumbnail"><i :class="thumbnail === 'new:' + index ? 'fa-solid' : 'fa-regular'" class="fa-star" aria-hidden="true"></i></button>
                                            <button type="button" class="btn-icon text-danger-700 dark:text-red-300" @click="dropNewImage(index)" aria-label="Batalkan gambar ini" title="Batalkan"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <label class="mt-4 block">
                            <span class="label">Tambah gambar</span>
                            <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp,image/gif" x-ref="imageInput" @change="pickImages($event)" class="block w-full text-small file:mr-3 file:rounded-md file:border-2 file:border-edge file:bg-sun-400 file:px-3 file:py-2 file:font-bold file:text-navy">
                        </label>
                        <p class="hint">Bisa pilih beberapa file sekaligus (Ctrl/Shift + klik). Memilih lagi akan menambah ke daftar.</p>
                        @error('images')<p class="field-error">{{ $message }}</p>@enderror
                        @error('images.*')<p class="field-error">{{ $message }}</p>@enderror
                    </x-ui.card>

                    {{-- Price & variants --}}
                    <x-ui.card>
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                            <h2 class="text-lg font-bold">Harga, stok &amp; varian</h2>
                            <label class="flex items-center gap-2 text-small font-semibold">
                                <input type="checkbox" name="has_variants" value="1" x-model="hasVariants" class="h-5 w-5 rounded border-line text-brand-600"> Produk ini punya varian
                            </label>
                        </div>
                        <p class="mb-4 rounded-lg bg-brand-soft p-3 text-caption"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Harga yang diisi adalah harga jual <strong>termasuk PPN</strong>. PPN dihitung otomatis per pesanan dan hanya terlihat di admin.</p>

                        {{-- Single --}}
                        <div class="grid gap-4 md:grid-cols-3" x-show="!hasVariants">
                            <x-ui.input name="price" type="number" min="0" label="Harga (Rp)" :value="$default?->price" x-bind:required="!hasVariants" />
                            <x-ui.input name="compare_at_price" type="number" min="0" label="Harga coret (opsional)" :value="$default?->compare_at_price" hint="Harga sebelum diskon." />
                            <x-ui.input name="sku" label="SKU (opsional)" :value="$default?->sku" />
                            <x-ui.input name="stock" type="number" min="0" label="Stok" :value="$default?->stock" hint="Kosong = tidak terbatas." />
                            <div x-show="type === 'physical'"><x-ui.input name="weight_grams" type="number" min="1" label="Berat (gram)" :value="$default?->weight_grams" x-bind:required="!hasVariants && type === 'physical'" /></div>
                        </div>

                        {{-- Variants --}}
                        <div x-show="hasVariants" x-cloak class="space-y-5">
                            <template x-for="(option, oi) in options" :key="oi">
                                <div class="rounded-lg border-2 border-line p-4">
                                    <div class="flex flex-wrap items-end gap-3">
                                        <div class="min-w-0 flex-1"><label class="label">Nama opsi (ID)</label><input class="input" :name="'options[' + oi + '][name_id]'" x-model="option.name_id" placeholder="Warna, Ukuran, Lisensi" :required="hasVariants"></div>
                                        <div class="min-w-0 flex-1"><label class="label">Option name (EN)</label><input class="input" :name="'options[' + oi + '][name_en]'" x-model="option.name_en" placeholder="Color, Size, License"></div>
                                        <button type="button" class="btn-icon text-danger-700 dark:text-red-300" @click="options.splice(oi, 1)" x-show="options.length > 1" aria-label="Hapus opsi"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
                                    </div>
                                    <p class="label mt-4">Nilai</p>
                                    <div class="space-y-2">
                                        <template x-for="(value, vi) in option.values" :key="vi">
                                            <div class="flex gap-2">
                                                <input class="input" :name="'options[' + oi + '][values][' + vi + '][value_id]'" x-model="value.value_id" placeholder="Nilai (ID), mis. Merah" :required="hasVariants" aria-label="Nilai (ID)">
                                                <input class="input" :name="'options[' + oi + '][values][' + vi + '][value_en]'" x-model="value.value_en" placeholder="Value (EN), e.g. Red" aria-label="Value (EN)">
                                                <button type="button" class="btn-icon shrink-0" @click="option.values.splice(vi, 1)" x-show="option.values.length > 1" aria-label="Hapus nilai"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                                            </div>
                                        </template>
                                    </div>
                                    <button type="button" class="btn-ghost btn-sm mt-2" @click="option.values.push({ value_id: '', value_en: '' })"><i class="fa-solid fa-plus" aria-hidden="true"></i> Tambah nilai</button>
                                </div>
                            </template>
                            <button type="button" class="btn-secondary btn-sm" x-show="options.length < {{ \App\Services\Shop\ProductSaver::MAX_OPTIONS }}" @click="options.push({ name_id: '', name_en: '', values: [{ value_id: '', value_en: '' }] })"><i class="fa-solid fa-plus" aria-hidden="true"></i> Tambah opsi</button>

                            <div x-show="combos.length">
                                <div class="flex flex-wrap items-end gap-3 rounded-lg bg-surface-2 p-3">
                                    <div><label class="label">Isi semua harga</label><input type="number" min="0" class="input w-36" x-model.number="bulk.price"></div>
                                    <div><label class="label">Isi semua stok</label><input type="number" min="0" class="input w-28" x-model="bulk.stock" placeholder="kosong = ∞"></div>
                                    <button type="button" class="btn-secondary btn-sm" @click="applyBulk()">Terapkan ke semua</button>
                                </div>
                                <div class="mt-3 overflow-x-auto">
                                    <table class="table-base min-w-[760px]">
                                        <thead><tr><th>Varian</th><th>Harga</th><th>Harga coret</th><th>Stok</th><th x-show="type === 'physical'">Berat (g)</th><th>SKU</th><th>Gambar</th><th>Aktif</th></tr></thead>
                                        <tbody>
                                            <template x-for="(key, i) in combos" :key="key">
                                                <tr>
                                                    <td class="whitespace-nowrap font-semibold">
                                                        <span x-text="key.split('|').join(' / ')"></span>
                                                        <input type="hidden" :name="'variants[' + i + '][key]'" :value="key">
                                                        <input type="hidden" :name="'variants[' + i + '][id]'" :value="row(key).id || ''">
                                                    </td>
                                                    <td><input type="number" min="0" class="input w-32" :name="'variants[' + i + '][price]'" x-model="row(key).price" :required="hasVariants" aria-label="Harga"></td>
                                                    <td><input type="number" min="0" class="input w-32" :name="'variants[' + i + '][compare_at_price]'" x-model="row(key).compare_at_price" aria-label="Harga coret"></td>
                                                    <td><input type="number" min="0" class="input w-24" :name="'variants[' + i + '][stock]'" x-model="row(key).stock" placeholder="∞" aria-label="Stok"></td>
                                                    <td x-show="type === 'physical'"><input type="number" min="1" class="input w-24" :name="'variants[' + i + '][weight_grams]'" x-model="row(key).weight_grams" :required="hasVariants && type === 'physical'" aria-label="Berat"></td>
                                                    <td><input type="text" class="input w-32" :name="'variants[' + i + '][sku]'" x-model="row(key).sku" aria-label="SKU"></td>
                                                    <td>
                                                        <select class="select w-28" :name="'variants[' + i + '][image_id]'" x-model="row(key).image_id" aria-label="Gambar varian">
                                                            <option value="">-</option>
                                                            <template x-for="(image, n) in images" :key="image.id"><option :value="image.id" x-text="'Gambar ' + (n + 1)"></option></template>
                                                        </select>
                                                    </td>
                                                    <td><input type="hidden" :name="'variants[' + i + '][is_active]'" value="0"><input type="checkbox" value="1" class="h-5 w-5 rounded border-line text-brand-600" :name="'variants[' + i + '][is_active]'" x-model="row(key).is_active" aria-label="Aktif"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="mt-2 text-caption text-muted">Stok kosong = tidak terbatas. Gambar varian bisa dipilih dari gambar yang sudah tersimpan.</p>
                            </div>
                        </div>
                    </x-ui.card>

                    {{-- Digital product payload --}}
                    <x-ui.card x-show="type === 'digital' && delivery !== 'link'" x-cloak>
                        <div x-show="delivery === 'file'">
                            <h2 class="text-lg font-bold">File produk digital</h2>
                            <p class="mt-1 text-small text-muted">File yang diunduh pembeli setelah lunas. Disimpan privat, tidak bisa dibuka lewat link publik. Bisa pilih beberapa file sekaligus (maks. 10, masing-masing hingga 500 MB).</p>
                            @if ($editing && $product->files->isNotEmpty())
                                <p class="mt-3 rounded-lg bg-mint-50 p-3 text-small text-mint-800 dark:bg-mint-400/15 dark:text-mint-300"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> {{ $product->files->count() }} file sudah terunggah. File baru di bawah akan ditambahkan; kelola atau hapus file di bagian bawah halaman.</p>
                            @elseif ($editing)
                                <p class="mt-3 rounded-lg border-2 border-dashed border-danger-500/50 p-3 text-small font-semibold text-danger-700 dark:text-red-300"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Belum ada file. Pembeli tidak mendapat apa pun sampai file diunggah.</p>
                            @endif
                            <label for="digital_files" class="label mt-4">Unggah file</label>
                            <input id="digital_files" type="file" name="digital_files[]" multiple x-ref="digitalFiles" @change="pickedFiles = Array.from($event.target.files).map(f => f.name + ' (' + (f.size / 1048576).toFixed(1) + ' MB)')"
                                class="block w-full text-small file:mr-3 file:rounded-md file:border-2 file:border-edge file:bg-sun-400 file:px-3 file:py-2 file:font-bold file:text-navy">
                            <p class="hint">ZIP, RAR, PDF, EPUB, PSD, FIG, gambar, video, audio, dokumen Office. Untuk banyak berkas, satukan dalam ZIP karena setiap unduhan file dihitung.{{ $product->has_variants ? ' File di sini berlaku untuk semua varian; file khusus varian diunggah di bagian bawah.' : '' }}</p>
                            <ul class="mt-2 space-y-1 text-small" x-show="pickedFiles.length">
                                <template x-for="name in pickedFiles" :key="name"><li class="flex items-center gap-2"><i class="fa-solid fa-file text-brand-600" aria-hidden="true"></i><span x-text="name"></span></li></template>
                            </ul>
                            @error('digital_files.*')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                        <div x-show="delivery === 'license_key'">
                            <h2 class="text-lg font-bold">Kunci lisensi</h2>
                            <p class="mt-1 text-small text-muted">Satu kunci per baris. Satu kunci diberikan untuk setiap unit yang dibeli; duplikat dilewati otomatis. Kunci disimpan terenkripsi.</p>
                            <textarea name="license_keys" rows="5" class="input mt-3 font-mono text-small" placeholder="AAAA-BBBB-CCCC-DDDD&#10;EEEE-FFFF-GGGG-HHHH" aria-label="Kunci lisensi">{{ old('license_keys') }}</textarea>
                            @if ($editing)<p class="hint">Kunci yang sudah ada tetap tersimpan; lihat daftarnya di bagian bawah halaman.</p>@endif
                        </div>
                    </x-ui.card>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6 lg:sticky lg:top-6">
                    <x-ui.card>
                        <h2 class="mb-4 text-lg font-bold">Publikasi</h2>
                        <div class="space-y-3">
                            <label class="flex items-center gap-2 text-small font-semibold"><input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-line text-brand-600" @checked(old('is_active', $product->is_active ?? true))> Aktif (tampil di toko)</label>
                            <label class="flex items-center gap-2 text-small font-semibold"><input type="checkbox" name="is_featured" value="1" class="h-5 w-5 rounded border-line text-brand-600" @checked(old('is_featured', $product->is_featured))> Produk unggulan</label>
                            <x-ui.input name="sort_order" type="number" min="0" label="Urutan" :value="$product->sort_order ?? 0" />
                        </div>
                        <button type="submit" class="btn-primary mt-5 w-full" @click.prevent="send()" :aria-busy="saving" :class="saving && 'pointer-events-none opacity-70'">
                            <span x-show="!saving"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> {{ $editing ? 'Simpan perubahan' : 'Simpan produk' }}</span>
                            <span x-show="saving" x-cloak><i class="fa-solid fa-spinner animate-spin" aria-hidden="true"></i> Menyimpan & mengunggah…</span>
                        </button>
                    </x-ui.card>

                    <x-ui.card>
                        <h2 class="mb-4 text-lg font-bold">Jenis &amp; kategori</h2>
                        <fieldset>
                            <legend class="label">Jenis produk</legend>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach (['digital' => ['Digital', 'fa-bolt'], 'physical' => ['Fisik', 'fa-box']] as $value => [$label, $icon])
                                    <label class="block cursor-pointer">
                                        <input type="radio" name="type" value="{{ $value }}" x-model="type" class="peer sr-only">
                                        <span class="flex items-center justify-center gap-2 rounded-lg border-2 border-line p-3 text-small font-bold peer-checked:border-edge peer-checked:bg-sun-100 dark:peer-checked:bg-surface-2"><i class="fa-solid {{ $icon }}" aria-hidden="true"></i> {{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @if (! $settings->shop_physical_enabled)
                                <p class="hint" x-show="type === 'physical'" x-cloak>Produk fisik baru tampil setelah diaktifkan di Pengaturan toko.</p>
                            @endif
                        </fieldset>
                        <div class="mt-4">
                            <x-ui.select name="product_category_id" label="Kategori">
                                <option value="">Tanpa kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('product_category_id', $product->product_category_id) === $category->id)>{{ $category->name_id }}</option>
                                @endforeach
                            </x-ui.select>
                            <a href="{{ route('dashboard.shop.categories.index') }}" class="mt-1 inline-block text-caption font-semibold text-brand-text hover:underline">Kelola kategori</a>
                        </div>
                    </x-ui.card>

                    <x-ui.card x-show="type === 'digital'">
                        <h2 class="mb-4 text-lg font-bold">Pengiriman digital</h2>
                        <fieldset class="space-y-2">
                            <legend class="sr-only">Cara pengiriman</legend>
                            @foreach (['file' => ['Unduhan file', 'File diunduh dari akun pembeli'], 'license_key' => ['Kunci lisensi', 'Satu kunci per unit dari stok kunci'], 'link' => ['Link akses', 'Mis. kelas online, Notion, Google Drive']] as $value => [$label, $desc])
                                <label class="block cursor-pointer">
                                    <input type="radio" name="digital_delivery" value="{{ $value }}" x-model="delivery" class="peer sr-only">
                                    <span class="block rounded-lg border-2 border-line p-3 peer-checked:border-edge peer-checked:bg-sun-100 dark:peer-checked:bg-surface-2"><span class="block text-small font-bold">{{ $label }}</span><span class="block text-caption text-muted">{{ $desc }}</span></span>
                                </label>
                            @endforeach
                        </fieldset>
                        <div class="mt-4" x-show="delivery === 'link'">
                            <x-ui.input name="access_url" type="url" label="URL akses" placeholder="https://" :hint="$product->access_url ? 'Tersimpan. Kosongkan untuk tetap memakai link yang ada.' : 'Hanya terlihat oleh pembeli setelah lunas.'" />
                        </div>
                        <div class="mt-4 space-y-3" x-show="delivery !== 'link'">
                            <label class="flex items-center gap-2 text-small"><input type="checkbox" name="unlimited_downloads" value="1" x-model="unlimited" class="h-5 w-5 rounded border-line text-brand-600"> Unduhan tanpa batas</label>
                            <div x-show="!unlimited"><x-ui.input name="max_downloads" type="number" min="1" label="Batas unduhan per pembelian" :value="$product->max_downloads ?? 5" hint="Setiap file yang diunduh dihitung satu. Untuk banyak file, unggah satu ZIP." /></div>
                            <x-ui.input name="download_expiry_days" type="number" min="1" label="Masa berlaku akses (hari)" :value="$product->download_expiry_days" hint="Kosong = selamanya." />
                        </div>
                    </x-ui.card>

                    <x-ui.card>
                        <h2 class="mb-4 text-lg font-bold">SEO</h2>
                        <div class="space-y-3">
                            <x-ui.input name="slug" label="Slug URL" :value="$product->slug" hint="Kosong = dibuat dari nama." />
                            <x-ui.input name="meta_title" label="Meta title" :value="$product->meta_title" />
                            <x-ui.textarea name="meta_description" label="Meta description" rows="3" :value="$product->meta_description" />
                        </div>
                    </x-ui.card>
                </div>
            </div>
        </form>

        @if ($editing && $product->isDigital())
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="min-w-0 space-y-6 lg:col-span-2">
                    @include('dashboard.shop.products._digital-assets')
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            /* TEMP-DEBUG: hapus setelah masalah "simpan tidak terjadi apa-apa" selesai */
            (function () {
                const name = (x) => x ? x.tagName + (x.id ? '#' + x.id : '') + '.' + String(x.className).slice(0, 50) : String(x);
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('#product-form button[type=submit]')) return;
                    console.log('[dbg] klik tombol simpan. target =', name(e.target), '| form =', e.target.closest('button').form ? e.target.closest('button').form.id : 'TIDAK ADA FORM');
                    console.log('[dbg] elemen di titik klik =', document.elementsFromPoint(e.clientX, e.clientY).slice(0, 6).map(name));
                }, true);
                const isSaveClick = (ev) => ev.type === 'click' && ev.target && ev.target.closest && ev.target.closest('#product-form button[type=submit]');
                const nativePrevent = Event.prototype.preventDefault;
                Event.prototype.preventDefault = function () {
                    if (isSaveClick(this)) console.log('[dbg] PENYEBAB: preventDefault() dipanggil pada klik tombol simpan oleh:\n' + new Error().stack);
                    return nativePrevent.apply(this, arguments);
                };
                window.addEventListener('click', (e) => { if (isSaveClick(e)) { const f = document.getElementById('product-form'); console.log('[dbg] setelah klik: dibatalkan =', e.defaultPrevented, '| checkValidity =', f.checkValidity(), '| noValidate =', f.noValidate, '| tombol disabled =', e.target.closest('button').disabled, '| jumlah #product-form =', document.querySelectorAll('#product-form').length); } });
                document.addEventListener('submit', (e) => console.log('[dbg] submit event, dibatalkan =', e.defaultPrevented, 'form =', e.target.id), true);
                document.addEventListener('invalid', (e) => console.log('[dbg] kolom tidak valid:', e.target.name, '-', e.target.validationMessage, '| terlihat =', e.target.offsetParent !== null), true);
                window.addEventListener('error', (e) => console.log('[dbg] JS error:', e.message));
                console.log('[dbg] pencatat aktif, halaman edit produk dimuat');
            })();

            document.addEventListener('alpine:init', () => {
                Alpine.data('productForm', (state) => ({
                    ...state,
                    bulk: { price: null, stock: '' },
                    pickedFiles: [],
                    saving: false,
                    init() {
                        window.addEventListener('pageshow', () => { this.saving = false; });
                        for (const key in this.rows) { this.rows[key].is_active = !!this.rows[key].is_active; }
                    },
                    get combos() {
                        const lists = this.options.map(o => o.values.map(v => (v.value_id || '').trim()).filter(Boolean));
                        if (!lists.length || lists.some(l => !l.length)) return [];
                        return lists.reduce((acc, list) => acc.flatMap(prefix => list.map(v => prefix === null ? v : prefix + '|' + v)), [null]);
                    },
                    row(key) {
                        if (!this.rows[key]) this.rows[key] = { id: '', price: '', compare_at_price: '', stock: '', weight_grams: '', sku: '', image_id: '', is_active: true };
                        return this.rows[key];
                    },
                    applyBulk() {
                        this.combos.forEach(key => {
                            if (this.bulk.price !== null && this.bulk.price !== '') this.row(key).price = this.bulk.price;
                            this.row(key).stock = this.bulk.stock;
                        });
                    },
                    moveImage(from, to) { const [m] = this.images.splice(from, 1); this.images.splice(to, 0, m); },
                    newImages: [],
                    newFiles: [],
                    setThumbnail(value) {
                        this.thumbnail = value;
                        // Keep the gallery order in line: a stored thumbnail moves to the front.
                        const at = this.images.findIndex(i => i.id === value);
                        if (at > 0) this.moveImage(at, 0);
                    },
                    pickImages(event) {
                        const picked = Array.from(event.target.files).filter(f => f.type.startsWith('image/'));
                        this.newFiles = this.newFiles.concat(picked).slice(0, 10);
                        this.syncNewImages();
                    },
                    dropNewImage(index) {
                        this.newFiles.splice(index, 1);
                        if (this.thumbnail === 'new:' + index) this.thumbnail = this.images[0]?.id ?? (this.newFiles.length ? 'new:0' : null);
                        else if (typeof this.thumbnail === 'string' && this.thumbnail.startsWith('new:') && Number(this.thumbnail.slice(4)) > index) this.thumbnail = 'new:' + (Number(this.thumbnail.slice(4)) - 1);
                        this.syncNewImages();
                    },
                    // The file input is the single source sent to the server, so it is rebuilt from newFiles.
                    syncNewImages() {
                        const transfer = new DataTransfer();
                        this.newFiles.forEach(f => transfer.items.add(f));
                        this.$refs.imageInput.files = transfer.files;
                        this.newImages.forEach(i => URL.revokeObjectURL(i.url));
                        this.newImages = this.newFiles.map((f, n) => ({ key: n + ':' + f.name + ':' + f.size, name: f.name, url: URL.createObjectURL(f) }));
                        if (!this.thumbnail && this.newImages.length) this.thumbnail = 'new:0';
                    },
                    removeImage(id) {
                        if (!confirm('Hapus gambar ini saat disimpan?')) return;
                        this.images = this.images.filter(i => i.id !== id);
                        this.deleted.push(id);
                        if (this.thumbnail === id) this.thumbnail = this.images[0]?.id ?? (this.newImages.length ? 'new:0' : null);
                    },
                    // Copies the editors' content into their textareas and locks the button; false = already saving.
                    prepare() {
                        if (this.saving) return false;
                        // A failing editor must never block saving.
                        try {
                            if (window.jQuery && jQuery.fn.summernote) {
                                jQuery('.summernote').each(function () { jQuery(this).val(jQuery(this).summernote('code')); });
                            }
                        } catch (e) { console.error('Summernote sync failed', e); }
                        this.saving = true;
                        window.toast?.('info', 'Menyimpan perubahan…', 6000);
                        // If the browser never navigates (blocked or aborted), unlock the button again.
                        setTimeout(() => { this.saving = false; }, 20000);
                        return true;
                    },
                    // Enter key inside a field: the browser's own submit.
                    beforeSubmit(event) {
                        if (!this.prepare()) event.preventDefault();
                    },
                    // Save button. Submits via form.submit() so the save does not depend on the browser dispatching a
                    // submit event, which some browser extensions and privacy features swallow.
                    send() {
                        const form = this.$root;
                        if (this.saving || !form.reportValidity()) return;
                        this.prepare();
                        console.log('[dbg] form.submit() dipanggil');
                        HTMLFormElement.prototype.submit.call(form);
                    },
                    // The browser silently refuses to submit when an invalid field is hidden (other tab, other section); say which one.
                    reportInvalid(event) {
                        const field = event.target;
                        if (field.offsetParent !== null) return;
                        const label = field.name || field.id || 'tidak diketahui';
                        window.toast?.('error', 'Ada isian yang belum valid dan sedang tersembunyi: ' + label + '. Periksa tab bahasa atau bagian varian.', 8000);
                        if (field.name && /_en$/.test(field.name)) this.$dispatch('show-lang', 'en');
                    },
                }));
            });

            $(function () {
                $('.summernote').summernote({
                    height: 260,
                    dialogsInBody: true,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video', 'hr']],
                        ['view', ['codeview']]
                    ],
                    callbacks: {
                        onImageUpload: function (files) {
                            const editor = $(this);
                            const data = new FormData();
                            data.append('image', files[0]);
                            $.ajax({
                                url: '{{ route('dashboard.blogs.upload_image') }}', type: 'POST', data, cache: false, contentType: false, processData: false,
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                success: (res) => { if (res.url) editor.summernote('insertImage', res.url); },
                                error: () => window.Swal && Swal.fire({ icon: 'error', title: 'Gagal mengunggah gambar' }),
                            });
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
