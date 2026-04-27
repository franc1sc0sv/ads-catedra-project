@props([
    'type' => 'submit',
    'variant' => 'primary',
    'size' => 'md',
    'block' => false,
])

@php
    $variantClasses = match ($variant) {
        'secondary' => 'bg-secondary hover:bg-secondary/90 text-white',
        'danger'    => 'bg-coral hover:bg-coral/90 text-white',
        'ghost'     => 'bg-transparent hover:bg-gray-100 text-gray-700',
        default     => 'bg-primary hover:bg-primary/90 text-white',
    };

    $sizeClasses = match ($size) {
        'sm'    => 'py-1.5 px-3 text-xs',
        default => 'py-2.5 px-4 text-sm',
    };

    $widthClass = $block ? 'w-full' : '';
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "$widthClass $variantClasses $sizeClasses font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary/50"]) }}
>
    {{ $slot }}
</button>
