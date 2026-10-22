@props(['eyebrow' => null, 'title', 'lead' => null])

<section class="relative overflow-hidden pb-10 pt-28 text-center md:pb-14 md:pt-36">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(50%_60%_at_50%_0%,rgb(124_58_237/0.14),transparent),radial-gradient(30%_40%_at_90%_20%,rgb(255_92_138/0.10),transparent)]" aria-hidden="true"></div>
    <div class="container-page relative">
        @if ($eyebrow)<span class="eyebrow">{{ $eyebrow }}</span>@endif
        <h1 class="mx-auto mt-4 max-w-3xl text-4xl font-extrabold leading-tight md:text-5xl">{{ $title }}</h1>
        @if ($lead)<p class="mx-auto mt-4 max-w-2xl text-base text-muted md:text-lg">{{ $lead }}</p>@endif
        {{ $slot }}
    </div>
</section>
