@props(['name', 'label' => null, 'hint' => null, 'required' => false, 'value' => null, 'rows' => 4])

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
    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @required($required)
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->except('id')->class(['textarea', 'input-invalid' => $hasError]) }}
    >{{ old($name, $value) }}</textarea>
    @if ($hint && ! $hasError)<p class="hint">{{ $hint }}</p>@endif
    @error($name)<p id="{{ $id }}-error" class="field-error">{{ $message }}</p>@enderror
</div>
