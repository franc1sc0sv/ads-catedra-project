@extends('layouts.app')

@section('title', 'Historial de Clientes')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('salesperson.dashboard') }}" class="p-2 bg-white rounded-lg border border-gray-200 text-gray-400 hover:text-indigo-600 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Historial de Clientes</h1>
        </div>
        @if($customer)
            <a href="{{ route('salesperson.clientes.show') }}" class="text-sm font-bold text-indigo-600 hover:underline">Consultar otro cliente</a>
        @endif
    </div>

    @if(!$customer)
        {{-- Buscador de Clientes --}}
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <form action="{{ route('salesperson.clientes.show') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre o identificación..." class="w-full pl-4 pr-12 py-3 rounded-xl border-gray-200 focus:ring-indigo-500">
                    <button type="submit" class="absolute right-3 top-2.5 text-gray-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"></path></svg>
                    </button>
                </form>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-100">
                @forelse($customers as $c)
                    <a href="{{ route('salesperson.clientes.show', $c->id) }}" class="flex items-center justify-between p-4 hover:bg-gray-50 group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center font-bold">{{ substr($c->name, 0, 1) }}</div>
                            <div>
                                <p class="font-bold text-gray-900">{{ $c->name }}</p>
                                <p class="text-xs text-gray-500">ID: {{ $c->identification ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-300 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2"></path></svg>
                    </a>
                @empty
                    <div class="p-10 text-center text-gray-400 italic">No se encontraron clientes.</div>
                @endforelse
            </div>
        </div>
    @else
        {{-- Ficha del Cliente Seleccionado --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4 sticky top-6">
                    <div class="text-center border-b pb-6">
                        <div class="w-20 h-20 bg-indigo-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-4">{{ substr($customer->name, 0, 1) }}</div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $customer->name }}</h2>
                        <span class="text-[10px] font-bold text-indigo-500 bg-indigo-50 px-2 py-1 rounded-full uppercase tracking-widest">Cliente Registrado</span>
                    </div>
                    <div class="text-sm space-y-3">
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">Identificación</span>
                            <p class="font-medium text-gray-900">{{ $customer->identification }}</p>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">Teléfono</span>
                            <p class="font-medium text-gray-900">{{ $customer->phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase">Dirección</span>
                            <p class="text-xs text-gray-600 italic leading-relaxed">{{ $customer->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-4">Venta</th>
                                <th class="px-6 py-4">Fecha</th>
                                <th class="px-6 py-4">Total</th>
                                <th class="px-6 py-4 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($customer->sales as $sale)
                                <tr class="{{ $sale->status === \App\Enums\SaleStatus::CANCELLED ? 'opacity-50' : '' }}">
                                    <td class="px-6 py-4 text-sm font-bold">#{{ $sale->id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $sale->created_at->timezone('America/El_Salvador')->format('d/m/Y h:i A') }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-green-600">${{ number_format($sale->total, 2) }}</td>
                                    <td class="px-6 py-4 text-right">
                                        @if($sale->status !== \App\Enums\SaleStatus::CANCELLED)
                                            <form action="{{ route('salesperson.ventas.cancel', $sale->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-[10px] font-bold uppercase">Anular</button>
                                            </form>
                                        @else
                                            <span class="text-red-600 text-[10px] font-bold uppercase italic font-medium">Anulada</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">Este cliente no tiene historial de compras.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection