@php
    $id = app()->getLocale() === 'id';
    $featured = $projects->take(5)->values();
    $spans = match (true) {
        $featured->count() >= 5 => ['lg:col-span-7', 'lg:col-span-5', 'lg:col-span-4', 'lg:col-span-4', 'lg:col-span-4'],
        $featured->count() === 4 => ['lg:col-span-7', 'lg:col-span-5', 'lg:col-span-6', 'lg:col-span-6'],
        $featured->count() === 3 => ['lg:col-span-7', 'lg:col-span-5', 'lg:col-span-12'],
        $featured->count() === 2 => ['lg:col-span-7', 'lg:col-span-5'],
        default => ['lg:col-span-12'],
    };
@endphp

@if ($featured->isNotEmpty())
    <section id="portfolio" class="section scroll-mt-20">
        <div class="container-page">
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="max-w-2xl">
                    <h2 class="section-title">{{ $id ? 'Karya terbaru kami' : 'Our latest work' }}</h2>
                    <p class="section-lead">{{ $id ? 'Beberapa proyek yang telah kami selesaikan bersama klien.' : 'A selection of projects we delivered together with our clients.' }}</p>
                </div>
                <a href="{{ route('portfolio.index') }}" class="btn-outline shrink-0 self-start md:self-auto">
                    {{ $id ? 'Lihat semua proyek' : 'View all projects' }} <i class="fa-solid fa-arrow-right text-caption" aria-hidden="true"></i>
                </a>
            </div>

            <div class="mt-14 grid gap-x-8 gap-y-10 lg:grid-cols-12">
                @foreach ($featured as $i => $project)
                    <div class="min-w-0 {{ $spans[$i] }}">
                        <x-site.project-card :project="$project" class="h-full" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
