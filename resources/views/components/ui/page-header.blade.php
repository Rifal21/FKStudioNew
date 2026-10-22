@props(['title', 'description' => null])

<div {{ $attributes->class(['mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between']) }}>
    <div class="min-w-0">
        <h1 class="text-2xl font-extrabold md:text-3xl">{{ $title }}</h1>
        @if ($description)<p class="mt-1 text-small text-muted md:text-body">{{ $description }}</p>@endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-3">{{ $actions }}</div>
    @endisset
</div>
