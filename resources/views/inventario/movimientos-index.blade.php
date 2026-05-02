@extends('layouts.app')

@section('title', 'Historial Global de Movimientos')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Historial de Movimientos</h1>
        <p class="text-gray-500 text-sm mt-1">Todos los movimientos de inventario, más recientes primero.</p>
    </div>

    <x-ui.card>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
            <x-ui.input name="desde" label="Desde" type="date" :value="$filters['desde'] ?? ''" />
            <x-ui.input name="hasta" label="Hasta" type="date" :value="$filters['hasta'] ?? ''" />

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Medicamento</label>
                <select name="medication_id" class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500">
                    <option value="">— Todos —</option>
                    @foreach($medicamentos as $m)
                        <option value="{{ $m->id }}" {{ ($filters['medication_id'] ?? null) == $m->id ? 'selected' : '' }}>
                            {{ $m->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Tipo</label>
                <select name="tipos[]" multiple class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 h-20">
                    @foreach($tiposDisponibles as $tipo)
                        <option value="{{ $tipo->value }}"
                            {{ in_array($tipo->value, $filters['tipos'] ?? []) ? 'selected' : '' }}>
                            {{ $tipo->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4 flex justify-end">
                <x-ui.button type="submit">Filtrar</x-ui.button>
                <a href="{{ route('inventory-manager.movimientos.index') }}" class="ml-2 text-xs text-gray-400 hover:text-gray-600 self-center">Limpiar</a>
            </div>
        </form>

        <x-ui.table>
            <x-slot:header>
                <tr>
                    <th class="px-3 py-2 text-left">Fecha</th>
                    <th class="px-3 py-2 text-left">Tipo</th>
                    <th class="px-3 py-2 text-left">Medicamento</th>
                    <th class="px-3 py-2 text-right">Cant.</th>
                    <th class="px-3 py-2 text-right">Antes</th>
                    <th class="px-3 py-2 text-right">Después</th>
                    <th class="px-3 py-2 text-left">Usuario</th>
                    <th class="px-3 py-2 text-left">Referencia</th>
                </tr>
            </x-slot:header>
            @forelse($movimientos as $mov)
                <tr>
                    <td class="px-3 py-2 text-xs text-gray-500">{{ $mov->created_at?->format('d/m/Y H:i') }}</td>
                    <td class="px-3 py-2 text-xs font-bold text-gray-700">{{ $mov->type?->label() ?? $mov->type }}</td>
                    <td class="px-3 py-2 text-xs text-gray-900">
                        <a href="{{ route('inventory-manager.movimientos.show', $mov->medication) }}" class="hover:underline text-indigo-600">
                            {{ $mov->medication?->name ?? '—' }}
                        </a>
                    </td>
                    <td class="px-3 py-2 text-xs text-right font-mono {{ $mov->quantity < 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $mov->quantity > 0 ? '+' : '' }}{{ $mov->quantity }}
                    </td>
                    <td class="px-3 py-2 text-xs text-right text-gray-500">{{ $mov->stock_before }}</td>
                    <td class="px-3 py-2 text-xs text-right text-gray-700 font-bold">{{ $mov->stock_after }}</td>
                    <td class="px-3 py-2 text-xs text-gray-500">{{ $mov->user?->name ?? '—' }}</td>
                    <td class="px-3 py-2 text-xs text-gray-500">
                        @if($mov->sale_id)
                            <a href="{{ route('ventas.show', $mov->sale_id) }}" class="text-indigo-500 hover:underline">Venta #{{ $mov->sale_id }}</a>
                        @elseif($mov->purchase_order_id)
                            <a href="{{ route('inventory-manager.pedidos.show', $mov->purchase_order_id) }}" class="text-indigo-500 hover:underline">Pedido #{{ $mov->purchase_order_id }}</a>
                        @elseif($mov->reason)
                            <span title="{{ $mov->reason }}">{{ Str::limit($mov->reason, 30) }}</span>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-3 py-8 text-center text-xs text-gray-400 font-bold uppercase">
                        No hay movimientos para los filtros seleccionados.
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">{{ $movimientos->links() }}</div>
    </x-ui.card>
@endsection
