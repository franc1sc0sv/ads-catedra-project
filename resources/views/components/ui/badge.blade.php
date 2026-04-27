@props(['variant' => 'gray'])

@php
    $styles = match ($variant) {
        'green'  => 'bg-green-100 text-green-800',
        'yellow' => 'bg-amber-100 text-amber-800',
        'red'    => 'bg-red-100 text-red-800',
        'blue'   => 'bg-blue-100 text-blue-800',
        default  => 'bg-gray-100 text-gray-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold $styles"]) }}>
    {{ $slot }}
</span>
