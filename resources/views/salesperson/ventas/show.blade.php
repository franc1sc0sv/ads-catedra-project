@extends('layouts.app')

@section('title', 'Detalle de Venta #' . ($sale->invoice_number ?? $sale->id))

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ url()->previous() }}"
           class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-indigo-600 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Detalle de Venta</p>
            <h1 class="text-2xl font-black text-gray-900">#{{ $sale->invoice_number ?? $sale->id }}</h1>
        </div>
    </div>

    @php
        $isCancelled = $sale->status?->value === 'cancelled';
    @endphp

    @if($isCancelled)
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-xl">
            <p class="text-xs font-black text-red-700 uppercase tracking-widest">Venta Cancelada</p>
            @if($sale->cancellation_reason)
                <p class="text-xs text-red-600 mt-1">Motivo: {{ $sale->cancellation_reason }}</p>
            @endif
        </div>
    @endif

    {{-- Metadata --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 mb-6 grid grid-cols-2 md:grid-cols-4 gap-6">
        <div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Fecha</p>
            <p class="text-sm font-bold text-gray-900">{{ ($sale->sold_at ?? $sale->created_at)->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Estado</p>
            <span class="px-2 py-0.5 text-[9px] font-black uppercase rounded
                {{ $isCancelled ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                {{ $sale->status?->label() ?? '—' }}
            </span>
        </div>
        <div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Método de Pago</p>
            <p class="text-sm font-bold text-gray-900">{{ $sale->payment_method?->label() ?? '—' }}</p>
        </div>
        <div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Vendedor</p>
            <p class="text-sm font-bold text-gray-900">{{ $sale->salesperson?->name ?? '—' }}</p>
        </div>
        @if($sale->customer)
        <div class="col-span-2">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Cliente</p>
            <p class="text-sm font-bold text-gray-900">{{ $sale->customer->name }}</p>
            <p class="text-xs text-gray-500">{{ $sale->customer->identification }}</p>
        </div>
        @endif
    </div>

    {{-- Line items --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-50">
            <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest">Productos</h2>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50/50">
                <tr>
                    <th class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Medicamento</th>
                    <th class="px-6 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Cant.</th>
                    <th class="px-6 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">P. Unit.</th>
                    <th class="px-6 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($sale->items as $item)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $item->medication?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-right text-gray-600">{{ $item->quantity }}</td>
                    <td class="px-6 py-4 text-sm text-right text-gray-600">${{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">${{ number_format((float) $item->line_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Totals --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <div class="flex flex-col items-end gap-1 text-sm">
            @if($sale->subtotal !== null && $sale->subtotal != $sale->total)
            <div class="flex justify-between w-48">
                <span class="text-gray-500">Subtotal</span>
                <span class="font-bold">${{ number_format((float) $sale->subtotal, 2) }}</span>
            </div>
            @if($sale->tax)
            <div class="flex justify-between w-48">
                <span class="text-gray-500">IVA</span>
                <span class="font-bold">${{ number_format((float) $sale->tax, 2) }}</span>
            </div>
            @endif
            @endif
            <div class="flex justify-between w-48 pt-2 border-t border-gray-100">
                <span class="font-black text-gray-900 uppercase tracking-widest text-xs">Total</span>
                <span class="font-black text-gray-900 text-base">${{ number_format((float) $sale->total, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
