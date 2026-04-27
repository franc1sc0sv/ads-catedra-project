@extends('layouts.app')

@section('title', 'Alertas de Stock')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Alertas de Stock</h1>
        <p class="text-gray-500 text-sm mt-1">Bajo mínimo y próximos a vencer.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-ui.card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Bajo mínimo</h3>
                    <x-ui.badge variant="red">{{ $bajoMinimo->count() }}</x-ui.badge>
                </div>
            </x-slot:header>

            @if ($bajoMinimo->isEmpty())
                <p class="text-sm text-gray-500">Sin medicamentos por debajo del mínimo.</p>
            @else
                <x-ui.table>
                    <x-slot:header>
                        <tr>
                            <th class="px-3 py-2">Medicamento</th>
                            <th class="px-3 py-2">Stock / mín.</th>
                            <th class="px-3 py-2 text-right">Acción</th>
                        </tr>
                    </x-slot:header>
                    @foreach ($bajoMinimo as $m)
                        <tr>
                            <td class="px-3 py-2">
                                <a href="{{ route('inventory-manager.catalogo.show', $m) }}" class="text-primary hover:underline">{{ $m->name }}</a>
                                <p class="text-xs text-gray-500">{{ $m->supplier?->company_name }}</p>
                            </td>
                            <td class="px-3 py-2">
                                <span class="font-semibold text-red-600">{{ $m->stock }}</span> / {{ $m->min_stock }}
                            </td>
                            <td class="px-3 py-2 text-right">
                                @if (Route::has('inventory-manager.pedidos.create'))
                                    <a href="{{ route('inventory-manager.pedidos.create', ['medicamento_id' => $m->id]) }}" class="text-sm text-primary hover:underline">Crear pedido</a>
                                @else
                                    <span class="text-xs text-gray-400">Pedidos próximamente</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Próximos a vencer</h3>
                    <x-ui.badge variant="yellow">{{ $proximosVencer->count() }}</x-ui.badge>
                </div>
            </x-slot:header>

            @if ($proximosVencer->isEmpty())
                <p class="text-sm text-gray-500">Sin medicamentos próximos a vencer.</p>
            @else
                <x-ui.table>
                    <x-slot:header>
                        <tr>
                            <th class="px-3 py-2">Medicamento</th>
                            <th class="px-3 py-2">Vence</th>
                            <th class="px-3 py-2">Stock</th>
                            <th class="px-3 py-2 text-right">Acción</th>
                        </tr>
                    </x-slot:header>
                    @foreach ($proximosVencer as $m)
                        <tr>
                            <td class="px-3 py-2">
                                <a href="{{ route('inventory-manager.catalogo.show', $m) }}" class="text-primary hover:underline">{{ $m->name }}</a>
                            </td>
                            <td class="px-3 py-2">{{ optional($m->expires_at)->format('d/m/Y') }}</td>
                            <td class="px-3 py-2">{{ $m->stock }}</td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('inventory-manager.ajustes.create', ['medicamento_id' => $m->id, 'motivo' => 'vencimiento']) }}" class="text-sm text-primary hover:underline">
                                    Registrar baja
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        </x-ui.card>
    </div>
@endsection
