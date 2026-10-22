@php
    $id = app()->getLocale() === 'id';
    $featured = $projects->take(6);
@endphp

@if ($featured->isNotEmpty())
    <section id="portfolio" class="section scroll-mt-20 bg-surface-2/60">
        <div class="container-page">
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <x-ui.section-heading align="left"
                    :eyebrow="$id ? 'Portofolio' : 'Portfolio'"
                    :title="$id ? 'Karya terbaru kami' : 'Our latest work'"
                    :lead="$id ? 'Beberapa proyek yang telah kami selesaikan bersama klien.' : 'A selection of projects we delivered together with our clients.'" />
                <x-ui.button :href="route('portfolio.index')" variant="secondary" icon="fa-arrow-right" :icon-right="true" class="shrink-0 self-start md:self-auto">
                    {{ $id ? 'Lihat semua proyek' : 'View all projects' }}
                </x-ui.button>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featured as $i => $project)
                    @php($title = $project->getTranslation('title'))
                    <article class="group card card-hover overflow-hidden" data-aos="fade-up" data-aos-delay="{{ ($i % 3) * 80 }}">
                        <div class="relative aspect-[16/10] overflow-hidden bg-brand-gradient-soft">
                            @if ($project->image)
                                <img src="{{ $project->media_url }}" alt="{{ $title }}" loading="lazy"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-5xl text-brand-400"><i class="fa-solid fa-globe" aria-hidden="true"></i></div>
                            @endif
                            <span class="badge-brand absolute left-3 top-3 bg-surface/90 backdrop-blur">{{ $project->getTranslation('category') ?? 'Digital' }}</span>
                        </div>
                        <div class="p-5">
                            <h3 class="text-lg">{{ $title }}</h3>
                            <p class="mt-1 line-clamp-2 text-small text-muted">{{ $project->getTranslation('description') }}</p>
                            @if ($project->url)
                                <a href="{{ $project->url }}" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 text-small font-semibold text-brand-text hover:underline">
                                    {{ $id ? 'Kunjungi website' : 'Visit website' }}
                                    <i class="fa-solid fa-arrow-up-right-from-square text-caption" aria-hidden="true"></i>
                                    <span class="sr-only">{{ $title }}</span>
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
