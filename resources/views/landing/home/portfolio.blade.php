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
                    <x-site.project-card :project="$project" data-aos="fade-up" data-aos-delay="{{ ($i % 3) * 80 }}" />
                @endforeach
            </div>
        </div>
    </section>
@endif
