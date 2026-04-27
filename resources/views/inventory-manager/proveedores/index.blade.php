@extends('layouts.app')

@section('title', 'Proveedores')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Proveedores</h1>
            <p class="text-gray-500 text-sm mt-1">Catálogo de empresas que abastecen la farmacia.</p>
        </div>
        <a href="{{ route('inventory-manager.proveedores.create') }}">
            <x-ui.button type="button">Nuevo proveedor</x-ui.button>
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
        <form method="GET" action="{{ route('inventory-manager.proveedores.index') }}" class="flex items-end gap-3 mb-4">
            <div class="flex-1">
                <x-ui.input name="search" label="Buscar (empresa o RFC)" :value="$search" />
            </div>
            <x-ui.button type="submit" variant="secondary">Buscar</x-ui.button>
            @if ($search)
                <a href="{{ route('inventory-manager.proveedores.index') }}">
                    <x-ui.button type="button" variant="ghost">Limpiar</x-ui.button>
                </a>
            @endif
        </form>

        <x-ui.table>
            <x-slot:header>
                <tr>
                    <th class="px-4 py-3">Empresa</th>
                    <th class="px-4 py-3">RFC</th>
                    <th class="px-4 py-3">Contacto</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </x-slot:header>

            @forelse ($suppliers as $supplier)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $supplier->company_name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $supplier->tax_id }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        <div>{{ $supplier->email ?? '—' }}</div>
                        <div class="text-xs text-gray-400">{{ $supplier->phone ?? '' }}</div>
                    </td>
                    <td class="px-4 py-3">
                        @if ($supplier->is_active)
                            <x-ui.badge variant="green">Activo</x-ui.badge>
                        @else
                            <x-ui.badge variant="gray">Inactivo</x-ui.badge>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('inventory-manager.proveedores.edit', $supplier) }}">
                                <x-ui.button type="button" variant="ghost" size="sm">Editar</x-ui.button>
                            </a>
                            <form method="POST" action="{{ route('inventory-manager.proveedores.toggle', $supplier) }}">
                                @csrf
                                @method('PATCH')
                                <x-ui.button type="submit" variant="secondary" size="sm">
                                    {{ $supplier->is_active ? 'Desactivar' : 'Activar' }}
                                </x-ui.button>
                            </form>
                            <form method="POST"
                                  action="{{ route('inventory-manager.proveedores.destroy', $supplier) }}"
                                  onsubmit="return confirm('¿Eliminar este proveedor?');">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" variant="danger" size="sm">Eliminar</x-ui.button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="px-4 py-6 text-center text-gray-500" colspan="5">Sin proveedores registrados.</td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">
            {{ $suppliers->links() }}
        </div>
    </x-ui.card>
@endsection
