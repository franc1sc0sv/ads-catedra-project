@extends('layouts.app')

@section('title', 'Movimientos de Inventario')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Movimientos de Inventario</h1>
        <p class="text-gray-500 text-sm mt-1">Selecciona un medicamento para ver su historial.</p>
    </div>

    <x-ui.card>
        <x-ui.table>
            <x-slot:header>
                <tr>
                    <th class="px-3 py-2">Medicamento</th>
                    <th class="px-3 py-2">Stock</th>
                    <th class="px-3 py-2 text-right">Acción</th>
                </tr>
            </x-slot:header>
            @foreach ($medicamentos as $m)
                <tr>
                    <td class="px-3 py-2">{{ $m->name }}</td>
                    <td class="px-3 py-2">{{ $m->stock }}</td>
                    <td class="px-3 py-2 text-right">
                        <a href="{{ route('inventory-manager.movimientos.show', $m) }}" class="text-sm text-primary hover:underline">
                            Ver historial
                        </a>
                    </td>
                </tr>
            @endforeach
        </x-ui.table>
    </x-ui.card>
@endsection
