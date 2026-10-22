<x-layouts.auth title="Masuk" heading="Selamat datang kembali" subheading="Masuk untuk memantau pesanan dan mengelola akun Anda.">
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <x-ui.input name="email" type="email" label="Email" placeholder="nama@email.com" autocomplete="username" autofocus :required="true" />

        <div>
            <x-ui.password name="password" label="Password" placeholder="••••••••" />
            @if (Route::has('password.request'))
                <p class="mt-2 text-right"><a class="text-small font-semibold text-brand-text hover:underline" href="{{ route('password.request') }}">Lupa password?</a></p>
            @endif
        </div>

        <label for="remember_me" class="flex cursor-pointer items-center gap-3">
            <input id="remember_me" type="checkbox" name="remember" class="h-5 w-5 rounded border-line text-brand-600 focus:ring-brand-500">
            <span class="text-small text-muted">Tetap masuk di perangkat ini</span>
        </label>

        <x-ui.button type="submit" size="lg" class="w-full" icon="fa-arrow-right" :icon-right="true">Masuk</x-ui.button>

        <p class="text-center text-small text-muted">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-brand-text hover:underline">Daftar sekarang</a></p>
    </form>
</x-layouts.auth>
