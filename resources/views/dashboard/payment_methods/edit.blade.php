<x-app-layout>
    <x-slot name="header">Metode Pembayaran</x-slot>

    @php
        $paymentMethods = $settings->payment_methods;
        if (empty($paymentMethods) || !is_array($paymentMethods)) {
            $paymentMethods = [['bank' => '', 'number' => '', 'name' => '']];
        }
        $paymentMethods = array_values($paymentMethods);
    @endphp

    <form action="{{ route('dashboard.payment_methods.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mx-auto max-w-5xl space-y-6">
            <x-ui.page-header title="Metode Pembayaran Manual" description="Rekening bank dan QRIS yang ditampilkan sebagai instruksi transfer saat klien checkout." />

            @if ($errors->any())
                <div class="rounded-lg border border-danger-500 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-danger-100" role="alert">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex items-start gap-4 rounded-lg border border-line bg-brand-soft p-4 text-brand-text">
                <i class="fa-solid fa-circle-info mt-1" aria-hidden="true"></i>
                <p class="text-small">Semua rekening dan kode QRIS di halaman ini otomatis muncul pada formulir checkout sebagai instruksi pembayaran transfer resmi.</p>
            </div>

            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
                {{-- QRIS --}}
                <div class="card card-pad lg:col-span-1"
                    x-data="{
                        invoiceQrisPreview: null,
                        qrisRemoved: false,
                        hasCurrent: {{ $settings->invoice_qris ? 'true' : 'false' }},
                        handleInvoiceQris(e) {
                            const file = e.target.files[0];
                            if (file) {
                                this.invoiceQrisPreview = URL.createObjectURL(file);
                                this.qrisRemoved = false;
                            }
                        },
                        handleRemoveQris() {
                            this.qrisRemoved = true;
                            this.invoiceQrisPreview = null;
                        }
                    }">
                    <h2 class="flex items-center gap-2 text-xl font-bold">
                        <i class="fa-solid fa-qrcode text-brand-text" aria-hidden="true"></i> Kode QRIS
                    </h2>
                    <p class="mt-1 text-small text-muted">Unggah kode QRIS agar klien bisa membayar dengan memindai.</p>

                    <div class="mt-5 flex aspect-square w-full items-center justify-center overflow-hidden rounded-lg border border-dashed border-line bg-surface-2 p-3">
                        <img x-show="invoiceQrisPreview" x-cloak :src="invoiceQrisPreview" alt="Pratinjau QRIS baru" class="h-full w-full rounded-md object-contain">
                        @if ($settings->invoice_qris)
                            <img x-show="!invoiceQrisPreview && !qrisRemoved" src="{{ $settings->invoice_qris_url }}" alt="QRIS saat ini" class="h-full w-full rounded-md object-contain">
                        @endif
                        <div x-show="!invoiceQrisPreview && (qrisRemoved || !hasCurrent)" @if ($settings->invoice_qris) x-cloak @endif class="flex flex-col items-center gap-2 text-muted">
                            <i class="fa-solid fa-qrcode text-6xl" aria-hidden="true"></i>
                            <span class="text-small">Belum ada QRIS</span>
                        </div>
                    </div>

                    <input type="hidden" name="remove_invoice_qris" :value="qrisRemoved ? '1' : '0'">

                    <div class="mt-4 flex flex-col gap-2">
                        <label class="btn btn-secondary w-full cursor-pointer focus-within:ring-2 focus-within:ring-brand-500/40">
                            <i class="fa-solid fa-cloud-arrow-up" aria-hidden="true"></i>
                            <span x-text="(hasCurrent && !qrisRemoved) || invoiceQrisPreview ? 'Ganti QRIS' : 'Unggah QRIS'">Unggah QRIS</span>
                            <input type="file" name="invoice_qris" accept="image/*" class="sr-only" @change="handleInvoiceQris">
                        </label>
                        @if ($settings->invoice_qris)
                            <button type="button" @click="handleRemoveQris" x-show="!qrisRemoved && !invoiceQrisPreview" class="btn btn-danger-soft w-full">
                                <i class="fa-solid fa-trash-can" aria-hidden="true"></i> Hapus QRIS
                            </button>
                            <button type="button" @click="qrisRemoved = false" x-show="qrisRemoved" x-cloak class="btn btn-ghost w-full">
                                <i class="fa-solid fa-rotate-left" aria-hidden="true"></i> Batalkan
                            </button>
                        @endif
                    </div>
                    <p class="hint">PNG atau JPG persegi, maks 2MB. Perubahan tersimpan saat Anda menekan Simpan.</p>
                </div>

                {{-- Rekening bank --}}
                <div class="card card-pad lg:col-span-2"
                    x-data="{
                        banks: @js($paymentMethods),
                        addBank() { this.banks.push({ bank: '', number: '', name: '' }) },
                        removeBank(index) { if (this.banks.length > 1) this.banks.splice(index, 1) }
                    }">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="flex items-center gap-2 text-xl font-bold">
                                <i class="fa-solid fa-building-columns text-brand-text" aria-hidden="true"></i> Rekening bank
                            </h2>
                            <p class="mt-1 text-small text-muted">Rekening untuk transfer manual.</p>
                        </div>
                        <button type="button" @click="addBank" class="btn btn-secondary shrink-0">
                            <i class="fa-solid fa-plus" aria-hidden="true"></i> Tambah rekening
                        </button>
                    </div>

                    <div class="mt-6 space-y-4">
                        <template x-for="(bank, index) in banks" :key="index">
                            <div class="rounded-lg border border-line bg-surface-2 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-soft text-small font-bold text-brand-text" x-text="index + 1"></span>
                                        <span class="text-small font-semibold">Rekening <span x-text="index + 1"></span></span>
                                    </div>
                                    <button type="button" @click="removeBank(index)" :disabled="banks.length <= 1"
                                        :aria-label="'Hapus rekening ' + (index + 1)"
                                        class="btn btn-danger-soft btn-sm">
                                        <i class="fa-solid fa-trash-can" aria-hidden="true"></i> Hapus
                                    </button>
                                </div>

                                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div>
                                        <label :for="'bank-name-' + index" class="label">Nama bank</label>
                                        <input type="text" :id="'bank-name-' + index" :name="'payment_methods[' + index + '][bank]'" x-model="bank.bank" required placeholder="BCA / Mandiri..." class="input">
                                    </div>
                                    <div>
                                        <label :for="'bank-number-' + index" class="label">Nomor rekening</label>
                                        <input type="text" :id="'bank-number-' + index" :name="'payment_methods[' + index + '][number]'" x-model="bank.number" required placeholder="123-456-789..." class="input">
                                    </div>
                                    <div>
                                        <label :for="'bank-holder-' + index" class="label">Atas nama</label>
                                        <input type="text" :id="'bank-holder-' + index" :name="'payment_methods[' + index + '][name]'" x-model="bank.name" required placeholder="Rifal Kurniawan..." class="input">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sticky save bar --}}
        <div class="sticky bottom-[calc(57px+env(safe-area-inset-bottom))] z-20 -mx-4 mt-6 border-t border-line bg-surface/90 px-4 py-3 backdrop-blur lg:bottom-0 lg:-mx-8 lg:px-8">
            <div class="mx-auto flex max-w-5xl items-center justify-end">
                <x-ui.button type="submit" icon="fa-floppy-disk" class="w-full sm:w-auto">Simpan metode pembayaran</x-ui.button>
            </div>
        </div>
    </form>
</x-app-layout>
