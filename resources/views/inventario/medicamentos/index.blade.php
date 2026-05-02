@extends('layouts.app')

@section('title', 'Catálogo de Medicamentos')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Catálogo de Medicamentos</h1>
            <p class="text-gray-500 text-sm mt-1">Listado maestro del inventario.</p>
        </div>
        @unless(auth()->user()->role === \App\Enums\UserRole::ADMINISTRATOR)
        <a href="{{ route('inventory-manager.catalogo.create') }}">
            <x-ui.button>Nuevo medicamento</x-ui.button>
        </a>
        @endunless
    </div>

    @if (session('status'))
        <div class="mb-4">
            <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
        </div>
    @endif

    <x-ui.card>
        <form method="GET" action="{{ route('inventory-manager.catalogo.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-6">
            <x-ui.input name="search" label="Buscar" :value="$filters['search']" />
            <x-ui.select
                name="category"
                label="Categoría"
                placeholder="Todas"
                :value="$filters['category']"
                :options="collect($categorias)->map(fn($c) => ['value' => $c->value, 'label' => $c->label()])->all()"
            />
            <x-ui.select
                name="supplier_id"
                label="Proveedor"
                placeholder="Todos"
                searchable
                :value="$filters['supplier_id']"
                :options="collect($suppliers)->map(fn($s) => ['value' => $s->id, 'label' => $s->company_name])->all()"
            />
            <x-ui.select
                name="is_active"
                label="Estado"
                placeholder="Todos"
                :value="$filters['is_active'] === true ? '1' : ($filters['is_active'] === false ? '0' : '')"
                :options="[
                    ['value' => '1', 'label' => 'Activos'],
                    ['value' => '0', 'label' => 'Inactivos'],
                ]"
            />
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
                        @unless(auth()->user()->role === \App\Enums\UserRole::ADMINISTRATOR)
                        <a href="{{ route('inventory-manager.catalogo.edit', $m) }}" class="text-sm text-primary hover:underline">Editar</a>
                        @endunless
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
