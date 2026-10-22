@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-lg border border-mint-400/40 bg-mint-50 p-4 text-small font-medium text-mint-800 dark:bg-mint-400/10 dark:text-mint-300']) }} role="status">
        <i class="fa-solid fa-circle-check mt-0.5" aria-hidden="true"></i>
        <span>{{ $status }}</span>
    </div>
@endif
