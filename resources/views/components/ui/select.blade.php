@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'placeholder' => 'Seleccionar...',
    'required' => false,
    'searchable' => false,
    'disabled' => false,
    'noSubmit' => false,
])

@php
    $resolvedValue = old($name, $value);
    $hasError = $errors->has($name);
    $borderClass = $hasError ? 'border-red-400' : 'border-gray-300';

    $normalized = collect($options)->map(function ($opt) {
        if (is_array($opt) && array_key_exists('value', $opt) && array_key_exists('label', $opt)) {
            return [
                'value' => $opt['value'],
                'label' => $opt['label'],
                'data'  => $opt['data'] ?? [],
                'badge' => $opt['badge'] ?? null,
            ];
        }
        return ['value' => $opt, 'label' => (string) $opt, 'data' => [], 'badge' => null];
    })->values();

    $jsConfig = [
        'name'        => (string) $name,
        'value'       => $resolvedValue === null ? '' : (string) $resolvedValue,
        'options'     => $normalized->map(fn ($o) => [
            'value' => (string) $o['value'],
            'label' => (string) $o['label'],
            'data'  => $o['data'] ?? [],
            'badge' => $o['badge'] ?? null,
        ])->all(),
        'searchable'  => (bool) $searchable,
        'required'    => (bool) $required,
        'disabled'    => (bool) $disabled,
        'hasError'    => (bool) $hasError,
        'placeholder' => (string) $placeholder,
    ];
@endphp

<div class="flex flex-col gap-1">
    @if ($label)
        <label for="{{ $name }}" class="text-sm font-medium text-gray-700">
            {{ $label }}@if($required) <span class="text-coral">*</span>@endif
        </label>
    @endif

    <div
        x-data="uiSelect({{ \Illuminate\Support\Js::from($jsConfig) }})"
        x-on:click.outside="close()"
        class="relative"
    >
        <select
            x-ref="native"
            id="{{ $name }}"
            @if(!$noSubmit) name="{{ $name }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            class="sr-only"
            tabindex="-1"
            aria-hidden="true"
        >
            <option value=""></option>
            @foreach ($normalized as $opt)
                <option
                    value="{{ $opt['value'] }}"
                    @selected((string) $resolvedValue === (string) $opt['value'])
                    @foreach (($opt['data'] ?? []) as $k => $v)
                        data-{{ $k }}="{{ $v }}"
                    @endforeach
                >{{ $opt['label'] }}</option>
            @endforeach
        </select>

        <button
            type="button"
            x-ref="trigger"
            @click="toggle()"
            @keydown="onTriggerKey($event)"
            :aria-expanded="open"
            :aria-disabled="disabled"
            aria-haspopup="listbox"
            {{ $attributes->merge(['class' => "w-full flex items-center justify-between gap-2 border $borderClass rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary/50 disabled:bg-gray-100 disabled:cursor-not-allowed"]) }}
            :class="disabled && 'opacity-60 cursor-not-allowed'"
        >
            <span class="truncate text-left flex-1 flex items-center gap-2 min-w-0" :class="!selected && 'text-gray-400'">
                <span class="truncate" x-text="selected ? selected.label : placeholder">{{ $placeholder }}</span>
                <template x-if="selected && selected.badge">
                    <span
                        class="shrink-0 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide"
                        :class="{
                            'bg-amber-100 text-amber-700': (selected.badge.variant ?? 'warning') === 'warning',
                            'bg-emerald-100 text-emerald-700': selected.badge.variant === 'success',
                            'bg-red-100 text-red-700': selected.badge.variant === 'danger',
                            'bg-blue-100 text-blue-700': selected.badge.variant === 'info',
                            'bg-gray-100 text-gray-700': selected.badge.variant === 'neutral',
                        }"
                        x-text="selected.badge.label"
                    ></span>
                </template>
            </span>
            <svg class="w-4 h-4 text-gray-400 transition-transform shrink-0" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div
            x-show="open"
            x-cloak
            x-transition.opacity.duration.100ms
            @keydown="onPanelKey($event)"
            style="display: none;"
            class="absolute left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 z-30 overflow-hidden"
        >
            <template x-if="searchable">
                <div class="p-2 border-b border-gray-100">
                    <input
                        x-ref="search"
                        x-model="query"
                        type="text"
                        placeholder="Buscar..."
                        class="w-full border border-gray-200 rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40"
                    >
                </div>
            </template>

            <ul
                x-ref="list"
                role="listbox"
                class="max-h-60 overflow-auto py-1"
            >
                <template x-for="(opt, i) in filtered" :key="opt.value">
                    <li
                        role="option"
                        :aria-selected="selected && String(selected.value) === String(opt.value)"
                        @click="pick(opt)"
                        @mouseenter="highlight = i"
                        class="px-3 py-2 text-sm cursor-pointer flex items-center gap-2"
                        :class="i === highlight ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-50'"
                    >
                        <span class="flex-1 truncate" x-text="opt.label"></span>
                        <template x-if="opt.badge">
                            <span
                                class="shrink-0 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide"
                                :class="{
                                    'bg-amber-100 text-amber-700': (opt.badge.variant ?? 'warning') === 'warning',
                                    'bg-emerald-100 text-emerald-700': opt.badge.variant === 'success',
                                    'bg-red-100 text-red-700': opt.badge.variant === 'danger',
                                    'bg-blue-100 text-blue-700': opt.badge.variant === 'info',
                                    'bg-gray-100 text-gray-700': opt.badge.variant === 'neutral',
                                }"
                                x-text="opt.badge.label"
                            ></span>
                        </template>
                        <svg x-show="selected && String(selected.value) === String(opt.value)" class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </li>
                </template>
                <li x-show="filtered.length === 0" class="px-3 py-2 text-sm text-gray-400 text-center">
                    Sin resultados
                </li>
            </ul>
        </div>
    </div>

    @error($name)
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>
