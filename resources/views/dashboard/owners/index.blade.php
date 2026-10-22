<x-app-layout>
    <x-slot name="header">Tim</x-slot>

    <div class="mx-auto max-w-7xl space-y-6" x-data="{ openAdd: false }" @keydown.escape.window="openAdd = false">

        <x-ui.page-header title="Tim & Pimpinan" description="Anggota tim yang tampil di halaman publik beserta peran dan tautan media sosialnya.">
            <x-slot:actions>
                <x-ui.button variant="primary" icon="fa-user-plus" @click="openAdd = true">Tambah anggota</x-ui.button>
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

        @if (count($owners) > 0)
            <p class="text-small text-muted">{{ count($owners) }} anggota tim</p>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($owners as $owner)
                    <x-ui.card class="flex flex-col items-center text-center" hover>
                        <img src="{{ $owner->media_url }}" alt="{{ $owner->name }}" class="h-24 w-24 rounded-full object-cover ring-4 ring-brand-soft">

                        <h3 class="mt-4 text-lg font-bold">{{ $owner->name }}</h3>
                        <p class="text-small font-semibold text-brand-text">{{ $owner->getTranslation('role') }}</p>

                        @if ($owner->getTranslation('bio'))
                            <p class="mt-3 line-clamp-3 text-small text-muted">{{ $owner->getTranslation('bio') }}</p>
                        @endif

                        <div class="mt-4 flex flex-1 items-start gap-2">
                            @if ($owner->instagram_url)
                                <a href="{{ $owner->instagram_url }}" target="_blank" rel="noopener" class="btn-icon h-11 w-11 border border-line hover:border-brand-300" aria-label="Instagram {{ $owner->name }}">
                                    <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                                </a>
                            @endif
                            @if ($owner->linkedin_url)
                                <a href="{{ $owner->linkedin_url }}" target="_blank" rel="noopener" class="btn-icon h-11 w-11 border border-line hover:border-brand-300" aria-label="LinkedIn {{ $owner->name }}">
                                    <i class="fa-brands fa-linkedin" aria-hidden="true"></i>
                                </a>
                            @endif
                        </div>

                        <div class="mt-4 w-full border-t border-line pt-4">
                            <form action="{{ route('dashboard.owners.destroy', $owner->id) }}" method="POST"
                                data-confirm="{{ $owner->name }} akan dihapus dari daftar tim." data-confirm-title="Hapus anggota tim?" data-confirm-ok="Ya, hapus">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger-soft w-full">
                                    <i class="fa-solid fa-trash-can" aria-hidden="true"></i> Hapus anggota
                                </button>
                            </form>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @else
            <x-ui.card :padded="false">
                <x-ui.empty-state icon="fa-users" title="Belum ada anggota tim" text="Tambahkan anggota tim agar pengunjung mengenal orang-orang di balik FKStudio.">
                    <x-ui.button variant="primary" icon="fa-user-plus" @click="openAdd = true">Tambah anggota</x-ui.button>
                </x-ui.empty-state>
            </x-ui.card>
        @endif

        {{-- Modal: tambah anggota --}}
        <div x-show="openAdd" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="add-owner-title">
            <div class="absolute inset-0 bg-ink/50 backdrop-blur-sm" x-show="openAdd"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                @click="openAdd = false"></div>
            <div class="card relative max-h-[90vh] w-full max-w-3xl overflow-y-auto custom-scrollbar" x-show="openAdd"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="sticky top-0 z-10 flex items-center justify-between gap-4 border-b border-line bg-surface px-5 py-4 md:px-6">
                    <h2 id="add-owner-title" class="text-lg font-bold">Tambah anggota tim</h2>
                    <button type="button" class="btn-icon" @click="openAdd = false" aria-label="Tutup">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>

                <form action="{{ route('dashboard.owners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-5 md:p-6"
                    x-data="{
                        preview: null,
                        handleFileChange(e) {
                            const file = e.target.files[0];
                            if (file) {
                                this.preview = URL.createObjectURL(file);
                            }
                        }
                    }">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <x-ui.input id="add-owner-name" name="name" label="Nama lengkap" :required="true" />
                        <div>
                            <label for="add-owner-image" class="label">Foto profil <span class="text-danger-600" aria-hidden="true">*</span></label>
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-surface-2 text-muted">
                                    <template x-if="preview"><img :src="preview" alt="" class="h-full w-full object-cover"></template>
                                    <template x-if="!preview"><i class="fa-solid fa-user" aria-hidden="true"></i></template>
                                </div>
                                <input id="add-owner-image" type="file" name="image" required accept="image/*" @change="handleFileChange"
                                    class="input min-w-0 flex-1 text-small file:mr-3 file:rounded-sm file:border-0 file:bg-brand-soft file:px-3 file:py-1 file:text-caption file:font-semibold file:text-brand-text">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="space-y-4 rounded-lg border border-line bg-surface-2 p-4">
                            <span class="badge-brand"><i class="fa-solid fa-flag" aria-hidden="true"></i> Bahasa Indonesia (ID)</span>
                            <x-ui.input id="add-owner-role-id" name="role_id" label="Jabatan (ID)" placeholder="Contoh: Direktur Utama" :required="true" />
                            <x-ui.textarea id="add-owner-bio-id" name="bio_id" label="Bio (ID)" rows="3" />
                        </div>
                        <div class="space-y-4 rounded-lg border border-line bg-surface-2 p-4">
                            <span class="badge-info"><i class="fa-solid fa-earth-americas" aria-hidden="true"></i> Bahasa Inggris (EN)</span>
                            <x-ui.input id="add-owner-role-en" name="role_en" label="Jabatan (EN)" placeholder="Example: Chief Executive Officer" :required="true" />
                            <x-ui.textarea id="add-owner-bio-en" name="bio_en" label="Bio (EN)" rows="3" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="add-owner-instagram" class="label">Profil Instagram</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-pink-600"><i class="fa-brands fa-instagram" aria-hidden="true"></i></span>
                                <input id="add-owner-instagram" type="url" name="instagram_url" value="{{ old('instagram_url') }}" placeholder="https://instagram.com/username" class="input pl-10">
                            </div>
                        </div>
                        <div>
                            <label for="add-owner-linkedin" class="label">Profil LinkedIn</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-sky-800"><i class="fa-brands fa-linkedin" aria-hidden="true"></i></span>
                                <input id="add-owner-linkedin" type="url" name="linkedin_url" value="{{ old('linkedin_url') }}" placeholder="https://linkedin.com/in/username" class="input pl-10">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-line pt-4 sm:flex-row sm:justify-end">
                        <button type="button" class="btn-secondary" @click="openAdd = false">Batal</button>
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-check" aria-hidden="true"></i> Simpan anggota</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
