<x-app-layout>
    <x-slot name="header">Paket layanan</x-slot>

    <div class="mx-auto max-w-7xl space-y-6" x-data="{
        open: false,
        editMode: false,
        currentPackage: {},
        features_id_raw: '',
        features_en_raw: '',
        openCreate() {
            this.editMode = false;
            this.currentPackage = {};
            this.features_id_raw = '';
            this.features_en_raw = '';
            this.open = true;
        },
        initEdit(packageData) {
            this.currentPackage = { ...packageData };
            this.editMode = true;
            this.features_id_raw = (this.currentPackage.features_id || []).join('\n');
            this.features_en_raw = (this.currentPackage.features_en || []).join('\n');
            this.open = true;
        },
        close() {
            this.open = false;
        }
    }" @keydown.escape.window="close()">
        <x-ui.page-header title="Paket layanan" description="Atur paket harga yang ditawarkan kepada calon klien.">
            <x-slot:actions>
                <x-ui.button icon="fa-plus" @click="openCreate()">Tambah paket</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        @if ($errors->any())
            <div class="rounded-lg border border-danger-500/40 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert">
                <p class="font-semibold">Paket belum tersimpan. Periksa isian berikut:</p>
                <ul class="mt-1 list-disc space-y-0.5 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (count($packages) > 0)
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($packages as $package)
                    @php
                        $features = $package->features_id ?? [];
                    @endphp
                    <x-ui.card hover :class="$package->is_featured ? 'flex flex-col border-brand-500' : 'flex flex-col'" data-aos="fade-up" data-aos-delay="{{ min($loop->index, 6) * 50 }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-xl font-bold leading-tight">{{ $package->name_id }}</h3>
                                    @if ($package->is_featured)
                                        <x-ui.badge variant="brand"><i class="fa-solid fa-star" aria-hidden="true"></i> Populer</x-ui.badge>
                                    @endif
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center">
                                <button type="button" @click="initEdit({{ $package->toJson() }})" class="btn-icon" aria-label="Ubah paket {{ $package->name_id }}">
                                    <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                </button>
                                <form action="{{ route('dashboard.packages.destroy', $package->id) }}" method="POST"
                                    data-confirm="Paket ini akan dihapus permanen." data-confirm-title="Hapus paket?" data-confirm-ok="Ya, hapus">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon hover:bg-danger-50 hover:text-danger-700 dark:hover:bg-danger-500/15 dark:hover:text-red-300" aria-label="Hapus paket {{ $package->name_id }}">
                                        <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <p class="mt-2 text-2xl font-extrabold text-brand-text">{{ $package->price }}</p>

                        <div class="mt-4 flex-1 space-y-4">
                            <p class="line-clamp-2 text-small text-muted">{{ $package->description_id }}</p>

                            <ul class="space-y-2.5">
                                @foreach (array_slice($features, 0, 4) as $feature)
                                    <li class="flex items-start gap-2.5 text-small">
                                        <i class="fa-solid fa-check mt-1 text-caption text-brand-text" aria-hidden="true"></i>
                                        <span class="min-w-0">{{ $feature }}</span>
                                    </li>
                                @endforeach
                                @if (count($features) > 4)
                                    <li class="pl-6 text-caption font-semibold text-muted">+ {{ count($features) - 4 }} fitur lainnya</li>
                                @endif
                            </ul>
                        </div>

                        <div class="divider mt-5"></div>
                        <div class="mt-4 flex items-center justify-between gap-3">
                            <span class="text-caption text-muted">Urutan: {{ $package->order }}</span>
                            <x-ui.badge variant="success" dot>Aktif</x-ui.badge>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @else
            <x-ui.card :padded="false">
                <x-ui.empty-state icon="fa-tags" title="Belum ada paket" text="Buat paket pertama agar calon klien bisa memesan.">
                    <x-ui.button icon="fa-plus" @click="openCreate()">Tambah paket</x-ui.button>
                </x-ui.empty-state>
            </x-ui.card>
        @endif

        {{-- Modal tambah / ubah paket --}}
        <div x-show="open" x-cloak x-transition.opacity.duration.150ms
            class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
            role="dialog" aria-modal="true" aria-labelledby="package-modal-title"
            x-effect="if (open) $nextTick(() => $refs.firstField && $refs.firstField.focus())">
            <div class="absolute inset-0 bg-ink/50 backdrop-blur-sm" @click="close()" aria-hidden="true"></div>

            <div class="card relative max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-b-none sm:rounded-lg">
                <div class="sticky top-0 z-10 flex items-center justify-between gap-4 border-b border-line bg-surface px-5 py-4 md:px-6">
                    <h2 id="package-modal-title" class="text-xl font-bold" x-text="editMode ? 'Ubah paket' : 'Tambah paket'"></h2>
                    <button type="button" class="btn-icon -mr-2" @click="close()" aria-label="Tutup"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                </div>

                <form :action="editMode ? '{{ route('dashboard.packages.index') }}/' + currentPackage.id : '{{ route('dashboard.packages.store') }}'"
                    method="POST" class="space-y-6 p-5 md:p-6">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PATCH">
                    </template>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="pkg-name-id" class="label">Nama paket (Indonesia)</label>
                            <input type="text" id="pkg-name-id" name="name_id" required x-model="currentPackage.name_id" x-ref="firstField" class="input">
                        </div>
                        <div>
                            <label for="pkg-name-en" class="label">Nama paket (Inggris)</label>
                            <input type="text" id="pkg-name-en" name="name_en" required x-model="currentPackage.name_en" class="input">
                        </div>

                        <div>
                            <label for="pkg-price" class="label">Label harga</label>
                            <input type="text" id="pkg-price" name="price" required x-model="currentPackage.price" placeholder="Rp 5.000.000" class="input">
                            <p class="hint">Teks harga yang ditampilkan, misalnya Rp 5.000.000.</p>
                        </div>
                        <div>
                            <label for="pkg-order" class="label">Urutan tampil</label>
                            <input type="number" id="pkg-order" name="order" x-model="currentPackage.order" class="input">
                            <p class="hint">Angka kecil tampil lebih dulu.</p>
                        </div>

                        <div>
                            <label for="pkg-desc-id" class="label">Deskripsi (Indonesia)</label>
                            <textarea id="pkg-desc-id" name="description_id" rows="3" x-model="currentPackage.description_id" class="textarea"></textarea>
                        </div>
                        <div>
                            <label for="pkg-desc-en" class="label">Deskripsi (Inggris)</label>
                            <textarea id="pkg-desc-en" name="description_en" rows="3" x-model="currentPackage.description_en" class="textarea"></textarea>
                        </div>

                        <div>
                            <label for="pkg-features-id" class="label">Fitur (Indonesia)</label>
                            <textarea id="pkg-features-id" name="features_id_raw" rows="5" x-model="features_id_raw" placeholder="Fitur 1&#10;Fitur 2&#10;Fitur 3" class="textarea"></textarea>
                            <p class="hint">Satu fitur per baris.</p>
                        </div>
                        <div>
                            <label for="pkg-features-en" class="label">Fitur (Inggris)</label>
                            <textarea id="pkg-features-en" name="features_en_raw" rows="5" x-model="features_en_raw" placeholder="Feature 1&#10;Feature 2&#10;Feature 3" class="textarea"></textarea>
                            <p class="hint">Satu fitur per baris.</p>
                        </div>

                        <div>
                            <label for="pkg-cta-id" class="label">Teks tombol (Indonesia)</label>
                            <input type="text" id="pkg-cta-id" name="cta_text_id" x-model="currentPackage.cta_text_id" class="input">
                        </div>
                        <div>
                            <label for="pkg-cta-en" class="label">Teks tombol (Inggris)</label>
                            <input type="text" id="pkg-cta-en" name="cta_text_en" x-model="currentPackage.cta_text_en" class="input">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 items-start gap-5 rounded-lg bg-surface-2 p-4 md:grid-cols-2 md:p-5">
                        <div>
                            <label for="pkg-cta-link" class="label">Tautan kustom (opsional)</label>
                            <input type="text" id="pkg-cta-link" name="cta_link" x-model="currentPackage.cta_link" placeholder="https://wa.me/..." class="input">
                            <p class="hint">Kosongkan jika tombol paket diarahkan ke halaman checkout.</p>
                        </div>
                        <label class="flex min-h-[44px] cursor-pointer items-center gap-3 md:mt-7">
                            <input type="checkbox" name="is_featured" value="1" x-model="currentPackage.is_featured" class="h-5 w-5 rounded border-line bg-surface text-brand-600 focus:ring-brand-500">
                            <span class="text-small font-semibold">Tandai sebagai paket populer</span>
                        </label>
                    </div>

                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <x-ui.button variant="secondary" @click="close()">Batal</x-ui.button>
                        <x-ui.button type="submit" icon="fa-check"><span x-text="editMode ? 'Simpan perubahan' : 'Simpan paket'"></span></x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
