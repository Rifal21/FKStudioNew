<x-layouts.auth title="Konfirmasi Password" heading="Konfirmasi password" subheading="Ini adalah area aman. Mohon konfirmasi password Anda sebelum melanjutkan.">
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf
        <x-ui.password name="password" label="Password" />
        <x-ui.button type="submit" size="lg" class="w-full">Konfirmasi</x-ui.button>
    </form>
</x-layouts.auth>
