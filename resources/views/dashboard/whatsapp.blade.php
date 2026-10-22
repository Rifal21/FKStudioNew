<x-app-layout>
    <x-slot name="header">WhatsApp</x-slot>

    <div class="mx-auto max-w-6xl space-y-6">
        <x-ui.page-header title="WhatsApp" description="Hubungkan nomor WhatsApp lewat server WAHA untuk mengirim invoice langganan otomatis." />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 lg:items-start">
            {{-- Koneksi --}}
            <form action="{{ route('dashboard.whatsapp.update') }}" method="POST" class="space-y-6">
                @csrf
                @if ($errors->any())
                    <div class="rounded-lg border border-danger-500/40 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert">
                        <ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <x-ui.card>
                    <h2 class="text-xl font-bold">Server WAHA</h2>
                    <p class="mt-1 text-small text-muted">Alamat dan kunci akses server tempat sesi WhatsApp berjalan.</p>

                    <div class="mt-6 space-y-5">
                        <x-ui.input name="waha_url" type="url" label="Alamat server" :value="$settings->waha_url ?: $defaultUrl" required />
                        <div>
                            <label for="waha_api_key" class="label">API key</label>
                            <input id="waha_api_key" name="waha_api_key" type="password" autocomplete="off" class="input"
                                placeholder="{{ $hasKey ? 'Tersimpan. Kosongkan untuk tetap memakai yang lama.' : 'Tempel API key WAHA' }}">
                            <p class="hint">Disimpan terenkripsi dan tidak ditampilkan lagi.</p>
                        </div>

                        <label class="flex items-start gap-3 rounded-lg border border-line bg-surface-2 p-4">
                            <input type="checkbox" name="waha_enabled" value="1" class="mt-1 h-5 w-5 rounded border-line accent-[rgb(var(--c-edge))]" @checked(old('waha_enabled', $settings->waha_enabled))>
                            <span>
                                <span class="block text-small font-bold">Aktifkan pengiriman WhatsApp</span>
                                <span class="block text-caption text-muted">Kalau mati, invoice langganan tetap dibuat tetapi tidak dikirim.</span>
                            </span>
                        </label>
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <h2 class="text-xl font-bold">Isi pesan invoice</h2>
                    <p class="mt-1 text-small text-muted">PDF invoice dikirim sebagai lampiran setelah pesan ini.</p>
                    <div class="mt-5">
                        <x-ui.textarea name="waha_invoice_template" rows="10" :value="$settings->waha_invoice_template ?: $defaultTemplate"
                            hint="Bisa dipakai: {nama} {nomor} {total} {jatuh_tempo} {tanggal} {perusahaan} {link}. Tebal: *teks*." />
                    </div>
                </x-ui.card>

                <x-ui.button type="submit" icon="fa-floppy-disk">Simpan pengaturan</x-ui.button>
            </form>

            {{-- Sesi --}}
            <div class="space-y-6 lg:sticky lg:top-24"
                x-data="wahaSessions({
                    list: @js(route('dashboard.whatsapp.sessions')),
                    create: @js(route('dashboard.whatsapp.sessions.create')),
                    test: @js(route('dashboard.whatsapp.test')),
                    hasKey: @js($hasKey),
                    active: @js($activeSession),
                })" x-init="load()">
                <x-ui.card>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-bold">Sesi WhatsApp</h2>
                            <p class="mt-1 text-small text-muted">Buat, tautkan, dan pilih nomor pengirim dari sini. Tidak perlu membuka dasbor WAHA.</p>
                        </div>
                        <x-ui.button type="button" variant="ghost" size="sm" icon="fa-rotate" class="shrink-0 whitespace-nowrap" @click="load()" x-bind:disabled="loading || !hasKey" aria-label="Muat ulang daftar sesi">Muat ulang</x-ui.button>
                    </div>

                    <p x-show="!hasKey" x-cloak class="mt-4 rounded-lg bg-sun-100 p-3 text-small text-navy dark:bg-surface-2 dark:text-ink">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i> Isi alamat server dan API key di sebelah kiri, lalu simpan. Setelah itu sesi bisa dibuat di sini.
                    </p>

                    <p x-show="error" x-cloak class="mt-4 rounded-lg border border-danger-500/40 bg-danger-50 p-3 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert" x-text="error"></p>

                    {{-- Buat sesi --}}
                    <form class="mt-5 flex gap-2" @submit.prevent="createSession()">
                        <div class="min-w-0 flex-1">
                            <label for="new-session" class="sr-only">Nama sesi baru</label>
                            <input id="new-session" type="text" x-model="newName" class="input" placeholder="Nama sesi baru, misalnya fkstudio" pattern="[A-Za-z0-9_\-]+" maxlength="60" autocomplete="off" x-bind:disabled="!hasKey">
                        </div>
                        <x-ui.button type="submit" icon="fa-plus" x-bind:disabled="busy || !hasKey || !newName.trim()">Buat sesi</x-ui.button>
                    </form>
                    <p class="hint">Huruf, angka, strip, dan garis bawah. Satu sesi = satu nomor WhatsApp. WAHA edisi Core hanya mengizinkan satu sesi bernama <code>default</code>.</p>

                    {{-- Daftar sesi --}}
                    <p x-show="loaded && hasKey && sessions.length === 0 && !error" x-cloak class="mt-5 rounded-lg border border-dashed border-line p-4 text-center text-small text-muted">Belum ada sesi di server. Buat yang pertama di atas.</p>

                    <ul class="mt-5 space-y-3">
                        <template x-for="s in sessions" :key="s.name">
                            <li class="rounded-lg border-2 bg-surface-2 p-4" :class="s.name === active ? 'border-edge' : 'border-line'">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="flex flex-wrap items-center gap-2 font-bold">
                                            <span class="truncate" x-text="s.name"></span>
                                            <span x-show="s.name === active" class="rounded-full bg-sun-400 px-2 py-0.5 text-caption font-bold text-navy">Pengirim invoice</span>
                                        </p>
                                        <p class="truncate text-caption text-muted" x-text="account(s) || 'Belum ada nomor tertaut'"></p>
                                    </div>
                                    <span class="inline-flex shrink-0 items-center gap-2 rounded-full border-2 px-2.5 py-0.5 text-caption font-bold" :class="chip(s.status).class">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current" :class="['STARTING','SCAN_QR_CODE'].includes(s.status) && 'animate-pulse'"></span>
                                        <span x-text="chip(s.status).label"></span>
                                    </span>
                                </div>

                                {{-- QR --}}
                                <div x-show="s.status === 'SCAN_QR_CODE'" x-cloak class="mt-4 text-center">
                                    <div class="mx-auto flex h-56 w-56 items-center justify-center rounded-lg border-2 border-edge bg-white p-3 shadow-hard-sm">
                                        <img x-show="qr[s.name]" :src="qr[s.name]" alt="Kode QR untuk menautkan WhatsApp" class="h-full w-full object-contain">
                                        <i x-show="!qr[s.name]" class="fa-solid fa-spinner fa-spin text-2xl text-muted" aria-hidden="true"></i>
                                    </div>
                                    <p class="mx-auto mt-3 max-w-xs text-small text-muted">Di ponsel: WhatsApp, Perangkat tertaut, Tautkan perangkat, lalu pindai kode ini.</p>
                                </div>
                                <p x-show="s.status === 'STARTING'" x-cloak class="mt-3 text-small text-muted"><i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Sesi dimulai, kode QR muncul sebentar lagi.</p>
                                <p x-show="s.status === 'FAILED'" x-cloak class="mt-3 text-small text-danger-700 dark:text-red-300">Sesi gagal berjalan. Hentikan lalu mulai ulang.</p>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    <x-ui.button type="button" size="sm" icon="fa-play" x-show="['STOPPED','FAILED'].includes(s.status)" @click="act(s, 'start')" x-bind:disabled="busy">Mulai</x-ui.button>
                                    <x-ui.button type="button" size="sm" variant="secondary" icon="fa-check" x-show="s.name !== active" @click="activate(s)" x-bind:disabled="busy">Jadikan pengirim</x-ui.button>
                                    <x-ui.button type="button" size="sm" variant="secondary" icon="fa-stop" x-show="['STARTING','SCAN_QR_CODE','WORKING'].includes(s.status)" @click="act(s, 'stop')" x-bind:disabled="busy">Hentikan</x-ui.button>
                                    <x-ui.button type="button" size="sm" variant="secondary" icon="fa-right-from-bracket" x-show="s.status === 'WORKING'" @click="confirmThen('Putuskan nomor?', 'Nomor keluar dari sesi ' + s.name + ' dan perlu dipindai ulang.', 'Putuskan', () => act(s, 'logout'))" x-bind:disabled="busy">Putuskan nomor</x-ui.button>
                                    <x-ui.button type="button" size="sm" variant="danger-soft" icon="fa-trash-can" @click="confirmThen('Hapus sesi?', 'Sesi ' + s.name + ' dihapus dari server WAHA.' + (s.name === active ? ' Ini sesi pengirim invoice saat ini.' : ''), 'Ya, hapus', () => destroy(s))" x-bind:disabled="busy">Hapus</x-ui.button>
                                </div>
                            </li>
                        </template>
                    </ul>
                </x-ui.card>

                <x-ui.card x-show="activeWorking" x-cloak>
                    <h2 class="text-xl font-bold">Kirim pesan percobaan</h2>
                    <p class="mt-1 text-small text-muted">Lewat sesi <strong x-text="active"></strong>. Pastikan berfungsi sebelum dipakai untuk invoice.</p>
                    <div class="mt-4 flex gap-2">
                        <input type="tel" x-model="testPhone" class="input min-w-0 flex-1" placeholder="0812-3456-7890" aria-label="Nomor tujuan">
                        <x-ui.button type="button" icon="fa-paper-plane" @click="sendTest()" x-bind:disabled="busy || !testPhone">Kirim</x-ui.button>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function wahaSessions(cfg) {
            return {
                cfg, hasKey: cfg.hasKey, active: cfg.active,
                sessions: [], qr: {}, loaded: false, loading: false, busy: false,
                error: null, newName: '', testPhone: '', timer: null,
                get activeWorking() { return this.sessions.some(s => s.name === this.active && s.status === 'WORKING'); },
                url(name, action = '') { return this.cfg.list + '/' + encodeURIComponent(name) + (action ? '/' + action : ''); },
                chip(status) {
                    const map = {
                        WORKING: ['Tersambung', 'border-navy bg-emerald-200 text-navy'],
                        SCAN_QR_CODE: ['Menunggu pindai', 'border-navy bg-sun-300 text-navy'],
                        STARTING: ['Memulai', 'border-navy bg-sun-300 text-navy'],
                        STOPPED: ['Berhenti', 'border-line bg-surface text-muted'],
                        FAILED: ['Gagal', 'border-navy bg-red-200 text-navy'],
                    };
                    const [label, cls] = map[status] ?? [status, 'border-line bg-surface text-muted'];
                    return { label, class: cls };
                },
                account(s) {
                    if (!s.me) return null;
                    return (s.me.pushName ? s.me.pushName + ' · ' : '') + String(s.me.id || '').replace('@c.us', '');
                },
                async call(url, method = 'GET', body = null) {
                    const res = await fetch(url, {
                        method,
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                        body: body ? JSON.stringify(body) : null,
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(data.error || (data.errors && Object.values(data.errors)[0][0]) || data.message || 'Permintaan gagal (' + res.status + ')');
                    return data;
                },
                async load() {
                    if (!this.hasKey) return;
                    this.loading = true;
                    try {
                        const r = await this.call(this.cfg.list);
                        this.sessions = r.sessions;
                        this.active = r.active;
                        this.error = null;
                        await Promise.all(this.sessions.filter(s => s.status === 'SCAN_QR_CODE').map(s => this.loadQr(s)));
                    } catch (e) { this.error = e.message; }
                    this.loading = false;
                    this.loaded = true;
                    this.schedule();
                },
                async loadQr(s) {
                    try {
                        const r = await this.call(this.url(s.name, 'qr'));
                        this.qr[s.name] = r.qr ? 'data:' + r.qr.mimetype + ';base64,' + r.qr.data : null;
                    } catch (e) { /* the next poll retries */ }
                },
                schedule() {
                    clearTimeout(this.timer);
                    // Poll while a session is moving (starting or waiting for a scan); stay quiet otherwise.
                    if (this.sessions.some(s => ['STARTING', 'SCAN_QR_CODE'].includes(s.status))) {
                        this.timer = setTimeout(() => this.load(), 4000);
                    }
                },
                async run(fn) {
                    this.busy = true;
                    try { await fn(); this.error = null; } catch (e) { this.error = e.message; }
                    this.busy = false;
                    setTimeout(() => this.load(), 700);
                },
                createSession() {
                    const name = this.newName.trim();
                    return this.run(async () => {
                        await this.call(this.cfg.create, 'POST', { name });
                        this.newName = '';
                        window.toast && window.toast('success', 'Sesi ' + name + ' dibuat');
                    });
                },
                act(s, action) { return this.run(() => this.call(this.url(s.name, action), 'POST')); },
                activate(s) {
                    return this.run(async () => {
                        await this.call(this.url(s.name, 'activate'), 'POST');
                        window.toast && window.toast('success', s.name + ' sekarang jadi pengirim invoice');
                    });
                },
                destroy(s) { return this.run(() => this.call(this.url(s.name), 'DELETE')); },
                async confirmThen(title, text, ok, fn) {
                    const yes = window.Swal
                        ? (await Swal.fire({ title, text, icon: 'warning', showCancelButton: true, confirmButtonText: ok, cancelButtonText: 'Batal' })).isConfirmed
                        : confirm(text);
                    if (yes) fn();
                },
                sendTest() {
                    return this.run(async () => {
                        await this.call(this.cfg.test, 'POST', { phone: this.testPhone });
                        window.toast && window.toast('success', 'Pesan percobaan terkirim');
                    });
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
