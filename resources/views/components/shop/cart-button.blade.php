@props(['count' => 0])
@php $id = app()->getLocale() === 'id'; @endphp
<a href="{{ route('cart.index') }}"
    class="relative flex h-10 w-10 items-center justify-center rounded-full border-2 border-edge bg-surface text-ink transition hover:bg-sun-100 dark:hover:bg-surface-2"
    aria-label="{{ $id ? 'Keranjang' : 'Cart' }}{{ $count ? ' ('.$count.' item)' : '' }}">
    <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
    @if ($count > 0)
        <span class="absolute -right-1.5 -top-1.5 flex h-5 min-w-5 items-center justify-center rounded-full border-2 border-edge bg-sun-400 px-1 text-[11px] font-extrabold leading-none text-navy">{{ $count > 99 ? '99+' : $count }}</span>
    @endif
</a>
