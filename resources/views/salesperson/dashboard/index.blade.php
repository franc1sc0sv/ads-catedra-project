@extends('layouts.app')

@section('title', 'Panel del Vendedor')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4">
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
                            <td class="py-4 px-4 text-sm">{{ $sale->customer->name }}</td>
                            <td class="py-4 px-4 text-sm font-bold">${{ number_format($sale->total, 2) }}</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase 
                                    {{ $sale->status === \App\Enums\SaleStatus::CANCELLED ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $sale->status->label() }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500">
                                {{ $sale->created_at->timezone('America/El_Salvador')->format('d/m/Y h:i A') }}
                            </td>
                            <td class="py-4 px-4 text-right">
                                @if($sale->status !== \App\Enums\SaleStatus::CANCELLED)
                                    <form action="{{ route('salesperson.ventas.cancel', $sale->id) }}" method="POST" onsubmit="return confirm('¿Anular venta #{{ $sale->id }}?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-[10px] font-bold uppercase">Anular</button>
                                    </form>
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
</div>
@endsection