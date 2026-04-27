@extends('layouts.app')

@section('title', 'Nuevo medicamento')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Nuevo medicamento</h1>
        <p class="text-gray-500 text-sm mt-1">Agrega un medicamento al catálogo.</p>
    </div>

    <x-ui.card>
        <form method="POST" action="{{ route('inventory-manager.catalogo.store') }}">
            @csrf
            @include('inventario.medicamentos._form', [
                'medicamento' => null,
                'suppliers' => $suppliers,
                'categorias' => $categorias,
                'mode' => 'create',
            ])

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('inventory-manager.catalogo.index') }}">
                    <x-ui.button type="button" variant="ghost">Cancelar</x-ui.button>
                </a>
                <x-ui.button type="submit">Guardar</x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
