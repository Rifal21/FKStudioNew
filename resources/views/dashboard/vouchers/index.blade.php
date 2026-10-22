<x-app-layout>
    <x-slot name="header">Voucher Diskon</x-slot>

    <div class="mx-auto max-w-7xl space-y-6" x-data="{
        modalOpen: false,
        editMode: false,
        currentVoucher: {
            code: '',
            type: 'percent',
            value: 0,
            max_uses: '',
            expires_at: '',
            is_active: 1
        },
        openCreate() {
            this.resetForm();
            this.modalOpen = true;
            this.$nextTick(() => this.$refs.codeInput && this.$refs.codeInput.focus());
        },
        initEdit(voucherData) {
            this.currentVoucher = { ...voucherData };
            // Format expires_at date to yyyy-MM-dd for standard HTML input
            if (this.currentVoucher.expires_at) {
                this.currentVoucher.expires_at = this.currentVoucher.expires_at.split('T')[0];
            }
            this.editMode = true;
            this.modalOpen = true;
            this.$nextTick(() => this.$refs.codeInput && this.$refs.codeInput.focus());
        },
        closeModal() {
            this.modalOpen = false;
        },
        resetForm() {
            this.editMode = false;
            this.currentVoucher = {
                code: '',
                type: 'percent',
                value: 0,
                max_uses: '',
                expires_at: '',
                is_active: 1
            };
        }
    }" @keydown.escape.window="if (modalOpen) closeModal()">

        <x-ui.page-header title="Voucher Diskon" description="Buat dan atur kode diskon untuk pelanggan.">
            <x-slot:actions>
                <button type="button" class="btn btn-primary" @click="openCreate()">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i> Buat Voucher
                </button>
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

        {{-- List --}}
        @if (count($vouchers) === 0)
            <x-ui.card>
                <x-ui.empty-state icon="fa-ticket" title="Belum ada voucher" text="Buat voucher pertama untuk memberi diskon kepada pelanggan.">
                    <button type="button" class="btn btn-primary" @click="openCreate()">
                        <i class="fa-solid fa-plus" aria-hidden="true"></i> Buat Voucher
                    </button>
                </x-ui.empty-state>
            </x-ui.card>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($vouchers as $voucher)
                    @php
                        $exhausted = $voucher->max_uses !== null && $voucher->used_count >= $voucher->max_uses;
                        $usagePercent = $voucher->max_uses ? min(100, round(($voucher->used_count / $voucher->max_uses) * 100)) : 0;
                    @endphp
                    <article class="card card-pad flex flex-col {{ $voucher->is_active ? '' : 'opacity-75' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-2">
                                @if (! $voucher->is_active)
                                    <x-ui.badge variant="neutral" dot>Nonaktif</x-ui.badge>
                                @elseif ($exhausted)
                                    <x-ui.badge variant="danger" dot>Kuota habis</x-ui.badge>
                                @else
                                    <x-ui.badge variant="success" dot>Siap dipakai</x-ui.badge>
                                @endif
                            </div>
                            <div class="-mr-2 -mt-2 flex items-center">
                                <button type="button" @click="initEdit({{ $voucher->toJson() }})" class="btn-icon" aria-label="Ubah voucher {{ $voucher->code }}" title="Ubah">
                                    <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                </button>
                                <form action="{{ route('dashboard.vouchers.destroy', $voucher->id) }}" method="POST"
                                      data-confirm="Voucher {{ $voucher->code }} akan dihapus permanen." data-confirm-title="Hapus voucher?" data-confirm-ok="Ya, hapus">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon hover:text-danger-600" aria-label="Hapus voucher {{ $voucher->code }}" title="Hapus">
                                        <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="inline-block select-all rounded-md border border-line bg-brand-soft px-3 py-1.5 font-mono text-lg font-bold uppercase tracking-wider text-brand-text">
                                {{ $voucher->code }}
                            </div>
                            <p class="mt-3 text-3xl font-extrabold">
                                @if ($voucher->type === 'percent')
                                    {{ (float) $voucher->value }}% <span class="text-small font-semibold text-muted">diskon</span>
                                @else
                                    Rp {{ number_format($voucher->value, 0, ',', '.') }} <span class="text-small font-semibold text-muted">potongan</span>
                                @endif
                            </p>
                        </div>

                        <div class="mt-5 flex-1 space-y-4">
                            <div class="space-y-2">
                                <div class="flex justify-between text-small text-muted">
                                    <span>Pemakaian / kuota</span>
                                    <span class="font-semibold text-ink">{{ $voucher->used_count }} / {{ $voucher->max_uses ?? '∞' }}</span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-surface-2" role="progressbar" aria-label="Pemakaian voucher"
                                     aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $usagePercent }}">
                                    @if ($voucher->max_uses)
                                        <div class="h-2 rounded-full bg-brand-600" style="width: {{ $usagePercent }}%"></div>
                                    @else
                                        <div class="h-2 rounded-full bg-line" style="width: 10%"></div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 border-t border-line pt-4 text-small text-muted">
                                <i class="fa-solid fa-calendar" aria-hidden="true"></i>
                                @if ($voucher->expires_at)
                                    Berlaku hingga {{ \Carbon\Carbon::parse($voucher->expires_at)->format('d M Y') }}
                                @else
                                    Tanpa batas waktu
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        {{-- Create / edit modal --}}
        <div x-show="modalOpen" x-cloak x-transition.opacity.duration.200ms class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center"
             role="dialog" aria-modal="true" aria-labelledby="voucher-modal-title">
            <div class="absolute inset-0 bg-ink/50 backdrop-blur-sm" @click="closeModal()"></div>

            <div class="card relative max-h-[90vh] w-full max-w-xl overflow-y-auto">
                <div class="flex items-start justify-between gap-4 border-b border-line px-5 py-4">
                    <h2 id="voucher-modal-title" class="text-lg font-bold" x-text="editMode ? 'Ubah Voucher Diskon' : 'Buat Voucher Baru'"></h2>
                    <button type="button" class="btn-icon shrink-0" @click="closeModal()" aria-label="Tutup">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>

                <form :action="editMode ? '{{ route('dashboard.vouchers.index') }}/' + currentVoucher.id : '{{ route('dashboard.vouchers.store') }}'"
                      method="POST" class="space-y-4 p-5">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PATCH">
                    </template>

                    <div>
                        <label for="voucher-code" class="label">Kode voucher</label>
                        <input id="voucher-code" type="text" name="code" required x-model="currentVoucher.code" x-ref="codeInput" placeholder="FKSTUDIO10"
                               class="input font-mono font-semibold uppercase tracking-wider">
                        <p class="hint">Contoh: LAUNCHING50</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="voucher-type" class="label">Jenis diskon</label>
                            <select id="voucher-type" name="type" required x-model="currentVoucher.type" class="select">
                                <option value="percent">Persen (%)</option>
                                <option value="fixed">Nominal tetap (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label for="voucher-value" class="label">Nilai diskon</label>
                            <input id="voucher-value" type="number" name="value" required x-model="currentVoucher.value" min="0" placeholder="10" class="input">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="voucher-max-uses" class="label">Kuota pakai</label>
                            <input id="voucher-max-uses" type="number" name="max_uses" x-model="currentVoucher.max_uses" min="0" placeholder="Tanpa batas" class="input">
                        </div>
                        <div>
                            <label for="voucher-expires" class="label">Masa berlaku</label>
                            <input id="voucher-expires" type="date" name="expires_at" x-model="currentVoucher.expires_at" class="input">
                        </div>
                    </div>

                    <label class="flex min-h-[44px] cursor-pointer items-center gap-3">
                        <span class="relative inline-flex items-center">
                            <input type="checkbox" name="is_active" value="1" x-model="currentVoucher.is_active" :checked="currentVoucher.is_active" class="peer sr-only">
                            <span class="h-6 w-11 rounded-full bg-muted transition peer-checked:bg-brand-600 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500/50 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-surface after:transition-all after:content-[''] peer-checked:after:translate-x-5"></span>
                        </span>
                        <span class="text-small font-semibold">Voucher aktif</span>
                    </label>

                    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                        <button type="button" class="btn btn-secondary" @click="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                            <span x-text="editMode ? 'Simpan Perubahan' : 'Terbitkan Voucher'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
