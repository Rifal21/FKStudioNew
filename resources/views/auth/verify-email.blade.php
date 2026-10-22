<x-layouts.auth title="Verifikasi Email" heading="Verifikasi email Anda" subheading="Terima kasih telah mendaftar! Klik tautan verifikasi yang baru kami kirim ke email Anda. Belum menerimanya? Kami bisa mengirim ulang.">
    @if (session('status') == 'verification-link-sent')
        <x-auth-session-status class="mb-6" status="Tautan verifikasi baru telah dikirim ke alamat email yang Anda daftarkan." />
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-ui.button type="submit" icon="fa-envelope">Kirim ulang email verifikasi</x-ui.button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-small font-semibold text-muted underline hover:text-ink">Keluar</button>
        </form>
    </div>
</x-layouts.auth>
