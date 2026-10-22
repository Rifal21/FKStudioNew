<x-app-layout>
    <x-slot name="header">Portofolio</x-slot>

    <div class="mx-auto max-w-7xl space-y-6" x-data="{
        open: false,
        editMode: false,
        currentProject: {},
        newPreview: null,
        openCreate() {
            this.editMode = false;
            this.currentProject = {};
            this.newPreview = null;
            this.open = true;
        },
        initEdit(project) {
            this.currentProject = project;
            this.editMode = true;
            this.newPreview = null;
            this.open = true;
        },
        close() {
            this.open = false;
        },
        handleFileChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.newPreview = URL.createObjectURL(file);
            }
        }
    }" @keydown.escape.window="close()">
        <x-ui.page-header title="Portofolio" description="Kelola proyek yang ditampilkan di halaman portofolio.">
            <x-slot:actions>
                <x-ui.button icon="fa-plus" @click="openCreate()">Tambah proyek</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        @if ($errors->any())
            <div class="rounded-lg border border-danger-500/40 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert">
                <p class="font-semibold">Proyek belum tersimpan. Periksa isian berikut:</p>
                <ul class="mt-1 list-disc space-y-0.5 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (count($projects) > 0)
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($projects as $project)
                    <x-ui.card :padded="false" hover class="flex flex-col overflow-hidden" data-aos="fade-up" data-aos-delay="{{ min($loop->index, 6) * 50 }}">
                        <div class="relative aspect-[16/10] bg-brand-gradient-soft">
                            @if ($project->image)
                                <img src="{{ $project->media_url }}" alt="{{ $project->title_id }}" loading="lazy" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-4xl text-brand-text/60">
                                    <i class="fa-solid fa-image" aria-hidden="true"></i>
                                </div>
                            @endif
                            <x-ui.badge variant="neutral" class="absolute left-3 top-3 bg-surface shadow-soft">{{ $project->category_id }}</x-ui.badge>
                        </div>

                        <div class="flex flex-1 flex-col p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="text-lg font-bold leading-tight">{{ $project->title_id }}</h3>
                                    <p class="mt-0.5 text-small text-muted">{{ $project->title_en }}</p>
                                </div>
                                <div class="-mr-2 -mt-1 flex shrink-0 items-center">
                                    <button type="button" @click="initEdit({{ $project->toJson() }})" class="btn-icon" aria-label="Ubah proyek {{ $project->title_id }}">
                                        <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                    </button>
                                    <form action="{{ route('dashboard.projects.destroy', $project->id) }}" method="POST"
                                        data-confirm="Proyek ini akan dihapus permanen beserta gambarnya." data-confirm-title="Hapus proyek?" data-confirm-ok="Ya, hapus">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon hover:bg-danger-50 hover:text-danger-700 dark:hover:bg-danger-500/15 dark:hover:text-red-300" aria-label="Hapus proyek {{ $project->title_id }}">
                                            <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <p class="mt-3 line-clamp-2 flex-1 text-small text-muted">{{ $project->description_id }}</p>

                            @if ($project->url)
                                <div class="divider mt-5"></div>
                                <a href="{{ $project->url }}" target="_blank" rel="noopener" class="mt-4 inline-flex min-h-[44px] items-center gap-2 self-start text-small font-semibold text-brand-text hover:underline">
                                    Kunjungi proyek <i class="fa-solid fa-arrow-up-right-from-square text-caption" aria-hidden="true"></i>
                                </a>
                            @endif
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @else
            <x-ui.card :padded="false">
                <x-ui.empty-state icon="fa-briefcase" title="Belum ada proyek" text="Tambahkan proyek pertama untuk ditampilkan di portofolio.">
                    <x-ui.button icon="fa-plus" @click="openCreate()">Tambah proyek</x-ui.button>
                </x-ui.empty-state>
            </x-ui.card>
        @endif

        {{-- Modal tambah / ubah proyek --}}
        <div x-show="open" x-cloak x-transition.opacity.duration.150ms
            class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
            role="dialog" aria-modal="true" aria-labelledby="project-modal-title"
            x-effect="if (open) $nextTick(() => $refs.firstField && $refs.firstField.focus())">
            <div class="absolute inset-0 bg-ink/50 backdrop-blur-sm" @click="close()" aria-hidden="true"></div>

            <div class="card relative max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-b-none sm:rounded-lg">
                <div class="sticky top-0 z-10 flex items-center justify-between gap-4 border-b border-line bg-surface px-5 py-4 md:px-6">
                    <h2 id="project-modal-title" class="text-xl font-bold" x-text="editMode ? 'Ubah proyek' : 'Tambah proyek'"></h2>
                    <button type="button" class="btn-icon -mr-2" @click="close()" aria-label="Tutup"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                </div>

                <form :action="editMode ? '{{ route('dashboard.projects.index') }}/' + currentProject.id : '{{ route('dashboard.projects.store') }}'"
                    method="POST" enctype="multipart/form-data" class="space-y-6 p-5 md:p-6">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PATCH">
                    </template>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="prj-title-id" class="label">Judul proyek (Indonesia)</label>
                            <input type="text" id="prj-title-id" name="title_id" required x-model="currentProject.title_id" x-ref="firstField" class="input">
                        </div>
                        <div>
                            <label for="prj-title-en" class="label">Judul proyek (Inggris)</label>
                            <input type="text" id="prj-title-en" name="title_en" required x-model="currentProject.title_en" class="input">
                        </div>

                        <div>
                            <label for="prj-cat-id" class="label">Kategori (Indonesia)</label>
                            <input type="text" id="prj-cat-id" name="category_id" required x-model="currentProject.category_id" placeholder="Contoh: Pengembangan Web" class="input">
                        </div>
                        <div>
                            <label for="prj-cat-en" class="label">Kategori (Inggris)</label>
                            <input type="text" id="prj-cat-en" name="category_en" required x-model="currentProject.category_en" placeholder="Example: Web Development" class="input">
                        </div>

                        <div>
                            <label for="prj-desc-id" class="label">Deskripsi (Indonesia)</label>
                            <textarea id="prj-desc-id" name="description_id" rows="4" x-model="currentProject.description_id" class="textarea"></textarea>
                        </div>
                        <div>
                            <label for="prj-desc-en" class="label">Deskripsi (Inggris)</label>
                            <textarea id="prj-desc-en" name="description_en" rows="4" x-model="currentProject.description_en" class="textarea"></textarea>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="prj-image" class="label">Gambar proyek</label>
                            <div class="flex items-center gap-4">
                                <div class="flex aspect-[16/10] w-28 shrink-0 items-center justify-center overflow-hidden rounded-md border border-line bg-brand-gradient-soft text-brand-text/60">
                                    <template x-if="newPreview">
                                        <img :src="newPreview" alt="Pratinjau gambar baru" class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!newPreview && currentProject.image">
                                        <img :src="currentProject.media_url" alt="Gambar saat ini" class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!newPreview && !currentProject.image">
                                        <i class="fa-solid fa-image text-xl" aria-hidden="true"></i>
                                    </template>
                                </div>
                                <input type="file" id="prj-image" name="image" accept="image/*" @change="handleFileChange" class="block w-full min-w-0 text-small text-muted file:mr-3 file:min-h-[44px] file:cursor-pointer file:rounded-md file:border-0 file:bg-brand-soft file:px-4 file:py-2 file:text-small file:font-semibold file:text-brand-text hover:file:bg-brand-100">
                            </div>
                            <p class="hint">Kosongkan untuk mempertahankan gambar saat ini.</p>
                        </div>
                        <div>
                            <label for="prj-url" class="label">Tautan proyek (opsional)</label>
                            <div class="relative">
                                <i class="fa-solid fa-link pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-small text-muted" aria-hidden="true"></i>
                                <input type="text" id="prj-url" name="url" x-model="currentProject.url" placeholder="https://example.com" class="input pl-10">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <x-ui.button variant="secondary" @click="close()">Batal</x-ui.button>
                        <x-ui.button type="submit" icon="fa-check"><span x-text="editMode ? 'Simpan perubahan' : 'Simpan proyek'"></span></x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
