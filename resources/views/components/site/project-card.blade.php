@props(['project', 'ratio' => 'aspect-[16/10]'])

@php
    $id = app()->getLocale() === 'id';
    $title = $project->getTranslation('title');
    $host = $project->url ? preg_replace('#^https?://(www\.)?#', '', rtrim($project->url, '/')) : \Illuminate\Support\Str::slug($title) . '.com';
@endphp

<article {{ $attributes->class(['group flex flex-col']) }}>
    <x-site.browser-frame :url="$host" class="pop-lift flex flex-1 flex-col">
        <div class="relative {{ $ratio }} overflow-hidden bg-brand-gradient-soft">
            @if ($project->image)
                <img src="{{ $project->media_url }}" alt="{{ $title }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover object-top transition duration-700 group-hover:scale-105">
            @else
                <div class="absolute inset-0 flex items-center justify-center text-5xl text-brand-400"><i class="fa-solid fa-globe" aria-hidden="true"></i></div>
            @endif
        </div>
        <div class="flex flex-1 flex-col p-5">
            <span class="self-start rounded-md bg-brand-100 px-2 py-0.5 text-caption font-bold text-brand-800 dark:bg-brand-500/20 dark:text-brand-200">{{ $project->getTranslation('category') ?? 'Digital' }}</span>
            <h3 class="mt-3 text-xl leading-tight">{{ $title }}</h3>
            <p class="mt-1.5 line-clamp-2 text-small text-muted">{{ $project->getTranslation('description') }}</p>
            @if ($project->url)
                <a href="{{ $project->url }}" target="_blank" rel="noopener" class="mt-auto inline-flex items-center gap-2 pt-4 text-small font-bold text-ink underline decoration-sun-400 decoration-[3px] underline-offset-4 hover:decoration-brand-500">
                    {{ $id ? 'Kunjungi website' : 'Visit website' }}
                    <span class="sr-only">{{ $title }}</span>
                </a>
            @endif
        </div>
    </x-site.browser-frame>
</article>
