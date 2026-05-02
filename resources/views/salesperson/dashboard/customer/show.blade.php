@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    {{-- Encabezado y Navegación --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('salesperson.clientes.index') }}" 
               class="p-2.5 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-indigo-600 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <nav class="flex mb-1" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-gray-400">
                        <li>Clientes</li>
                        <li><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                        <li class="text-indigo-500">Historial y Perfil</li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{ $customer->name }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('salesperson.clientes.edit', ['cliente' => $customer->id]) }}" 
               class="px-6 py-3 bg-white border border-gray-200 rounded-2xl text-[10px] font-black uppercase tracking-widest text-gray-600 hover:bg-gray-50 transition-all">
                Editar Perfil
            </a>
            <button onclick="window.print()" class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl hover:bg-indigo-100 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
            </button>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs font-bold rounded-r-xl">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Tarjeta de Información --}}
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-8 text-center border-b border-gray-50">
                    <div class="w-20 h-20 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-black">{{ substr($customer->name, 0, 1) }}</span>
                    </div>
                    <h2 class="text-lg font-black text-gray-900">{{ $customer->name }}</h2>
                    <span class="text-[10px] font-black px-3 py-1 rounded-full {{ $customer->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} uppercase tracking-widest">
                        {{ $customer->is_active ? 'Cuenta Activa' : 'Inactivo' }}
                    </span>
                </div>
                <div class="p-8 space-y-6">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">DUI</p>
                        <p class="text-sm font-mono text-gray-900 font-bold">{{ $customer->identification }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Contacto</p>
                        <p class="text-sm text-gray-900 font-bold">{{ $customer->phone ?? 'Sin teléfono' }}</p>
                        <p class="text-xs text-gray-500 font-medium">{{ $customer->email ?? 'Sin correo' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Dirección</p>
                        <p class="text-xs text-gray-600 font-medium leading-relaxed">{{ $customer->address ?? 'No registrada' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Historial de Ventas --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest italic">Historial de Compras</h3>
                        <p class="text-[10px] font-bold text-gray-400 mt-1">Total: {{ $sales->total() }} ventas</p>
                    </div>
                    @if($customer->is_frequent)
                        <span class="px-3 py-1 bg-amber-100 text-amber-600 text-[9px] font-black uppercase rounded-lg">Cliente Frecuente ★</span>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Folio</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Fecha</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Método</th>
                                <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</th>
                                <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($sales as $sale)
                                @php
                                    $isCancelled = $sale->status?->value === 'cancelled';
                                @endphp
                                <tr class="{{ $isCancelled ? 'bg-red-50' : 'hover:bg-gray-50/50' }} transition-colors">
                                    <td class="px-6 py-4 text-xs font-bold text-indigo-600">#{{ $sale->invoice_number ?? $sale->id }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-600 font-medium">{{ $sale->created_at->format('d/m/Y h:i A') }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-600 font-medium">{{ $sale->payment_method?->label() ?? '—' }}</td>
                                    <td class="px-6 py-4 text-right text-xs font-black text-gray-900">${{ number_format((float) $sale->total, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($isCancelled)
                                            <span class="px-2 py-1 bg-red-100 text-red-600 text-[9px] font-black uppercase rounded-md">Cancelada</span>
                                        @elseif($sale->status?->value === 'completed')
                                            <span class="px-2 py-1 bg-green-100 text-green-600 text-[9px] font-black uppercase rounded-md">Completada</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-[9px] font-black uppercase rounded-md">{{ $sale->status?->label() ?? '—' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('salesperson.ventas.show', $sale) }}" class="text-indigo-500 hover:text-indigo-700 text-[10px] font-black uppercase">Ver</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-10 h-10 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                            </svg>
                                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">No hay historial de compras</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($sales->hasPages())
                    <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/30">
                        {{ $sales->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection