<x-app-layout>
    <x-slot name="header">Buat Invoice</x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <x-ui.page-header title="Buat invoice baru" description="Isi detail klien dan item tagihan. Total dihitung otomatis.">
            <x-slot:actions>
                <x-ui.button :href="route('dashboard.invoices.index')" variant="secondary" icon="fa-arrow-left">Kembali</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        <form action="{{ route('dashboard.invoices.store') }}" method="POST" x-data="{
            items: @js(old('items', [['description' => '', 'quantity' => 1, 'unit_price' => 0]])),
            discount: @js((float) old('discount', 0)),
            discount_type: @js(old('discount_type', 'percent')),
            tax: @js((float) old('tax', 0)),
            tax_type: @js(old('tax_type', 'percent')),
            addItem() {
                this.items.push({ description: '', quantity: 1, unit_price: 0 });
            },
            moveItem(from, to) {
                const [moved] = this.items.splice(from, 1);
                this.items.splice(to, 0, moved);
            },
            removeItem(index) {
                if (this.items.length > 1) this.items.splice(index, 1);
            },
            money(value) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0);
            },
            get subtotal() {
                return this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
            },
            get discountAmount() {
                return this.discount_type === 'percent'
                    ? (this.subtotal * (this.discount / 100))
                    : this.discount;
            },
            get taxAmount() {
                return this.tax_type === 'percent'
                    ? ((this.subtotal - this.discountAmount) * (this.tax / 100))
                    : this.tax;
            },
            get finalTotal() {
                return this.subtotal - this.discountAmount + this.taxAmount;
            }
        }">
            @csrf

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-danger-500/40 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert">
                    <p class="font-semibold"><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i> Periksa kembali isian Anda:</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:items-start">
                <div class="space-y-6 lg:col-span-2">
                    {{-- Detail invoice --}}
                    <x-ui.card>
                        <h2 class="mb-5 flex items-center gap-3 text-lg font-bold">
                            <span class="flex h-10 w-10 items-center justify-center rounded-md bg-brand-soft text-brand-text"><i class="fa-solid fa-file-invoice" aria-hidden="true"></i></span>
                            Detail invoice
                        </h2>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="md:col-span-2">
                            <x-ui.input name="invoice_number" label="Nomor invoice" :value="$invoiceNumber" required readonly class="bg-surface-2 font-semibold" />
                            </div>
                            <x-ui.input name="issue_date" type="date" label="Tanggal terbit" :value="date('Y-m-d')" required />
                            <x-ui.input name="due_date" type="date" label="Jatuh tempo (opsional)" />
                        </div>
                    </x-ui.card>

                    {{-- Klien --}}
                    <x-ui.card>
                        <h2 class="mb-5 flex items-center gap-3 text-lg font-bold">
                            <span class="flex h-10 w-10 items-center justify-center rounded-md bg-brand-soft text-brand-text"><i class="fa-solid fa-user-tie" aria-hidden="true"></i></span>
                            Informasi klien
                        </h2>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <x-ui.input name="client_name" label="Nama klien" required placeholder="Pemilik proyek / perusahaan" />
                            <x-ui.input name="client_email" type="email" label="Email klien" placeholder="klien@contoh.com" />
                            <div class="md:col-span-2">
                                <x-ui.textarea name="client_address" label="Alamat klien" rows="2" placeholder="Alamat penagihan lengkap" />
                            </div>
                        </div>
                    </x-ui.card>

                    {{-- Item --}}
                    <x-ui.card>
                        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                            <h2 class="flex items-center gap-3 text-lg font-bold">
                                <span class="flex h-10 w-10 items-center justify-center rounded-md bg-brand-soft text-brand-text"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span>
                                Item tagihan
                            </h2>
                            <x-ui.button variant="secondary" size="sm" icon="fa-plus" @click="addItem">Tambah item</x-ui.button>
                        </div>

                        @error('items')<p class="field-error mb-3">{{ $message }}</p>@enderror

                        <p class="mb-3 text-small text-muted">Seret <i class="fa-solid fa-grip-vertical" aria-hidden="true"></i> untuk mengubah urutan item. Urutan ini dipakai di invoice dan PDF.</p>

                        <div class="space-y-4" x-sortable="moveItem">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="rounded-lg border border-line bg-surface-2 p-4" data-sortable-item>
                                    <div class="mb-3 flex items-center justify-between gap-3">
                                        <button type="button" data-drag-handle class="btn-icon -ml-1 cursor-grab touch-none active:cursor-grabbing" aria-label="Seret untuk mengubah urutan" title="Seret untuk mengubah urutan"
                                            @keydown.up.prevent="index > 0 && (moveItem(index, index - 1), $nextTick(() => $el.closest('[data-sortable-item]').previousElementSibling?.querySelector('[data-drag-handle]')?.focus()))"
                                            @keydown.down.prevent="index < items.length - 1 && (moveItem(index, index + 1), $nextTick(() => $el.closest('[data-sortable-item]').nextElementSibling?.querySelector('[data-drag-handle]')?.focus()))"><i class="fa-solid fa-grip-vertical" aria-hidden="true"></i></button>
                                        <p class="mr-auto text-small font-semibold">Item <span x-text="index + 1"></span> <span class="font-normal text-muted">&middot; <span x-text="money(item.quantity * item.unit_price)"></span></span></p>
                                        <button type="button" class="btn-icon disabled:cursor-not-allowed disabled:opacity-40" @click="removeItem(index)" :disabled="items.length === 1" aria-label="Hapus item"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                                    </div>
                                    <div class="grid grid-cols-12 gap-3">
                                        <div class="col-span-12 md:col-span-6">
                                            <label class="label" :for="`item-description-${index}`">Deskripsi</label>
                                            <input class="input" type="text" :id="`item-description-${index}`" :name="`items[${index}][description]`" x-model="item.description" required placeholder="Nama layanan / produk">
                                        </div>
                                        <div class="col-span-5 md:col-span-2">
                                            <label class="label" :for="`item-quantity-${index}`">Jumlah</label>
                                            <input class="input" type="number" :id="`item-quantity-${index}`" :name="`items[${index}][quantity]`" x-model.number="item.quantity" required min="1">
                                        </div>
                                        <div class="col-span-7 md:col-span-4">
                                            <label class="label" :for="`item-price-${index}`">Harga satuan (Rp)</label>
                                            <input class="input" type="number" :id="`item-price-${index}`" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" required>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </x-ui.card>

                    {{-- Catatan --}}
                    <x-ui.card>
                        <x-ui.textarea name="notes" label="Catatan tambahan" rows="4" placeholder="Ketentuan pembayaran, detail rekening, dll." />
                    </x-ui.card>
                </div>

                {{-- Ringkasan --}}
                <aside class="space-y-6 lg:sticky lg:top-24">
                    <x-ui.card>
                        <h2 class="mb-5 text-lg font-bold">Ringkasan</h2>
                        <div class="space-y-5">
                            <div class="flex items-center justify-between gap-3 text-small">
                                <span class="text-muted">Subtotal</span>
                                <span class="font-semibold" x-text="money(subtotal)"></span>
                            </div>

                            <div>
                                <div class="flex items-center justify-between gap-3">
                                    <label class="label !mb-0" for="discount">Diskon</label>
                                    <span class="text-small font-semibold text-brand-text" x-text="'- ' + money(discountAmount)"></span>
                                </div>
                                <div class="mt-1.5 flex gap-2">
                                    <input class="input min-w-0 flex-1" type="number" id="discount" name="discount" x-model.number="discount" min="0" step="any">
                                    <div class="w-32 shrink-0">
                                        <select class="select" name="discount_type" x-model="discount_type" aria-label="Jenis diskon">
                                            <option value="percent">%</option>
                                            <option value="fixed">Tetap (Rp)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between gap-3">
                                    <label class="label !mb-0" for="tax">Pajak (PPN)</label>
                                    <span class="text-small font-semibold" x-text="'+ ' + money(taxAmount)"></span>
                                </div>
                                <div class="mt-1.5 flex gap-2">
                                    <input class="input min-w-0 flex-1" type="number" id="tax" name="tax" x-model.number="tax" min="0" step="any">
                                    <div class="w-32 shrink-0">
                                        <select class="select" name="tax_type" x-model="tax_type" aria-label="Jenis pajak">
                                            <option value="percent">%</option>
                                            <option value="fixed">Tetap (Rp)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-3 border-t border-line pt-4">
                                <span class="font-bold">Total tagihan</span>
                                <span class="text-xl font-extrabold text-brand-text" x-text="money(finalTotal)"></span>
                            </div>
                        </div>
                    </x-ui.card>

                    <x-ui.card>
                        <div class="space-y-5">
                            <x-ui.select name="status" label="Status awal">
                                <option value="Draft" @selected(old('status', 'Draft') === 'Draft')>Draft</option>
                                <option value="Sent" @selected(old('status') === 'Sent')>Terkirim ke klien</option>
                                <option value="Paid" @selected(old('status') === 'Paid')>Lunas</option>
                            </x-ui.select>
                            <div class="flex flex-col gap-3">
                                <x-ui.button type="submit" size="lg" icon="fa-floppy-disk" class="w-full">Simpan invoice</x-ui.button>
                                <x-ui.button :href="route('dashboard.invoices.index')" variant="ghost" class="w-full">Batal</x-ui.button>
                            </div>
                        </div>
                    </x-ui.card>
                </aside>
            </div>
        </form>
    </div>
</x-app-layout>
