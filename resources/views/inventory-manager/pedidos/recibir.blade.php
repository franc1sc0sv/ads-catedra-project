@extends('layouts.app')

@section('title', 'Recibir pedido #' . $order->id)

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Recibir pedido #{{ $order->id }}</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $order->supplier->company_name ?? '—' }}</p>
    </div>

    @if ($errors->any())
        <div class="mb-4">
            <x-ui.alert variant="danger" title="Revisa los datos">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        </div>
    @endif

    <x-ui.alert variant="info">
        Captura por línea la cantidad realmente recibida y el precio facturado. La recepción es atómica: o se aplica todo
        (stock, movimientos y cierre del pedido) o nada.
    </x-ui.alert>

    <x-ui.card class="mt-4">
        <form id="recibir-pedido-form" method="POST" action="{{ route('inventory-manager.pedidos.recibir', $order) }}">
            @csrf

            <x-ui.table>
                <x-slot:header>
                    <tr>
                        <th class="px-4 py-3">Medicamento</th>
                        <th class="px-4 py-3 text-right">Solicitado</th>
                        <th class="px-4 py-3 text-right">Precio estimado</th>
                        <th class="px-4 py-3 w-32">Cantidad recibida</th>
                        <th class="px-4 py-3 w-32">Precio real (opcional)</th>
                    </tr>
                </x-slot:header>

                @foreach ($order->items as $item)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $item->medication->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">{{ $item->quantity_requested }}</td>
                        <td class="px-4 py-3 text-right">
                            ${{ number_format((float) ($item->purchase_price ?? 0), 2) }}
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" min="0" step="1" required
                                name="items[{{ $item->id }}][quantity_received]"
                                value="{{ old('items.'.$item->id.'.quantity_received', $item->quantity_requested) }}"
                                class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm" />
                            @error('items.'.$item->id.'.quantity_received')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" min="0" step="0.01"
                                name="items[{{ $item->id }}][unit_price]"
                                value="{{ old('items.'.$item->id.'.unit_price') }}"
                                placeholder="{{ number_format((float) ($item->purchase_price ?? 0), 2) }}"
                                class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm" />
                            @error('items.'.$item->id.'.unit_price')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>

            <div class="flex items-center gap-3 pt-6">
                <x-ui.confirm
                    target-form="recibir-pedido-form"
                    title="Recibir pedido"
                    message="¿Confirmas la recepción del pedido? Esta acción actualizará el stock y no se puede deshacer."
                    confirm-label="Confirmar recepción"
                    variant="primary"
                >
                    <x-ui.button type="button">Confirmar recepción</x-ui.button>
                </x-ui.confirm>
                <a href="{{ route('inventory-manager.pedidos.show', $order) }}">
                    <x-ui.button type="button" variant="ghost">Cancelar</x-ui.button>
                </a>
            </div>
        </form>
    </x-ui.card>
@endsection
