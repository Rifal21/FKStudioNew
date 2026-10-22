@php
    $editing = $subscription->exists;
    $oldItems = old('items', $editing
        ? $subscription->items->map(fn ($i) => ['description' => $i->description, 'quantity' => $i->quantity, 'unit_price' => (float) $i->unit_price])->values()->all()
        : [['description' => 'Hosting & maintenance {bulan} {tahun}', 'quantity' => 1, 'unit_price' => 0]]);
@endphp
<x-app-layout>
    <x-slot name="header">{{ $editing ? 'Ubah Langganan' : 'Langganan Baru' }}</x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <x-ui.page-header :title="$editing ? 'Ubah langganan' : 'Buat langganan'" description="Invoice dibuat otomatis tiap bulan dari daftar item di bawah. Perubahan berlaku untuk invoice berikutnya.">
            <x-slot:actions>
                <x-ui.button :href="$editing ? route('dashboard.subscriptions.show', $subscription) : route('dashboard.subscriptions.index')" variant="secondary" icon="fa-arrow-left">Kembali</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        <form action="{{ $editing ? route('dashboard.subscriptions.update', $subscription) : route('dashboard.subscriptions.store') }}" method="POST" x-data="{
            items: @js($oldItems),
            discount: @js((float) old('discount', $subscription->discount ?? 0)),
            discount_type: @js(old('discount_type', $subscription->discount_type ?? 'percent')),
            tax: @js((float) old('tax', $subscription->tax ?? 0)),
            tax_type: @js(old('tax_type', $subscription->tax_type ?? 'percent')),
            billing_day: @js((int) old('billing_day', $subscription->billing_day ?? 5)),
            start_date: @js(old('start_date', optional($subscription->start_date)->toDateString())),
            auto_send: @js((bool) old('auto_send', $subscription->auto_send)),
            addItem() { this.items.push({ description: '', quantity: 1, unit_price: 0 }); },
            moveItem(from, to) { const [moved] = this.items.splice(from, 1); this.items.splice(to, 0, moved); },
            removeItem(index) { if (this.items.length > 1) this.items.splice(index, 1); },
            money(value) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0); },
            get subtotal() { return this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0); },
            get discountAmount() { return this.discount_type === 'percent' ? this.subtotal * (this.discount / 100) : this.discount; },
            get taxAmount() { return this.tax_type === 'percent' ? (this.subtotal - this.discountAmount) * (this.tax / 100) : this.tax; },
            get finalTotal() { return this.subtotal - this.discountAmount + this.taxAmount; },
            get nextRun() {
                const day = Math.min(Math.max(parseInt(this.billing_day) || 1, 1), 31);
                const now = new Date();
                let from = this.start_date ? new Date(this.start_date + 'T00:00:00') : now;
                if (from < now && !(from.toDateString() === now.toDateString())) from = now;
                const pick = (y, m) => new Date(y, m, Math.min(day, new Date(y, m + 1, 0).getDate()));
                let d = pick(from.getFullYear(), from.getMonth());
                const floor = new Date(from.getFullYear(), from.getMonth(), from.getDate());
                if (d < floor) d = pick(from.getFullYear(), from.getMonth() + 1);
                return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            }
        }">
            @csrf
            @if ($editing) @method('PATCH') @endif

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-danger-500/40 bg-danger-50 p-4 text-small text-danger-700 dark:bg-danger-500/15 dark:text-red-300" role="alert">
                    <p class="font-semibold"><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i> Periksa kembali isian Anda:</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:items-start">
                <div class="space-y-6 lg:col-span-2">
                    <x-ui.card>
                        <h2 class="mb-5 flex items-center gap-3 text-lg font-bold">
                            <span class="flex h-10 w-10 items-center justify-center rounded-md bg-brand-soft text-brand-text"><i class="fa-solid fa-user-tie" aria-hidden="true"></i></span>
                            Klien
                        </h2>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <x-ui.input name="title" label="Nama langganan" :value="$subscription->title" required placeholder="Contoh: Hosting & maintenance toko online" />
                            </div>
                            <x-ui.input name="client_name" label="Nama klien" :value="$subscription->client_name" required placeholder="Pemilik proyek / perusahaan" />
                            <x-ui.input name="client_phone" type="tel" label="Nomor WhatsApp" :value="$subscription->client_phone" placeholder="0812-3456-7890" hint="Tujuan pengiriman invoice otomatis." />
                            <x-ui.input name="client_email" type="email" label="Email (opsional)" :value="$subscription->client_email" placeholder="klien@contoh.com" />
                            <div class="md:col-span-2">
                                <x-ui.textarea name="client_address" label="Alamat klien (opsional)" rows="2" :value="$subscription->client_address" />
                            </div>
                        </div>
                    </x-ui.card>

                    <x-ui.card>
                        <h2 class="mb-5 flex items-center gap-3 text-lg font-bold">
                            <span class="flex h-10 w-10 items-center justify-center rounded-md bg-brand-soft text-brand-text"><i class="fa-solid fa-calendar-days" aria-hidden="true"></i></span>
                            Jadwal tagihan
                        </h2>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label for="billing_day" class="label">Terbit tiap tanggal <span class="text-danger-600" aria-hidden="true">*</span></label>
                                <input id="billing_day" name="billing_day" type="number" min="1" max="31" required class="input" x-model.number="billing_day">
                                <p class="hint">Tanggal 29–31 memakai hari terakhir di bulan yang lebih pendek.</p>
                            </div>
                            <x-ui.input name="due_days" type="number" min="0" max="90" label="Jatuh tempo (hari setelah terbit)" :value="$subscription->due_days ?? 7" required />
                            <div>
                                <label for="start_date" class="label">Mulai <span class="text-danger-600" aria-hidden="true">*</span></label>
                                <input id="start_date" name="start_date" type="date" required class="input" x-model="start_date">
                            </div>
                            <x-ui.input name="end_date" type="date" label="Berakhir (opsional)" :value="optional($subscription->end_date)->toDateString()" hint="Kosongkan kalau berlangganan terus." />
                        </div>
                        <p class="mt-5 flex items-center gap-3 rounded-lg border-2 border-edge bg-brand-soft p-4 text-small">
                            <i class="fa-solid fa-paper-plane text-brand-text" aria-hidden="true"></i>
                            <span>Invoice berikutnya terbit <strong x-text="nextRun"></strong>, lalu tiap tanggal <strong x-text="billing_day || '-'"></strong>.</span>
                        </p>
                    </x-ui.card>

                    <x-ui.card>
                        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                            <h2 class="flex items-center gap-3 text-lg font-bold">
                                <span class="flex h-10 w-10 items-center justify-center rounded-md bg-brand-soft text-brand-text"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span>
                                Item tagihan
                            </h2>
                            <x-ui.button variant="secondary" size="sm" icon="fa-plus" @click="addItem">Tambah item</x-ui.button>
                        </div>

                        <p class="mb-3 text-small text-muted">Seret <i class="fa-solid fa-grip-vertical" aria-hidden="true"></i> untuk mengatur urutan. Tulis <code class="rounded bg-surface-2 px-1">{bulan}</code> dan <code class="rounded bg-surface-2 px-1">{tahun}</code> di deskripsi, nanti diganti bulan tagihan, misalnya "November 2026".</p>

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
                                            <input class="input" type="number" :id="`item-price-${index}`" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" required min="0">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </x-ui.card>

                    <x-ui.card>
                        <x-ui.textarea name="notes" label="Catatan di invoice" rows="3" :value="$subscription->notes" placeholder="Contoh: Pembayaran paling lambat sebelum jatuh tempo. {bulan} {tahun} juga bisa dipakai di sini." />
                    </x-ui.card>
                </div>

                <aside class="space-y-6 lg:sticky lg:top-24">
                    <x-ui.card>
                        <h2 class="mb-5 text-lg font-bold">Total per bulan</h2>
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
                            <div class="rounded-lg border-2 border-edge bg-navy px-4 py-3">
                                <p class="text-caption text-brand-200">Tagihan tiap bulan</p>
                                <p class="font-display text-2xl font-extrabold text-sun-400 [font-stretch:85%]" x-text="money(finalTotal)"></p>
                            </div>
                        </div>
                    </x-ui.card>

                    <x-ui.card>
                        <div class="space-y-5">
                            <label class="flex items-start gap-3">
                                <input type="checkbox" name="auto_send" value="1" class="mt-1 h-5 w-5 rounded border-line accent-[rgb(var(--c-edge))]" x-model="auto_send">
                                <span>
                                    <span class="block text-small font-bold">Kirim otomatis lewat WhatsApp</span>
                                    <span class="block text-caption text-muted" x-text="auto_send ? 'Invoice dan PDF langsung dikirim saat terbit.' : 'Invoice dibuat sebagai Draft. Anda kirim sendiri setelah memeriksanya.'"></span>
                                </span>
                            </label>
                            <div class="flex flex-col gap-3">
                                <x-ui.button type="submit" size="lg" icon="fa-floppy-disk" class="w-full">{{ $editing ? 'Simpan perubahan' : 'Buat langganan' }}</x-ui.button>
                                <x-ui.button :href="route('dashboard.subscriptions.index')" variant="ghost" class="w-full">Batal</x-ui.button>
                            </div>
                        </div>
                    </x-ui.card>
                </aside>
            </div>
        </form>
    </div>
</x-app-layout>
