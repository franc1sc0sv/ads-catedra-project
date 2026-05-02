@extends('layouts.app')

@section('title', 'Historial global de movimientos')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Historial global de movimientos</h1>
            <p class="text-gray-500 text-sm mt-1">Todos los movimientos de inventario, ordenados del más reciente al más antiguo.</p>
        </div>
    </div>

    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('inventory-manager.movimientos.global') }}"
              class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

            <x-ui.input name="desde" label="Desde" type="date" :value="$filters['desde']" />
            <x-ui.input name="hasta" label="Hasta" type="date" :value="$filters['hasta']" />

            <x-ui.select
                name="medication_id"
                label="Medicamento"
                placeholder="Todos"
                searchable
                :value="$filters['medication_id'] ?? ''"
                :options="$medicamentos->map(fn($m) => ['value' => $m->id, 'label' => $m->name])->all()"
            />

            <x-ui.select
                name="user_id"
                label="Usuario"
                placeholder="Todos"
                searchable
                :value="$filters['user_id'] ?? ''"
                :options="$usuarios->map(fn($u) => ['value' => $u->id, 'label' => $u->name])->all()"
            />

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-700">Tipos</label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($tiposDisponibles as $t)
                        <label class="flex items-center gap-1 text-xs">
                            <input type="checkbox" name="tipos[]" value="{{ $t->value }}"
                                   @checked(in_array($t->value, (array) $filters['tipos'], true))>
                            {{ $t->label() }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="md:col-span-5 flex gap-2 justify-end">
                <a href="{{ route('inventory-manager.movimientos.global') }}">
                    <x-ui.button type="button" variant="ghost">Limpiar</x-ui.button>
                </a>
                <x-ui.button type="submit" variant="primary">Filtrar</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <x-ui.table>
        <x-slot:header>
            <tr>
                <th class="px-3 py-2 text-left">Fecha</th>
                <th class="px-3 py-2 text-left">Tipo</th>
                <th class="px-3 py-2 text-left">Medicamento</th>
                <th class="px-3 py-2 text-right">Cantidad</th>
                <th class="px-3 py-2 text-right">Antes → Después</th>
                <th class="px-3 py-2 text-left">Usuario</th>
                <th class="px-3 py-2 text-left">Origen / Motivo</th>
            </tr>
        </x-slot:header>

        @forelse ($movimientos as $mov)
            <tr>
                <td class="px-3 py-2 text-xs text-gray-600 whitespace-nowrap">
                    {{ $mov->created_at?->format('d/m/Y H:i') }}
                </td>
                <td class="px-3 py-2">
                    <x-ui.badge variant="blue">{{ $mov->type?->label() }}</x-ui.badge>
                </td>
                <td class="px-3 py-2 font-medium">
                    @if ($mov->medication)
                        <a href="{{ route('inventory-manager.movimientos.show', $mov->medication_id) }}"
                           class="text-indigo-600 hover:underline">
                            {{ $mov->medication->name }}
                        </a>
                    @else
                        —
                    @endif
                </td>
                <td class="px-3 py-2 font-mono text-right">
                    @if ($mov->quantity > 0)
                        <span class="text-green-700">+{{ $mov->quantity }}</span>
                    @else
                        <span class="text-red-700">{{ $mov->quantity }}</span>
                    @endif
                </td>
                <td class="px-3 py-2 text-xs text-right">{{ $mov->stock_before }} → {{ $mov->stock_after }}</td>
                <td class="px-3 py-2 text-xs">{{ $mov->user?->name ?? '—' }}</td>
                <td class="px-3 py-2 text-xs">
                    @if ($mov->sale_id && \Illuminate\Support\Facades\Route::has('ventas.show'))
                        <a href="{{ route('ventas.show', $mov->sale_id) }}"
                           class="text-indigo-600 hover:underline">
                            Venta #{{ $mov->sale_id }}
                        </a>
                    @elseif ($mov->sale_id)
                        Venta #{{ $mov->sale_id }}
                    @elseif ($mov->purchase_order_id && \Illuminate\Support\Facades\Route::has('inventory-manager.pedidos.show'))
                        <a href="{{ route('inventory-manager.pedidos.show', $mov->purchase_order_id) }}"
                           class="text-indigo-600 hover:underline">
                            Pedido #{{ $mov->purchase_order_id }}
                        </a>
                    @elseif ($mov->purchase_order_id)
                        Pedido #{{ $mov->purchase_order_id }}
                    @else
                        {{ $mov->reason ?? '—' }}
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-3 py-8 text-center text-gray-500">No se encontraron movimientos.</td>
            </tr>
        @endforelse
    </x-ui.table>

    <div class="mt-4">
        {{ $movimientos->links() }}
    </div>
@endsection
