@props(['variant' => 'neutral', 'dot' => false])

@php
    $variants = [
        'brand' => 'badge-brand',
        'success' => 'badge-success',
        'warning' => 'badge-warning',
        'danger' => 'badge-danger',
        'info' => 'badge-info',
        'neutral' => 'badge-neutral',
    ];
@endphp

<span {{ $attributes->merge(['class' => $variants[$variant] ?? 'badge-neutral']) }}>
    @if ($dot)<span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>@endif
    {{ $slot }}
</span>
