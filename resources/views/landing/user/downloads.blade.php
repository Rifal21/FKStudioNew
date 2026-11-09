@php
    $id = app()->getLocale() === 'id';
@endphp
<x-layouts.public :noindex="true" :title="$id ? 'Produk Digital Saya' : 'My Digital Products'">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <div class="pb-16 pt-28 md:pb-24 md:pt-36">
        <div class="container-page max-w-5xl">
            <x-ui.page-header :title="$id ? 'Produk digital saya' : 'My digital products'" :description="$id ? 'Semua file, kunci lisensi, dan link akses dari pembelian Anda.' : 'All files, license keys and access links from your purchases.'">
                <x-slot:actions>
                    <x-ui.button :href="route('user.purchases')" variant="secondary" icon="fa-bag-shopping">{{ $id ? 'Pembelian toko' : 'Shop purchases' }}</x-ui.button>
                </x-slot:actions>
            </x-ui.page-header>

            @include('landing.user._flash')

            @if ($entitlements->isEmpty())
                <x-ui.card>
                    <x-ui.empty-state icon="fa-download" :title="$id ? 'Belum ada produk digital' : 'No digital products yet'" :text="$id ? 'Produk digital muncul di sini setelah pembayaran lunas.' : 'Digital products appear here once your payment is confirmed.'">
                        <a href="{{ route('shop.index') }}" class="btn-primary">{{ $id ? 'Buka toko' : 'Go to shop' }}</a>
                    </x-ui.empty-state>
                </x-ui.card>
            @else
                <ul class="space-y-5">
                    @foreach ($entitlements as $entitlement)
                        @php
                            $product = $entitlement->product;
                            $cover = $product?->images->first();
                            $active = $entitlement->isActive();
                            $files = $active ? $entitlement->files() : collect();
                            $remaining = $entitlement->remainingDownloads();
                        @endphp
                        <li class="rounded-xl border-2 border-edge bg-surface p-5 shadow-hard-sm">
                            <div class="flex gap-4">
                                <div class="h-16 w-16 shrink-0 overflow-hidden rounded-lg border-2 border-edge bg-sun-100 dark:bg-surface-2">
                                    @if ($cover)<img src="{{ $cover->media_url }}" alt="" class="h-full w-full object-cover">@else<div class="flex h-full items-center justify-center text-xl text-navy/30 dark:text-ink/30" aria-hidden="true"><i class="fa-solid fa-file-zipper"></i></div>@endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-lg font-bold">{{ $entitlement->orderItem?->product_name ?? $product?->getTranslation('name') }}</h2>
                                        @if (! $active)
                                            <x-ui.badge variant="neutral">{{ $entitlement->revoked_at ? ($id ? 'Akses dicabut' : 'Access revoked') : ($id ? 'Kedaluwarsa' : 'Expired') }}</x-ui.badge>
                                        @endif
                                    </div>
                                    <p class="text-small text-muted">
                                        {{ $entitlement->orderItem?->variant_title ? $entitlement->orderItem->variant_title.' · ' : '' }}
                                        @if ($entitlement->orderItem?->order)
                                            <a href="{{ route('shop.orders.show', $entitlement->orderItem->order) }}" class="font-mono hover:underline">{{ $entitlement->orderItem->order->order_number }}</a>
                                        @endif
                                        @if ($entitlement->expires_at)
                                            · {{ $id ? 'berlaku sampai' : 'valid until' }} {{ $entitlement->expires_at->timezone('Asia/Jakarta')->locale(app()->getLocale())->translatedFormat('d M Y') }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if ($active)
                                <div class="mt-4 space-y-3">
                                    @if ($entitlement->licenseKey)
                                        <div class="rounded-lg border-2 border-edge bg-sun-100 p-3 dark:bg-surface-2" x-data="{ copied: false }">
                                            <p class="text-caption font-bold">{{ $id ? 'Kunci lisensi' : 'License key' }}</p>
                                            <div class="mt-1 flex flex-wrap items-center gap-3">
                                                <code class="break-all font-mono text-base font-bold">{{ $entitlement->licenseKey->key }}</code>
                                                <button type="button" class="btn-secondary btn-sm" @click="navigator.clipboard.writeText(@js($entitlement->licenseKey->key)); copied = true"><i class="fa-regular fa-copy" aria-hidden="true"></i> <span x-text="copied ? '{{ $id ? 'Tersalin' : 'Copied' }}' : '{{ $id ? 'Salin' : 'Copy' }}'"></span></button>
                                            </div>
                                        </div>
                                    @elseif ($product?->digital_delivery === 'license_key')
                                        <p class="rounded-lg bg-surface-2 p-3 text-small">{{ $id ? 'Kunci lisensi sedang kami siapkan. Anda akan diberi tahu setelah tersedia.' : 'Your license key is being prepared. We will notify you when it is ready.' }}</p>
                                    @endif

                                    @if ($product?->digital_delivery === 'link' && $product->access_url)
                                        <a href="{{ $product->access_url }}" target="_blank" rel="noopener noreferrer" class="btn-primary"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i> {{ $id ? 'Buka akses' : 'Open access' }}</a>
                                    @endif

                                    @if ($files->isNotEmpty())
                                        <ul class="divide-y divide-line rounded-lg border-2 border-line">
                                            @foreach ($files as $file)
                                                <li class="flex flex-wrap items-center justify-between gap-3 p-3">
                                                    <span class="min-w-0 text-small">
                                                        <span class="block truncate font-semibold">{{ $file->original_name }}</span>
                                                        <span class="text-muted">{{ $file->humanSize() }}{{ $file->version_label ? ' · '.$file->version_label : '' }}</span>
                                                    </span>
                                                    @if ($entitlement->canDownload())
                                                        <a href="{{ route('user.downloads.get', [$entitlement, $file]) }}" class="btn-primary btn-sm"><i class="fa-solid fa-download" aria-hidden="true"></i> {{ $id ? 'Unduh' : 'Download' }}</a>
                                                    @else
                                                        <span class="text-caption font-semibold text-muted">{{ $id ? 'Batas unduhan habis' : 'Download limit reached' }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                        <p class="text-caption text-muted">
                                            {{ $remaining === null ? ($id ? 'Unduhan tanpa batas.' : 'Unlimited downloads.') : ($id ? "Sisa {$remaining} dari {$entitlement->max_downloads} unduhan. Setiap file yang diunduh dihitung." : "{$remaining} of {$entitlement->max_downloads} downloads left. Each file download counts.") }}
                                        </p>
                                    @elseif ($product?->digital_delivery === 'file')
                                        <p class="text-small text-muted">{{ $id ? 'File belum tersedia. Hubungi kami bila perlu bantuan.' : 'No files available yet. Contact us if you need help.' }}</p>
                                    @endif
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <x-slot:footer><x-site.footer /></x-slot:footer>
</x-layouts.public>
