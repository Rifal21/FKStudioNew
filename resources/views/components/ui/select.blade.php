@props(['name', 'label' => null, 'hint' => null, 'required' => false, 'value' => null])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="label">
            {{ $label }}@if ($required)<span class="text-danger-600" aria-hidden="true"> *</span>@endif
        </label>
    @endif
    <select
        id="{{ $id }}"
        name="{{ $name }}"
        @required($required)
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->except('id')->class(['select', 'input-invalid' => $hasError]) }}
    >
        {{ $slot }}
    </select>
    @if ($hint && ! $hasError)<p class="hint">{{ $hint }}</p>@endif
    @error($name)<p id="{{ $id }}-error" class="field-error">{{ $message }}</p>@enderror
</div>
