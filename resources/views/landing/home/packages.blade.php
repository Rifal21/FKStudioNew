@php
    $id = app()->getLocale() === 'id';
@endphp

@if ($packages->isNotEmpty())
    <section id="packages" class="dots-paper scroll-mt-20 border-y-2 border-edge bg-brand-100/60 py-20 dark:bg-surface-2/50 md:py-32">
        <div class="container-page">
            <div class="max-w-2xl">
                <h2 class="section-title">{{ $id ? 'Pilih paket, bayar DP dulu kalau perlu' : 'Pick a package, start with a deposit' }}</h2>
                <p class="section-lead">{{ $id ? 'Harga tertera di tiap paket. Tagihan dan progres bisa Anda pantau dari satu akun.' : 'Prices are listed on every package. Invoices and progress live in one account.' }}</p>
            </div>

            <div class="mt-14 space-y-10">
                @foreach ($packages as $package)
                    <x-site.package-card :package="$package" />
                @endforeach
            </div>
        </div>
    </section>
@endif
