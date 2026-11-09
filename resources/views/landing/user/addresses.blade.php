@php
    $id = app()->getLocale() === 'id';
    $return = request('return') === 'checkout' ? 'checkout' : null;
    $fields = [
        ['label', $id ? 'Label (mis. Rumah, Kantor)' : 'Label (e.g. Home, Office)', false],
        ['recipient_name', $id ? 'Nama penerima' : 'Recipient name', true],
        ['phone', $id ? 'Nomor HP penerima' : 'Recipient phone', true],
        ['province', $id ? 'Provinsi' : 'Province', true],
        ['city', $id ? 'Kota / kabupaten' : 'City / regency', true],
        ['district', $id ? 'Kecamatan' : 'District', false],
        ['postal_code', $id ? 'Kode pos' : 'Postal code', false],
    ];
@endphp
<x-layouts.public :noindex="true" :title="$id ? 'Alamat Saya' : 'My Addresses'">
    <x-slot:navbar><x-site.navbar /></x-slot:navbar>

    <div class="pb-16 pt-28 md:pb-24 md:pt-36" x-data="{ editing: null }">
        <div class="container-page max-w-4xl">
            <x-ui.page-header :title="$id ? 'Alamat pengiriman' : 'Shipping addresses'" :description="$id ? 'Dipakai untuk mengirim produk fisik.' : 'Used to ship physical products.'">
                @if ($return)
                    <x-slot:actions><x-ui.button :href="route('shop.checkout')" variant="secondary" icon="fa-arrow-left">{{ $id ? 'Kembali ke checkout' : 'Back to checkout' }}</x-ui.button></x-slot:actions>
                @endif
            </x-ui.page-header>

            @include('landing.user._flash')

            @if ($addresses->isNotEmpty())
                <ul class="mb-8 grid gap-4 md:grid-cols-2">
                    @foreach ($addresses as $address)
                        <li class="rounded-xl border-2 border-edge bg-surface p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 text-small">
                                    <p class="font-bold">{{ $address->label ?: $address->recipient_name }} @if ($address->is_default)<x-ui.badge variant="brand">{{ $id ? 'Utama' : 'Default' }}</x-ui.badge>@endif</p>
                                    <p class="mt-1 text-muted">{{ $address->recipient_name }} · {{ $address->phone }}</p>
                                    <p class="text-muted">{{ $address->address_line }}, {{ $address->district ? $address->district.', ' : '' }}{{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                                </div>
                                <div class="flex shrink-0 gap-1">
                                    <button type="button" class="btn-icon" @click="editing = @js($address->only(['id', 'label', 'recipient_name', 'phone', 'province', 'city', 'district', 'postal_code', 'address_line', 'is_default']))" aria-label="{{ $id ? 'Ubah alamat' : 'Edit address' }}"><i class="fa-solid fa-pencil" aria-hidden="true"></i></button>
                                    <form method="POST" action="{{ route('user.addresses.destroy', $address) }}" x-data @submit="if (!confirm('{{ $id ? 'Hapus alamat ini?' : 'Delete this address?' }}')) $event.preventDefault()">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon text-danger-700 dark:text-red-300" aria-label="{{ $id ? 'Hapus alamat' : 'Delete address' }}"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            <x-ui.card>
                <h2 class="text-xl font-bold" x-text="editing ? '{{ $id ? 'Ubah alamat' : 'Edit address' }}' : '{{ $id ? 'Tambah alamat' : 'Add address' }}'"></h2>
                <form method="POST" :action="editing ? '{{ url('/my-addresses') }}/' + editing.id : '{{ route('user.addresses.store') }}'" class="mt-5 space-y-4">
                    @csrf
                    <template x-if="editing"><input type="hidden" name="_method" value="PATCH"></template>
                    @if ($return)<input type="hidden" name="return" value="checkout">@endif
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($fields as [$name, $label, $required])
                            <div>
                                <label for="addr-{{ $name }}" class="label">{{ $label }}@if ($required)<span class="text-danger-600" aria-hidden="true"> *</span>@endif</label>
                                <input id="addr-{{ $name }}" name="{{ $name }}" class="input" value="{{ old($name) }}" @if ($required) required @endif x-bind:value="editing ? (editing.{{ $name }} ?? '') : @js(old($name, ''))">
                                @error($name)<p class="field-error">{{ $message }}</p>@enderror
                            </div>
                        @endforeach
                    </div>
                    <div>
                        <label for="addr-line" class="label">{{ $id ? 'Alamat lengkap' : 'Street address' }}<span class="text-danger-600" aria-hidden="true"> *</span></label>
                        <textarea id="addr-line" name="address_line" rows="2" class="input" required x-text="editing ? editing.address_line : @js(old('address_line', ''))"></textarea>
                        @error('address_line')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <label class="flex items-center gap-2 text-small"><input type="checkbox" name="is_default" value="1" class="h-5 w-5 rounded border-line text-brand-600" x-bind:checked="editing && editing.is_default"> {{ $id ? 'Jadikan alamat utama' : 'Make this my default address' }}</label>
                    <div class="flex gap-3">
                        <button type="submit" class="btn-primary">{{ $id ? 'Simpan alamat' : 'Save address' }}</button>
                        <button type="button" class="btn-secondary" x-show="editing" x-cloak @click="editing = null">{{ $id ? 'Batal' : 'Cancel' }}</button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>

    <x-slot:footer><x-site.footer /></x-slot:footer>
</x-layouts.public>
