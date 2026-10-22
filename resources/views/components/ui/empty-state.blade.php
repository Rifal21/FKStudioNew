@props(['icon' => 'fa-inbox', 'title', 'text' => null])

<div {{ $attributes->class(['flex flex-col items-center justify-center px-6 py-14 text-center']) }}>
    <span class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-soft text-2xl text-brand-text">
        <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
    </span>
    <h3 class="mt-4 text-lg font-bold">{{ $title }}</h3>
    @if ($text)<p class="mt-1 max-w-sm text-small text-muted">{{ $text }}</p>@endif
    @if (! $slot->isEmpty())<div class="mt-5">{{ $slot }}</div>@endif
</div>
