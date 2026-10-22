@php
    $id = app()->getLocale() === 'id';
@endphp

@if ($packages->isNotEmpty())
    <section id="packages" class="section dots-paper scroll-mt-20 border-y-2 border-edge bg-brand-100/60 dark:bg-surface-2/50">
        <div class="container-page">
            <div class="max-w-2xl">
                <h2 class="section-title">{{ $id ? 'Pilih paket yang sesuai kebutuhan Anda' : 'Pick the package that fits your needs' }}</h2>
                <p class="section-lead">{{ $id ? 'Harga transparan, bisa bayar DP, dan proses mudah dari satu akun.' : 'Transparent pricing, deposit option, and an easy process from one account.' }}</p>
            </div>

            <div class="mt-16 grid items-stretch gap-x-8 gap-y-12 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($packages as $package)
                    <x-site.package-card :package="$package" />
                @endforeach
            </div>
        </div>
    </section>
@endif
