<x-app-layout>
    <x-slot name="header">Klien &amp; Mitra</x-slot>

    <div class="mx-auto max-w-7xl space-y-6" x-data="{ editingClient: null, openAdd: false }"
        @keydown.escape.window="openAdd = false; editingClient = null">

        <x-ui.page-header title="Klien & Mitra" description="Logo klien yang tampil di halaman publik, lengkap dengan pengaturan langganan hosting server.">
            <x-slot:actions>
                <x-ui.button variant="primary" icon="fa-plus" @click="openAdd = true">Tambah klien</x-ui.button>
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

        @if ($clients->isEmpty())
            <x-ui.card :padded="false">
                <x-ui.empty-state icon="fa-handshake" title="Belum ada klien" text="Unggah logo klien pertama Anda untuk ditampilkan di halaman publik.">
                    <x-ui.button variant="primary" icon="fa-plus" @click="openAdd = true">Tambah klien</x-ui.button>
                </x-ui.empty-state>
            </x-ui.card>
        @else
            <p class="text-small text-muted">{{ $clients->count() }} klien</p>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($clients as $client)
                    <x-ui.card class="flex flex-col" hover>
                        <div class="flex aspect-video items-center justify-center rounded-md bg-surface-2 p-4">
                            <img src="{{ $client->media_url }}" alt="Logo {{ $client->name ?? 'klien' }}" class="max-h-full max-w-full object-contain">
                        </div>

                        <div class="mt-4 min-w-0 flex-1">
                            <h3 class="truncate text-body font-bold">{{ $client->name ?: 'Tanpa nama' }}</h3>
                            @if ($client->url)
                                <a href="{{ $client->url }}" target="_blank" rel="noopener"
                                    class="mt-0.5 flex items-center gap-1.5 truncate text-small text-brand-text hover:underline">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-caption" aria-hidden="true"></i>
                                    <span class="truncate">{{ preg_replace('#^https?://#', '', $client->url) }}</span>
                                </a>
                            @endif

                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                @if ($client->is_server_subscribed)
                                    <span class="badge-success"><span class="h-1.5 w-1.5 rounded-full bg-mint-500" aria-hidden="true"></span> Langganan server</span>
                                    @if ($client->billing_date)
                                        <span class="badge-info">Tagih tgl {{ $client->billing_date }}</span>
                                    @endif
                                    @if ($client->subscription_price > 0)
                                        <span class="badge-neutral">Rp {{ number_format((float) $client->subscription_price, 0, ',', '.') }}/bln</span>
                                    @endif
                                @else
                                    <span class="badge-neutral">Tanpa langganan</span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-end gap-2 border-t border-line pt-4">
                            <button type="button" class="btn-secondary btn-sm" @click="editingClient = @js($client->toArray() + ['media_url' => $client->media_url])" aria-label="Ubah klien {{ $client->name }}">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i> Ubah
                            </button>
                            <form action="{{ route('dashboard.clients.destroy', $client->id) }}" method="POST"
                                data-confirm="Klien {{ $client->name }} akan dihapus permanen beserta logonya." data-confirm-title="Hapus klien?" data-confirm-ok="Ya, hapus">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon hover:bg-danger-50 hover:text-danger-700" aria-label="Hapus klien {{ $client->name }}">
                                    <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                </button>
                            </form>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @endif

        {{-- Modal: tambah klien --}}
        <div x-show="openAdd" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="add-client-title">
            <div class="absolute inset-0 bg-ink/50 backdrop-blur-sm" x-show="openAdd"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                @click="openAdd = false"></div>
            <div class="card relative max-h-[90vh] w-full max-w-2xl overflow-y-auto custom-scrollbar" x-show="openAdd"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="sticky top-0 z-10 flex items-center justify-between gap-4 border-b border-line bg-surface px-5 py-4 md:px-6">
                    <h2 id="add-client-title" class="text-lg font-bold">Tambah klien</h2>
                    <button type="button" class="btn-icon" @click="openAdd = false" aria-label="Tutup">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>

                <form action="{{ route('dashboard.clients.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-5 md:p-6"
                    x-data="{ preview: null, isSubscribed: false }">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <x-ui.input id="add-client-name" name="name" label="Nama klien" placeholder="Contoh: PT Maju Jaya" :required="true" />
                        <x-ui.input id="add-client-url" name="url" type="url" label="URL situs" placeholder="https://..." />
                    </div>

                    <div>
                        <label for="add-client-logo" class="label">Logo <span class="text-danger-600" aria-hidden="true">*</span></label>
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-20 shrink-0 items-center justify-center overflow-hidden rounded-md bg-surface-2 text-muted">
                                <template x-if="preview"><img :src="preview" alt="" class="h-full w-full object-contain p-1.5"></template>
                                <template x-if="!preview"><i class="fa-solid fa-image" aria-hidden="true"></i></template>
                            </div>
                            <input id="add-client-logo" type="file" name="logo" required accept="image/*"
                                @change="const file = $event.target.files[0]; if (file) preview = URL.createObjectURL(file)"
                                class="input min-w-0 flex-1 text-small file:mr-3 file:rounded-sm file:border-0 file:bg-brand-soft file:px-3 file:py-1 file:text-caption file:font-semibold file:text-brand-text">
                        </div>
                    </div>

                    <div class="rounded-lg border border-line bg-surface-2 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-brand-soft text-brand-text"><i class="fa-solid fa-server" aria-hidden="true"></i></span>
                                <div class="min-w-0">
                                    <p class="text-small font-semibold">Langganan hosting server</p>
                                    <p class="text-caption text-muted">Aktifkan penagihan bulanan otomatis untuk klien ini.</p>
                                </div>
                            </div>
                            <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                                <input type="checkbox" name="is_server_subscribed" value="1" class="peer sr-only" x-model="isSubscribed" aria-label="Aktifkan langganan hosting server">
                                <span class="block h-7 w-12 rounded-full bg-line transition peer-checked:bg-brand-600 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500/40 after:absolute after:left-0.5 after:top-0.5 after:h-6 after:w-6 after:rounded-full after:bg-surface after:shadow-soft after:transition-all peer-checked:after:translate-x-5"></span>
                            </label>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-4 border-t border-line pt-4 sm:grid-cols-2" x-show="isSubscribed" x-transition>
                            <x-ui.input id="add-client-billing" name="billing_date" type="number" min="1" max="31" label="Tanggal penagihan (1-31)" placeholder="Contoh: 5" />
                            <x-ui.input id="add-client-price" name="subscription_price" type="number" label="Biaya bulanan (Rp)" placeholder="500000" />
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-line pt-4 sm:flex-row sm:justify-end">
                        <button type="button" class="btn-secondary" @click="openAdd = false">Batal</button>
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-check" aria-hidden="true"></i> Simpan klien</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal: ubah klien --}}
        <div x-show="editingClient" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="edit-client-title">
            <div class="absolute inset-0 bg-ink/50 backdrop-blur-sm" x-show="editingClient"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                @click="editingClient = null"></div>
            <div class="card relative max-h-[90vh] w-full max-w-2xl overflow-y-auto custom-scrollbar" x-show="editingClient"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="sticky top-0 z-10 flex items-center justify-between gap-4 border-b border-line bg-surface px-5 py-4 md:px-6">
                    <div class="min-w-0">
                        <h2 id="edit-client-title" class="text-lg font-bold">Ubah klien</h2>
                        <p class="truncate text-small text-muted" x-text="editingClient?.name"></p>
                    </div>
                    <button type="button" class="btn-icon" @click="editingClient = null" aria-label="Tutup">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>

                <form :action="'/dashboard/clients/' + editingClient?.id" method="POST" enctype="multipart/form-data" class="space-y-6 p-5 md:p-6"
                    x-data="{ isSubscribed: false }" x-init="$watch('editingClient', value => isSubscribed = value?.is_server_subscribed ? true : false)">
                    @csrf @method('PATCH')

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="edit-client-name" class="label">Nama klien</label>
                            <input id="edit-client-name" type="text" name="name" :value="editingClient?.name" required class="input">
                        </div>
                        <div>
                            <label for="edit-client-url" class="label">URL situs</label>
                            <input id="edit-client-url" type="url" name="url" :value="editingClient?.url" placeholder="https://..." class="input">
                        </div>
                    </div>

                    <div>
                        <label for="edit-client-logo" class="label">Logo (kosongkan jika tidak diganti)</label>
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-20 shrink-0 items-center justify-center overflow-hidden rounded-md bg-surface-2 text-muted">
                                <img :src="editingClient?.media_url" alt="" class="h-full w-full object-contain p-1.5" x-show="editingClient?.media_url">
                            </div>
                            <input id="edit-client-logo" type="file" name="logo" accept="image/*"
                                class="input min-w-0 flex-1 text-small file:mr-3 file:rounded-sm file:border-0 file:bg-brand-soft file:px-3 file:py-1 file:text-caption file:font-semibold file:text-brand-text">
                        </div>
                    </div>

                    <div class="rounded-lg border border-line bg-surface-2 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-brand-soft text-brand-text"><i class="fa-solid fa-server" aria-hidden="true"></i></span>
                                <div class="min-w-0">
                                    <p class="text-small font-semibold">Langganan hosting server</p>
                                    <p class="text-caption text-muted">Penagihan bulanan untuk klien ini.</p>
                                </div>
                            </div>
                            <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                                <input type="checkbox" name="is_server_subscribed" value="1" class="peer sr-only" x-model="isSubscribed" aria-label="Aktifkan langganan hosting server">
                                <span class="block h-7 w-12 rounded-full bg-line transition peer-checked:bg-brand-600 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500/40 after:absolute after:left-0.5 after:top-0.5 after:h-6 after:w-6 after:rounded-full after:bg-surface after:shadow-soft after:transition-all peer-checked:after:translate-x-5"></span>
                            </label>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-4 border-t border-line pt-4 sm:grid-cols-2" x-show="isSubscribed" x-transition>
                            <div>
                                <label for="edit-client-billing" class="label">Tanggal penagihan (1-31)</label>
                                <input id="edit-client-billing" type="number" name="billing_date" :value="editingClient?.billing_date" min="1" max="31" class="input">
                            </div>
                            <div>
                                <label for="edit-client-price" class="label">Biaya bulanan (Rp)</label>
                                <input id="edit-client-price" type="number" name="subscription_price" :value="parseInt(editingClient?.subscription_price || 0)" class="input">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-line pt-4 sm:flex-row sm:justify-end">
                        <button type="button" class="btn-secondary" @click="editingClient = null">Batal</button>
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-check" aria-hidden="true"></i> Simpan perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
