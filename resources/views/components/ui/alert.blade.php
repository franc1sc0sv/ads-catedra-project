@props(['variant' => 'info', 'title' => null])

@php
    $styles = match ($variant) {
        'success' => 'bg-green-50 border-green-200 text-green-800',
        'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
        'danger'  => 'bg-red-50 border-red-200 text-red-800',
        default   => 'bg-blue-50 border-blue-200 text-blue-800',
    };
@endphp

<div role="alert" {{ $attributes->merge(['class' => "border rounded-lg px-4 py-3 text-sm $styles"]) }}>
    @if ($title)
        <p class="font-semibold mb-1">{{ $title }}</p>
    @endif
    <div>{{ $slot }}</div>
</div>
