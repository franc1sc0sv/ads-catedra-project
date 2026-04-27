@extends('layouts.app')

@section('title', 'Pedido #' . $order->id)

@php
    $statusBadge = match ($order->status->value) {
        'requested' => 'yellow',
        'shipped'   => 'blue',
        'received'  => 'green',
        'cancelled' => 'red',
        default     => 'gray',
    };
    $isRequested = $order->status === \App\Enums\PurchaseOrderStatus::REQUESTED;
    $isShipped = $order->status === \App\Enums\PurchaseOrderStatus::SHIPPED;
    $isReceived = $order->status === \App\Enums\PurchaseOrderStatus::RECEIVED;
@endphp

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pedido #{{ $order->id }}</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $order->supplier->company_name ?? '—' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <x-ui.badge :variant="$statusBadge">{{ $order->status->label() }}</x-ui.badge>
        </div>
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <x-ui.card title="Detalles">
            <dl class="space-y-2 text-sm">
                <div>
                    <dt class="text-gray-400">Fecha de creación</dt>
                    <dd class="text-gray-900">{{ optional($order->ordered_at)->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">Solicitado por</dt>
                    <dd class="text-gray-900">{{ $order->requestedBy->name ?? '—' }}</dd>
                </div>
                @if ($order->expected_at)
                    <div>
                        <dt class="text-gray-400">Fecha esperada</dt>
                        <dd class="text-gray-900">{{ $order->expected_at->format('d/m/Y') }}</dd>
                    </div>
                @endif
                @if ($order->received_at)
                    <div>
                        <dt class="text-gray-400">Recibido el</dt>
                        <dd class="text-gray-900">{{ $order->received_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Recibido por</dt>
                        <dd class="text-gray-900">{{ $order->receivedBy->name ?? '—' }}</dd>
                    </div>
                @endif
                @if ($order->cancellation_reason)
                    <div>
                        <dt class="text-gray-400">Motivo de cancelación</dt>
                        <dd class="text-gray-900">{{ $order->cancellation_reason }}</dd>
                    </div>
                @endif
                @if ($order->notes)
                    <div>
                        <dt class="text-gray-400">Observaciones</dt>
                        <dd class="text-gray-900">{{ $order->notes }}</dd>
                    </div>
                @endif
            </dl>
        </x-ui.card>

        <x-ui.card title="Acciones">
            <div class="space-y-2">
                @if ($isRequested)
                    <form method="POST" action="{{ route('inventory-manager.pedidos.send', $order) }}">
                        @csrf
                        @method('PATCH')
                        <x-ui.button type="submit" variant="secondary" block>
                            Marcar como Enviado
                        </x-ui.button>
                    </form>

                    <button type="button"
                        x-on:click="$dispatch('open-modal', 'cancel-order')"
                        class="w-full bg-coral hover:bg-coral/90 text-white py-2.5 px-4 text-sm font-medium rounded-lg transition-colors">
                        Cancelar pedido
                    </button>
                @endif

                @if ($isRequested || $isShipped)
                    <a href="{{ route('inventory-manager.pedidos.recibir.form', $order) }}" class="block">
                        <x-ui.button type="button" block>Recibir pedido</x-ui.button>
                    </a>
                @endif

                @if ($isReceived || (! $isRequested && ! $isShipped))
                    <p class="text-sm text-gray-500">
                        El pedido está en estado <strong>{{ $order->status->label() }}</strong> y no admite más acciones.
                    </p>
                @endif
            </div>
        </x-ui.card>

        <x-ui.card title="Totales">
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-400">Total estimado</dt>
                    <dd class="font-semibold">${{ number_format((float) $order->total_estimated, 2) }}</dd>
                </div>
                @if ($isReceived)
                    @php
                        $totalReal = $order->items->sum(fn ($it) => ((int) $it->quantity_received) * ((float) ($it->purchase_price ?? 0)));
                    @endphp
                    <div class="flex justify-between">
                        <dt class="text-gray-400">Total real</dt>
                        <dd class="font-semibold">${{ number_format($totalReal, 2) }}</dd>
                    </div>
                @endif
            </dl>
        </x-ui.card>
    </div>

    <x-ui.card title="Líneas">
        <x-ui.table>
            <x-slot:header>
                <tr>
                    <th class="px-4 py-3">Medicamento</th>
                    <th class="px-4 py-3 text-right">Solicitado</th>
                    @if ($isReceived)
                        <th class="px-4 py-3 text-right">Recibido</th>
                    @endif
                    <th class="px-4 py-3 text-right">Precio unit.</th>
                    <th class="px-4 py-3 text-right">Subtotal</th>
                </tr>
            </x-slot:header>

            @foreach ($order->items as $item)
                @php
                    $price = (float) ($item->purchase_price ?? 0);
                    $qtyForSubtotal = $isReceived ? (int) $item->quantity_received : (int) $item->quantity_requested;
                @endphp
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $item->medication->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">{{ $item->quantity_requested }}</td>
                    @if ($isReceived)
                        <td class="px-4 py-3 text-right">{{ $item->quantity_received }}</td>
                    @endif
                    <td class="px-4 py-3 text-right">${{ number_format($price, 2) }}</td>
                    <td class="px-4 py-3 text-right">${{ number_format($qtyForSubtotal * $price, 2) }}</td>
                </tr>
            @endforeach
        </x-ui.table>
    </x-ui.card>

    @if ($isRequested)
        <x-ui.modal name="cancel-order" title="Cancelar pedido" maxWidth="lg">
            <form method="POST" action="{{ route('inventory-manager.pedidos.cancel', $order) }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <p class="text-sm text-gray-600">Indica el motivo de la cancelación. Esta acción no se puede deshacer.</p>

                <div class="flex flex-col gap-1">
                    <label for="reason" class="text-sm font-medium text-gray-700">Motivo</label>
                    <textarea id="reason" name="reason" rows="3" required minlength="5" maxlength="255"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">{{ old('reason') }}</textarea>
                    @error('reason')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" x-on:click="$dispatch('close-modal', 'cancel-order')"
                        class="bg-transparent hover:bg-gray-100 text-gray-700 py-2.5 px-4 text-sm font-medium rounded-lg">
                        Volver
                    </button>
                    <x-ui.button type="submit" variant="danger">Cancelar pedido</x-ui.button>
                </div>
            </form>
        </x-ui.modal>
    @endif
@endsection
