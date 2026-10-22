@php
    $id = app()->getLocale() === 'id';
@endphp

@if ($packages->isNotEmpty())
    <section id="packages" class="section scroll-mt-20">
        <div class="container-page">
            <x-ui.section-heading
                :eyebrow="$id ? 'Produk & paket' : 'Products & packages'"
                :title="$id ? 'Pilih paket yang sesuai kebutuhan Anda' : 'Pick the package that fits your needs'"
                :lead="$id ? 'Harga transparan, bisa bayar DP, dan proses mudah dari satu akun.' : 'Transparent pricing, deposit option, and an easy process from one account.'" />

            <div class="mt-14 grid items-stretch gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($packages as $package)
                    <x-site.package-card :package="$package" />
                @endforeach
            </div>
        </div>
    </section>
@endif
