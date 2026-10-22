@php
    $id = app()->getLocale() === 'id';
    $slides = $heroSlides->isNotEmpty()
        ? $heroSlides->map(fn ($s) => $s->media_url)->filter()->values()
        : ($hero->image ? collect([$hero->media_url]) : collect());
    $rating = $testimonials->count() ? round($testimonials->avg('rating') ?: 5, 1) : null;
    $avatarTones = ['bg-brand-100 text-brand-700', 'bg-pink-100 text-pink-700', 'bg-sun-100 text-sun-700'];
@endphp

<section id="home" class="relative overflow-hidden pb-16 pt-28 md:pb-24 md:pt-36">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(60%_50%_at_85%_0%,rgb(124_58_237/0.16),transparent),radial-gradient(40%_40%_at_0%_35%,rgb(255_92_138/0.10),transparent),radial-gradient(35%_35%_at_60%_100%,rgb(255_200_61/0.12),transparent)]" aria-hidden="true"></div>

    <div class="container-page relative">
        <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-12 lg:gap-8">
            {{-- Copy --}}
            <div class="min-w-0 lg:col-span-6">
                <span class="eyebrow animate-fade-up">
                    <span class="h-2 w-2 rounded-full bg-mint-400" aria-hidden="true"></span>
                    {{ $id ? 'Mitra solusi digital Anda' : 'Your digital solution partner' }}
                </span>

                <h1 class="mt-5 text-4xl font-extrabold leading-[1.1] sm:text-5xl lg:text-6xl animate-fade-up">
                    <span class="gradient-text">{{ $hero->getTranslation('title') }}</span>
                </h1>

                <p class="mt-5 max-w-xl text-base text-muted sm:text-lg animate-fade-up">
                    {{ $hero->getTranslation('subtitle') ?? ($id
                        ? 'Kami menghadirkan jasa pembuatan website custom, platform e-commerce, dan aplikasi mobile berkinerja tinggi yang dirancang khusus untuk mempercepat pertumbuhan bisnis Anda.'
                        : 'We deliver custom websites, e-commerce platforms, and high-performance mobile apps built to accelerate your business growth.') }}
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row animate-fade-up">
                    <a href="{{ $hero->cta_link ?? route('package.index') }}" class="btn-primary btn-lg">
                        {{ $hero->getTranslation('cta_text') ?? ($id ? 'Mulai Konsultasi' : 'Start Consultation') }}
                        <i class="fa-solid fa-arrow-right text-small" aria-hidden="true"></i>
                    </a>
                    <a href="#portfolio" class="btn-secondary btn-lg">
                        {{ $id ? 'Lihat Portofolio' : 'View Portfolio' }}
                    </a>
                </div>

                @if ($clients->isNotEmpty())
                    <div class="mt-10 flex items-center gap-4 border-t border-line pt-6">
                        <div class="flex -space-x-3">
                            @foreach ($clients->take(3) as $i => $client)
                                <span class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-bg text-caption font-bold {{ $avatarTones[$i % 3] }}">{{ mb_strtoupper(mb_substr($client->name ?? 'C', 0, 1)) }}</span>
                            @endforeach
                            @if ($clients->count() > 3)
                                <span class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-bg bg-brand-600 text-caption font-bold text-white">+{{ $clients->count() - 3 }}</span>
                            @endif
                        </div>
                        <p class="text-small text-muted">
                            <strong class="block text-ink">{{ $id ? 'Mitra bisnis terpercaya' : 'Trusted business partners' }}</strong>
                            {{ $id ? 'Telah berkolaborasi dengan brand terbaik.' : 'Collaborated with great brands.' }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Visual --}}
            <div class="relative min-w-0 lg:col-span-6 lg:pl-8">
                <div class="absolute -inset-3 rotate-3 rounded-xl bg-brand-gradient opacity-25 blur-sm" aria-hidden="true"></div>

                <div class="relative aspect-[4/3] overflow-hidden rounded-xl border border-line bg-surface shadow-pop">
                    @if ($slides->isNotEmpty())
                        <div class="swiper js-hero h-full w-full">
                            <div class="swiper-wrapper">
                                @foreach ($slides as $i => $src)
                                    <div class="swiper-slide bg-brand-gradient-soft">
                                        <img src="{{ $src }}" alt="{{ $id ? 'Contoh hasil kerja FKStudio' : 'FKStudio showcase' }}"
                                            class="h-full w-full object-cover" width="800" height="600"
                                            @if ($i === 0) fetchpriority="high" @else loading="lazy" @endif>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-brand-gradient-soft text-6xl text-brand-500">
                            <i class="fa-solid fa-laptop-code" aria-hidden="true"></i>
                        </div>
                    @endif
                </div>

                @if ($projects->count())
                    <div class="absolute -top-4 right-2 flex items-center gap-3 rounded-lg border border-line bg-surface px-4 py-3 shadow-card sm:-right-3 animate-float">
                        <span class="flex h-10 w-10 items-center justify-center rounded-md bg-sun-100 text-sun-700"><i class="fa-solid fa-trophy" aria-hidden="true"></i></span>
                        <div>
                            <p class="text-lg font-extrabold leading-none">{{ $projects->count() }}+</p>
                            <p class="text-caption text-muted">{{ $id ? 'Proyek selesai' : 'Projects done' }}</p>
                        </div>
                    </div>
                @endif

                @if ($rating)
                    <div class="absolute -bottom-5 left-2 flex items-center gap-3 rounded-lg border border-line bg-surface px-4 py-3 shadow-card sm:-left-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-md bg-pink-50 text-pink-600 dark:bg-pink-500/15 dark:text-pink-300"><i class="fa-solid fa-star" aria-hidden="true"></i></span>
                        <div>
                            <p class="text-lg font-extrabold leading-none">{{ number_format($rating, 1) }}/5</p>
                            <p class="text-caption text-muted">{{ $id ? 'Kepuasan klien' : 'Client rating' }}</p>
                        </div>
                    </div>
                @endif

                @if ($slides->count() > 1)
                    <div class="mt-14 flex items-center justify-center gap-4 lg:justify-end">
                        <button type="button" class="js-hero-prev btn-icon border border-line bg-surface" aria-label="{{ $id ? 'Slide sebelumnya' : 'Previous slide' }}"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                        <div class="js-hero-pagination swiper-pagination !static !w-auto"></div>
                        <button type="button" class="js-hero-next btn-icon border border-line bg-surface" aria-label="{{ $id ? 'Slide berikutnya' : 'Next slide' }}"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
