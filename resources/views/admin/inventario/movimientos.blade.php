@extends('layouts.app')

@section('title', 'Historial — '.$medicamento->name)

@section('content')
    <div class="mb-6">
        <a href="{{ url()->previous(route('admin.reportes.movimientos.index')) }}" class="text-sm text-indigo-600 hover:underline mb-2 inline-block">← Volver</a>
        <h1 class="text-2xl font-bold text-gray-900">Historial de movimientos</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $medicamento->name }} · stock actual: {{ $medicamento->stock }}</p>
    </div>

    <x-ui.card>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
            <x-ui.input name="desde" label="Desde" type="date" :value="$filters['desde']" />
            <x-ui.input name="hasta" label="Hasta" type="date" :value="$filters['hasta']" />
            <div class="flex flex-col gap-1 md:col-span-2">
                <label class="text-sm font-medium text-gray-700">Tipos</label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($tiposDisponibles as $t)
                        <label class="flex items-center gap-1 text-sm">
                            <input type="checkbox" name="tipos[]" value="{{ $t->value }}"
                                   @checked(in_array($t->value, (array) $filters['tipos'], true))>
                            {{ $t->label() }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="md:col-span-4 flex justify-end">
                <x-ui.button type="submit">Filtrar</x-ui.button>
            </div>
        </form>

        <x-ui.table>
            <x-slot:header>
                <tr>
                    <th class="px-3 py-2">Fecha</th>
                    <th class="px-3 py-2">Tipo</th>
                    <th class="px-3 py-2">Cantidad</th>
                    <th class="px-3 py-2">Antes → Después</th>
                    <th class="px-3 py-2">Usuario</th>
                    <th class="px-3 py-2">Motivo / Origen</th>
                </tr>
            </x-slot:header>

            @forelse ($movimientos as $mov)
                <tr>
                    <td class="px-3 py-2 text-xs">{{ $mov->created_at?->format('d/m/Y H:i') }}</td>
                    <td class="px-3 py-2">{{ $mov->type->label() }}</td>
                    <td class="px-3 py-2 font-mono">
                        @if ($mov->quantity > 0)
                            <span class="text-green-700">+{{ $mov->quantity }}</span>
                        @else
                            <span class="text-red-700">{{ $mov->quantity }}</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-xs">{{ $mov->stock_before }} → {{ $mov->stock_after }}</td>
                    <td class="px-3 py-2 text-xs">{{ $mov->user?->name ?? '—' }}</td>
                    <td class="px-3 py-2 text-xs">
                        @if ($mov->sale_id && \Illuminate\Support\Facades\Route::has('ventas.show'))
                            <a href="{{ route('ventas.show', $mov->sale_id) }}" class="text-indigo-600 hover:underline">Venta #{{ $mov->sale_id }}</a>
                        @elseif ($mov->sale_id)
                            Venta #{{ $mov->sale_id }}
                        @elseif ($mov->purchase_order_id && \Illuminate\Support\Facades\Route::has('inventory-manager.pedidos.show'))
                            <a href="{{ route('inventory-manager.pedidos.show', $mov->purchase_order_id) }}" class="text-indigo-600 hover:underline">Pedido #{{ $mov->purchase_order_id }}</a>
                        @elseif ($mov->purchase_order_id)
                            Pedido #{{ $mov->purchase_order_id }}
                        @else
                            {{ $mov->reason ?? '—' }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-3 py-6 text-center text-gray-500">No se encontraron movimientos.</td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">
            {{ $movimientos->links() }}
        </div>
    </x-ui.card>
@endsection
