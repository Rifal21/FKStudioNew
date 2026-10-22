@php
    $id = app()->getLocale() === 'id';
    $title = $blog->getTranslation('title');
    $author = $blog->author_name ?: $blog->author?->name;
    $date = $blog->published_at ?? $blog->created_at;
    $url = url()->current();
@endphp
<x-layouts.public
    :title="$title"
    :description="Str::limit(strip_tags($blog->getTranslation('content')), 155, '')"
    :image="$blog->image ? $blog->media_url : null"
    type="article"
    :published-at="$date?->toAtomString()"
    :modified-at="$blog->updated_at?->toAtomString()">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <x-seo.schema :data="['@graph' => [
        \App\Support\Seo::blogPosting($blog, $settings, $title, $blog->image ? $blog->media_url : null),
        \App\Support\Seo::breadcrumbs([[$id ? 'Beranda' : 'Home', route('home')], ['Blog', route('blog.index')], [$title, route('blog.show', $blog->slug)]]),
    ]]" />

    <article class="relative pb-16 pt-28 md:pb-24 md:pt-36">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-96 bg-[radial-gradient(50%_60%_at_50%_0%,rgb(30_155_229/0.12),transparent)]" aria-hidden="true"></div>

        <div class="container-page relative max-w-3xl">
            <a href="{{ route('blog.index') }}" class="btn-secondary btn-sm rounded-full"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> {{ $id ? 'Semua artikel' : 'All articles' }}</a>

            <header class="mt-8">
                <span class="badge-brand">{{ $blog->getTranslation('category') ?? 'Blog' }}</span>
                <h1 class="mt-4 text-3xl font-extrabold leading-tight md:text-5xl">{{ $title }}</h1>
                <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-3 text-small text-muted">
                    <span class="flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-gradient font-bold uppercase text-navy">{{ mb_substr($author ?? 'F', 0, 1) }}</span>
                        <span class="font-semibold text-ink">{{ $author }}</span>
                    </span>
                    <time datetime="{{ $date->toDateString() }}"><i class="fa-regular fa-calendar mr-1" aria-hidden="true"></i>{{ $date->translatedFormat('d F Y') }}</time>
                    <span><i class="fa-regular fa-eye mr-1" aria-hidden="true"></i>{{ number_format($blog->views) }} {{ $id ? 'dibaca' : 'views' }}</span>
                </div>
            </header>

            @if ($blog->image)
                <img src="{{ $blog->media_url }}" alt="{{ $title }}" class="mt-8 aspect-[16/9] w-full rounded-xl border border-line object-cover shadow-card" fetchpriority="high">
            @endif

            <div class="rich-text mt-10">{!! $blog->content !!}</div>

            {{-- Share --}}
            <div class="mt-12 flex flex-wrap items-center gap-3 border-t border-line pt-6" x-data>
                <span class="text-small font-semibold">{{ $id ? 'Bagikan:' : 'Share:' }}</span>
                <a class="btn-icon border border-line" target="_blank" rel="noopener" aria-label="WhatsApp" href="https://wa.me/?text={{ urlencode($title . ' ' . $url) }}"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
                <a class="btn-icon border border-line" target="_blank" rel="noopener" aria-label="Facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
                <a class="btn-icon border border-line" target="_blank" rel="noopener" aria-label="X" href="https://twitter.com/intent/tweet?url={{ urlencode($url) }}&text={{ urlencode($title) }}"><i class="fa-brands fa-x-twitter" aria-hidden="true"></i></a>
                <button type="button" class="btn-secondary btn-sm"
                    @click="navigator.clipboard.writeText(@js($url)).then(() => toast('success', @js($id ? 'Tautan disalin.' : 'Link copied.')))">
                    <i class="fa-solid fa-link" aria-hidden="true"></i> {{ $id ? 'Salin tautan' : 'Copy link' }}
                </button>
            </div>
        </div>
    </article>

    @if ($relatedBlogs->isNotEmpty())
        <section class="border-t border-line bg-surface-2/60 py-14 md:py-20">
            <div class="container-page">
                <h2 class="text-2xl font-extrabold md:text-3xl">{{ $id ? 'Artikel lainnya' : 'More articles' }}</h2>
                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    @foreach ($relatedBlogs as $r)
                        <a href="{{ route('blog.show', $r->slug) }}" class="group card card-hover overflow-hidden">
                            <div class="aspect-[16/10] overflow-hidden bg-brand-gradient-soft">
                                @if ($r->image)
                                    <img src="{{ $r->media_url }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <span class="flex h-full w-full items-center justify-center text-4xl text-brand-400"><i class="fa-solid fa-newspaper"></i></span>
                                @endif
                            </div>
                            <div class="p-5">
                                <span class="badge-brand">{{ $r->getTranslation('category') ?? 'Blog' }}</span>
                                <h3 class="mt-3 text-lg leading-snug group-hover:text-brand-text">{{ $r->getTranslation('title') }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-slot:footer><x-site.footer /><x-site.whatsapp /></x-slot:footer>
</x-layouts.public>
