@extends('layouts.app')

@section('title', 'Configuración')

@section('content')
    <div class="flex flex-col gap-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Configuración del Sistema</h1>
            <p class="text-gray-500 text-sm mt-1">
                Ajusta parámetros operativos del sistema. Los cambios se aplican en el siguiente request.
            </p>
        </div>

        @if (session('success'))
            <x-ui.alert variant="success">{{ session('success') }}</x-ui.alert>
        @endif

        @if (session('error'))
            <x-ui.alert variant="danger">{{ session('error') }}</x-ui.alert>
        @endif

        @if ($configs->isEmpty())
            <x-ui.card>
                <p class="text-sm text-gray-600">No hay parámetros de configuración registrados.</p>
            </x-ui.card>
        @else
            <x-ui.table>
                <x-slot:header>
                    <tr>
                        <th class="px-4 py-3">Clave</th>
                        <th class="px-4 py-3">Descripción</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Valor</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </x-slot:header>

                @foreach ($configs as $config)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs text-gray-900">{{ $config->key }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $config->description }}</td>
                        <td class="px-4 py-3">
                            <x-ui.badge variant="blue">{{ $config->data_type->label() }}</x-ui.badge>
                        </td>

                        @if ($config->editable)
                            @php $formId = 'config-form-'.$config->id; @endphp
                            <td class="px-4 py-3">
                                <form
                                    id="{{ $formId }}"
                                    method="POST"
                                    action="{{ route('admin.configuracion.update', $config->key) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    @switch($config->data_type)
                                        @case(\App\Enums\SettingType::INTEGER)
                                            <input
                                                type="number"
                                                step="1"
                                                name="value"
                                                value="{{ old('value', $config->value) }}"
                                                required
                                                class="w-full md:w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
                                            />
                                            @break

                                        @case(\App\Enums\SettingType::DECIMAL)
                                            <input
                                                type="number"
                                                step="0.01"
                                                name="value"
                                                value="{{ old('value', $config->value) }}"
                                                required
                                                class="w-full md:w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
                                            />
                                            @break

                                        @case(\App\Enums\SettingType::BOOLEAN)
                                            <div class="md:w-32">
                                                <x-ui.select
                                                    name="value"
                                                    :value="(bool) $config->typedValue() ? '1' : '0'"
                                                    :options="[
                                                        ['value' => '1', 'label' => 'Sí'],
                                                        ['value' => '0', 'label' => 'No'],
                                                    ]"
                                                />
                                            </div>
                                            @break

                                        @default
                                            <input
                                                type="text"
                                                name="value"
                                                value="{{ old('value', $config->value) }}"
                                                maxlength="255"
                                                required
                                                class="w-full md:w-64 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
                                            />
                                    @endswitch
                                </form>

                                @error('value')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    <x-ui.button type="submit" variant="primary" size="sm" form="{{ $formId }}">Guardar</x-ui.button>
                                </div>
                            </td>
                        @else
                            <td class="px-4 py-3 text-gray-800">
                                {{ $config->value }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    <x-ui.badge variant="gray">Solo lectura</x-ui.badge>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </x-ui.table>
        @endif
    </div>
@endsection
