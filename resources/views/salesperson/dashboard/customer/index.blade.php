@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gestión de Clientes</h1>
        <a href="{{ route('salesperson.clientes.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-indigo-700">
            + Nuevo Cliente
        </a>
    </div>

    <x-ui.card title="Listado de Clientes">
        {{-- Buscador --}}
        <form action="{{ route('salesperson.clientes.index') }}" method="GET" class="mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o DUI..." class="w-full max-w-md rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500">
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3">Nombre</th>
                        <th class="px-6 py-3">Identificación</th>
                        <th class="px-6 py-3">Teléfono</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($customers as $customer)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $customer->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $customer->identification }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $customer->phone ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('salesperson.clientes.edit', $customer->id) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold uppercase">Editar</a>
                            <a href="{{ route('salesperson.clientes.show', $customer->id) }}" class="text-gray-400 hover:text-gray-600 text-xs font-bold uppercase">Historial</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $customers->links() }}</div>
    </x-ui.card>
</div>
@endsection