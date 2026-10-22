@php
    $id = app()->getLocale() === 'id';
    $slides = $heroSlides->isNotEmpty()
        ? $heroSlides->map(fn ($s) => $s->media_url)->filter()->values()
        : ($hero->image ? collect([$hero->media_url]) : collect());
    $rating = $testimonials->count() ? round($testimonials->avg('rating') ?: 5, 1) : null;
@endphp

<section id="home" class="grid-paper relative overflow-hidden pb-20 pt-28 md:pb-28 md:pt-36">
    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-bg to-transparent" aria-hidden="true"></div>

    <div class="container-page relative">
        <div class="grid grid-cols-1 items-center gap-14 lg:grid-cols-12 lg:gap-10">
            {{-- Copy --}}
            <div class="min-w-0 lg:col-span-6">
                <h1 class="font-display text-[2.6rem] font-extrabold leading-[1.02] tracking-tight sm:text-6xl lg:text-[4.25rem]">
                    {{ $hero->getTranslation('title') }}
                </h1>

                <p class="mt-6 max-w-lg text-base text-muted sm:text-lg">
                    {{ $hero->getTranslation('subtitle') ?? ($id
                        ? 'Kami menghadirkan jasa pembuatan website custom, platform e-commerce, dan aplikasi mobile berkinerja tinggi yang dirancang khusus untuk mempercepat pertumbuhan bisnis Anda.'
                        : 'We deliver custom websites, e-commerce platforms, and high-performance mobile apps built to accelerate your business growth.') }}
                </p>

                <div class="mt-9 flex flex-col gap-4 sm:flex-row">
                    <a href="{{ $hero->cta_link ?? route('package.index') }}" class="btn-primary btn-lg">
                        {{ $hero->getTranslation('cta_text') ?? ($id ? 'Mulai konsultasi' : 'Start a consultation') }}
                        <i class="fa-solid fa-arrow-right text-small" aria-hidden="true"></i>
                    </a>
                    <a href="#portfolio" class="btn-outline btn-lg">{{ $id ? 'Lihat portofolio' : 'See our work' }}</a>
                </div>

                @include('landing.home.stats')
            </div>

            {{-- A browser window that builds a website --}}
            <div class="relative min-w-0 lg:col-span-6">
                @if ($rating)
                    <span class="sticker absolute -right-1 -top-6 z-20 rotate-6 sm:-right-4">
                        <i class="fa-solid fa-star" aria-hidden="true"></i>
                        {{ number_format($rating, 1, $id ? ',' : '.', '') }}/5 {{ $id ? 'dari klien' : 'from clients' }}
                    </span>
                @endif

                <div class="pop relative overflow-hidden rounded-xl bg-surface shadow-hard-lg lg:rotate-[1.2deg]">
                    <div class="flex items-center gap-3 border-b-2 border-edge bg-sun-300 px-4 py-2.5">
                        <span class="flex gap-1.5" aria-hidden="true">
                            <span class="h-3 w-3 rounded-full border-2 border-navy bg-white"></span>
                            <span class="h-3 w-3 rounded-full border-2 border-navy bg-brand-300"></span>
                            <span class="h-3 w-3 rounded-full border-2 border-navy bg-navy"></span>
                        </span>
                        <span class="min-w-0 flex-1 truncate rounded-full border-2 border-navy bg-white px-3 py-0.5 text-caption font-semibold text-navy">
                            <i class="fa-solid fa-lock mr-1 text-[0.65rem]" aria-hidden="true"></i>tokokopi.fkstudio.id
                        </span>
                    </div>

                    @if ($slides->isNotEmpty())
                        <div class="swiper js-hero aspect-[4/3] w-full">
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
                        {{-- Skeleton site that assembles itself once on load --}}
                        <div class="relative aspect-[4/3] overflow-hidden bg-bg" aria-hidden="true">
                            <div class="flex items-center gap-3 border-b-2 border-edge/20 bg-surface px-5 py-3.5" style="animation: build-in .45s ease-out .1s both">
                                <span class="h-6 w-6 rounded-md border-2 border-edge bg-sun-400"></span>
                                <span class="h-2.5 w-20 rounded-full bg-ink/80"></span>
                                <span class="ml-auto flex gap-2"><span class="h-2 w-9 rounded-full bg-ink/20"></span><span class="h-2 w-9 rounded-full bg-ink/20"></span><span class="h-2 w-9 rounded-full bg-ink/20"></span></span>
                            </div>
                            <div class="grid grid-cols-5 gap-5 px-5 pt-6">
                                <div class="col-span-3 space-y-3">
                                    <span class="block h-5 rounded-full bg-ink" style="--w:92%; width:var(--w); animation: type-line .7s steps(12) .5s both"></span>
                                    <span class="block h-5 rounded-full bg-ink" style="--w:64%; width:var(--w); animation: type-line .6s steps(9) 1.1s both"></span>
                                    <span class="block h-2.5 rounded-full bg-ink/25" style="--w:95%; width:var(--w); animation: type-line .5s linear 1.6s both"></span>
                                    <span class="block h-2.5 rounded-full bg-ink/25" style="--w:78%; width:var(--w); animation: type-line .5s linear 1.8s both"></span>
                                    <span class="mt-3 block h-10 w-32 rounded-md border-2 border-edge bg-sun-400 shadow-hard-sm" style="animation: build-in .4s ease-out 2.1s both"></span>
                                </div>
                                <div class="col-span-2 flex aspect-square items-center justify-center rounded-lg border-2 border-edge bg-brand-200 text-5xl text-brand-700" style="animation: build-in .5s ease-out .9s both">
                                    <i class="fa-solid fa-mug-hot"></i>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4 px-5 pt-7">
                                @foreach ([2.4, 2.6, 2.8] as $d)
                                    <div class="rounded-lg border-2 border-edge/70 bg-surface p-3" style="animation: build-in .4s ease-out {{ $d }}s both">
                                        <span class="block aspect-[5/3] rounded-md {{ $loop->index === 1 ? 'bg-sun-200' : 'bg-brand-100' }}"></span>
                                        <span class="mt-2.5 block h-2 w-3/4 rounded-full bg-ink/70"></span>
                                        <span class="mt-1.5 block h-2 w-1/2 rounded-full bg-ink/20"></span>
                                    </div>
                                @endforeach
                            </div>
                            <span class="absolute bottom-10 right-8 flex items-start" style="animation: cursor-move 1.4s ease-in-out 3s both">
                                <i class="fa-solid fa-arrow-pointer rotate-[-12deg] text-2xl text-ink drop-shadow-[2px_2px_0_rgb(255_255_255)]"></i>
                                <span class="ml-1 mt-4 rounded-full border-2 border-navy bg-brand-300 px-2 py-0.5 text-caption font-bold text-navy">{{ $id ? 'Anda' : 'You' }}</span>
                            </span>
                        </div>
                    @endif
                </div>

                @if ($slides->count() > 1)
                    <div class="mt-8 flex items-center justify-end gap-4">
                        <button type="button" class="js-hero-prev btn-outline h-11 min-h-0 w-11 px-0" aria-label="{{ $id ? 'Slide sebelumnya' : 'Previous slide' }}"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                        <div class="js-hero-pagination swiper-pagination !static !w-auto"></div>
                        <button type="button" class="js-hero-next btn-outline h-11 min-h-0 w-11 px-0" aria-label="{{ $id ? 'Slide berikutnya' : 'Next slide' }}"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
