<x-app-layout>
    <x-slot name="header">Bagian Hero</x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <x-ui.page-header title="Bagian Hero" description="Teks utama dan gambar latar slider di bagian atas halaman depan." />

        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
            {{-- Teks hero --}}
            <x-ui.card class="lg:col-span-2">
                <h2 class="flex items-center gap-2 text-xl font-bold">
                    <i class="fa-solid fa-i-cursor text-brand-text" aria-hidden="true"></i> Konten teks
                </h2>

                <form action="{{ route('dashboard.hero.update') }}" method="POST" class="mt-6 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        {{-- Indonesia --}}
                        <div class="space-y-5">
                            <span class="badge-brand"><i class="fa-solid fa-flag" aria-hidden="true"></i> Bahasa Indonesia</span>
                            <x-ui.input name="title_id" label="Judul utama (Indonesia)" :value="$hero->title_id" />
                            <x-ui.textarea name="subtitle_id" label="Deskripsi subjudul (Indonesia)" rows="6" :value="$hero->subtitle_id" />
                            <x-ui.input name="cta_text_id" label="Teks tombol (Indonesia)" :value="$hero->cta_text_id" />
                        </div>

                        {{-- English --}}
                        <div class="space-y-5">
                            <span class="badge-info"><i class="fa-solid fa-earth-americas" aria-hidden="true"></i> English</span>
                            <x-ui.input name="title_en" label="Judul utama (English)" :value="$hero->title_en" />
                            <x-ui.textarea name="subtitle_en" label="Deskripsi subjudul (English)" rows="6" :value="$hero->subtitle_en" />
                            <x-ui.input name="cta_text_en" label="Teks tombol (English)" :value="$hero->cta_text_en" />
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="max-w-md">
                        <label for="cta_link" class="label">Tautan tombol (Call to Action)</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-muted">
                                <i class="fa-solid fa-link" aria-hidden="true"></i>
                            </span>
                            <input type="text" id="cta_link" name="cta_link" value="{{ old('cta_link', $hero->cta_link) }}" class="input pl-11" placeholder="#contact">
                        </div>
                        <p class="hint">Contoh: #contact atau https://...</p>
                    </div>

                    <div class="flex justify-end">
                        <x-ui.button type="submit" icon="fa-floppy-disk" class="w-full sm:w-auto">Simpan perubahan</x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            {{-- Slider latar --}}
            <x-ui.card>
                <h2 class="flex items-center gap-2 text-xl font-bold">
                    <i class="fa-solid fa-images text-brand-text" aria-hidden="true"></i> Slider latar
                </h2>

                <form action="{{ route('dashboard.hero.slides.store') }}" method="POST" enctype="multipart/form-data"
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
                    <input type="file" name="image" required id="slide-upload" x-ref="fileInput" accept="image/*" class="sr-only" @change="handleFileChange">

                    <div x-show="!preview" class="rounded-lg border border-dashed border-line bg-surface-2 p-6 text-center">
                        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-soft text-brand-text">
                            <i class="fa-solid fa-cloud-arrow-up text-xl" aria-hidden="true"></i>
                        </span>
                        <p class="mt-3 text-small font-semibold">Tambah slide baru</p>
                        <p class="text-caption text-muted">Gambar lebar (16:9) untuk hasil terbaik.</p>
                        <label for="slide-upload" class="btn btn-secondary mt-4 cursor-pointer focus-within:ring-2 focus-within:ring-brand-500/40">
                            <i class="fa-solid fa-image" aria-hidden="true"></i> Pilih gambar
                        </label>
                    </div>

                    <div x-show="preview" x-cloak class="space-y-3">
                        <div class="aspect-video overflow-hidden rounded-lg border border-line bg-surface-2">
                            <img :src="preview" alt="Pratinjau slide baru" class="h-full w-full object-cover">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-primary flex-1"><i class="fa-solid fa-cloud-arrow-up" aria-hidden="true"></i> Unggah slide</button>
                            <button type="button" class="btn btn-secondary" @click="preview = null; $refs.fileInput.value = ''">Batal</button>
                        </div>
                    </div>
                </form>

                <div class="mt-6">
                    <p class="label">Slide aktif ({{ count($slides) }})</p>
                    <div class="grid grid-cols-1 gap-4">
                        @forelse ($slides as $slide)
                            <div class="relative aspect-video overflow-hidden rounded-lg border border-line bg-surface-2">
                                <img src="{{ $slide->media_url }}" alt="Slide hero {{ $loop->iteration }}" class="h-full w-full object-cover">
                                <span class="absolute left-3 top-3 rounded-full bg-surface px-2.5 py-1 text-caption font-semibold text-ink shadow-soft">#{{ $loop->iteration }}</span>
                                <form action="{{ route('dashboard.hero.slides.destroy', $slide) }}" method="POST"
                                    data-confirm="Slide ini akan dihapus dari slider hero." data-confirm-title="Hapus slide?" data-confirm-ok="Ya, hapus"
                                    class="absolute bottom-3 right-3">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" aria-label="Hapus slide {{ $loop->iteration }}">
                                        <i class="fa-solid fa-trash-can" aria-hidden="true"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        @empty
                            <x-ui.empty-state icon="fa-film" title="Belum ada slide" text="Unggah gambar pertama untuk latar hero." />
                        @endforelse
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
