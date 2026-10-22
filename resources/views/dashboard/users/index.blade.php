<x-app-layout>
    <x-slot name="header">Pengguna</x-slot>

    <div class="mx-auto max-w-7xl space-y-6" x-data="{
        editMode: false,
        currentUser: {},
        search: '',
        initEdit(userData) {
            this.currentUser = { ...userData };
            this.editMode = true;
            this.$nextTick(() => this.$refs.editName && this.$refs.editName.focus());
        },
        closeEdit() {
            this.editMode = false;
            this.currentUser = {};
        }
    }" @keydown.escape.window="if (editMode) closeEdit()">

        <x-ui.page-header title="Pengguna" description="Kelola akses platform dan peran pengguna terdaftar.">
            <x-slot:actions>
                <x-ui.badge variant="brand">{{ count($users) }} pengguna</x-ui.badge>
            </x-slot:actions>
        </x-ui.page-header>

        @if ($errors->any())
            <div class="rounded-lg border border-danger-500/40 bg-danger-50 px-4 py-3 text-small text-danger-700 dark:bg-danger-500/15 dark:text-danger-100" role="alert">
                <ul class="list-inside list-disc space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($users->isEmpty())
            <x-ui.card>
                <x-ui.empty-state icon="fa-users" title="Belum ada pengguna" text="Pengguna yang mendaftar akan muncul di sini." />
            </x-ui.card>
        @else
            <div class="relative">
                <label for="user-search" class="sr-only">Cari pengguna</label>
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-muted" aria-hidden="true"></i>
                <input id="user-search" type="search" x-model="search" class="input pl-10" placeholder="Cari nama atau email">
            </div>

            {{-- Desktop table --}}
            <div class="table-wrap hidden lg:block">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th scope="col">Pengguna</th>
                            <th scope="col">Peran</th>
                            <th scope="col">Pesanan</th>
                            <th scope="col">Bergabung</th>
                            <th scope="col" class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr x-show="!search || {{ \Illuminate\Support\Js::from(mb_strtolower($user->name . ' ' . $user->email)) }}.includes(search.toLowerCase().trim())">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full font-bold {{ $user->isSuperAdmin() ? 'bg-brand-gradient text-white' : 'bg-brand-soft text-brand-text' }}" aria-hidden="true">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="font-semibold">
                                                {{ $user->name }}
                                                @if ($user->id === auth()->id())
                                                    <span class="badge-neutral ml-1">Anda</span>
                                                @endif
                                            </p>
                                            <p class="text-caption text-muted">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <x-ui.badge :variant="$user->isSuperAdmin() ? 'brand' : 'neutral'">
                                        {{ $user->isSuperAdmin() ? 'Super Admin' : 'Pelanggan' }}
                                    </x-ui.badge>
                                </td>
                                <td>
                                    <span class="font-semibold">{{ $user->orders->count() }}</span>
                                    <span class="text-caption text-muted">pesanan</span>
                                </td>
                                <td class="whitespace-nowrap text-muted">{{ $user->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" @click="initEdit({{ $user->toJson() }})" class="btn-icon" aria-label="Ubah {{ $user->name }}" title="Ubah">
                                            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                        </button>
                                        @if ($user->id !== auth()->id())
                                            <form action="{{ route('dashboard.users.destroy', $user->id) }}" method="POST"
                                                  data-confirm="Semua data terkait (pesanan, dsb) mungkin akan ikut hilang." data-confirm-title="Hapus pengguna ini?" data-confirm-ok="Ya, hapus">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-icon hover:text-danger-600" aria-label="Hapus {{ $user->name }}" title="Hapus">
                                                    <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="space-y-3 lg:hidden">
                @foreach ($users as $user)
                    <article class="card card-pad" x-show="!search || {{ \Illuminate\Support\Js::from(mb_strtolower($user->name . ' ' . $user->email)) }}.includes(search.toLowerCase().trim())">
                        <div class="flex items-start gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full font-bold {{ $user->isSuperAdmin() ? 'bg-brand-gradient text-white' : 'bg-brand-soft text-brand-text' }}" aria-hidden="true">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="font-bold">{{ $user->name }}</p>
                                <p class="break-all text-caption text-muted">{{ $user->email }}</p>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <x-ui.badge :variant="$user->isSuperAdmin() ? 'brand' : 'neutral'">
                                        {{ $user->isSuperAdmin() ? 'Super Admin' : 'Pelanggan' }}
                                    </x-ui.badge>
                                    @if ($user->id === auth()->id())
                                        <x-ui.badge variant="neutral">Anda</x-ui.badge>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-3 border-t border-line pt-3">
                            <div class="text-small text-muted">
                                <span class="font-semibold text-ink">{{ $user->orders->count() }}</span> pesanan &middot; sejak {{ $user->created_at->format('d M Y') }}
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button" @click="initEdit({{ $user->toJson() }})" class="btn-icon" aria-label="Ubah {{ $user->name }}">
                                    <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                </button>
                                @if ($user->id !== auth()->id())
                                    <form action="{{ route('dashboard.users.destroy', $user->id) }}" method="POST"
                                          data-confirm="Semua data terkait (pesanan, dsb) mungkin akan ikut hilang." data-confirm-title="Hapus pengguna ini?" data-confirm-ok="Ya, hapus">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon hover:text-danger-600" aria-label="Hapus {{ $user->name }}">
                                            <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        {{-- Edit modal --}}
        <div x-show="editMode" x-cloak x-transition.opacity.duration.200ms class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center"
             role="dialog" aria-modal="true" aria-labelledby="user-modal-title">
            <div class="absolute inset-0 bg-ink/50 backdrop-blur-sm" @click="closeEdit()"></div>

            <div class="card relative max-h-[90vh] w-full max-w-lg overflow-y-auto">
                <div class="flex items-start justify-between gap-4 border-b border-line px-5 py-4">
                    <div class="min-w-0">
                        <h2 id="user-modal-title" class="text-lg font-bold">Ubah Pengguna</h2>
                        <p class="truncate text-small text-muted" x-text="currentUser.name"></p>
                    </div>
                    <button type="button" class="btn-icon shrink-0" @click="closeEdit()" aria-label="Tutup">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>

                <form :action="'{{ route('dashboard.users.index') }}/' + currentUser.id" method="POST" class="space-y-4 p-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="edit-user-name" class="label">Nama lengkap</label>
                        <input id="edit-user-name" type="text" name="name" required x-model="currentUser.name" x-ref="editName" class="input">
                    </div>

                    <div>
                        <label for="edit-user-email" class="label">Alamat email</label>
                        <input id="edit-user-email" type="email" name="email" required x-model="currentUser.email" class="input">
                    </div>

                    <div>
                        <label for="edit-user-role" class="label">Peran</label>
                        <select id="edit-user-role" name="role" x-model="currentUser.role" class="select">
                            <option value="user">Pengguna / Pelanggan</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>

                    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                        <button type="button" class="btn btn-secondary" @click="closeEdit()">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
