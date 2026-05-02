@extends('layouts.app')

@section('title', 'Panel del Vendedor')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4" x-data="{ open: false, saleId: null, action: '' }">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <x-ui.card title="Nueva venta">
            <p class="text-gray-500 mb-4 text-sm">Iniciar una transacción de mostrador.</p>
            <a href="{{ route('salesperson.ventas.create') }}" class="text-indigo-600 font-bold hover:underline">
                Abrir Punto de Venta (POS) →
            </a>
        </x-ui.card>

        <x-ui.card title="Resumen del día">
            <p class="text-3xl font-bold text-gray-900">${{ number_format($todayTotal, 2) }}</p>
            <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Ventas netas de hoy</p>
        </x-ui.card>
    </div>

    <x-ui.card title="Historial de Ventas Recientes">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Cliente</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Estado</th>
                        <th class="py-3 px-4">Fecha</th>
                        <th class="py-3 px-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sales as $sale)
                        <tr class="{{ $sale->status === \App\Enums\SaleStatus::CANCELLED ? 'opacity-50 bg-gray-50' : '' }}">
                            <td class="py-4 px-4 text-sm">#{{ $sale->id }}</td>
                            <td class="py-4 px-4 text-sm">{{ $sale->customer?->name ?? 'Cliente anónimo' }}</td>
                            <td class="py-4 px-4 text-sm font-bold">${{ number_format($sale->total, 2) }}</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase {{ match($sale->status) {
                                    \App\Enums\SaleStatus::CANCELLED => 'bg-red-100 text-red-700',
                                    \App\Enums\SaleStatus::PENDING   => 'bg-orange-100 text-orange-700',
                                    default                          => 'bg-green-100 text-green-700',
                                } }}">
                                    {{ $sale->status->label() }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500">
                                {{ $sale->created_at->timezone('America/El_Salvador')->format('d/m/Y h:i A') }}
                            </td>
                            <td class="py-4 px-4 text-right">
                                @if($sale->status === \App\Enums\SaleStatus::PENDING)
                                    <button type="button"
                                            @click="saleId = {{ $sale->id }}; action = '{{ route('salesperson.ventas.cancel', $sale->id) }}'; open = true"
                                            class="text-red-500 hover:text-red-700 text-[10px] font-bold uppercase">
                                        Anular
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-gray-400 italic">No hay ventas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $sales->links() }}</div>
    </x-ui.card>

    {{-- Cancel modal --}}
    <div x-show="open"
         x-cloak
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
         @keydown.escape.window="open = false">
        <div @click.outside="open = false" class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 mx-4">
            <h2 class="text-lg font-bold text-gray-900 mb-1">Anular venta</h2>
            <p class="text-sm text-gray-500 mb-4">
                Indica el motivo de anulación de la venta <span x-text="'#' + saleId" class="font-bold text-gray-700"></span>.
                El stock será devuelto y las recetas asociadas serán rechazadas.
            </p>

            <form :action="action" method="POST">
                @csrf
                @method('PATCH')

                <label class="text-xs font-bold text-gray-400 uppercase block mb-1" for="cancellation_reason">Motivo</label>
                <textarea id="cancellation_reason"
                          name="cancellation_reason"
                          required
                          minlength="3"
                          maxlength="255"
                          rows="3"
                          placeholder="Ej. Cliente desistió de la compra"
                          class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500"></textarea>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button"
                            @click="open = false"
                            class="px-4 py-2 text-sm font-bold text-gray-600 hover:bg-gray-100 rounded-lg">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-lg">
                        Confirmar anulación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
