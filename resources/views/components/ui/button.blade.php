@props(['type' => 'submit', 'variant' => 'primary'])

@php
$classes = match($variant) {
    'secondary' => 'bg-secondary hover:bg-secondary/90 text-white',
    'outline'   => 'border border-primary text-primary hover:bg-primary/5',
    default     => 'bg-primary hover:bg-primary/90 text-white',
};
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "w-full $classes font-medium py-2.5 px-4 rounded-lg text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-primary/50"]) }}
>
    {{ $slot }}
</button>
