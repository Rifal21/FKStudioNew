@php
    $id = app()->getLocale() === 'id';
    $missions = collect(explode("\n", str_replace("\r", '', (string) $about->getTranslation('mission'))))
        ->map(fn ($m) => trim($m))->filter()->values();
@endphp

<section id="about" class="section scroll-mt-20">
    <div class="container-page">
        <div class="grid grid-cols-1 items-center gap-16 lg:grid-cols-12 lg:gap-12">
            {{-- Photo, taped to the page --}}
            <div class="relative min-w-0 lg:col-span-6">
                <span class="tape -left-3 -top-3 z-20 -rotate-12" aria-hidden="true"></span>
                <span class="tape -right-3 -top-2 z-20 rotate-12" aria-hidden="true"></span>
                <div class="pop relative aspect-[4/3] -rotate-1 overflow-hidden rounded-lg bg-surface shadow-hard-lg">
                    <div class="swiper js-about h-full w-full">
                        <div class="swiper-wrapper">
                            @forelse ($aboutSlides as $aslide)
                                <div class="swiper-slide bg-brand-gradient-soft">
                                    <img src="{{ $aslide->media_url }}" alt="{{ $id ? 'Tim dan kantor FKStudio' : 'FKStudio team and office' }}" loading="lazy" class="h-full w-full object-cover">
                                </div>
                            @empty
                                <div class="swiper-slide flex items-center justify-center bg-brand-gradient-soft text-6xl text-brand-500">
                                    <i class="fa-solid fa-people-group" aria-hidden="true"></i>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="absolute bottom-4 left-4 z-10 rounded-full border-2 border-edge bg-surface px-4 py-2">
                        <div class="js-about-pagination swiper-pagination !static !w-auto"></div>
                    </div>
                </div>
                <span class="sticker absolute -bottom-5 right-2 rotate-3 bg-brand-300 sm:right-8">{{ $id ? 'Berdiri sejak 2021' : 'Since 2021' }}</span>
            </div>

            {{-- Copy --}}
            <div class="min-w-0 lg:col-span-6">
                <h2 class="section-title">{{ $about->getTranslation('title') }}</h2>
                <p class="mt-5 max-w-xl text-base text-muted md:text-lg">{{ $about->getTranslation('description') }}</p>
                @include('landing.home.stats')

                @if ($about->getTranslation('vision'))
                    <figure class="relative mt-9 max-w-xl rotate-1 rounded-md border-2 border-edge bg-sun-200 p-6 shadow-hard text-navy">
                        <figcaption class="text-small font-bold">{{ $id ? 'Visi kami' : 'Our vision' }}</figcaption>
                        <blockquote class="mt-2 font-display text-xl font-bold leading-snug">{{ $about->getTranslation('vision') }}</blockquote>
                    </figure>
                @endif

                @if ($missions->isNotEmpty())
                    <div class="mt-10">
                        <h3 class="text-xl">{{ $id ? 'Yang kami kerjakan' : 'What we work on' }}</h3>
                        <ul class="mt-4 space-y-3.5">
                            @foreach ($missions as $point)
                                <li class="flex items-start gap-3.5">
                                    <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-md border-2 border-edge bg-sun-400 text-caption text-navy"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                                    <span>{{ $point }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        {{-- Team: name badges --}}
        @if ($owners->isNotEmpty())
            <div class="mt-28 grid gap-12 lg:grid-cols-12 lg:gap-10">
                <div class="lg:col-span-5">
                    <h2 class="section-title lg:sticky lg:top-28">{{ $id ? 'Orang-orang di balik FKStudio' : 'The people behind FKStudio' }}</h2>
                </div>
                <div class="flex flex-wrap gap-x-8 gap-y-14 pt-4 lg:col-span-7 lg:justify-center">
                    @foreach ($owners as $owner)
                        <article class="pop relative w-full max-w-xs rounded-lg bg-surface px-5 pb-6 pt-9 {{ $loop->odd ? '-rotate-1' : 'rotate-1' }}">
                            <span class="absolute left-1/2 top-3 h-2.5 w-16 -translate-x-1/2 rounded-full border-2 border-edge bg-bg" aria-hidden="true"></span>
                            <div class="aspect-square overflow-hidden rounded-md border-2 border-edge bg-brand-gradient-soft">
                                @if ($owner->image)
                                    <img src="{{ $owner->media_url }}" alt="{{ $owner->name }}" loading="lazy" class="h-full w-full object-cover object-top">
                                @else
                                    <div class="flex h-full items-center justify-center text-6xl text-brand-400"><i class="fa-solid fa-user" aria-hidden="true"></i></div>
                                @endif
                            </div>
                            <h3 class="mt-5 text-2xl leading-tight">{{ $owner->name }}</h3>
                            <p class="mt-1 inline-block rounded-md bg-sun-300 px-2 py-0.5 text-small font-bold text-navy">{{ $owner->getTranslation('role') }}</p>
                            @if ($owner->getTranslation('bio'))
                                <p class="mt-3 line-clamp-3 text-small text-muted">{{ $owner->getTranslation('bio') }}</p>
                            @endif
                            @if ($owner->instagram_url || $owner->linkedin_url)
                                <div class="mt-4 flex gap-2">
                                    @if ($owner->instagram_url)
                                        <a href="{{ $owner->instagram_url }}" target="_blank" rel="noopener" class="btn-icon border-2 border-edge bg-surface hover:bg-sun-300 hover:text-navy" aria-label="Instagram {{ $owner->name }}"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>
                                    @endif
                                    @if ($owner->linkedin_url)
                                        <a href="{{ $owner->linkedin_url }}" target="_blank" rel="noopener" class="btn-icon border-2 border-edge bg-surface hover:bg-sun-300 hover:text-navy" aria-label="LinkedIn {{ $owner->name }}"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></a>
                                    @endif
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
