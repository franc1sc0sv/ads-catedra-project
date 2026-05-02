@extends('layouts.app')

@section('title', 'Venta #'.$sale->id)

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Venta #{{ $sale->id }}</h1>
            <p class="text-gray-500 text-sm mt-1">
                {{ $sale->sold_at->format('d/m/Y H:i') }}
                · {{ $sale->salesperson?->name ?? '—' }}
            </p>
        </div>
        <a href="javascript:history.back()">
            <x-ui.button variant="ghost">Volver</x-ui.button>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Detalle de ítems --}}
        <div class="lg:col-span-2">
            <x-ui.card title="Productos vendidos">
                <x-ui.table>
                    <x-slot:header>
                        <tr>
                            <th class="px-4 py-3 text-left">Medicamento</th>
                            <th class="px-4 py-3 text-right">Cant.</th>
                            <th class="px-4 py-3 text-right">Precio unit.</th>
                            <th class="px-4 py-3 text-right">Subtotal</th>
                        </tr>
                    </x-slot:header>
                    @forelse ($sale->items as $item)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $item->medication?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right font-mono">${{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold">${{ number_format((float) $item->line_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">Sin ítems.</td>
                        </tr>
                    @endforelse
                </x-ui.table>
            </x-ui.card>
        </div>

        {{-- Resumen --}}
        <div class="space-y-4">
            <x-ui.card title="Resumen">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Estado</dt>
                        <dd>
                            @php
                                $color = match ($sale->status) {
                                    \App\Enums\SaleStatus::COMPLETED => 'green',
                                    \App\Enums\SaleStatus::PENDING   => 'yellow',
                                    \App\Enums\SaleStatus::CANCELLED => 'red',
                                };
                            @endphp
                            <x-ui.badge :variant="$color">{{ $sale->status->label() }}</x-ui.badge>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Cliente</dt>
                        <dd class="font-medium">{{ $sale->customer?->name ?? 'Anónimo' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Vendedor</dt>
                        <dd class="font-medium">{{ $sale->salesperson?->name ?? '—' }}</dd>
                    </div>
                    <div class="border-t pt-3 flex justify-between">
                        <dt class="text-gray-500">Subtotal</dt>
                        <dd class="font-mono">${{ number_format((float) $sale->subtotal, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">IVA</dt>
                        <dd class="font-mono">${{ number_format((float) $sale->tax, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-base font-bold">
                        <dt>Total</dt>
                        <dd class="font-mono">${{ number_format((float) $sale->total, 2) }}</dd>
                    </div>
                </dl>
            </x-ui.card>

            @if ($sale->cancellation_reason)
                <x-ui.card title="Motivo de anulación">
                    <p class="text-sm text-red-700">{{ $sale->cancellation_reason }}</p>
                </x-ui.card>
            @endif
        </div>
    </div>
@endsection
