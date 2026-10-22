@php
    $id = app()->getLocale() === 'id';
@endphp

@if ($packages->isNotEmpty())
    <section id="packages" class="section scroll-mt-20">
        <div class="container-page">
            <x-ui.section-heading
                :eyebrow="$id ? 'Produk & paket' : 'Products & packages'"
                :title="$id ? 'Pilih paket yang sesuai kebutuhan Anda' : 'Pick the package that fits your needs'"
                :lead="$id ? 'Harga transparan, bisa bayar DP, dan proses mudah dari satu akun.' : 'Transparent pricing, deposit option, and an easy process from one account.'" />

            <div class="mt-14 grid items-stretch gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($packages as $package)
                    @php
                        $features = $id ? $package->features_id : $package->features_en;
                        $isNumericPrice = is_numeric(str_replace(['Rp', '.', ',', ' '], '', $package->price));
                    @endphp
                    <article @class([
                        'relative flex flex-col rounded-xl p-6 md:p-8 transition',
                        'border-2 border-brand-500 bg-surface shadow-pop lg:-translate-y-3' => $package->is_featured,
                        'card card-hover' => ! $package->is_featured,
                    ])>
                        @if ($package->is_featured)
                            <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 rounded-full bg-sun-400 px-4 py-1 text-caption font-bold text-[#17123A] shadow-soft">
                                <i class="fa-solid fa-star" aria-hidden="true"></i> {{ $id ? 'Terpopuler' : 'Most popular' }}
                            </span>
                        @endif

                        <h3 class="text-xl">{{ $package->getTranslation('name') }}</h3>
                        <p class="mt-2 text-small text-muted">{{ $package->getTranslation('description') }}</p>

                        <p class="mt-6 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-brand-text">{{ $package->price }}</span>
                            @if ($isNumericPrice)<span class="text-small text-muted">/ {{ $id ? 'proyek' : 'project' }}</span>@endif
                        </p>

                        <div class="divider my-6"></div>

                        <ul class="flex-1 space-y-3">
                            @foreach ($features ?? [] as $feature)
                                <li class="flex items-start gap-3 text-small">
                                    <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-mint-50 text-[0.625rem] text-mint-800 dark:bg-mint-400/15 dark:text-mint-300"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('package.show', $package->slug) }}" @class(['mt-8 w-full', 'btn-primary' => $package->is_featured, 'btn-secondary' => ! $package->is_featured])>
                            {{ $id ? 'Lihat Detail' : 'View Details' }}
                            <i class="fa-solid fa-arrow-right text-caption" aria-hidden="true"></i>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
