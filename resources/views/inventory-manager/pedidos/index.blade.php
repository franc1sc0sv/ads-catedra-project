@extends('layouts.app')

@section('title', 'Pedidos a proveedores')

@php
    $statusBadge = function ($status) {
        return match ($status->value) {
            'requested' => 'yellow',
            'shipped'   => 'blue',
            'received'  => 'green',
            'cancelled' => 'red',
            default     => 'gray',
        };
    };
@endphp

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pedidos a proveedores</h1>
            <p class="text-gray-500 text-sm mt-1">Histórico de órdenes de compra.</p>
        </div>
        <a href="{{ route('inventory-manager.pedidos.create') }}">
            <x-ui.button type="button">Nuevo pedido</x-ui.button>
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4">
            <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4">
            <x-ui.alert variant="danger">{{ session('error') }}</x-ui.alert>
        </div>
    @endif

    <x-ui.card>
        <form method="GET" action="{{ route('inventory-manager.pedidos.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-6">
            <x-ui.select
                name="status"
                label="Estado"
                placeholder="Todos"
                :value="$filters['status'] ?? ''"
                :options="collect($statuses)->map(fn($st) => ['value' => $st->value, 'label' => $st->label()])->all()"
            />

            <x-ui.select
                name="supplier_id"
                label="Proveedor"
                placeholder="Todos"
                searchable
                :value="$filters['supplier_id'] ?? ''"
                :options="collect($suppliers)->map(fn($s) => ['value' => $s->id, 'label' => $s->company_name])->all()"
            />

            <x-ui.input name="from" type="date" label="Desde" :value="$filters['from'] ?? null" />
            <x-ui.input name="to" type="date" label="Hasta" :value="$filters['to'] ?? null" />

            <div class="flex items-end gap-2">
                <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
                <a href="{{ route('inventory-manager.pedidos.index') }}">
                    <x-ui.button type="button" variant="ghost">Limpiar</x-ui.button>
                </a>
            </div>
        </form>

        <x-ui.table>
            <x-slot:header>
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Proveedor</th>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3 text-right">Total estimado</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3">Solicitado por</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </x-slot:header>

            @forelse ($orders as $order)
                <tr>
                    <td class="px-4 py-3 font-medium">#{{ $order->id }}</td>
                    <td class="px-4 py-3">{{ $order->supplier->company_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ optional($order->ordered_at)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-right">${{ number_format((float) $order->total_estimated, 2) }}</td>
                    <td class="px-4 py-3">
                        <x-ui.badge :variant="$statusBadge($order->status)">{{ $order->status->label() }}</x-ui.badge>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $order->requestedBy->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('inventory-manager.pedidos.show', $order) }}">
                            <x-ui.button type="button" variant="ghost" size="sm">Ver</x-ui.button>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="px-4 py-6 text-center text-gray-500" colspan="7">Sin pedidos registrados.</td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </x-ui.card>
@endsection
