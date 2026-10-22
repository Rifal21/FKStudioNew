<x-layouts.public :noindex="true" title="Website Saya" description="Daftar website Anda yang sudah selesai dan aktif.">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <div class="relative pb-16 pt-28 md:pb-24 md:pt-36">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(50%_30%_at_50%_0%,rgb(45_212_191/0.12),transparent)]" aria-hidden="true"></div>

        <div class="container-page relative max-w-5xl">
            <x-ui.page-header title="Website saya" description="Website Anda yang sudah selesai dikerjakan dan aktif.">
                <x-slot:actions>
                    <x-ui.button :href="route('user.orders')" variant="secondary" icon="fa-box">Lihat pesanan</x-ui.button>
                </x-slot:actions>
            </x-ui.page-header>

            @if ($orders->isEmpty())
                <x-ui.card>
                    <x-ui.empty-state icon="fa-laptop-code" title="Belum ada website selesai" text="Proyek website Anda masih dalam pengembangan atau belum dimulai. Periksa kemajuannya di menu Pesanan Saya.">
                        <x-ui.button :href="route('user.orders')">Periksa pesanan saya</x-ui.button>
                    </x-ui.empty-state>
                </x-ui.card>
            @else
                <div class="grid gap-6 md:grid-cols-2">
                    @foreach ($orders as $order)
                        <article class="card card-hover flex flex-col justify-between p-6">
                            <div>
                                <div class="flex items-start justify-between gap-3">
                                    <span class="flex h-14 w-14 items-center justify-center rounded-lg bg-mint-50 text-2xl text-mint-800 dark:bg-mint-400/15 dark:text-mint-300"><i class="fa-solid fa-globe" aria-hidden="true"></i></span>
                                    <x-ui.badge variant="success" :dot="true">Online &amp; aktif</x-ui.badge>
                                </div>
                                <h2 class="mt-5 text-2xl">{{ $order->website_name ?: 'Website proyek' }}</h2>
                                <p class="mt-1 text-small text-muted">Paket layanan: <strong class="text-ink">{{ $order->package_name }}</strong></p>
                                <p class="mt-4 inline-flex max-w-full items-center gap-2 rounded-md bg-surface-2 px-3 py-2 font-mono text-small text-brand-text">
                                    <i class="fa-solid fa-link text-caption" aria-hidden="true"></i><span class="truncate">{{ $order->website_url }}</span>
                                </p>
                            </div>
                            <div class="mt-6 flex items-center justify-between gap-3 border-t border-line pt-5">
                                <span class="text-caption text-muted">Selesai: {{ $order->updated_at->translatedFormat('d M Y') }}</span>
                                <div class="flex gap-2">
                                    @if ($order->finalInvoice)
                                        <a href="{{ route('invoices.public.show', $order->finalInvoice->id) }}" target="_blank" rel="noopener" class="btn-icon border border-line" aria-label="Lihat invoice"><i class="fa-solid fa-file-invoice" aria-hidden="true"></i></a>
                                    @endif
                                    <x-ui.button :href="'https://' . $order->website_url" target="_blank" rel="noopener" size="sm" icon="fa-arrow-up-right-from-square" :icon-right="true">Kunjungi</x-ui.button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <x-slot:footer><x-site.footer /></x-slot:footer>
</x-layouts.public>
