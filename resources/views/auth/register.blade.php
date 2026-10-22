<x-layouts.auth title="Daftar" heading="Buat akun baru" subheading="Daftar untuk memesan paket dan memantau progres website Anda.">
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf
        <x-ui.input name="name" label="Nama lengkap" placeholder="Nama Anda" autocomplete="name" autofocus :required="true" />
        <x-ui.input name="email" type="email" label="Email" placeholder="nama@email.com" autocomplete="username" :required="true" />
        <x-ui.password name="password" label="Password" autocomplete="new-password" placeholder="Minimal 8 karakter" />
        <x-ui.password name="password_confirmation" label="Konfirmasi password" autocomplete="new-password" placeholder="Ulangi password" />

        <x-ui.button type="submit" size="lg" class="w-full" icon="fa-user-plus">Buat akun</x-ui.button>

        <p class="text-center text-small text-muted">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-brand-text hover:underline">Masuk</a></p>
    </form>
</x-layouts.auth>
