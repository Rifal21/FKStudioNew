<x-layouts.public :noindex="true" title="Profil Saya" description="Kelola informasi profil dan kata sandi akun Anda.">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <div class="relative pb-16 pt-28 md:pb-24 md:pt-36">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(50%_30%_at_50%_0%,rgb(30_155_229/0.12),transparent)]" aria-hidden="true"></div>

        <div class="container-page relative max-w-3xl">
            <x-ui.page-header title="Pengaturan profil" description="Perbarui informasi akun dan jaga keamanan kata sandi Anda." />

            @foreach (['profile-updated' => ['Profil berhasil diperbarui', 'Data profil baru Anda telah disimpan.'], 'password-updated' => ['Password berhasil diubah', 'Kata sandi akun Anda telah diganti.']] as $key => [$title, $text])
                @if (session('status') === $key)
                    <div class="mb-6 flex items-start gap-3 rounded-lg border border-mint-400/40 bg-mint-50 p-4 text-mint-800 dark:bg-mint-400/10 dark:text-mint-300" role="status">
                        <i class="fa-solid fa-circle-check mt-0.5" aria-hidden="true"></i>
                        <div><p class="font-bold">{{ $title }}</p><p class="text-small">{{ $text }}</p></div>
                    </div>
                @endif
            @endforeach

            <div class="space-y-6">
                <x-ui.card>
                    <div class="flex items-center gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-brand-50 text-xl text-brand-700 dark:bg-brand-500/20 dark:text-brand-300"><i class="fa-solid fa-user-gear" aria-hidden="true"></i></span>
                        <div><h2 class="text-xl">Informasi profil</h2><p class="text-small text-muted">Perbarui nama dan alamat email Anda.</p></div>
                    </div>
                    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
                        @csrf
                        @method('patch')
                        <div class="grid gap-5 md:grid-cols-2">
                            <x-ui.input name="name" label="Nama lengkap" :value="$user->name" autocomplete="name" :required="true" />
                            <x-ui.input name="email" type="email" label="Alamat email" :value="$user->email" autocomplete="email" :required="true" />
                        </div>
                        <div class="flex justify-end"><x-ui.button type="submit">Simpan profil</x-ui.button></div>
                    </form>
                </x-ui.card>

                <x-ui.card>
                    <div class="flex items-center gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-pink-50 text-xl text-pink-700 dark:bg-pink-500/15 dark:text-pink-300"><i class="fa-solid fa-lock" aria-hidden="true"></i></span>
                        <div><h2 class="text-xl">Ubah password</h2><p class="text-small text-muted">Gunakan kata sandi yang kuat dan unik.</p></div>
                    </div>
                    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
                        @csrf
                        @method('put')
                        <div class="grid gap-5 md:grid-cols-3">
                            @foreach ([['current_password', 'Password saat ini', 'current-password'], ['password', 'Password baru', 'new-password'], ['password_confirmation', 'Konfirmasi password', 'new-password']] as [$field, $label, $ac])
                                <div>
                                    <label for="update_password_{{ $field }}" class="label">{{ $label }}</label>
                                    <input id="update_password_{{ $field }}" name="{{ $field }}" type="password" required autocomplete="{{ $ac }}" @class(['input', 'input-invalid' => $errors->updatePassword->has($field)])>
                                    @if ($errors->updatePassword->has($field))<p class="field-error">{{ $errors->updatePassword->first($field) }}</p>@endif
                                </div>
                            @endforeach
                        </div>
                        <div class="flex justify-end"><x-ui.button type="submit">Ubah password</x-ui.button></div>
                    </form>
                </x-ui.card>
            </div>
        </div>
    </div>

    <x-slot:footer><x-site.footer /></x-slot:footer>
</x-layouts.public>
