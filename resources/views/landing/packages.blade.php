@php($id = app()->getLocale() === 'id')
<x-layouts.public
    :title="$id ? 'Paket Layanan' : 'Service Packages'"
    :description="$id ? 'Pilih paket pembuatan website dan layanan digital FKStudio dengan harga transparan dan pembayaran fleksibel.' : 'Choose a FKStudio website and digital service package with transparent pricing and flexible payment.'">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <x-site.page-hero
        :eyebrow="$id ? 'Produk & paket' : 'Products & packages'"
        :title="$id ? 'Paket layanan untuk setiap tahap bisnis Anda' : 'Service packages for every stage of your business'"
        :lead="$id ? 'Harga transparan, bisa bayar DP, dan seluruh proses dipantau dari satu akun.' : 'Transparent pricing, deposit option, and the whole process tracked from one account.'" />

    <section class="container-page pb-16 pt-6 md:pb-24">
        @if ($packages->isEmpty())
            <x-ui.card><x-ui.empty-state icon="fa-tags" :title="$id ? 'Belum ada paket tersedia' : 'No packages available yet'" :text="$id ? 'Silakan kembali lagi nanti.' : 'Please check back soon.'" /></x-ui.card>
        @else
            <div class="grid items-stretch gap-6 pt-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($packages as $package)
                    <x-site.package-card :package="$package" />
                @endforeach
            </div>
        @endif
    </section>

    @include('landing.home.cta')

    <x-slot:footer><x-site.footer /><x-site.whatsapp /></x-slot:footer>
</x-layouts.public>
