@extends('layouts.app')

@section('title', 'Catálogo de Medicamentos')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Catálogo de Medicamentos</h1>
            <p class="text-gray-500 text-sm mt-1">Listado maestro del inventario.</p>
        </div>
        <a href="{{ route('inventory-manager.catalogo.create') }}">
            <x-ui.button>Nuevo medicamento</x-ui.button>
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4">
            <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
        </div>
    @endif

    <x-ui.card>
        <form method="GET" action="{{ route('inventory-manager.catalogo.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-6">
            <x-ui.input name="search" label="Buscar" :value="$filters['search']" />
            <div class="flex flex-col gap-1">
                <label for="category" class="text-sm font-medium text-gray-700">Categoría</label>
                <select name="category" id="category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Todas</option>
                    @foreach ($categorias as $cat)
                        <option value="{{ $cat->value }}" @selected($filters['category'] === $cat->value)>{{ $cat->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label for="supplier_id" class="text-sm font-medium text-gray-700">Proveedor</label>
                <select name="supplier_id" id="supplier_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Todos</option>
                    @foreach ($suppliers as $s)
                        <option value="{{ $s->id }}" @selected((int) $filters['supplier_id'] === (int) $s->id)>{{ $s->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label for="is_active" class="text-sm font-medium text-gray-700">Estado</label>
                <select name="is_active" id="is_active" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Todos</option>
                    <option value="1" @selected($filters['is_active'] === true)>Activos</option>
                    <option value="0" @selected($filters['is_active'] === false)>Inactivos</option>
                </select>
            </div>
            <div class="flex items-end">
                <x-ui.button type="submit" :block="true">Filtrar</x-ui.button>
            </div>
        </form>

        <x-ui.table>
            <x-slot:header>
                <tr>
                    <th class="px-4 py-2">Nombre</th>
                    <th class="px-4 py-2">Código</th>
                    <th class="px-4 py-2">Categoría</th>
                    <th class="px-4 py-2">Stock</th>
                    <th class="px-4 py-2">Vence</th>
                    <th class="px-4 py-2">Estado</th>
                    <th class="px-4 py-2 text-right">Acciones</th>
                </tr>
            </x-slot:header>

            @forelse ($medicamentos as $m)
                <tr>
                    <td class="px-4 py-2">
                        <a href="{{ route('inventory-manager.catalogo.show', $m) }}" class="text-primary hover:underline">
                            {{ $m->name }}
                        </a>
                        <p class="text-xs text-gray-500">{{ $m->supplier?->company_name }}</p>
                    </td>
                    <td class="px-4 py-2 font-mono text-xs">{{ $m->barcode }}</td>
                    <td class="px-4 py-2">{{ $m->category->label() }}</td>
                    <td class="px-4 py-2">
                        {{ $m->stock }}
                        @if ($m->isLowStock())
                            <x-ui.badge variant="red">Bajo</x-ui.badge>
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ optional($m->expires_at)->format('d/m/Y') }}</td>
                    <td class="px-4 py-2">
                        @if ($m->is_active)
                            <x-ui.badge variant="green">Activo</x-ui.badge>
                        @else
                            <x-ui.badge variant="gray">Inactivo</x-ui.badge>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('inventory-manager.catalogo.edit', $m) }}" class="text-sm text-primary hover:underline">Editar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">No se encontraron medicamentos.</td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">
            {{ $medicamentos->links() }}
        </div>
    </x-ui.card>
@endsection
