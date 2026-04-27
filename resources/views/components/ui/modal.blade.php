@props(['name', 'title' => null, 'maxWidth' => 'md'])

@php
    $maxWidthClass = match ($maxWidth) {
        'sm' => 'max-w-sm',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        default => 'max-w-md',
    };
@endphp

<div
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-40 flex items-center justify-center p-4"
>
    <div class="fixed inset-0 bg-black/40" x-on:click="open = false" aria-hidden="true"></div>

    <div class="relative bg-white rounded-2xl shadow-xl w-full {{ $maxWidthClass }} p-6"
         role="dialog" aria-modal="true">
        @if ($title)
            <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ $title }}</h2>
        @endif

        {{ $slot }}
    </div>
</div>
