<x-layouts.public :landing="true" bodyClass="overflow-x-hidden">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    @include('landing.home.hero')
    @include('landing.home.clients')
    @include('landing.home.stats')
    @include('landing.home.about')
    @include('landing.home.services')
    @include('landing.home.process')
    @include('landing.home.portfolio')
    @include('landing.home.packages')
    @include('landing.home.testimonials')
    @include('landing.home.faq')
    @include('landing.home.cta')

    <x-slot:footer>
        <x-site.footer />
        <x-site.whatsapp />
    </x-slot:footer>
</x-layouts.public>
