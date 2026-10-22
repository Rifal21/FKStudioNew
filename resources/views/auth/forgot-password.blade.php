<x-layouts.auth title="Lupa Password" heading="Lupa password?" subheading="Masukkan email Anda dan kami kirimkan tautan untuk membuat password baru.">
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <x-ui.input name="email" type="email" label="Email" placeholder="nama@email.com" autofocus :required="true" />
        <x-ui.button type="submit" size="lg" class="w-full" icon="fa-paper-plane">Kirim tautan reset</x-ui.button>
        <p class="text-center text-small text-muted"><a href="{{ route('login') }}" class="font-bold text-brand-text hover:underline"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Kembali ke halaman masuk</a></p>
    </form>
</x-layouts.auth>
