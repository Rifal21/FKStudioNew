@props([
    'variant' => 'primary', // primary | secondary | ghost | sun | danger | danger-soft
    'size' => 'md',         // sm | md | lg
    'href' => null,
    'type' => 'button',
    'icon' => null,         // FontAwesome class, e.g. "fa-arrow-right"
    'iconRight' => false,
])

@php
    // Full literal class names so Tailwind can see them at build time.
    $variants = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'ghost' => 'btn-ghost',
        'sun' => 'btn-sun',
        'danger' => 'btn-danger',
        'danger-soft' => 'btn-danger-soft',
    ];
    $sizes = ['sm' => 'btn-sm', 'md' => '', 'lg' => 'btn-lg'];
    $classes = trim(($variants[$variant] ?? 'btn-primary') . ' ' . ($sizes[$size] ?? ''));
    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @else type="{{ $type }}" @endif
    {{ $attributes->merge(['class' => $classes]) }}
>
    @if ($icon && ! $iconRight)<i class="{{ str_contains($icon, 'fa-brands') ? '' : 'fa-solid ' }}{{ $icon }}" aria-hidden="true"></i>@endif
    {{ $slot }}
    @if ($icon && $iconRight)<i class="{{ str_contains($icon, 'fa-brands') ? '' : 'fa-solid ' }}{{ $icon }}" aria-hidden="true"></i>@endif
</{{ $tag }}>
