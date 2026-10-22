@php
    $id = app()->getLocale() === 'id';
    $categories = $blogs->map(fn ($b) => ['raw' => $b->category_id, 'name' => $b->getTranslation('category')])
        ->filter(fn ($c) => $c['raw'])->unique('raw')->values();
    $index = $blogs->map(fn ($b) => [
        'c' => $b->category_id,
        't' => mb_strtolower($b->getTranslation('title') . ' ' . Str::limit(strip_tags($b->getTranslation('content')), 400, '')),
    ])->values();
@endphp
<x-layouts.public
    :title="$id ? 'Blog & Artikel' : 'Blog & Insights'"
    :description="$id ? 'Artikel, tips, dan wawasan seputar website, teknologi, dan pertumbuhan bisnis digital dari FKStudio.' : 'Articles, tips, and insights on websites, technology and digital business growth from FKStudio.'">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <div x-data="{ category: 'all', q: '', index: @js($index), show(i) { const it = this.index[i]; return (this.category === 'all' || this.category === it.c) && (!this.q || it.t.includes(this.q.toLowerCase())); } }">
        <x-site.page-hero
            eyebrow="Blog"
            :title="$id ? 'Wawasan dan tips seputar dunia digital' : 'Insights and tips from the digital world'"
            :lead="$id ? 'Bacaan ringan untuk membantu bisnis Anda tumbuh lewat teknologi.' : 'Easy reads to help your business grow through technology.'">
            <div class="mx-auto mt-8 max-w-xl">
                <label for="blog-search" class="sr-only">{{ $id ? 'Cari artikel' : 'Search articles' }}</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-muted" aria-hidden="true"></i>
                    <input id="blog-search" type="search" x-model="q" placeholder="{{ $id ? 'Cari artikel…' : 'Search articles…' }}" class="input rounded-full pl-11">
                </div>
            </div>
            @if ($categories->count() > 1)
                <div class="mt-6 flex gap-2 overflow-x-auto pb-2 sm:flex-wrap sm:justify-center">
                    <button type="button" @click="category = 'all'" class="shrink-0 rounded-full border px-4 py-2 text-small font-semibold transition"
                        :class="category === 'all' ? 'border-brand-600 bg-brand-600 text-white shadow-glow' : 'border-line bg-surface text-muted hover:text-ink'">{{ $id ? 'Semua Artikel' : 'All articles' }}</button>
                    @foreach ($categories as $category)
                        <button type="button" @click="category = @js($category['raw'])" class="shrink-0 rounded-full border px-4 py-2 text-small font-semibold transition"
                            :class="category === @js($category['raw']) ? 'border-brand-600 bg-brand-600 text-white shadow-glow' : 'border-line bg-surface text-muted hover:text-ink'">{{ $category['name'] }}</button>
                    @endforeach
                </div>
            @endif
        </x-site.page-hero>

        <section class="container-page pb-16 pt-6 md:pb-24">
            @if ($blogs->isEmpty())
                <x-ui.card><x-ui.empty-state icon="fa-newspaper" :title="$id ? 'Belum ada artikel' : 'No articles yet'" /></x-ui.card>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($blogs as $i => $blog)
                        <article x-show="show({{ $i }})" x-transition.opacity class="group card card-hover flex flex-col overflow-hidden">
                            <a href="{{ route('blog.show', $blog->slug) }}" class="block aspect-[16/10] overflow-hidden bg-brand-gradient-soft" tabindex="-1" aria-hidden="true">
                                @if ($blog->image)
                                    <img src="{{ $blog->media_url }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <span class="flex h-full w-full items-center justify-center text-5xl text-brand-400"><i class="fa-solid fa-newspaper"></i></span>
                                @endif
                            </a>
                            <div class="flex flex-1 flex-col p-5">
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-caption text-muted">
                                    <span class="badge-brand">{{ $blog->getTranslation('category') ?? 'Blog' }}</span>
                                    <time datetime="{{ ($blog->published_at ?? $blog->created_at)->toDateString() }}">{{ ($blog->published_at ?? $blog->created_at)->translatedFormat('d M Y') }}</time>
                                </div>
                                <h2 class="mt-3 text-lg leading-snug"><a href="{{ route('blog.show', $blog->slug) }}" class="hover:text-brand-text">{{ $blog->getTranslation('title') }}</a></h2>
                                <p class="mt-2 line-clamp-3 flex-1 text-small text-muted">{{ Str::limit(strip_tags($blog->getTranslation('content')), 160) }}</p>
                                <div class="mt-4 flex items-center justify-between border-t border-line pt-4 text-small">
                                    <span class="truncate text-muted">{{ $blog->author_name ?: $blog->author?->name }}</span>
                                    <a href="{{ route('blog.show', $blog->slug) }}" class="inline-flex shrink-0 items-center gap-1.5 font-semibold text-brand-text">{{ $id ? 'Baca' : 'Read' }} <i class="fa-solid fa-arrow-right text-caption" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <p x-show="! index.some((_, i) => show(i))" x-cloak class="py-16 text-center text-muted">{{ $id ? 'Tidak ada artikel yang cocok dengan pencarian Anda.' : 'No articles match your search.' }}</p>
            @endif
        </section>
    </div>

    <x-slot:footer><x-site.footer /><x-site.whatsapp /></x-slot:footer>
</x-layouts.public>
