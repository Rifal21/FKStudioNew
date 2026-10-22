@props(['steps' => [], 'current' => 1])

<ol {{ $attributes->class(['flex items-center gap-2 overflow-x-auto']) }} aria-label="Langkah">
    @foreach ($steps as $i => $label)
        @php($n = $i + 1)
        <li class="flex shrink-0 items-center gap-2" @if ($n === $current) aria-current="step" @endif>
            <span @class([
                'flex h-8 w-8 items-center justify-center rounded-full text-small font-bold',
                'bg-brand-600 text-white shadow-glow' => $n === $current,
                'bg-mint-50 text-mint-800 dark:bg-mint-400/15 dark:text-mint-300' => $n < $current,
                'bg-surface-2 text-muted' => $n > $current,
            ])>
                @if ($n < $current)<i class="fa-solid fa-check text-caption" aria-hidden="true"></i>@else{{ $n }}@endif
            </span>
            <span @class(['text-small font-semibold', 'text-ink' => $n <= $current, 'text-muted' => $n > $current])>{{ $label }}</span>
            @unless ($loop->last)<span class="mx-1 h-px w-6 bg-line sm:w-10" aria-hidden="true"></span>@endunless
        </li>
    @endforeach
</ol>
