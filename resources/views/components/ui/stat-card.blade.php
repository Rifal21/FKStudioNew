@props([
    'label',
    'value',
    'icon' => 'fa-chart-simple',
    'tone' => 'brand',   // brand | pink | sun | mint | sky
    'trend' => null,     // e.g. "+12%"
    'trendUp' => true,
])

@php
    // Literal classes (icon tile background/text per tone).
    $tones = [
        'brand' => 'bg-brand-50 text-brand-700 dark:bg-brand-500/20 dark:text-brand-300',
        'pink' => 'bg-pink-50 text-pink-700 dark:bg-pink-500/15 dark:text-pink-300',
        'sun' => 'bg-sun-50 text-sun-700 dark:bg-sun-400/15 dark:text-sun-300',
        'mint' => 'bg-mint-50 text-mint-800 dark:bg-mint-400/15 dark:text-mint-300',
        'sky' => 'bg-sky-50 text-sky-800 dark:bg-sky-400/15 dark:text-sky-300',
    ];
@endphp

<div {{ $attributes->class(['card card-pad flex items-center gap-4']) }}>
    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md text-xl {{ $tones[$tone] ?? $tones['brand'] }}">
        <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
    </span>
    <div class="min-w-0">
        <p class="text-small leading-snug text-muted">{{ $label }}</p>
        <p class="truncate text-2xl font-extrabold leading-tight">{{ $value }}</p>
        @if ($trend)
            <p class="mt-0.5 text-caption font-semibold {{ $trendUp ? 'text-mint-800 dark:text-mint-300' : 'text-danger-700 dark:text-red-300' }}">
                <i class="fa-solid {{ $trendUp ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}" aria-hidden="true"></i> {{ $trend }}
            </p>
        @endif
    </div>
</div>
