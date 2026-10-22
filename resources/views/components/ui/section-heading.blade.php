@props(['eyebrow' => null, 'title', 'lead' => null, 'align' => 'center'])

<div {{ $attributes->class(['max-w-3xl', 'mx-auto text-center' => $align === 'center']) }}>
    @if ($eyebrow)<span class="eyebrow">{{ $eyebrow }}</span>@endif
    <h2 class="section-title {{ $eyebrow ? 'mt-4' : '' }}">{{ $title }}</h2>
    @if ($lead)<p class="section-lead {{ $align === 'center' ? 'mx-auto' : '' }}">{{ $lead }}</p>@endif
</div>
