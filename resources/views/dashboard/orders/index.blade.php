<x-app-layout>
    <x-slot name="header">Pesanan Paket</x-slot>

    @php
        $orderStatusLabels = [
            'pending' => 'Menunggu',
            'paid' => 'Dibayar',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
        $orderStatusVariants = [
            'pending' => 'warning',
            'paid' => 'success',
            'completed' => 'info',
            'cancelled' => 'danger',
        ];
        $workStatusLabels = [
            'pending' => 'Menunggu',
            'in_progress' => 'Pengerjaan',
            'revision' => 'Revisi',
            'completed' => 'Selesai',
            'cancelled' => 'Batal',
        ];
        $workStatusVariants = [
            'pending' => 'neutral',
            'in_progress' => 'info',
            'revision' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
        ];
        $domStatusVariants = [
            'pending' => 'warning',
            'registered' => 'success',
            'failed' => 'danger',
        ];
        $domStatusLabels = [
            'pending' => 'Menunggu registrasi',
            'registered' => 'Aktif / terdaftar',
            'failed' => 'Registrasi gagal',
        ];
        $rowsMeta = [];
        foreach ($orders as $o) {
            $rowsMeta[$o->id] = [
                'status' => $o->status,
                'search' => mb_strtolower(implode(' ', array_filter([
                    substr($o->id, 0, 8),
                    $o->user->name ?? '',
                    $o->user->email ?? '',
                    $o->website_name,
                    $o->domain_name,
                    $o->package_name,
                ]))),
            ];
        }
    @endphp

    <div class="mx-auto max-w-7xl space-y-6"
         x-data="{
            q: '',
            st: '',
            rows: @js($rowsMeta),
            show(id) {
                const r = this.rows[id];
                if (!r) return true;
                return (!this.st || r.status === this.st) && (!this.q || r.search.includes(this.q.toLowerCase().trim()));
            },
            get count() { return Object.keys(this.rows).filter((id) => this.show(id)).length; }
         }">

        <x-ui.page-header title="Pesanan Paket" description="Kelola pembayaran, domain, dan progres pengerjaan website klien.">
            <x-slot:actions>
                <x-ui.badge variant="brand">{{ $orders->count() }} pesanan</x-ui.badge>
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

        @if ($orders->isEmpty())
            <x-ui.card>
                <x-ui.empty-state icon="fa-box-open" title="Belum ada pesanan" text="Pesanan paket dari pelanggan akan muncul di sini." />
            </x-ui.card>
        @else
            {{-- Toolbar --}}
            <div class="flex flex-col gap-3 sm:flex-row">
                <div class="relative flex-1">
                    <label for="order-search" class="sr-only">Cari pesanan</label>
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-muted" aria-hidden="true"></i>
                    <input id="order-search" type="search" x-model="q" class="input pl-10" placeholder="Cari ID, pelanggan, website, atau domain">
                </div>
                <div class="sm:w-56">
                    <label for="order-status-filter" class="sr-only">Filter status pembayaran</label>
                    <select id="order-status-filter" x-model="st" class="select">
                        <option value="">Semua status</option>
                        <option value="pending">Menunggu</option>
                        <option value="paid">Dibayar</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
            </div>

            <div class="space-y-4">
                @foreach ($orders as $order)
                    @php
                        $orderTaxRate = ($order->subtotal_amount - $order->discount_amount) > 0
                            ? round(($order->tax_amount / ($order->subtotal_amount - $order->discount_amount)) * 100)
                            : 11;
                        $paymentTickets = $order->tickets->filter(fn ($t) => ! empty($t->attachment));
                        $canConfirm = $order->status === 'pending';
                        $canConfirmFinal = $order->status === 'paid' && $order->payment_scheme === 'dp' && $order->final_invoice_id && $order->finalInvoice && $order->finalInvoice->status === 'Unpaid';
                        $canBill = $order->payment_scheme === 'dp' && $order->status === 'paid' && ! $order->final_invoice_id;
                        $hasActions = $canConfirm || $canConfirmFinal || $canBill || $order->invoice || $order->finalInvoice || ($order->tickets && $order->tickets->count() > 0);
                    @endphp

                    <article class="card overflow-hidden" x-show="show('{{ $order->id }}')">
                        {{-- Header --}}
                        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-line px-5 py-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2 text-caption text-muted">
                                    <span class="font-mono">#{{ substr($order->id, 0, 8) }}</span>
                                    <span aria-hidden="true">&bull;</span>
                                    <span>{{ $order->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="mt-1 font-bold">{{ $order->user->name }}</div>
                                <div class="break-all text-small text-muted">{{ $order->user->email }}</div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <x-ui.badge :variant="$orderStatusVariants[$order->status] ?? 'neutral'" dot>
                                    {{ $orderStatusLabels[$order->status] ?? $order->status }}
                                </x-ui.badge>
                                @if ($order->payment_scheme === 'dp')
                                    <x-ui.badge variant="brand">DP (50%)</x-ui.badge>
                                @else
                                    <x-ui.badge variant="success">Lunas (100%)</x-ui.badge>
                                @endif
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="grid gap-6 p-5 lg:grid-cols-3">
                            {{-- Website --}}
                            <section class="min-w-0 space-y-2" aria-label="Detail website">
                                <h3 class="eyebrow">Detail website</h3>
                                @if ($order->website_name)
                                    <div class="font-bold">{{ $order->website_name }}</div>
                                    <div class="text-small text-muted">Jenis: {{ $order->business_type }}</div>
                                    @if ($order->website_url && ! $order->buy_domain)
                                        <div class="break-all font-mono text-caption text-brand-text">
                                            <i class="fa-solid fa-link mr-1" aria-hidden="true"></i>{{ $order->website_url }}
                                        </div>
                                    @endif
                                    @if ($order->client_notes)
                                        <p class="line-clamp-3 border-l-2 border-line pl-3 text-small italic text-muted" title="{{ $order->client_notes }}">
                                            "{{ $order->client_notes }}"
                                        </p>
                                    @endif

                                    @if ($order->buy_domain && $order->domain_name)
                                        <div class="rounded-md border border-line bg-surface-2 p-3">
                                            <div class="flex items-center gap-1.5 text-caption font-semibold text-brand-text">
                                                <i class="fa-solid fa-server" aria-hidden="true"></i> Beli domain baru
                                            </div>
                                            <div class="mt-1 break-all font-mono text-small font-bold">{{ $order->domain_name }}</div>
                                            <div class="text-caption text-muted">Harga: Rp {{ number_format($order->domain_price, 0, ',', '.') }}</div>

                                            <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-line pt-3">
                                                <x-ui.badge :variant="$domStatusVariants[$order->domain_status] ?? 'neutral'">
                                                    {{ $domStatusLabels[$order->domain_status] ?? $order->domain_status }}
                                                </x-ui.badge>

                                                @if ($order->status === 'paid' && $order->domain_status !== 'registered')
                                                    <form action="{{ route('dashboard.orders.register_domain', $order->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                            <i class="fa-solid fa-globe" aria-hidden="true"></i> Daftarkan
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-small italic text-muted">Belum ada detail website.</p>
                                @endif
                            </section>

                            {{-- Package & payment --}}
                            <section class="min-w-0 space-y-3" aria-label="Paket dan pembayaran">
                                <h3 class="eyebrow">Paket dan pembayaran</h3>
                                <div class="font-bold">{{ $order->package_name }}</div>

                                <dl class="space-y-1 text-small">
                                    <div class="flex justify-between gap-3">
                                        <dt class="text-muted">Subtotal</dt>
                                        <dd class="font-semibold">Rp {{ number_format($order->subtotal_amount > 0 ? $order->subtotal_amount : $order->total_amount, 0, ',', '.') }}</dd>
                                    </div>
                                    @if ($order->discount_amount > 0)
                                        <div class="flex justify-between gap-3 text-mint-800 dark:text-mint-300">
                                            <dt>Diskon ({{ $order->voucher_code }})</dt>
                                            <dd class="font-semibold">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</dd>
                                        </div>
                                    @endif
                                    <div class="flex justify-between gap-3">
                                        <dt class="text-muted">PPN ({{ $orderTaxRate }}%)</dt>
                                        <dd class="font-semibold">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</dd>
                                    </div>
                                    <div class="flex justify-between gap-3 border-t border-dashed border-line pt-1.5 font-bold">
                                        <dt>Total</dt>
                                        <dd>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</dd>
                                    </div>
                                    @if ($order->payment_scheme === 'dp')
                                        <div class="flex justify-between gap-3 text-muted">
                                            <dt>DP</dt>
                                            <dd>Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</dd>
                                        </div>
                                        <div class="flex justify-between gap-3 text-muted">
                                            <dt>Sisa</dt>
                                            <dd>Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}</dd>
                                        </div>
                                    @endif
                                </dl>

                                <div class="text-caption text-muted">
                                    <i class="fa-solid fa-credit-card mr-1" aria-hidden="true"></i>
                                    {{ str_replace('Duitku|', '', $order->payment_method ?: 'Belum memilih') }}
                                </div>

                                @if ($paymentTickets->isNotEmpty())
                                    <div class="border-t border-line pt-3">
                                        <div class="mb-2 flex items-center gap-1.5 text-caption font-semibold text-muted">
                                            <i class="fa-solid fa-receipt" aria-hidden="true"></i> Bukti transfer ({{ $paymentTickets->count() }})
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($paymentTickets as $pt)
                                                <a href="{{ Storage::url($pt->attachment) }}" target="_blank" rel="noopener"
                                                   class="group relative block h-12 w-12 overflow-hidden rounded-md border border-line transition hover:border-brand-500"
                                                   title="Buka bukti transfer: {{ $pt->subject }}" aria-label="Buka bukti transfer: {{ $pt->subject }}">
                                                    <img src="{{ Storage::url($pt->attachment) }}" alt="" loading="lazy" class="h-full w-full object-cover">
                                                    <span class="absolute inset-0 flex items-center justify-center bg-ink/50 text-caption text-white opacity-0 transition-opacity group-hover:opacity-100">
                                                        <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </section>

                            {{-- Work progress --}}
                            <section class="min-w-0 space-y-3" aria-label="Progres pengerjaan">
                                <h3 class="eyebrow">Progres pengerjaan</h3>
                                <x-ui.badge :variant="$workStatusVariants[$order->work_status] ?? 'neutral'" dot>
                                    {{ $workStatusLabels[$order->work_status] ?? $order->work_status }}
                                </x-ui.badge>

                                <form action="{{ route('dashboard.orders.work_status', $order->id) }}" method="POST" class="space-y-3">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label for="work-status-{{ $order->id }}" class="label">Status pengerjaan</label>
                                        <select id="work-status-{{ $order->id }}" name="work_status" onchange="this.form.requestSubmit()" class="select">
                                            @foreach ($workStatusLabels as $wsValue => $wsLabel)
                                                <option value="{{ $wsValue }}" {{ $order->work_status == $wsValue ? 'selected' : '' }}>{{ $wsLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="delivery-date-{{ $order->id }}" class="label">Tanggal selesai</label>
                                        <input id="delivery-date-{{ $order->id }}" type="date" name="delivery_date"
                                               value="{{ $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '' }}"
                                               onchange="this.form.requestSubmit()" class="input" title="Tanggal selesai">
                                    </div>
                                </form>
                            </section>
                        </div>

                        {{-- Actions --}}
                        @if ($hasActions)
                            <div class="flex flex-wrap items-center gap-2 border-t border-line bg-surface-2 px-5 py-3">
                                @if ($canConfirm)
                                    <form action="{{ route('dashboard.orders.update', $order->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="paid">
                                        <button type="submit" class="btn btn-primary btn-sm" title="Konfirmasi dan proses">
                                            <i class="fa-solid fa-check" aria-hidden="true"></i> Konfirmasi @if ($order->payment_scheme === 'dp') DP @endif
                                        </button>
                                    </form>
                                    <form action="{{ route('dashboard.orders.update', $order->id) }}" method="POST"
                                          data-confirm="Pesanan ini akan dibatalkan." data-confirm-title="Batalkan pesanan?" data-confirm-ok="Ya, batalkan">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="btn btn-danger-soft btn-sm" aria-label="Batalkan pesanan" title="Batalkan pesanan">
                                            <i class="fa-solid fa-xmark" aria-hidden="true"></i> Batalkan
                                        </button>
                                    </form>
                                @endif

                                @if ($canConfirmFinal)
                                    <form action="{{ route('dashboard.orders.update', $order->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-primary btn-sm" title="Konfirmasi pembayaran pelunasan dan selesaikan pesanan">
                                            <i class="fa-solid fa-circle-check" aria-hidden="true"></i> Konfirmasi Pelunasan
                                        </button>
                                    </form>
                                @endif

                                @if ($canBill)
                                    <form action="{{ route('dashboard.orders.tagih_pelunasan', $order->id) }}" method="POST"
                                          data-confirm="Kirim invoice pelunasan sisa 50% ke klien?" data-confirm-title="Tagih pelunasan?" data-confirm-ok="Ya, kirim">
                                        @csrf
                                        <button type="submit" class="btn btn-sun btn-sm">
                                            <i class="fa-solid fa-file-invoice-dollar" aria-hidden="true"></i> Tagih Pelunasan
                                        </button>
                                    </form>
                                @endif

                                <div class="flex flex-wrap items-center gap-2 sm:ml-auto">
                                    @if ($order->invoice)
                                        <a href="{{ route('dashboard.invoices.show', $order->invoice->id) }}" class="btn btn-secondary btn-sm"
                                           title="Invoice @if ($order->payment_scheme === 'dp') DP @else Utama @endif">
                                            <i class="fa-solid fa-file-invoice" aria-hidden="true"></i> Invoice @if ($order->payment_scheme === 'dp') DP @endif
                                        </a>
                                    @endif
                                    @if ($order->finalInvoice)
                                        <a href="{{ route('dashboard.invoices.show', $order->finalInvoice->id) }}" class="btn btn-secondary btn-sm" title="Invoice pelunasan">
                                            <i class="fa-solid fa-file-invoice-dollar" aria-hidden="true"></i> Invoice Pelunasan
                                        </a>
                                    @endif
                                    @if ($order->tickets && $order->tickets->count() > 0)
                                        <a href="{{ route('dashboard.tickets.index') }}" class="btn btn-secondary btn-sm" title="Lihat bukti transfer">
                                            <i class="fa-solid fa-receipt" aria-hidden="true"></i> Bukti
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </article>
                @endforeach

                <div x-show="count === 0" x-cloak>
                    <x-ui.card>
                        <x-ui.empty-state icon="fa-magnifying-glass" title="Pesanan tidak ditemukan" text="Coba ubah kata kunci atau filter status." />
                    </x-ui.card>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
