@props(['title' => null, 'header' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-sm border border-gray-100']) }}>
    @if ($header || $title)
        <div class="px-6 pt-6 pb-3 border-b border-gray-100">
            @if ($header)
                {{ $header }}
            @else
                <h3 class="text-base font-semibold text-gray-900">{{ $title }}</h3>
            @endif
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
            {{ $footer }}
        </div>
    @endif
</div>
