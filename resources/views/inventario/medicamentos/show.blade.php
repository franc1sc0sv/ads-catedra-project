@extends('layouts.app')

@section('title', $medicamento->name)

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $medicamento->name }}</h1>
            <p class="text-gray-500 text-sm mt-1 font-mono">{{ $medicamento->barcode }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('inventory-manager.movimientos.show', $medicamento) }}">
                <x-ui.button variant="ghost">Movimientos</x-ui.button>
            </a>
            <a href="{{ route('inventory-manager.catalogo.edit', $medicamento) }}">
                <x-ui.button variant="secondary">Editar</x-ui.button>
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4">
            <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4">
            <x-ui.alert variant="danger">
                @foreach ($errors->all() as $err)
                    <p>{{ $err }}</p>
                @endforeach
            </x-ui.alert>
        </div>
    @endif

    @if ($estaVencido)
        <div class="mb-4">
            <x-ui.alert variant="warning" title="Medicamento vencido">
                Este medicamento está vencido y será rechazado al intentar agregarlo a una venta.
            </x-ui.alert>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-ui.card title="Detalle">
            <dl class="text-sm space-y-3">
                <div>
                    <dt class="text-gray-400">Categoría</dt>
                    <dd class="font-medium">{{ $medicamento->category->label() }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">Proveedor</dt>
                    <dd class="font-medium">{{ $medicamento->supplier?->company_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">Precio</dt>
                    <dd class="font-medium">${{ number_format((float) $medicamento->price, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">Vencimiento</dt>
                    <dd class="font-medium">{{ optional($medicamento->expires_at)->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">Estado</dt>
                    <dd>
                        @if ($medicamento->is_active)
                            <x-ui.badge variant="green">Activo</x-ui.badge>
                        @else
                            <x-ui.badge variant="gray">Inactivo</x-ui.badge>
                        @endif
                    </dd>
                </div>
            </dl>
        </x-ui.card>

        <x-ui.card title="Stock">
            <dl class="text-sm space-y-3">
                <div>
                    <dt class="text-gray-400">Stock actual</dt>
                    <dd class="font-bold text-2xl">{{ $medicamento->stock }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">Stock mínimo</dt>
                    <dd class="font-medium">{{ $medicamento->min_stock }}</dd>
                </div>
                @if ($medicamento->isLowStock())
                    <x-ui.badge variant="red">Bajo el mínimo</x-ui.badge>
                @endif
            </dl>
            <div class="mt-4">
                <a href="{{ route('inventory-manager.ajustes.create', ['medicamento_id' => $medicamento->id]) }}">
                    <x-ui.button variant="secondary" size="sm">Ajustar stock</x-ui.button>
                </a>
            </div>
        </x-ui.card>

        <x-ui.card title="Acciones">
            <p class="text-sm text-gray-500 mb-3">{{ $medicamento->description ?? '—' }}</p>
            @if ($medicamento->is_active)
                <form method="POST" action="{{ route('inventory-manager.catalogo.destroy', $medicamento) }}"
                      onsubmit="return confirm('¿Desactivar este medicamento?');">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" variant="danger" size="sm">Desactivar</x-ui.button>
                </form>
            @else
                <form method="POST" action="{{ route('inventory-manager.catalogo.restore', $medicamento) }}">
                    @csrf
                    <x-ui.button type="submit" variant="secondary" size="sm">Reactivar</x-ui.button>
                </form>
            @endif
        </x-ui.card>
    </div>
@endsection
