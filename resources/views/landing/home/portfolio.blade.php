@php
    $id = app()->getLocale() === 'id';
    $lead = $projects->first();
    $rest = $projects->slice(1)->values();
    if ($lead) {
        $leadTitle = $lead->getTranslation('title');
        $leadHost = $lead->url ? preg_replace('#^https?://(www\.)?#', '', rtrim($lead->url, '/')) : \Illuminate\Support\Str::slug($leadTitle) . '.com';
    }
@endphp

@if ($lead)
    <section id="portfolio" class="scroll-mt-20 py-20 md:py-28" x-data="{ rail: null, by(d) { this.rail.scrollBy({ left: d * this.rail.clientWidth * 0.8, behavior: 'smooth' }) } }">
        <div class="container-page">
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="max-w-2xl">
                    <h2 class="section-title">{{ $id ? 'Proyek yang sudah kami kerjakan' : 'Projects we have built' }}</h2>
                </div>
                <a href="{{ route('portfolio.index') }}" class="btn-outline shrink-0 self-start md:self-auto">{{ $id ? 'Lihat semua proyek' : 'View all projects' }}</a>
            </div>

            {{-- The newest project gets the room; the rest sit on a rail --}}
            <article class="group mt-14 grid items-center gap-10 lg:grid-cols-12 lg:gap-12">
                <x-site.browser-frame :url="$leadHost" class="pop-lift shadow-hard-lg lg:col-span-7 lg:-rotate-1">
                    <div class="relative aspect-[16/10] overflow-hidden bg-brand-gradient-soft">
                        @if ($lead->image)
                            <img src="{{ $lead->media_url }}" alt="{{ $leadTitle }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover object-top transition duration-700 group-hover:scale-105">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center text-6xl text-brand-400"><i class="fa-solid fa-globe" aria-hidden="true"></i></div>
                        @endif
                    </div>
                </x-site.browser-frame>
                <div class="min-w-0 lg:col-span-5">
                    <span class="inline-block rounded-md bg-sun-300 px-2 py-0.5 text-small font-bold text-navy">{{ $lead->getTranslation('category') ?? 'Digital' }}</span>
                    <h3 class="mt-4 font-display text-3xl font-extrabold leading-tight [font-stretch:85%] md:text-5xl">{{ $leadTitle }}</h3>
                    <p class="mt-4 max-w-md text-base text-muted md:text-lg">{{ $lead->getTranslation('description') }}</p>
                    @if ($lead->url)
                        <a href="{{ $lead->url }}" target="_blank" rel="noopener" class="btn-primary mt-8">{{ $id ? 'Kunjungi website' : 'Visit website' }}<span class="sr-only"> {{ $leadTitle }}</span></a>
                    @endif
                </div>
            </article>

            @if ($rest->isNotEmpty())
                <div class="mt-20 flex items-center justify-between gap-4">
                    <h3 class="text-xl">{{ $id ? 'Proyek lainnya' : 'More projects' }}</h3>
                    <div class="flex gap-2">
                        <button type="button" @click="by(-1)" class="btn-outline h-11 min-h-0 w-11 px-0" aria-label="{{ $id ? 'Geser ke kiri' : 'Scroll left' }}"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                        <button type="button" @click="by(1)" class="btn-outline h-11 min-h-0 w-11 px-0" aria-label="{{ $id ? 'Geser ke kanan' : 'Scroll right' }}"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                    </div>
                </div>
            @endif
        </div>

        @if ($rest->isNotEmpty())
            <div x-ref="rail" x-init="rail = $refs.rail" class="custom-scrollbar mt-8 flex snap-x snap-mandatory gap-8 overflow-x-auto px-4 pb-8 pt-1 sm:px-6 lg:px-[max(2rem,calc((100vw-80rem)/2+2rem))]" tabindex="0" role="region" aria-label="{{ $id ? 'Proyek lainnya' : 'More projects' }}">
                @foreach ($rest as $project)
                    <div class="w-[82%] shrink-0 snap-start sm:w-[24rem]">
                        <x-site.project-card :project="$project" class="h-full" />
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endif
