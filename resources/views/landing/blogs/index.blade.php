@php
    $id = app()->getLocale() === 'id';
    $categories = $blogs->map(fn ($b) => ['raw' => $b->category_id, 'name' => $b->getTranslation('category')])
        ->filter(fn ($c) => $c['raw'])->unique('raw')->values();
    $index = $blogs->map(fn ($b) => [
        'c' => $b->category_id,
        't' => mb_strtolower($b->getTranslation('title') . ' ' . Str::limit(strip_tags($b->getTranslation('content')), 400, '')),
    ])->values();
    $lead = $blogs->first();
    $covers = ['bg-sun-300 text-navy', 'bg-brand-200 text-navy', 'bg-sun-200 text-navy'];
    $dateOf = fn ($b) => $b->published_at ?? $b->created_at;
@endphp
<x-layouts.public
    :title="$id ? 'Blog & Artikel' : 'Blog & Insights'"
    :description="$id ? 'Artikel, tips, dan wawasan seputar website, teknologi, dan pertumbuhan bisnis digital dari FKStudio.' : 'Articles, tips, and insights on websites, technology and digital business growth from FKStudio.'">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <div x-data="{ category: 'all', q: '', index: @js($index), get leadOn() { return this.category === 'all' && !this.q }, show(i) { const it = this.index[i]; return (this.category === 'all' || this.category === it.c) && (!this.q || it.t.includes(this.q.toLowerCase())); } }">
        <x-site.page-head
            :title="$id ? 'Catatan dari tim FKStudio' : 'Notes from the FKStudio team'"
            :lead="$id ? 'Panduan singkat soal website, desain, dan teknologi untuk bisnis Anda.' : 'Short guides on websites, design, and technology for your business.'">
            <x-slot:aside>
                <label for="blog-search" class="sr-only">{{ $id ? 'Cari artikel' : 'Search articles' }}</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-muted" aria-hidden="true"></i>
                    <input id="blog-search" type="search" x-model="q" placeholder="{{ $id ? 'Cari tulisan' : 'Search posts' }}" class="input rounded-full border-2 border-edge pl-11 shadow-hard-sm">
                </div>
            </x-slot:aside>

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
            @if ($blogs->isEmpty())
                <x-ui.card><x-ui.empty-state icon="fa-newspaper" :title="$id ? 'Belum ada tulisan' : 'No posts yet'" /></x-ui.card>
            @else
                {{-- Newest post, shown big when nothing is filtered --}}
                <article x-show="leadOn" class="group relative grid items-center gap-10 lg:grid-cols-12 lg:gap-12">
                    <a href="{{ route('blog.show', $lead->slug) }}" class="relative lg:col-span-7" tabindex="-1" aria-hidden="true">
                        <span class="tape -top-3 left-8 z-10 -rotate-6" aria-hidden="true"></span>
                        <span class="pop pop-lift block aspect-[16/10] overflow-hidden rounded-lg bg-brand-gradient-soft shadow-hard-lg lg:-rotate-1">
                            @if ($lead->image)
                                <img src="{{ $lead->media_url }}" alt="" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                            @else
                                <span class="relative flex h-full w-full items-center justify-center overflow-hidden {{ $covers[0] }}">
                                    <span class="select-none font-display text-[16rem] font-extrabold leading-none text-sun-400 [-webkit-text-stroke:3px_rgb(10_31_58)]">{{ mb_strtoupper(mb_substr($lead->getTranslation('title'), 0, 1)) }}</span>
                                </span>
                            @endif
                        </span>
                    </a>
                    <div class="min-w-0 lg:col-span-5">
                        <p class="text-small font-bold text-ink/70"><span class="rounded-md bg-sun-300 px-2 py-0.5 text-navy">{{ $lead->getTranslation('category') ?? 'Blog' }}</span> <time class="ml-2" datetime="{{ $dateOf($lead)->toDateString() }}">{{ $dateOf($lead)->translatedFormat('d F Y') }}</time></p>
                        <h2 class="mt-4 font-display text-3xl font-extrabold leading-[1.05] [font-stretch:85%] md:text-5xl"><a href="{{ route('blog.show', $lead->slug) }}" class="hover:underline hover:decoration-sun-400 hover:decoration-4 hover:underline-offset-4">{{ $lead->getTranslation('title') }}</a></h2>
                        <p class="mt-4 max-w-md text-base text-muted md:text-lg">{{ Str::limit(strip_tags($lead->getTranslation('content')), 180) }}</p>
                        <p class="mt-5 text-small text-muted">{{ $id ? 'Ditulis oleh' : 'By' }} <strong class="text-ink">{{ $lead->author_name ?: $lead->author?->name }}</strong></p>
                        <a href="{{ route('blog.show', $lead->slug) }}" class="btn-primary mt-7">{{ $id ? 'Baca tulisan ini' : 'Read this post' }}</a>
                    </div>
                </article>

                <div class="mt-4" x-show="! leadOn || index.length > 1" :class="leadOn && 'md:mt-20'">
                    <ul>
                        @foreach ($blogs as $i => $blog)
                            @php($d = $dateOf($blog))
                            <li x-show="show({{ $i }}) @if ($i === 0) && ! leadOn @endif" class="border-t-2 border-edge/30 last:border-b-2">
                                <a href="{{ route('blog.show', $blog->slug) }}" class="group grid gap-4 py-7 transition-colors hover:bg-sun-100/70 sm:grid-cols-[6.5rem_minmax(0,1fr)_9rem] sm:items-center sm:gap-7 md:px-4 dark:hover:bg-sun-400/10">
                                    <time datetime="{{ $d->toDateString() }}" class="font-display text-4xl font-extrabold leading-none [font-stretch:85%]">{{ $d->format('d') }}<span class="mt-1 block text-small font-bold text-muted">{{ $d->translatedFormat('M Y') }}</span></time>
                                    <div class="min-w-0">
                                        <span class="rounded-md bg-brand-100 px-2 py-0.5 text-caption font-bold text-brand-800 dark:bg-brand-500/20 dark:text-brand-200">{{ $blog->getTranslation('category') ?? 'Blog' }}</span>
                                        <h3 class="mt-2.5 text-2xl leading-snug">{{ $blog->getTranslation('title') }}</h3>
                                        <p class="mt-1.5 line-clamp-2 max-w-2xl text-muted">{{ Str::limit(strip_tags($blog->getTranslation('content')), 170) }}</p>
                                        <p class="mt-2.5 text-small text-muted">{{ $blog->author_name ?: $blog->author?->name }}</p>
                                    </div>
                                    <span class="hidden aspect-[4/3] overflow-hidden rounded-md border-2 border-edge sm:block {{ $blog->image ? 'bg-brand-gradient-soft' : $covers[$i % 3] }}">
                                        @if ($blog->image)
                                            <img src="{{ $blog->media_url }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                        @else
                                            <span class="flex h-full w-full items-center justify-center font-display text-6xl font-extrabold">{{ mb_strtoupper(mb_substr($blog->getTranslation('title'), 0, 1)) }}</span>
                                        @endif
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <p x-show="! index.some((_, i) => show(i))" x-cloak class="py-16 text-center text-muted">{{ $id ? 'Tidak ada tulisan yang cocok dengan pencarian Anda.' : 'No posts match your search.' }}</p>
            @endif
        </section>
    </div>

    <x-slot:footer><x-site.footer /><x-site.whatsapp /></x-slot:footer>
</x-layouts.public>
