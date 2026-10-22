@props(['url' => ''])

<div {{ $attributes->class(['pop overflow-hidden rounded-lg bg-surface']) }}>
    <div class="flex items-center gap-2.5 border-b-2 border-edge bg-sun-300 px-3.5 py-2">
        <span class="flex gap-1.5" aria-hidden="true">
            <span class="h-2.5 w-2.5 rounded-full border-2 border-navy bg-white"></span>
            <span class="h-2.5 w-2.5 rounded-full border-2 border-navy bg-brand-300"></span>
            <span class="h-2.5 w-2.5 rounded-full border-2 border-navy bg-navy"></span>
        </span>
        <span class="min-w-0 flex-1 truncate rounded-full border-2 border-navy bg-white px-2.5 text-caption font-semibold text-navy">{{ $url }}</span>
    </div>
    {{ $slot }}
</div>
