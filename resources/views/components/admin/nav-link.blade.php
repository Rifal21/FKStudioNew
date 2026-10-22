@props(['route', 'match' => null, 'icon', 'label', 'badge' => null])

@php
    $active = request()->routeIs($match ?? $route);
@endphp

<a href="{{ route($route) }}" @if ($active) aria-current="page" @endif
    @class([
        'group relative flex min-h-[44px] items-center gap-3 rounded-md px-3 text-small font-semibold transition',
        'bg-brand-soft text-brand-text' => $active,
        'text-muted hover:bg-surface-2 hover:text-ink' => ! $active,
    ])
    @click="sidebarOpen = false">
    @if ($active)<span class="absolute -left-3 top-2 h-[calc(100%-1rem)] w-1 rounded-r bg-brand-600" aria-hidden="true"></span>@endif
    <i class="fa-solid {{ $icon }} w-5 text-center {{ $active ? 'text-brand-600 dark:text-brand-300' : 'text-muted group-hover:text-brand-500' }}" aria-hidden="true"></i>
    <span class="flex-1 truncate">{{ $label }}</span>
    @if ($badge)
        <span class="rounded-full bg-pink-500 px-2 py-0.5 text-caption font-bold leading-none text-white">{{ $badge }}</span>
    @endif
</a>
