@props([
    'name',
    'label' => null,
    'type' => 'text',
    'hint' => null,
    'required' => false,
    'value' => null,
])

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
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($type !== 'password' && $type !== 'file') value="{{ old($name, $value) }}" @endif
        @required($required)
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @elseif ($hint) aria-describedby="{{ $id }}-hint" @endif
        {{ $attributes->except('id')->class(['input', 'input-invalid' => $hasError]) }}
    >
    @if ($hint && ! $hasError)<p id="{{ $id }}-hint" class="hint">{{ $hint }}</p>@endif
    @error($name)<p id="{{ $id }}-error" class="field-error">{{ $message }}</p>@enderror
</div>
