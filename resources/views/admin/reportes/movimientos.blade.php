@extends('layouts.app')

@section('title', 'Reporte de movimientos')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reporte de movimientos</h1>
                <p class="text-gray-500 text-sm mt-1">
                    Movimientos de inventario. Por defecto se muestran los del día actual.
                </p>
            </div>
            <a href="{{ route('admin.reportes.movimientos.export', request()->query()) }}">
                <x-ui.button variant="secondary">Descargar CSV</x-ui.button>
            </a>
        </div>

        <x-ui.card>
            <form method="GET" action="{{ route('admin.reportes.movimientos.index') }}"
                  class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div class="flex flex-col gap-1">
                    <label for="type" class="text-sm font-medium text-gray-700">Tipo</label>
                    <select id="type" name="type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Todos</option>
                        @foreach ($tipos as $t)
                            <option value="{{ $t->value }}" @selected(($filters['type'] ?? '') === $t->value)>
                                {{ $t->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label for="medication_id" class="text-sm font-medium text-gray-700">Medicamento</label>
                    <select id="medication_id" name="medication_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Todos</option>
                        @foreach ($medicamentos as $m)
                            <option value="{{ $m->id }}" @selected((string) ($filters['medication_id'] ?? '') === (string) $m->id)>
                                {{ $m->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label for="user_id" class="text-sm font-medium text-gray-700">Usuario</label>
                    <select id="user_id" name="user_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Todos</option>
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}" @selected((string) ($filters['user_id'] ?? '') === (string) $u->id)>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-ui.input name="from" type="date" label="Desde" :value="$filters['from'] ?? ''" />
                <x-ui.input name="to" type="date" label="Hasta" :value="$filters['to'] ?? ''" />

                <div class="md:col-span-5 flex gap-2 justify-end">
                    <a href="{{ route('admin.reportes.movimientos.index') }}">
                        <x-ui.button type="button" variant="ghost">Limpiar</x-ui.button>
                    </a>
                    <x-ui.button type="submit" variant="primary">Filtrar</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        @if ($rows->isEmpty())
            <x-ui.card>
                <p class="text-sm text-gray-600">No hay movimientos para los filtros seleccionados.</p>
            </x-ui.card>
        @else
            <x-ui.table>
                <x-slot:header>
                    <tr>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Medicamento</th>
                        <th class="px-4 py-3 text-right">Cantidad</th>
                        <th class="px-4 py-3 text-right">Antes</th>
                        <th class="px-4 py-3 text-right">Después</th>
                        <th class="px-4 py-3">Usuario</th>
                        <th class="px-4 py-3">Origen</th>
                        <th class="px-4 py-3">Motivo</th>
                    </tr>
                </x-slot:header>

                @foreach ($rows as $mov)
                    <tr>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                            {{ $mov->created_at?->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3">
                            <x-ui.badge variant="blue">{{ $mov->type?->label() }}</x-ui.badge>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $mov->medication?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format((int) $mov->quantity) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format((int) $mov->stock_before) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format((int) $mov->stock_after) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $mov->user?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($mov->sale_id)
                                <span class="text-gray-600">Venta #{{ $mov->sale_id }}</span>
                            @elseif ($mov->purchase_order_id && \Illuminate\Support\Facades\Route::has('inventory-manager.pedidos.show'))
                                <a href="{{ route('inventory-manager.pedidos.show', $mov->purchase_order_id) }}"
                                   class="text-primary hover:underline">
                                    Pedido #{{ $mov->purchase_order_id }}
                                </a>
                            @elseif ($mov->purchase_order_id)
                                <span class="text-gray-600">Pedido #{{ $mov->purchase_order_id }}</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $mov->reason ?? '—' }}</td>
                    </tr>
                @endforeach
            </x-ui.table>

            <div>
                {{ $rows->links() }}
            </div>
        @endif
    </div>
@endsection
