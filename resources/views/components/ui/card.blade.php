@props(['padded' => true, 'hover' => false])

<div {{ $attributes->class(['card', 'card-pad' => $padded, 'card-hover' => $hover]) }}>
    {{ $slot }}
</div>
