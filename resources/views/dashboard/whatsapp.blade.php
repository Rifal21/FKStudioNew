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
                        <x-ui.input name="waha_session" label="Nama sesi" :value="$settings->waha_session ?: $defaultSession" required hint="Nama bebas, tanpa spasi. Satu sesi = satu nomor WhatsApp pengirim." />
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
                x-data="wahaSession({
                    status: @js(route('dashboard.whatsapp.status')),
                    qr: @js(route('dashboard.whatsapp.qr')),
                    start: @js(route('dashboard.whatsapp.start')),
                    stop: @js(route('dashboard.whatsapp.stop')),
                    logout: @js(route('dashboard.whatsapp.logout')),
                    test: @js(route('dashboard.whatsapp.test')),
                    hasKey: @js($hasKey),
                })" x-init="refresh()">
                <x-ui.card>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-bold">Sesi WhatsApp</h2>
                            <p class="mt-1 text-small text-muted" x-text="session"></p>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-2 rounded-full border-2 px-3 py-1 text-small font-bold" :class="chip.class">
                            <span class="h-2 w-2 rounded-full bg-current" :class="busy && 'animate-pulse'"></span>
                            <span x-text="chip.label"></span>
                        </span>
                    </div>

                    <p x-show="!hasKey" x-cloak class="mt-4 rounded-lg bg-sun-100 p-3 text-small text-navy dark:bg-surface-2 dark:text-ink">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i> Simpan alamat server dan API key dulu di sebelah kiri, baru sesi bisa dikelola.
                    </p>

                    <p x-show="error" x-cloak class="mt-4 rounded-lg border border-danger-500/40 bg-danger-50 p-3 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert" x-text="error"></p>

                    {{-- Terhubung --}}
                    <div x-show="state === 'WORKING'" x-cloak class="mt-5 flex items-center gap-4 rounded-lg border-2 border-edge bg-brand-soft p-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-sun-400 text-xl text-navy"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></span>
                        <div class="min-w-0">
                            <p class="text-small font-bold">Tersambung</p>
                            <p class="truncate text-small text-muted" x-text="account || 'Nomor pengirim aktif'"></p>
                        </div>
                    </div>

                    {{-- QR --}}
                    <div x-show="state === 'SCAN_QR_CODE'" x-cloak class="mt-5 text-center">
                        <div class="mx-auto flex h-64 w-64 items-center justify-center rounded-lg border-2 border-edge bg-white p-3 shadow-hard-sm">
                            <img x-show="qrImage" :src="qrImage" alt="Kode QR untuk menautkan WhatsApp" class="h-full w-full object-contain">
                            <i x-show="!qrImage" class="fa-solid fa-spinner fa-spin text-2xl text-muted" aria-hidden="true"></i>
                        </div>
                        <ol class="mx-auto mt-4 max-w-xs list-inside list-decimal space-y-1 text-left text-small text-muted">
                            <li>Buka WhatsApp di ponsel</li>
                            <li>Perangkat tertaut, lalu Tautkan perangkat</li>
                            <li>Pindai kode ini</li>
                        </ol>
                    </div>

                    <p x-show="state === 'STARTING'" x-cloak class="mt-5 text-small text-muted"><i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Sesi sedang dimulai, kode QR muncul sebentar lagi.</p>
                    <p x-show="state === 'STOPPED' || state === 'NOT_CREATED'" x-cloak class="mt-5 text-small text-muted" x-text="state === 'NOT_CREATED' ? 'Sesi belum dibuat di server. Tekan tombol di bawah untuk membuat dan memulainya.' : 'Sesi berhenti. Mulai lagi untuk menautkan nomor.'"></p>
                    <p x-show="state === 'FAILED'" x-cloak class="mt-5 text-small text-danger-700 dark:text-red-300">Sesi gagal berjalan. Coba hentikan lalu mulai ulang.</p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <x-ui.button type="button" icon="fa-play" @click="act('start')" x-show="['NOT_CREATED','STOPPED','FAILED'].includes(state)" x-bind:disabled="busy || !hasKey">
                            <span x-text="state === 'NOT_CREATED' ? 'Buat & mulai sesi' : 'Mulai sesi'"></span>
                        </x-ui.button>
                        <x-ui.button type="button" variant="secondary" icon="fa-stop" @click="act('stop')" x-show="['STARTING','SCAN_QR_CODE','WORKING'].includes(state)" x-bind:disabled="busy">Hentikan</x-ui.button>
                        <x-ui.button type="button" variant="secondary" icon="fa-right-from-bracket" @click="confirmLogout()" x-show="state === 'WORKING'" x-bind:disabled="busy">Putuskan nomor</x-ui.button>
                        <x-ui.button type="button" variant="ghost" icon="fa-rotate" @click="refresh()" x-bind:disabled="busy || !hasKey">Segarkan</x-ui.button>
                    </div>
                </x-ui.card>

                <x-ui.card x-show="state === 'WORKING'" x-cloak>
                    <h2 class="text-xl font-bold">Kirim pesan percobaan</h2>
                    <p class="mt-1 text-small text-muted">Pastikan nomor pengirim berfungsi sebelum dipakai untuk invoice.</p>
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
        function wahaSession(urls) {
            return {
                urls, hasKey: urls.hasKey, session: @js($settings->waha_session ?: $defaultSession),
                state: 'UNKNOWN', account: null, qrImage: null, error: null, busy: false, testPhone: '', timer: null,
                get chip() {
                    const map = {
                        WORKING: ['Tersambung', 'border-navy bg-emerald-200 text-navy'],
                        SCAN_QR_CODE: ['Menunggu pindai', 'border-navy bg-sun-300 text-navy'],
                        STARTING: ['Memulai', 'border-navy bg-sun-300 text-navy'],
                        STOPPED: ['Berhenti', 'border-line bg-surface-2 text-muted'],
                        NOT_CREATED: ['Belum dibuat', 'border-line bg-surface-2 text-muted'],
                        FAILED: ['Gagal', 'border-navy bg-red-200 text-navy'],
                        UNKNOWN: ['Memeriksa', 'border-line bg-surface-2 text-muted'],
                    };
                    const [label, cls] = map[this.state] ?? [this.state, 'border-line bg-surface-2 text-muted'];
                    return { label, class: cls };
                },
                async call(url, method = 'GET', body = null) {
                    const res = await fetch(url, {
                        method,
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                        body: body ? JSON.stringify(body) : null,
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(data.error || data.message || 'Permintaan gagal (' + res.status + ')');
                    return data;
                },
                async refresh() {
                    if (!this.hasKey) { this.state = 'NOT_CREATED'; return; }
                    try {
                        const s = await this.call(this.urls.status);
                        this.error = null;
                        this.state = s.status;
                        this.account = s.me ? (s.me.pushName ? s.me.pushName + ' · ' : '') + String(s.me.id || '').replace('@c.us', '') : null;
                        if (this.state === 'SCAN_QR_CODE') await this.loadQr();
                        else this.qrImage = null;
                    } catch (e) {
                        this.error = e.message;
                        this.state = 'UNKNOWN';
                    }
                    this.schedule();
                },
                async loadQr() {
                    try {
                        const r = await this.call(this.urls.qr);
                        this.qrImage = r.qr ? 'data:' + r.qr.mimetype + ';base64,' + r.qr.data : null;
                    } catch (e) { /* the next poll retries */ }
                },
                schedule() {
                    clearTimeout(this.timer);
                    // Poll while the session is moving; stay quiet once it is settled.
                    if (['STARTING', 'SCAN_QR_CODE'].includes(this.state)) this.timer = setTimeout(() => this.refresh(), 4000);
                },
                async act(name) {
                    this.busy = true;
                    try { await this.call(this.urls[name], 'POST'); this.error = null; }
                    catch (e) { this.error = e.message; }
                    this.busy = false;
                    setTimeout(() => this.refresh(), 800);
                },
                async confirmLogout() {
                    const ok = window.Swal
                        ? (await Swal.fire({ title: 'Putuskan nomor?', text: 'Nomor akan keluar dari sesi ini dan perlu dipindai ulang.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Putuskan', cancelButtonText: 'Batal' })).isConfirmed
                        : confirm('Putuskan nomor dari sesi ini?');
                    if (ok) this.act('logout');
                },
                async sendTest() {
                    this.busy = true;
                    try {
                        await this.call(this.urls.test, 'POST', { phone: this.testPhone });
                        window.toast && window.toast('success', 'Pesan percobaan terkirim');
                        this.error = null;
                    } catch (e) { this.error = e.message; }
                    this.busy = false;
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
