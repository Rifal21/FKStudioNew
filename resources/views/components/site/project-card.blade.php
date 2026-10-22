@props(['project'])

@php
    $id = app()->getLocale() === 'id';
    $title = $project->getTranslation('title');
@endphp

<article {{ $attributes->class(['group card card-hover overflow-hidden']) }}>
    <div class="relative aspect-[16/10] overflow-hidden bg-brand-gradient-soft">
        @if ($project->image)
            <img src="{{ $project->media_url }}" alt="{{ $title }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
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
