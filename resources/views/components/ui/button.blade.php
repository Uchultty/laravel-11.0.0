@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
])

@php
    $variants = [
        'primary' => 'ui-btn-primary',
        'secondary' => 'ui-btn-secondary',
        'danger' => 'ui-btn-danger',
    ];

    $classes = $variants[$variant] ?? $variants['primary'];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
