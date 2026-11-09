<x-app-layout>
    <x-slot name="header">Kategori Toko</x-slot>

    <div class="mx-auto max-w-5xl space-y-6" x-data="{ editing: null }">
        <x-ui.page-header title="Kategori" description="Mengelompokkan produk di halaman toko. Kategori tanpa produk aktif tidak ditampilkan." />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
            <div class="lg:col-span-3">
                @if ($categories->isEmpty())
                    <div class="card"><x-ui.empty-state icon="fa-folder-tree" title="Belum ada kategori" text="Contoh: Template Website, E-book, Aset Desain, Merchandise." /></div>
                @else
                    <div class="table-wrap">
                        <table class="table-base">
                            <thead><tr><th scope="col">Nama</th><th scope="col">Produk</th><th scope="col">Status</th><th scope="col" class="text-right">Aksi</th></tr></thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td><span class="font-bold">{{ $category->name_id }}</span><p class="text-caption text-muted">{{ $category->name_en }} · /{{ $category->slug }}</p></td>
                                        <td>{{ $category->products_count }}</td>
                                        <td><x-ui.badge :variant="$category->is_active ? 'success' : 'neutral'" dot>{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></td>
                                        <td>
                                            <div class="flex justify-end gap-1">
                                                <button type="button" class="btn-icon" @click="editing = @js($category->only(['id', 'name_id', 'name_en', 'slug', 'description_id', 'description_en', 'sort_order', 'is_active']))" aria-label="Ubah {{ $category->name_id }}"><i class="fa-solid fa-pencil" aria-hidden="true"></i></button>
                                                <form method="POST" action="{{ route('dashboard.shop.categories.destroy', $category) }}" x-data @submit="if (!confirm('Hapus kategori? Produknya tetap ada tanpa kategori.')) $event.preventDefault()">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-icon text-danger-700 dark:text-red-300" aria-label="Hapus {{ $category->name_id }}"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-2">
                <x-ui.card>
                    <h2 class="text-lg font-bold" x-text="editing ? 'Ubah kategori' : 'Kategori baru'"></h2>
                    <form method="POST" :action="editing ? '{{ url('/dashboard/shop/categories') }}/' + editing.id : '{{ route('dashboard.shop.categories.store') }}'" class="mt-4 space-y-4">
                        @csrf
                        <template x-if="editing"><input type="hidden" name="_method" value="PATCH"></template>
                        <div><label class="label" for="cat-name-id">Nama (ID) <span class="text-danger-600">*</span></label><input id="cat-name-id" name="name_id" class="input" required :value="editing ? editing.name_id : ''"></div>
                        <div><label class="label" for="cat-name-en">Nama (EN)</label><input id="cat-name-en" name="name_en" class="input" :value="editing ? editing.name_en : ''"></div>
                        <div><label class="label" for="cat-slug">Slug URL</label><input id="cat-slug" name="slug" class="input" :value="editing ? editing.slug : ''"><p class="hint">Kosongkan untuk dibuat otomatis.</p></div>
                        <div><label class="label" for="cat-desc-id">Deskripsi (ID)</label><textarea id="cat-desc-id" name="description_id" rows="2" class="input" x-text="editing ? editing.description_id : ''"></textarea></div>
                        <div><label class="label" for="cat-desc-en">Deskripsi (EN)</label><textarea id="cat-desc-en" name="description_en" rows="2" class="input" x-text="editing ? editing.description_en : ''"></textarea></div>
                        <div><label class="label" for="cat-sort">Urutan</label><input id="cat-sort" type="number" min="0" name="sort_order" class="input" :value="editing ? editing.sort_order : 0"></div>
                        <label class="flex items-center gap-2 text-small"><input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-line text-brand-600" :checked="editing ? editing.is_active : true"> Aktif</label>
                        @if ($errors->any())<p class="field-error">{{ $errors->first() }}</p>@endif
                        <div class="flex gap-2">
                            <button type="submit" class="btn-primary">Simpan</button>
                            <button type="button" class="btn-secondary" x-show="editing" x-cloak @click="editing = null">Batal</button>
                        </div>
                    </form>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>
