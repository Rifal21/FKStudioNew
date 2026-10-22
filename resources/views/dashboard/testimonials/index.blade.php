<x-app-layout>
    <x-slot name="header">Testimoni</x-slot>

    <div class="mx-auto max-w-7xl space-y-6" x-data="{
        openAdd: false,
        openEdit: false,
        newPreview: null,
        newEditPreview: null,
        editData: {
            id: '',
            name: '',
            role_id: '',
            role_en: '',
            content_id: '',
            content_en: '',
            rating: 5,
            action: '',
            avatar_url: ''
        },
        startEdit(tm) {
            this.editData = {
                id: tm.id,
                name: tm.name,
                role_id: tm.role_id,
                role_en: tm.role_en,
                content_id: tm.content_id,
                content_en: tm.content_en,
                rating: tm.rating || 5,
                action: '{{ url('/dashboard/testimonials') }}/' + tm.id,
                avatar_url: tm.media_url
            };
            this.newEditPreview = null;
            this.openEdit = true;
        },
        handleFileChange(e, mode = 'add') {
            const file = e.target.files[0];
            if (file) {
                if (mode === 'add') this.newPreview = URL.createObjectURL(file);
                else this.newEditPreview = URL.createObjectURL(file);
            }
        }
    }" @keydown.escape.window="openAdd = false; openEdit = false">

        <x-ui.page-header title="Testimoni Klien" description="Kelola ulasan klien yang tampil di halaman publik. Setiap testimoni tersedia dalam dua bahasa.">
            <x-slot:actions>
                <x-ui.button variant="primary" icon="fa-plus" @click="openAdd = true">Tambah testimoni</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        @if ($errors->any())
            <div class="card card-pad border-danger-500" role="alert">
                <p class="text-small font-semibold text-danger-600">Data belum dapat disimpan:</p>
                <ul class="mt-1 list-disc space-y-0.5 pl-5 text-small text-danger-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (count($testimonials) > 0)
            <p class="text-small text-muted">{{ count($testimonials) }} testimoni</p>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                @foreach ($testimonials as $tm)
                    <x-ui.card class="flex flex-col" hover>
                        <div class="flex items-start gap-4">
                            @if ($tm->avatar)
                                <img src="{{ $tm->media_url }}" alt="{{ $tm->name }}" class="h-14 w-14 shrink-0 rounded-full object-cover">
                            @else
                                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-brand-soft text-xl font-bold text-brand-text" aria-hidden="true">{{ strtoupper(substr($tm->name, 0, 1)) }}</span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <h3 class="truncate text-lg font-bold">{{ $tm->name }}</h3>
                                <p class="truncate text-small text-muted">{{ $tm->role_id }}</p>
                                <div class="mt-1.5 flex items-center gap-0.5" role="img" aria-label="Rating {{ $tm->rating }} dari 5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star text-small {{ $i <= $tm->rating ? 'text-sun-500' : 'text-line' }}" aria-hidden="true"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <blockquote class="mt-4 flex-1 rounded-md bg-surface-2 p-4 text-small text-muted">
                            <p class="line-clamp-4 italic">"{{ $tm->content_id }}"</p>
                        </blockquote>

                        <div class="mt-4 flex items-center justify-end gap-2 border-t border-line pt-4">
                            <button type="button" class="btn-secondary btn-sm" @click="startEdit(@js($tm->toArray() + ['media_url' => $tm->media_url]))" aria-label="Ubah testimoni {{ $tm->name }}">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i> Ubah
                            </button>
                            <form action="{{ route('dashboard.testimonials.destroy', $tm->id) }}" method="POST"
                                data-confirm="Testimoni dari {{ $tm->name }} akan dihapus permanen." data-confirm-title="Hapus testimoni?" data-confirm-ok="Ya, hapus">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon hover:bg-danger-50 hover:text-danger-700" aria-label="Hapus testimoni {{ $tm->name }}">
                                    <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                </button>
                            </form>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @else
            <x-ui.card :padded="false">
                <x-ui.empty-state icon="fa-comments" title="Belum ada testimoni" text="Tambahkan ulasan dari klien untuk membangun kepercayaan calon pelanggan.">
                    <x-ui.button variant="primary" icon="fa-plus" @click="openAdd = true">Tambah testimoni</x-ui.button>
                </x-ui.empty-state>
            </x-ui.card>
        @endif

        {{-- Modal: tambah testimoni --}}
        <div x-show="openAdd" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="add-title">
            <div class="absolute inset-0 bg-ink/50 backdrop-blur-sm" x-show="openAdd"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                @click="openAdd = false"></div>
            <div class="card relative max-h-[90vh] w-full max-w-3xl overflow-y-auto custom-scrollbar" x-show="openAdd"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="sticky top-0 z-10 flex items-center justify-between gap-4 border-b border-line bg-surface px-5 py-4 md:px-6">
                    <h2 id="add-title" class="text-lg font-bold">Tambah testimoni</h2>
                    <button type="button" class="btn-icon" @click="openAdd = false" aria-label="Tutup">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>

                <form action="{{ route('dashboard.testimonials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-5 md:p-6">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                        <x-ui.input id="add-name" name="name" label="Nama klien" placeholder="Budi Santoso" :required="true" />
                        <div>
                            <label for="add-avatar" class="label">Foto klien</label>
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-surface-2 text-muted">
                                    <template x-if="newPreview"><img :src="newPreview" alt="" class="h-full w-full object-cover"></template>
                                    <template x-if="!newPreview"><i class="fa-solid fa-user" aria-hidden="true"></i></template>
                                </div>
                                <input id="add-avatar" type="file" name="avatar" accept="image/*" @change="handleFileChange($event, 'add')"
                                    class="input min-w-0 flex-1 text-small file:mr-3 file:rounded-sm file:border-0 file:bg-brand-soft file:px-3 file:py-1 file:text-caption file:font-semibold file:text-brand-text">
                            </div>
                        </div>
                        <x-ui.select id="add-rating" name="rating" label="Rating">
                            <option value="5">5 - Sangat baik</option>
                            <option value="4">4 - Baik</option>
                            <option value="3">3 - Cukup</option>
                            <option value="2">2 - Kurang</option>
                            <option value="1">1 - Buruk</option>
                        </x-ui.select>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="space-y-4 rounded-lg border border-line bg-surface-2 p-4">
                            <span class="badge-brand"><i class="fa-solid fa-flag" aria-hidden="true"></i> Bahasa Indonesia (ID)</span>
                            <x-ui.input id="add-role-id" name="role_id" label="Jabatan / perusahaan (ID)" placeholder="Direktur di PT Maju" :required="true" />
                            <x-ui.textarea id="add-content-id" name="content_id" label="Isi testimoni (ID)" rows="4" placeholder="Apa kata klien..." :required="true" />
                        </div>
                        <div class="space-y-4 rounded-lg border border-line bg-surface-2 p-4">
                            <span class="badge-info"><i class="fa-solid fa-earth-americas" aria-hidden="true"></i> Bahasa Inggris (EN)</span>
                            <x-ui.input id="add-role-en" name="role_en" label="Jabatan / perusahaan (EN)" placeholder="CEO at Maju Inc." :required="true" />
                            <x-ui.textarea id="add-content-en" name="content_en" label="Isi testimoni (EN)" rows="4" placeholder="What the client said..." :required="true" />
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-line pt-4 sm:flex-row sm:justify-end">
                        <button type="button" class="btn-secondary" @click="openAdd = false">Batal</button>
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-check" aria-hidden="true"></i> Simpan testimoni</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal: ubah testimoni --}}
        <div x-show="openEdit" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="edit-title">
            <div class="absolute inset-0 bg-ink/50 backdrop-blur-sm" x-show="openEdit"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                @click="openEdit = false"></div>
            <div class="card relative max-h-[90vh] w-full max-w-3xl overflow-y-auto custom-scrollbar" x-show="openEdit"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="sticky top-0 z-10 flex items-center justify-between gap-4 border-b border-line bg-surface px-5 py-4 md:px-6">
                    <div class="min-w-0">
                        <h2 id="edit-title" class="text-lg font-bold">Ubah testimoni</h2>
                        <p class="truncate text-small text-muted" x-text="editData.name"></p>
                    </div>
                    <button type="button" class="btn-icon" @click="openEdit = false" aria-label="Tutup">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>

                <form :action="editData.action" method="POST" enctype="multipart/form-data" class="space-y-6 p-5 md:p-6">
                    @csrf @method('PATCH')

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="edit-name" class="label">Nama klien</label>
                            <input id="edit-name" type="text" name="name" x-model="editData.name" required class="input">
                        </div>
                        <div>
                            <label for="edit-rating" class="label">Rating</label>
                            <select id="edit-rating" name="rating" x-model="editData.rating" class="select">
                                <option value="5">5 - Sangat baik</option>
                                <option value="4">4 - Baik</option>
                                <option value="3">3 - Cukup</option>
                                <option value="2">2 - Kurang</option>
                                <option value="1">1 - Buruk</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="edit-avatar" class="label">Ganti foto (opsional)</label>
                        <div class="flex items-center gap-3">
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full bg-surface-2 text-muted">
                                <template x-if="newEditPreview"><img :src="newEditPreview" alt="" class="h-full w-full object-cover"></template>
                                <template x-if="!newEditPreview && editData.avatar_url"><img :src="editData.avatar_url" alt="" class="h-full w-full object-cover"></template>
                                <template x-if="!newEditPreview && !editData.avatar_url"><i class="fa-solid fa-user" aria-hidden="true"></i></template>
                            </div>
                            <input id="edit-avatar" type="file" name="avatar" accept="image/*" @change="handleFileChange($event, 'edit')"
                                class="input min-w-0 flex-1 text-small file:mr-3 file:rounded-sm file:border-0 file:bg-brand-soft file:px-3 file:py-1 file:text-caption file:font-semibold file:text-brand-text">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="space-y-4 rounded-lg border border-line bg-surface-2 p-4">
                            <span class="badge-brand"><i class="fa-solid fa-flag" aria-hidden="true"></i> Bahasa Indonesia (ID)</span>
                            <div>
                                <label for="edit-role-id" class="label">Jabatan / perusahaan (ID)</label>
                                <input id="edit-role-id" type="text" name="role_id" x-model="editData.role_id" required class="input">
                            </div>
                            <div>
                                <label for="edit-content-id" class="label">Isi testimoni (ID)</label>
                                <textarea id="edit-content-id" name="content_id" x-model="editData.content_id" required rows="4" class="textarea"></textarea>
                            </div>
                        </div>
                        <div class="space-y-4 rounded-lg border border-line bg-surface-2 p-4">
                            <span class="badge-info"><i class="fa-solid fa-earth-americas" aria-hidden="true"></i> Bahasa Inggris (EN)</span>
                            <div>
                                <label for="edit-role-en" class="label">Jabatan / perusahaan (EN)</label>
                                <input id="edit-role-en" type="text" name="role_en" x-model="editData.role_en" required class="input">
                            </div>
                            <div>
                                <label for="edit-content-en" class="label">Isi testimoni (EN)</label>
                                <textarea id="edit-content-en" name="content_en" x-model="editData.content_en" required rows="4" class="textarea"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-line pt-4 sm:flex-row sm:justify-end">
                        <button type="button" class="btn-secondary" @click="openEdit = false">Batal</button>
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-check" aria-hidden="true"></i> Simpan perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
