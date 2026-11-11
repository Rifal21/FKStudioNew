<x-app-layout>
    <x-slot name="header">Promosi Toko</x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <x-ui.page-header title="Promosi" description="Konten promosi produk yang ditulis Claude. Hasilnya tersimpan di sini; salin dan pakai di mana Anda mau.">
            <x-slot:actions>
                <x-ui.button :href="route('dashboard.shop.promos.create')" icon="fa-wand-magic-sparkles">Buat konten</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        @unless ($configured)
            <div class="rounded-lg border border-warning-500/40 bg-warning-50 p-4 text-small text-warning-700 dark:bg-warning-500/15 dark:text-yellow-200" role="alert">
                <p class="font-semibold"><i class="fa-solid fa-key" aria-hidden="true"></i> API key Claude belum diatur.</p>
                <p class="mt-1">Isi <code>ANTHROPIC_API_KEY</code> di file <code>.env</code> server, lalu jalankan <code>php artisan config:clear</code>.</p>
            </div>
        @endunless

        @if ($promos->isEmpty())
            <div class="card"><x-ui.empty-state icon="fa-wand-magic-sparkles" title="Belum ada konten promosi" text="Pilih produk, tentukan tujuan dan nada, lalu Claude menulis caption, teks WhatsApp, headline banner, dan lainnya." /></div>
        @else
            <div class="table-wrap">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th scope="col">Produk</th>
                            <th scope="col">Tujuan</th>
                            <th scope="col">Status</th>
                            <th scope="col">Dibuat</th>
                            <th scope="col" class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($promos as $promo)
                            <tr>
                                <td>
                                    <a href="{{ route('dashboard.shop.promos.show', $promo) }}" class="font-bold hover:underline">{{ $promo->product?->name_id ?? 'Produk dihapus' }}</a>
                                    <p class="text-caption text-muted">{{ count($promo->formats) }} format · {{ strtoupper($promo->locale) }}</p>
                                </td>
                                <td class="text-small">
                                    {{ \App\Services\Promo\PromoWriter::GOALS[$promo->goal]['label'] ?? $promo->goal }}
                                    <span class="block text-caption text-muted">{{ \App\Services\Promo\PromoWriter::TONES[$promo->tone]['label'] ?? $promo->tone }}</span>
                                </td>
                                <td>
                                    <x-ui.badge :variant="$promo->status === 'final' ? 'success' : 'neutral'" dot>{{ $promo->status === 'final' ? 'Final' : 'Draf' }}</x-ui.badge>
                                    @if (! empty($promo->warnings))<x-ui.badge variant="warning">{{ count($promo->warnings) }} peringatan</x-ui.badge>@endif
                                </td>
                                <td class="whitespace-nowrap text-small">
                                    {{ $promo->created_at->translatedFormat('j M Y, H:i') }}
                                    <span class="block text-caption text-muted">{{ $promo->author?->name }}</span>
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('dashboard.shop.promos.show', $promo) }}" class="btn-icon" title="Buka" aria-label="Buka konten promosi"><i class="fa-solid fa-eye" aria-hidden="true"></i></a>
                                        <form method="POST" action="{{ route('dashboard.shop.promos.destroy', $promo) }}" x-data @submit="if (!confirm('Hapus konten promosi ini?')) $event.preventDefault()">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-icon text-danger-700 dark:text-red-300" title="Hapus" aria-label="Hapus konten promosi"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $promos->links() }}
        @endif
    </div>
</x-app-layout>
