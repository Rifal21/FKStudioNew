<x-app-layout>
    <x-slot name="header">Bagian Tentang</x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <x-ui.page-header title="Bagian Tentang" description="Kisah brand, visi misi, galeri slider, dan anggota tim yang tampil di halaman depan." />

        @if ($errors->any())
            <div class="rounded-lg border border-danger-500 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-danger-100" role="alert">
                <p class="font-semibold"><i class="fa-solid fa-circle-exclamation mr-1" aria-hidden="true"></i> Periksa kembali isian berikut:</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
            {{-- Kisah brand --}}
            <x-ui.card class="lg:col-span-2">
                <h2 class="flex items-center gap-2 text-xl font-bold">
                    <i class="fa-solid fa-feather-pointed text-brand-text" aria-hidden="true"></i> Kisah brand & filosofi
                </h2>

                <form id="about-form" action="{{ route('dashboard.about.update') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        {{-- Indonesia --}}
                        <div class="space-y-5">
                            <span class="badge-brand"><i class="fa-solid fa-flag" aria-hidden="true"></i> Bahasa Indonesia</span>
                            <x-ui.input name="title_id" label="Judul utama (Indonesia)" :value="$about->title_id" />
                            <x-ui.textarea name="description_id" label="Deskripsi singkat (Indonesia)" rows="7" :value="$about->description_id" />
                            <x-ui.textarea name="vision_id" label="Visi perusahaan (Indonesia)" rows="5" :value="$about->vision_id" />
                            <x-ui.textarea name="mission_id" label="Misi perusahaan (Indonesia)" rows="6" :value="$about->mission_id" />
                        </div>

                        {{-- English --}}
                        <div class="space-y-5">
                            <span class="badge-info"><i class="fa-solid fa-earth-americas" aria-hidden="true"></i> English</span>
                            <x-ui.input name="title_en" label="Judul utama (English)" :value="$about->title_en" />
                            <x-ui.textarea name="description_en" label="Deskripsi singkat (English)" rows="7" :value="$about->description_en" />
                            <x-ui.textarea name="vision_en" label="Visi perusahaan (English)" rows="5" :value="$about->vision_en" />
                            <x-ui.textarea name="mission_en" label="Misi perusahaan (English)" rows="6" :value="$about->mission_en" />
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-line pt-6">
                        <x-ui.button type="submit" icon="fa-floppy-disk" class="w-full sm:w-auto">Simpan perubahan</x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            {{-- Slider --}}
            <x-ui.card>
                <h2 class="flex items-center gap-2 text-xl font-bold">
                    <i class="fa-solid fa-images text-brand-text" aria-hidden="true"></i> Slider tentang
                </h2>

                <form action="{{ route('dashboard.about.slides.store') }}" method="POST" enctype="multipart/form-data"
                    x-data="{
                        preview: null,
                        handleFileChange(e) {
                            const file = e.target.files[0];
                            if (file) {
                                this.preview = URL.createObjectURL(file);
                            }
                        }
                    }"
                    class="mt-5">
                    @csrf
                    <input type="file" name="image" required id="about-slide-upload" x-ref="fileInput" accept="image/*" class="sr-only" @change="handleFileChange">

                    <div x-show="!preview" class="rounded-lg border border-dashed border-line bg-surface-2 p-6 text-center">
                        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-soft text-brand-text">
                            <i class="fa-solid fa-cloud-arrow-up text-xl" aria-hidden="true"></i>
                        </span>
                        <p class="mt-3 text-small font-semibold">Tambah foto slider</p>
                        <p class="text-caption text-muted">Pilih gambar, lalu konfirmasi unggahan.</p>
                        <label for="about-slide-upload" class="btn btn-secondary mt-4 cursor-pointer focus-within:ring-2 focus-within:ring-brand-500/40">
                            <i class="fa-solid fa-image" aria-hidden="true"></i> Pilih foto
                        </label>
                    </div>

                    <div x-show="preview" x-cloak class="space-y-3">
                        <div class="aspect-square overflow-hidden rounded-lg border border-line bg-surface-2">
                            <img :src="preview" alt="Pratinjau foto baru" class="h-full w-full object-cover">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-primary flex-1"><i class="fa-solid fa-cloud-arrow-up" aria-hidden="true"></i> Unggah foto</button>
                            <button type="button" class="btn btn-secondary" @click="preview = null; $refs.fileInput.value = ''">Batal</button>
                        </div>
                    </div>
                </form>

                <div class="mt-6">
                    <p class="label">Foto slider ({{ count($slides) }})</p>
                    @if (count($slides))
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($slides as $slide)
                                <div class="relative aspect-square overflow-hidden rounded-lg border border-line bg-surface-2">
                                    <img src="{{ $slide->media_url }}" alt="Foto slider {{ $loop->iteration }}" class="h-full w-full object-cover">
                                    <form action="{{ route('dashboard.about.slides.destroy', $slide->id) }}" method="POST"
                                        data-confirm="Foto ini akan dihapus dari slider." data-confirm-title="Hapus foto slider?" data-confirm-ok="Ya, hapus"
                                        class="absolute bottom-2 right-2">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger !px-0 w-11" aria-label="Hapus foto slider {{ $loop->iteration }}">
                                            <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <x-ui.empty-state icon="fa-images" title="Belum ada foto" text="Unggah foto pertama untuk slider." />
                    @endif
                </div>
            </x-ui.card>
        </div>

        {{-- Tim --}}
        <section class="space-y-6 pt-4" aria-labelledby="team-heading">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="eyebrow">Sumber daya manusia</p>
                    <h2 id="team-heading" class="text-2xl font-extrabold">Tim & kepemimpinan</h2>
                    <p class="mt-1 text-small text-muted">Tampilkan orang-orang di balik brand: jabatan, bio, dan tautan sosial.</p>
                </div>
                <span class="badge-brand self-start sm:self-auto"><i class="fa-solid fa-users" aria-hidden="true"></i> {{ count($owners) }} anggota</span>
            </div>

            <x-ui.card>
                <h3 class="flex items-center gap-2 text-xl font-bold">
                    <i class="fa-solid fa-user-plus text-brand-text" aria-hidden="true"></i> Tambah anggota tim
                </h3>

                <form action="{{ route('dashboard.owners.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-6"
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
                        <div class="space-y-5">
                            <x-ui.input name="name" label="Nama lengkap" required />

                            <div>
                                <span class="label">Foto profil</span>
                                <div class="flex items-center gap-4 rounded-lg border border-dashed border-line bg-surface-2 p-4">
                                    <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-line bg-surface">
                                        <img x-show="preview" x-cloak :src="preview" alt="Pratinjau foto profil" class="h-full w-full object-cover">
                                        <i x-show="!preview" class="fa-solid fa-user-tie text-3xl text-muted" aria-hidden="true"></i>
                                    </div>
                                    <div>
                                        <label for="member-photo" class="btn btn-secondary btn-sm cursor-pointer focus-within:ring-2 focus-within:ring-brand-500/40">
                                            <i class="fa-solid fa-camera" aria-hidden="true"></i>
                                            <span x-text="preview ? 'Ganti foto' : 'Unggah foto'">Unggah foto</span>
                                        </label>
                                        <input type="file" name="image" required @change="handleFileChange" id="member-photo" accept="image/*" class="sr-only">
                                        <p class="hint">Disarankan rasio persegi.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <x-ui.input name="role_id" label="Jabatan (Indonesia)" placeholder="mis. Pendiri & CEO" required />
                                <x-ui.input name="role_en" label="Jabatan (English)" placeholder="e.g. Chief Director" required />
                            </div>
                            <x-ui.input name="instagram_url" type="url" label="URL Instagram" placeholder="https://..." />
                            <x-ui.input name="linkedin_url" type="url" label="URL LinkedIn" placeholder="https://..." />
                        </div>

                        <x-ui.textarea name="bio_id" label="Biografi (Indonesia)" rows="3" />
                        <x-ui.textarea name="bio_en" label="Biografi (English)" rows="3" />
                    </div>

                    <div class="flex justify-end border-t border-line pt-6">
                        <x-ui.button type="submit" icon="fa-user-plus" class="w-full sm:w-auto">Tambah anggota</x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            @if (count($owners))
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($owners as $owner)
                        <x-ui.card class="flex flex-col items-center text-center">
                            <div class="h-28 w-28 overflow-hidden rounded-xl border border-line bg-surface-2">
                                <img src="{{ $owner->media_url }}" alt="Foto {{ $owner->name }}" class="h-full w-full object-cover">
                            </div>

                            <h3 class="mt-4 text-lg font-bold">{{ $owner->name }}</h3>
                            <span class="badge-brand mt-2">{{ $owner->getTranslation('role') }}</span>

                            @if ($owner->getTranslation('bio'))
                                <p class="mt-3 line-clamp-4 text-small text-muted">{{ $owner->getTranslation('bio') }}</p>
                            @endif

                            @if ($owner->instagram_url || $owner->linkedin_url)
                                <div class="mt-3 flex gap-2">
                                    @if ($owner->instagram_url)
                                        <a href="{{ $owner->instagram_url }}" target="_blank" rel="noopener" class="btn-icon border border-line" aria-label="Instagram {{ $owner->name }}">
                                            <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                    @if ($owner->linkedin_url)
                                        <a href="{{ $owner->linkedin_url }}" target="_blank" rel="noopener" class="btn-icon border border-line" aria-label="LinkedIn {{ $owner->name }}">
                                            <i class="fa-brands fa-linkedin" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <form action="{{ route('dashboard.owners.destroy', $owner->id) }}" method="POST"
                                data-confirm="{{ $owner->name }} akan dihapus dari daftar tim." data-confirm-title="Hapus anggota tim?" data-confirm-ok="Ya, hapus"
                                class="mt-auto w-full pt-5">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger-soft w-full">
                                    <i class="fa-solid fa-trash-can" aria-hidden="true"></i> Hapus anggota
                                </button>
                            </form>
                        </x-ui.card>
                    @endforeach
                </div>
            @else
                <x-ui.card>
                    <x-ui.empty-state icon="fa-users-slash" title="Belum ada anggota tim" text="Mulai dengan menambahkan anggota tim pertama di formulir di atas." />
                </x-ui.card>
            @endif
        </section>
    </div>
</x-app-layout>
