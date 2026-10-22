@props(['settings', 'duitkuMethods' => []])

@php
    $banks = $settings->payment_methods ?? [];
    $hasManual = count($banks) > 0 || $settings->invoice_qris;
@endphp

<div class="space-y-8">
    @if ($hasManual)
        <div>
            <h3 class="text-small font-bold">Transfer Manual</h3>
            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                @foreach ($banks as $index => $bank)
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment_method" value="{{ $bank['bank'] }} - {{ $bank['number'] }}" class="peer sr-only" @checked($index === 0) required>
                        <span class="flex h-full items-center gap-4 rounded-lg border border-line bg-surface p-4 transition hover:border-brand-300 peer-checked:border-brand-600 peer-checked:bg-brand-soft peer-checked:ring-2 peer-checked:ring-brand-600/30 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-brand-50 text-xl text-brand-700 dark:bg-brand-500/20 dark:text-brand-300"><i class="fa-solid fa-building-columns" aria-hidden="true"></i></span>
                            <span class="min-w-0">
                                <span class="block font-bold uppercase">{{ $bank['bank'] }}</span>
                                <span class="block truncate text-caption text-muted">{{ $bank['name'] ?? '' }}</span>
                            </span>
                        </span>
                    </label>
                @endforeach
                @if ($settings->invoice_qris)
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment_method" value="QRIS" class="peer sr-only">
                        <span class="flex h-full items-center gap-4 rounded-lg border border-line bg-surface p-4 transition hover:border-brand-300 peer-checked:border-brand-600 peer-checked:bg-brand-soft peer-checked:ring-2 peer-checked:ring-brand-600/30 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-pink-50 text-xl text-pink-700 dark:bg-pink-500/15 dark:text-pink-300"><i class="fa-solid fa-qrcode" aria-hidden="true"></i></span>
                            <span>
                                <span class="block font-bold">QRIS</span>
                                <span class="block text-caption text-muted">Konfirmasi manual</span>
                            </span>
                        </span>
                    </label>
                @endif
            </div>
        </div>
    @endif

    <div>
        <h3 class="text-small font-bold">Pembayaran Otomatis</h3>
        @if (count($duitkuMethods) > 0)
            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach ($duitkuMethods as $method)
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment_method" value="Duitku|{{ $method['paymentMethod'] }}" class="peer sr-only">
                        <span class="flex h-full flex-col items-center gap-3 rounded-lg border border-line bg-surface p-4 text-center transition hover:border-brand-300 peer-checked:border-brand-600 peer-checked:bg-brand-soft peer-checked:ring-2 peer-checked:ring-brand-600/30 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500">
                            <span class="flex h-10 w-full items-center justify-center rounded-md bg-white p-2">
                                <img src="{{ $method['paymentImage'] }}" alt="" loading="lazy" class="max-h-full max-w-full object-contain">
                            </span>
                            <span class="line-clamp-2 text-caption font-semibold">{{ $method['paymentName'] }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        @else
            <label class="mt-3 block cursor-pointer">
                <input type="radio" name="payment_method" value="Duitku" class="peer sr-only">
                <span class="flex items-center gap-4 rounded-lg border border-line bg-surface p-4 transition hover:border-brand-300 peer-checked:border-brand-600 peer-checked:bg-brand-soft peer-checked:ring-2 peer-checked:ring-brand-600/30 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-sun-50 text-xl text-sun-700 dark:bg-sun-400/15 dark:text-sun-300"><i class="fa-solid fa-wallet" aria-hidden="true"></i></span>
                    <span>
                        <span class="block font-bold">Duitku (Otomatis)</span>
                        <span class="block text-caption text-muted">E-Wallet, Virtual Account &amp; gerai retail</span>
                    </span>
                </span>
            </label>
        @endif
    </div>
</div>
