@props(['header' => null])

<div {{ $attributes->merge(['class' => 'overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100']) }}>
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        @isset($header)
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                {{ $header }}
            </thead>
        @endisset
        <tbody class="divide-y divide-gray-100 text-gray-800">
            {{ $slot }}
        </tbody>
    </table>
</div>
