@extends('layouts.app')

@section('title', 'Editar medicamento')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Editar: {{ $medicamento->name }}</h1>
        <p class="text-gray-500 text-sm mt-1">El stock no se modifica desde aquí — usa Ajuste de stock.</p>
    </div>

    <x-ui.card>
        <form method="POST" action="{{ route('inventory-manager.catalogo.update', $medicamento) }}">
            @csrf
            @method('PUT')
            @include('inventario.medicamentos._form', [
                'medicamento' => $medicamento,
                'suppliers' => $suppliers,
                'categorias' => $categorias,
                'mode' => 'edit',
            ])

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('inventory-manager.catalogo.show', $medicamento) }}">
                    <x-ui.button type="button" variant="ghost">Cancelar</x-ui.button>
                </a>
                <x-ui.button type="submit">Guardar cambios</x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
