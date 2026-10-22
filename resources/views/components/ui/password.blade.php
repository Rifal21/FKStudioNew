@props(['name' => 'password', 'label' => 'Password', 'required' => true, 'autocomplete' => 'current-password', 'hint' => null])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
@endphp

<div x-data="{ show: false }">
    <label for="{{ $id }}" class="label">{{ $label }}@if ($required)<span class="text-danger-600" aria-hidden="true"> *</span>@endif</label>
    <div class="relative">
        <input id="{{ $id }}" name="{{ $name }}" :type="show ? 'text' : 'password'" type="password" @required($required) autocomplete="{{ $autocomplete }}"
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
            {{ $attributes->except('id')->class(['input pr-12', 'input-invalid' => $hasError]) }}>
        <button type="button" @click="show = !show" class="absolute right-1 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-md text-muted hover:text-ink"
            :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
            <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
        </button>
    </div>
    @if ($hint && ! $hasError)<p class="hint">{{ $hint }}</p>@endif
    @error($name)<p id="{{ $id }}-error" class="field-error">{{ $message }}</p>@enderror
</div>
