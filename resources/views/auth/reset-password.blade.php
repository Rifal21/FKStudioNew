<x-layouts.auth title="Reset Password" heading="Buat password baru" subheading="Pilih password baru yang kuat untuk akun Anda.">
    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-ui.input name="email" type="email" label="Email" :value="$request->email" autocomplete="username" :required="true" />
        <x-ui.password name="password" label="Password baru" autocomplete="new-password" />
        <x-ui.password name="password_confirmation" label="Konfirmasi password" autocomplete="new-password" />

        <x-ui.button type="submit" size="lg" class="w-full" icon="fa-check">Simpan password</x-ui.button>
    </form>
</x-layouts.auth>
