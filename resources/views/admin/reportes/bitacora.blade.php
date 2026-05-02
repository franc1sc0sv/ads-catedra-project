@extends('layouts.app')

@section('title', 'Bitácora de auditoría')

@section('content')
    <div class="flex flex-col gap-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bitácora de auditoría</h1>
            <p class="text-gray-500 text-sm mt-1">
                Registro inmutable de acciones críticas. Por defecto se muestran las últimas 24 horas.
            </p>
        </div>

        <x-ui.card>
            <form method="GET" action="{{ route('admin.reportes.bitacora.index') }}"
                  class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <x-ui.select
                    name="user_id"
                    label="Usuario"
                    placeholder="Todos"
                    searchable
                    :value="$filters['user_id'] ?? ''"
                    :options="collect($usuarios)->map(fn($u) => ['value' => $u->id, 'label' => $u->name])->all()"
                />

                <x-ui.input name="action" label="Acción" :value="$filters['action'] ?? ''" placeholder="LOGIN_OK" />
                <x-ui.input name="table_affected" label="Tabla" :value="$filters['table_affected'] ?? ''" placeholder="users" />
                <x-ui.input name="from" type="date" label="Desde" :value="$filters['from'] ?? ''" />
                <x-ui.input name="to" type="date" label="Hasta" :value="$filters['to'] ?? ''" />

                <div class="md:col-span-5 flex gap-2 justify-end">
                    <a href="{{ route('admin.reportes.bitacora.index') }}">
                        <x-ui.button type="button" variant="ghost">Limpiar</x-ui.button>
                    </a>
                    <x-ui.button type="submit" variant="primary">Filtrar</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        @if ($entries->isEmpty())
            <x-ui.card>
                <p class="text-sm text-gray-600">No hay registros en el rango seleccionado.</p>
            </x-ui.card>
        @else
            <x-ui.table>
                <x-slot:header>
                    <tr>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Usuario</th>
                        <th class="px-4 py-3">Acción</th>
                        <th class="px-4 py-3">Tabla</th>
                        <th class="px-4 py-3">Registro</th>
                        <th class="px-4 py-3">IP</th>
                        <th class="px-4 py-3">Detalles</th>
                    </tr>
                </x-slot:header>

                @foreach ($entries as $entry)
                    <tr>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                            {{ $entry->created_at?->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-4 py-3">{{ $entry->user?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <x-ui.badge variant="blue">{{ $entry->action }}</x-ui.badge>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $entry->table_affected ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $entry->record_affected ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $entry->ip_address ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($entry->details)
                                <pre class="text-xs text-gray-700 whitespace-pre-wrap break-all max-w-md">{{ json_encode(json_decode($entry->details, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>

            <div>
                {{ $entries->links() }}
            </div>
        @endif
    </div>
@endsection
