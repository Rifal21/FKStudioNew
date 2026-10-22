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
        <x-site.page-hero
            :eyebrow="$id ? 'Portofolio' : 'Portfolio'"
            :title="$id ? 'Karya yang bercerita tentang kualitas kami' : 'Work that speaks for our quality'"
            :lead="$id ? 'Jelajahi proyek yang telah kami kerjakan bersama klien dari berbagai bidang.' : 'Explore projects we built together with clients across many industries.'">
            @if ($categories->count() > 1)
                <div class="mt-8 flex gap-2 overflow-x-auto pb-2 sm:flex-wrap sm:justify-center" role="tablist">
                    <button type="button" @click="category = 'all'" role="tab" :aria-selected="category === 'all'"
                        class="shrink-0 rounded-full border px-4 py-2 text-small font-semibold transition"
                        :class="category === 'all' ? 'border-brand-600 bg-brand-600 text-white shadow-glow' : 'border-line bg-surface text-muted hover:text-ink'">
                        {{ $id ? 'Semua' : 'All' }}
                    </button>
                    @foreach ($categories as $category)
                        <button type="button" @click="category = @js($category['raw'])" role="tab" :aria-selected="category === @js($category['raw'])"
                            class="shrink-0 rounded-full border px-4 py-2 text-small font-semibold transition"
                            :class="category === @js($category['raw']) ? 'border-brand-600 bg-brand-600 text-white shadow-glow' : 'border-line bg-surface text-muted hover:text-ink'">
                            {{ $category['name'] }}
                        </button>
                    @endforeach
                </div>
            @endif
        </x-site.page-hero>

        <section class="container-page pb-16 pt-6 md:pb-24">
            @if ($projects->isEmpty())
                <x-ui.card><x-ui.empty-state icon="fa-layer-group" :title="$id ? 'Belum ada proyek' : 'No projects yet'" /></x-ui.card>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <div x-show="category === 'all' || category === @js($project->category_id)" x-transition.opacity>
                            <x-site.project-card :project="$project" class="h-full" />
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    @include('landing.home.cta')

    <x-slot:footer><x-site.footer /><x-site.whatsapp /></x-slot:footer>
</x-layouts.public>
