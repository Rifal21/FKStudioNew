@php $variants = $product->variants->where('is_default', false); @endphp

@if ($product->digital_delivery === 'file')
    <x-ui.card id="files">
        <h2 class="text-lg font-bold">File produk</h2>
        <p class="mt-1 text-small text-muted">Disimpan di penyimpanan privat, hanya bisa diunduh pembeli lewat link sementara. File "semua varian" didapat setiap pembeli; file khusus varian hanya untuk pembeli varian itu.</p>

        @if ($product->files->isEmpty())
            <p class="mt-4 rounded-lg border-2 border-dashed border-danger-500/50 p-4 text-small font-semibold text-danger-700 dark:text-red-300"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Belum ada file. Pembeli tidak akan mendapat apa pun sampai file diunggah.</p>
        @else
            <ul class="mt-4 divide-y divide-line rounded-lg border-2 border-line">
                @foreach ($product->files as $file)
                    <li class="flex flex-wrap items-center justify-between gap-3 p-3">
                        <div class="min-w-0 text-small">
                            <p class="truncate font-semibold">{{ $file->original_name }}</p>
                            <p class="text-muted">{{ $file->humanSize() }} · {{ $file->variant ? 'Varian '.$file->variant->title_id : 'Semua varian' }}{{ $file->version_label ? ' · '.$file->version_label : '' }} · {{ $file->created_at->format('d/m/Y') }}</p>
                        </div>
                        <form method="POST" action="{{ route('dashboard.shop.products.files.destroy', [$product, $file]) }}" x-data @submit="if (!confirm('Hapus file ini? Pembeli tidak bisa mengunduhnya lagi.')) $event.preventDefault()">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-icon text-danger-700 dark:text-red-300" aria-label="Hapus {{ $file->original_name }}"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('dashboard.shop.products.files.store', $product) }}" enctype="multipart/form-data" class="mt-5 grid gap-3 md:grid-cols-3 md:items-end" x-data="{ busy: false }" @submit="busy = true">
            @csrf
            <div class="md:col-span-3">
                <label for="file-upload" class="label">Unggah file <span class="text-danger-600">*</span></label>
                <input id="file-upload" type="file" name="file" required class="block w-full text-small file:mr-3 file:rounded-md file:border-2 file:border-edge file:bg-sun-400 file:px-3 file:py-2 file:font-bold file:text-navy">
                <p class="hint">ZIP, PDF, EPUB, PSD, FIG, gambar, video, dll. Maks. 500 MB (tergantung batas unggah server).</p>
            </div>
            @if ($variants->isNotEmpty())
                <x-ui.select name="product_variant_id" label="Untuk varian">
                    <option value="">Semua varian</option>
                    @foreach ($variants as $variant)<option value="{{ $variant->id }}">{{ $variant->title_id }}</option>@endforeach
                </x-ui.select>
            @endif
            <x-ui.input name="version_label" label="Label versi (opsional)" placeholder="v1.2" />
            <button type="submit" class="btn-primary" :disabled="busy"><span x-show="!busy"><i class="fa-solid fa-upload" aria-hidden="true"></i> Unggah</span><span x-show="busy" x-cloak><i class="fa-solid fa-spinner animate-spin" aria-hidden="true"></i> Mengunggah…</span></button>
        </form>
    </x-ui.card>
@endif

@if ($product->digital_delivery === 'license_key')
    <x-ui.card id="keys">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-bold">Kunci lisensi</h2>
            <div class="flex gap-2">
                <x-ui.badge :variant="$keyStats['available'] < 5 ? 'danger' : 'success'">{{ $keyStats['available'] }} tersedia</x-ui.badge>
                <x-ui.badge variant="neutral">{{ $keyStats['assigned'] }} terpakai</x-ui.badge>
            </div>
        </div>
        <p class="mt-1 text-small text-muted">Satu kunci diberikan untuk setiap unit yang dibeli. Kunci disimpan terenkripsi. Bila stok kunci habis, pesanan ditandai "perlu tindakan".</p>

        <form method="POST" action="{{ route('dashboard.shop.products.keys.store', $product) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
            @csrf
            <x-ui.textarea name="keys" label="Tempel kunci (satu per baris)" rows="4" placeholder="AAAA-BBBB-CCCC-DDDD" />
            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label for="keys-file" class="label">Atau unggah .txt / .csv</label>
                    <input id="keys-file" type="file" name="keys_file" accept=".txt,.csv" class="block w-full text-small file:mr-3 file:rounded-md file:border-2 file:border-edge file:bg-surface-2 file:px-3 file:py-2 file:font-bold">
                    <p class="hint">Kolom pertama tiap baris dipakai sebagai kunci.</p>
                </div>
                @if ($variants->isNotEmpty())
                    <x-ui.select name="product_variant_id" label="Untuk varian">
                        <option value="">Semua varian</option>
                        @foreach ($variants as $variant)<option value="{{ $variant->id }}">{{ $variant->title_id }}</option>@endforeach
                    </x-ui.select>
                @endif
            </div>
            <button type="submit" class="btn-primary"><i class="fa-solid fa-plus" aria-hidden="true"></i> Tambah kunci</button>
        </form>

        @if ($keys->isNotEmpty())
            <div class="mt-5 overflow-x-auto">
                <table class="table-base">
                    <thead><tr><th>Kunci</th><th>Varian</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($keys as $key)
                            <tr>
                                <td class="font-mono text-small">{{ $key->masked() }}</td>
                                <td class="text-small">{{ $key->variant?->title_id ?? 'Semua' }}</td>
                                <td>@if ($key->assigned_at)<x-ui.badge variant="neutral">Terpakai {{ $key->assigned_at->format('d/m/Y') }}</x-ui.badge>@else<x-ui.badge variant="success">Tersedia</x-ui.badge>@endif</td>
                                <td class="text-right">
                                    @unless ($key->assigned_at)
                                        <form method="POST" action="{{ route('dashboard.shop.products.keys.destroy', [$product, $key]) }}" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-icon text-danger-700 dark:text-red-300" aria-label="Hapus kunci"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($keys->count() >= 200)<p class="mt-2 text-caption text-muted">Menampilkan 200 kunci terbaru.</p>@endif
            </div>
        @endif
    </x-ui.card>
@endif

@if ($product->digital_delivery === 'link')
    <x-ui.card>
        <h2 class="text-lg font-bold">Link akses</h2>
        <p class="mt-1 text-small text-muted">{{ $product->access_url ? 'Link tersimpan (terenkripsi) dan tampil di halaman "Produk Digital Saya" pembeli setelah lunas.' : 'Belum ada link. Isi "URL akses" di kolom Pengiriman digital lalu simpan.' }}</p>
    </x-ui.card>
@endif
