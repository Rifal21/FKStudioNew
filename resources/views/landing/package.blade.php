@php
    $id = app()->getLocale() === 'id';
    $features = $id ? $package->features_id : $package->features_en;
    $ctaHref = $package->cta_link ?: route('checkout', $package->id);
    $ctaExternal = $package->cta_link && str_starts_with($package->cta_link, 'http');
@endphp
<x-layouts.public :title="$package->getTranslation('name')" :description="Str::limit(strip_tags($package->getTranslation('description')), 160)">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <div class="relative overflow-hidden pb-16 pt-28 md:pb-24 md:pt-36">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(50%_40%_at_80%_0%,rgb(124_58_237/0.14),transparent),radial-gradient(30%_30%_at_0%_20%,rgb(255_92_138/0.10),transparent)]" aria-hidden="true"></div>

        <div class="container-page relative">
            <a href="{{ route('package.index') }}" class="btn-secondary btn-sm rounded-full"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> {{ $id ? 'Kembali ke Paket' : 'Back to Packages' }}</a>

            <header class="mt-8 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="min-w-0">
                    <span class="eyebrow"><i class="fa-solid fa-gem" aria-hidden="true"></i> {{ $id ? 'Layanan unggulan' : 'Premium service' }}</span>
                    <h1 class="mt-4 text-4xl font-extrabold leading-tight md:text-5xl">{{ $package->getTranslation('name') }}</h1>
                </div>
                <p class="shrink-0 rounded-xl border border-line bg-surface px-6 py-4 shadow-soft">
                    <span class="block text-caption font-semibold text-muted">{{ $id ? 'Mulai dari' : 'Starting at' }}</span>
                    <span class="text-3xl font-extrabold text-brand-text md:text-4xl">{{ $package->price }}</span>
                </p>
            </header>

            <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="min-w-0 space-y-6 lg:col-span-2">
                    <x-ui.card>
                        <h2 class="text-xl">{{ $id ? 'Deskripsi layanan' : 'About this service' }}</h2>
                        <p class="mt-3 leading-relaxed text-muted">{!! nl2br(e($package->getTranslation('description'))) !!}</p>
                    </x-ui.card>

                    @if (! empty($features))
                        <x-ui.card>
                            <h2 class="text-xl">{{ $id ? 'Yang Anda dapatkan' : 'What you get' }}</h2>
                            <ul class="mt-5 grid gap-4 sm:grid-cols-2">
                                @foreach ($features as $feature)
                                    <li class="flex items-start gap-3">
                                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-mint-50 text-caption text-mint-800 dark:bg-mint-400/15 dark:text-mint-300"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </x-ui.card>
                    @endif
                </div>

                <aside class="lg:col-span-1">
                    <div class="card card-pad space-y-5 lg:sticky lg:top-28">
                        <div>
                            <h2 class="text-xl">{{ $id ? 'Siap memulai?' : 'Ready to start?' }}</h2>
                            <p class="mt-2 text-small text-muted">{{ $id ? 'Amankan slot pemesanan Anda sekarang dan mari bangun website impian Anda bersama.' : 'Secure your slot now and let us build your dream website together.' }}</p>
                        </div>

                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 rounded-lg bg-brand-soft p-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-brand-600 text-white"><i class="fa-solid fa-receipt" aria-hidden="true"></i></span>
                                <div><p class="text-small font-bold">Down Payment (DP 50%)</p><p class="text-caption text-muted">{{ $id ? 'Mulai dengan DP 50%, pelunasan setelah selesai.' : 'Start with a 50% deposit, settle the rest when done.' }}</p></div>
                            </li>
                            <li class="flex items-start gap-3 rounded-lg bg-mint-50 p-3 dark:bg-mint-400/10">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-mint-400 text-[#17123A]"><i class="fa-solid fa-ticket" aria-hidden="true"></i></span>
                                <div><p class="text-small font-bold">{{ $id ? 'Dukungan voucher diskon' : 'Discount voucher support' }}</p><p class="text-caption text-muted">{{ $id ? 'Gunakan kode voucher di checkout untuk potongan ekstra.' : 'Use a voucher code at checkout for an extra discount.' }}</p></div>
                            </li>
                        </ul>

                        <a href="{{ $ctaHref }}" @if ($ctaExternal) target="_blank" rel="noopener" @endif class="btn-primary btn-lg w-full">
                            {{ $package->getTranslation('cta_text') ?: ($id ? 'Pesan Sekarang' : 'Order Now') }}
                            <i class="fa-solid fa-arrow-right text-small" aria-hidden="true"></i>
                        </a>

                        <div class="grid grid-cols-3 gap-2 border-t border-line pt-5 text-center">
                            <div><i class="fa-solid fa-bolt text-brand-500" aria-hidden="true"></i><p class="mt-1 text-caption font-semibold text-muted">{{ $id ? 'Proses cepat' : 'Fast launch' }}</p></div>
                            <div><i class="fa-solid fa-shield-halved text-mint-500" aria-hidden="true"></i><p class="mt-1 text-caption font-semibold text-muted">{{ $id ? 'Bayar aman' : 'Secure pay' }}</p></div>
                            <div><i class="fa-solid fa-headset text-pink-500" aria-hidden="true"></i><p class="mt-1 text-caption font-semibold text-muted">{{ $id ? 'Dukungan' : 'Support' }}</p></div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <x-slot:footer><x-site.footer /><x-site.whatsapp /></x-slot:footer>
</x-layouts.public>
