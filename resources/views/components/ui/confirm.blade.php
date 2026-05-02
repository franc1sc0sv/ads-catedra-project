@props([
    'targetForm' => null,
    'title' => 'Confirmar acción',
    'message' => '¿Confirmas esta acción?',
    'confirmLabel' => 'Confirmar',
    'cancelLabel' => 'Cancelar',
    'variant' => 'danger',
])

@php
    $variantBtn = in_array($variant, ['primary', 'secondary', 'danger', 'ghost'], true) ? $variant : 'danger';
    $iconClasses = match ($variant) {
        'danger' => 'bg-coral/10 text-coral',
        'warning' => 'bg-accent/20 text-neutralDark',
        default => 'bg-primary/10 text-primary',
    };
@endphp

<div
    x-data="{
        open: false,
        targetForm: '{{ $targetForm }}',
        request() {
            const form = this.targetForm ? document.getElementById(this.targetForm) : null;
            if (form && typeof form.checkValidity === 'function' && !form.checkValidity()) {
                form.reportValidity();
                return;
            }
            this.open = true;
        },
        confirm() {
            const form = this.targetForm ? document.getElementById(this.targetForm) : null;
            if (!form) { this.open = false; return; }
            this.open = false;
            if (typeof form.requestSubmit === 'function') form.requestSubmit();
            else form.submit();
        },
    }"
    class="inline-block"
>
    <span @click="request()" class="contents">
        {{ $slot }}
    </span>

    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            x-on:keydown.escape.window="open = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
            <div
                x-show="open"
                x-transition.opacity
                class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                @click="open = false"
                aria-hidden="true"
            ></div>

            <div
                x-show="open"
                x-transition
                class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6"
                role="dialog"
                aria-modal="true"
            >
                <div class="flex items-start gap-4">
                    <div class="flex-none w-10 h-10 rounded-full {{ $iconClasses }} flex items-center justify-center">
                        @if ($variant === 'danger')
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        @elseif ($variant === 'warning')
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ $message }}</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <x-ui.button type="button" variant="ghost" size="sm" @click="open = false">
                        {{ $cancelLabel }}
                    </x-ui.button>
                    <x-ui.button
                        type="button"
                        variant="{{ $variantBtn }}"
                        size="sm"
                        @click="confirm()"
                    >
                        {{ $confirmLabel }}
                    </x-ui.button>
                </div>
            </div>
        </div>
    </template>
</div>
