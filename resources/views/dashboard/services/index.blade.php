<x-app-layout>
    <x-slot name="header">Layanan</x-slot>

    <div class="mx-auto max-w-7xl space-y-6" x-data="{ open: false }" @keydown.escape.window="open = false">
        <x-ui.page-header title="Layanan" description="Kelola layanan yang tampil di halaman utama situs.">
            <x-slot:actions>
                <x-ui.button icon="fa-plus" @click="open = true">Tambah layanan</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        @if ($errors->any())
            <div class="rounded-lg border border-danger-500/40 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert">
                <p class="font-semibold">Layanan belum tersimpan. Periksa isian berikut:</p>
                <ul class="mt-1 list-disc space-y-0.5 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (count($services) > 0)
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $service)
                    <x-ui.card hover class="flex flex-col" data-aos="fade-up" data-aos-delay="{{ min($loop->index, 6) * 50 }}">
                        <div class="flex items-start justify-between gap-3">
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg bg-brand-soft text-2xl text-brand-text">
                                <i class="{{ str_starts_with($service->icon, 'fa-') ? $service->icon : 'fa-solid fa-' . ($service->icon == 'swatch' ? 'palette' : ($service->icon == 'code-bracket' ? 'code' : 'rocket')) }}" aria-hidden="true"></i>
                            </span>

                            <form action="{{ route('dashboard.services.destroy', $service->id) }}" method="POST"
                                data-confirm="Layanan ini akan dihapus permanen." data-confirm-title="Hapus layanan?" data-confirm-ok="Ya, hapus">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon hover:bg-danger-50 hover:text-danger-700 dark:hover:bg-danger-500/15 dark:hover:text-red-300" aria-label="Hapus layanan {{ $service->title_id }}">
                                    <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                </button>
                            </form>
                        </div>

                        <div class="mt-4 min-w-0 flex-1">
                            <h3 class="text-lg font-bold leading-tight">{{ $service->title_id }}</h3>
                            <p class="mt-0.5 text-small text-muted">{{ $service->title_en }}</p>
                            <p class="mt-3 line-clamp-3 text-small text-muted">{{ $service->description_id }}</p>
                        </div>

                        <div class="divider mt-5"></div>
                        <div class="mt-4 flex items-center justify-between gap-3">
                            <code class="min-w-0 truncate text-caption text-muted">{{ $service->icon }}</code>
                            <x-ui.badge variant="success" dot>Aktif</x-ui.badge>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @else
            <x-ui.card :padded="false">
                <x-ui.empty-state icon="fa-box-open" title="Belum ada layanan" text="Tambahkan layanan pertama agar tampil di halaman utama.">
                    <x-ui.button icon="fa-plus" @click="open = true">Tambah layanan</x-ui.button>
                </x-ui.empty-state>
            </x-ui.card>
        @endif

        {{-- Modal tambah layanan --}}
        <div x-show="open" x-cloak x-transition.opacity.duration.150ms
            class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
            role="dialog" aria-modal="true" aria-labelledby="service-modal-title"
            x-effect="if (open) $nextTick(() => $refs.firstField && $refs.firstField.focus())">
            <div class="absolute inset-0 bg-ink/50 backdrop-blur-sm" @click="open = false" aria-hidden="true"></div>

            <div class="card relative max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-b-none sm:rounded-lg">
                <div class="sticky top-0 z-10 flex items-center justify-between gap-4 border-b border-line bg-surface px-5 py-4 md:px-6">
                    <h2 id="service-modal-title" class="text-xl font-bold">Tambah layanan</h2>
                    <button type="button" class="btn-icon -mr-2" @click="open = false" aria-label="Tutup"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                </div>

                <form action="{{ route('dashboard.services.store') }}" method="POST" class="space-y-5 p-5 md:p-6">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <x-ui.input name="title_id" label="Judul (Indonesia)" required x-ref="firstField" />
                        <x-ui.input name="title_en" label="Judul (Inggris)" required />
                        <x-ui.textarea name="description_id" label="Deskripsi (Indonesia)" rows="3" required />
                        <x-ui.textarea name="description_en" label="Deskripsi (Inggris)" rows="3" required />
                    </div>

                    {{-- Pemilih ikon --}}
                    <div x-data="{
                        icon: 'fa-solid fa-star',
                        search: '',
                        icons: [
                            'fa-solid fa-code', 'fa-solid fa-laptop-code', 'fa-solid fa-mobile-screen',
                            'fa-solid fa-paint-roller', 'fa-solid fa-palette', 'fa-solid fa-pen-nib',
                            'fa-solid fa-bullhorn', 'fa-solid fa-chart-line', 'fa-solid fa-magnifying-glass',
                            'fa-solid fa-server', 'fa-solid fa-database', 'fa-solid fa-cloud',
                            'fa-solid fa-shield-halved', 'fa-solid fa-robot', 'fa-solid fa-microchip',
                            'fa-solid fa-desktop', 'fa-solid fa-earth-americas', 'fa-solid fa-rocket',
                            'fa-solid fa-cart-shopping', 'fa-solid fa-store', 'fa-solid fa-camera'
                        ],
                        get filteredIcons() {
                            if (this.search === '') return this.icons;
                            return this.icons.filter(i => i.toLowerCase().includes(this.search.toLowerCase()));
                        }
                    }">
                        <p class="label">Ikon layanan</p>
                        <div class="grid grid-cols-1 gap-5 rounded-lg border border-line bg-surface-2 p-4 md:p-5 lg:grid-cols-3">
                            <div class="space-y-4">
                                <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-lg bg-surface text-3xl text-brand-text shadow-soft ring-4 ring-brand-100 lg:mx-0 dark:ring-brand-500/20">
                                    <i :class="icon" aria-hidden="true"></i>
                                </span>
                                <div>
                                    <label for="service-icon" class="label">Kelas ikon (kustom)</label>
                                    <input type="text" id="service-icon" name="icon" x-model="icon" required class="input font-mono text-small">
                                </div>
                            </div>

                            <div class="space-y-3 lg:col-span-2">
                                <div>
                                    <label for="service-icon-search" class="label">Cari ikon</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-small text-muted" aria-hidden="true"></i>
                                        <input type="text" id="service-icon-search" x-model="search" placeholder="Ketik untuk menyaring ikon..." class="input pl-10">
                                    </div>
                                </div>
                                <div class="custom-scrollbar grid max-h-48 grid-cols-4 gap-2 overflow-y-auto p-1 sm:grid-cols-6 lg:grid-cols-7">
                                    <template x-for="i in filteredIcons" :key="i">
                                        <button type="button" @click="icon = i" :aria-label="'Pilih ikon ' + i" :aria-pressed="icon === i"
                                            class="flex aspect-square min-h-[44px] items-center justify-center rounded-md border text-lg transition"
                                            :class="icon === i ? 'border-brand-500 bg-brand-soft text-brand-text' : 'border-line bg-surface text-muted hover:border-brand-300 hover:text-brand-text'">
                                            <i :class="i" aria-hidden="true"></i>
                                        </button>
                                    </template>
                                </div>
                                <p x-show="filteredIcons.length === 0" x-cloak class="hint">Tidak ada ikon yang cocok. Anda tetap bisa mengisi kelas ikon secara manual.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                        <x-ui.button variant="secondary" @click="open = false">Batal</x-ui.button>
                        <x-ui.button type="submit" icon="fa-check">Simpan layanan</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
