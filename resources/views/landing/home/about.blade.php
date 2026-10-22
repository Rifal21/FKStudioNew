@php
    $id = app()->getLocale() === 'id';
    $missions = collect(explode("\n", str_replace("\r", '', (string) $about->getTranslation('mission'))))
        ->map(fn ($m) => trim($m))->filter()->values();
@endphp

<section id="about" class="section scroll-mt-20">
    <div class="container-page">
        <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-16">
            {{-- Visual --}}
            <div class="relative min-w-0">
                <div class="absolute -bottom-4 -left-4 h-full w-full rounded-xl bg-sunrise opacity-30" aria-hidden="true"></div>
                <div class="relative aspect-[4/3] overflow-hidden rounded-xl border border-line bg-surface shadow-card">
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
                    <div class="absolute bottom-4 left-4 z-10 flex items-center gap-3 rounded-full border border-line bg-surface/90 px-4 py-2 backdrop-blur">
                        <div class="js-about-pagination swiper-pagination !static !w-auto"></div>
                    </div>
                    <span class="badge-brand absolute left-4 top-4 z-10 bg-surface/90 backdrop-blur">Est. 2021</span>
                </div>
            </div>

            {{-- Copy --}}
            <div class="min-w-0">
                <span class="eyebrow">{{ $id ? 'Tentang kami' : 'About us' }}</span>
                <h2 class="section-title mt-4">{{ $about->getTranslation('title') }}</h2>
                <p class="mt-4 text-base text-muted md:text-lg">{{ $about->getTranslation('description') }}</p>

                @if ($about->getTranslation('vision'))
                    <figure class="mt-8 rounded-lg border-l-4 border-brand-500 bg-brand-soft p-5">
                        <figcaption class="text-caption font-bold uppercase tracking-wider text-brand-text">{{ $id ? 'Visi kami' : 'Our vision' }}</figcaption>
                        <blockquote class="mt-2 text-lg font-semibold leading-snug">{{ $about->getTranslation('vision') }}</blockquote>
                    </figure>
                @endif

                @if ($missions->isNotEmpty())
                    <div class="mt-8">
                        <h3 class="text-lg font-bold">{{ $id ? 'Misi kami' : 'Our mission' }}</h3>
                        <ul class="mt-4 space-y-3">
                            @foreach ($missions as $point)
                                <li class="flex items-start gap-3">
                                    <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-mint-50 text-caption text-mint-800 dark:bg-mint-400/15 dark:text-mint-300"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
                                    <span class="text-muted">{{ $point }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        {{-- Team --}}
        @if ($owners->isNotEmpty())
            <div class="mt-20">
                <x-ui.section-heading :eyebrow="$id ? 'Tim kami' : 'Our team'" :title="$id ? 'Orang-orang di balik FKStudio' : 'The people behind FKStudio'" />
                <div class="mt-10 flex flex-wrap justify-center gap-6">
                    @foreach ($owners as $owner)
                        <article class="card card-hover w-full max-w-sm overflow-hidden sm:w-[calc(50%-0.75rem)] lg:w-[calc(33.333%-1rem)]">
                            <div class="aspect-[4/3] overflow-hidden bg-surface-2">
                                @if ($owner->image)
                                    <img src="{{ $owner->media_url }}" alt="{{ $owner->name }}" loading="lazy" class="h-full w-full object-cover object-top">
                                @else
                                    <div class="flex h-full items-center justify-center text-5xl text-brand-300"><i class="fa-solid fa-user" aria-hidden="true"></i></div>
                                @endif
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg">{{ $owner->name }}</h3>
                                <p class="text-small font-semibold text-brand-text">{{ $owner->getTranslation('role') }}</p>
                                @if ($owner->getTranslation('bio'))
                                    <p class="mt-2 line-clamp-3 text-small text-muted">{{ $owner->getTranslation('bio') }}</p>
                                @endif
                                @if ($owner->instagram_url || $owner->linkedin_url)
                                    <div class="mt-4 flex gap-2">
                                        @if ($owner->instagram_url)
                                            <a href="{{ $owner->instagram_url }}" target="_blank" rel="noopener" class="btn-icon border border-line" aria-label="Instagram {{ $owner->name }}"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>
                                        @endif
                                        @if ($owner->linkedin_url)
                                            <a href="{{ $owner->linkedin_url }}" target="_blank" rel="noopener" class="btn-icon border border-line" aria-label="LinkedIn {{ $owner->name }}"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
