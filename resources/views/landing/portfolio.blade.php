@php
    $id = app()->getLocale() === 'id';
    $categories = $projects->map(fn ($p) => ['raw' => $p->category_id, 'name' => $p->getTranslation('category')])
        ->filter(fn ($c) => $c['raw'])->unique('raw')->values();
@endphp
<x-layouts.public
    :title="$id ? 'Portofolio Proyek' : 'Project Portfolio'"
    :description="$id ? 'Lihat hasil kerja dan proyek website yang telah diselesaikan FKStudio bersama klien.' : 'Browse the websites and projects FKStudio has delivered together with clients.'">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <div x-data="{ category: 'all' }">
        <x-site.page-head
            :title="$id ? 'Proyek yang sudah kami kerjakan' : 'Projects we have built'"
            :lead="$id ? 'Buka tiap proyek untuk melihat websitenya langsung.' : 'Open any project to see the live website.'">
            @if ($categories->count() > 1)
                <div class="mt-10 flex gap-6 overflow-x-auto" role="tablist">
                    @foreach ([['all', $id ? 'Semua' : 'All']] + $categories->map(fn ($c) => [$c['raw'], $c['name']])->all() as [$key, $label])
                        <button type="button" role="tab" @click="category = @js($key)" :aria-selected="category === @js($key)"
                            class="shrink-0 border-b-4 pb-1.5 text-base font-bold transition"
                            :class="category === @js($key) ? 'border-sun-400 text-ink' : 'border-transparent text-ink/60 hover:text-ink'">{{ $label }}</button>
                    @endforeach
                </div>
            @endif
        </x-site.page-head>

        <section class="container-page py-14 md:py-20">
            @if ($projects->isEmpty())
                <x-ui.card><x-ui.empty-state icon="fa-layer-group" :title="$id ? 'Belum ada proyek' : 'No projects yet'" /></x-ui.card>
            @else
                <div>
                    @foreach ($projects as $i => $project)
                        @php
                            $title = $project->getTranslation('title');
                            $host = $project->url ? preg_replace('#^https?://(www\.)?#', '', rtrim($project->url, '/')) : \Illuminate\Support\Str::slug($title) . '.com';
                            $flip = $i % 2 === 1;
                        @endphp
                        <article x-show="category === 'all' || category === @js($project->category_id)" class="group grid items-center gap-10 border-t-2 border-edge/30 py-14 first:border-t-0 first:pt-0 lg:grid-cols-12 lg:gap-14">
                            <x-site.browser-frame :url="$host" @class(['pop-lift shadow-hard-lg lg:col-span-7', 'lg:order-2 lg:rotate-1' => $flip, 'lg:-rotate-1' => ! $flip])>
                                <div class="relative aspect-[16/10] overflow-hidden bg-brand-gradient-soft">
                                    @if ($project->image)
                                        <img src="{{ $project->media_url }}" alt="{{ $title }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover object-top transition duration-700 group-hover:scale-105">
                                    @else
                                        <div class="absolute inset-0 flex items-center justify-center text-6xl text-brand-400"><i class="fa-solid fa-globe" aria-hidden="true"></i></div>
                                    @endif
                                </div>
                            </x-site.browser-frame>
                            <div @class(['min-w-0 lg:col-span-5', 'lg:order-1' => $flip])>
                                <span class="inline-block rounded-md bg-sun-300 px-2 py-0.5 text-small font-bold text-navy">{{ $project->getTranslation('category') ?? 'Digital' }}</span>
                                <h2 class="mt-4 font-display text-3xl font-extrabold leading-[1.05] [font-stretch:85%] md:text-5xl">{{ $title }}</h2>
                                <p class="mt-4 max-w-md text-base text-muted md:text-lg">{{ $project->getTranslation('description') }}</p>
                                @if ($project->url)
                                    <a href="{{ $project->url }}" target="_blank" rel="noopener" class="btn-primary mt-7">{{ $id ? 'Kunjungi website' : 'Visit website' }}<span class="sr-only"> {{ $title }}</span></a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <x-slot:footer><x-site.footer /><x-site.whatsapp /></x-slot:footer>
</x-layouts.public>
