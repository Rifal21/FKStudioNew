<button
    type="button"
    @click="$store.theme.toggle()"
    {{ $attributes->merge(['class' => 'btn-icon border border-line bg-surface']) }}
    :aria-label="$store.theme.dark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'"
    title="Ganti tema"
>
    <i class="fa-solid fa-sun" x-show="$store.theme.dark" x-cloak aria-hidden="true"></i>
    <i class="fa-solid fa-moon" x-show="!$store.theme.dark" aria-hidden="true"></i>
</button>
