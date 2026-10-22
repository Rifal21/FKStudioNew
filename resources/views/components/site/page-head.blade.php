@props(['title', 'lead' => null])

<section class="border-b-2 border-edge bg-brand-100 pb-12 pt-32 dark:bg-surface-2 md:pb-16 md:pt-40">
    <div class="container-page">
        <div class="grid gap-8 lg:grid-cols-12 lg:items-end">
            <div class="min-w-0 lg:col-span-8">
                <h1 class="font-display text-[2.75rem] font-extrabold leading-[0.98] tracking-tight [font-stretch:80%] sm:text-6xl lg:text-7xl">{{ $title }}</h1>
                @if ($lead)<p class="mt-5 max-w-xl text-base text-ink/80 md:text-lg">{{ $lead }}</p>@endif
            </div>
            @isset($aside)<div class="lg:col-span-4">{{ $aside }}</div>@endisset
        </div>
        {{ $slot }}
    </div>
</section>
