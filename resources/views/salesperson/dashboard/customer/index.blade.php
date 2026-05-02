@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gestión de Clientes</h1>
        <a href="{{ route('salesperson.clientes.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-indigo-700">
            + Nuevo Cliente
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-500 text-green-700 text-xs font-bold rounded-r-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs font-bold rounded-r-lg">
            {{ session('error') }}
        </div>
    @endif

    <x-ui.card title="Listado de Clientes">
        <form action="{{ route('salesperson.clientes.index') }}" method="GET" class="mb-4 flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Buscar por nombre, DUI, teléfono o correo..." class="flex-1 min-w-48 rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 text-sm">
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" name="incluir_inactivos" value="1" {{ ($filters['incluir_inactivos'] ?? '') === '1' ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-indigo-600">
                Incluir inactivos
            </label>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700">Buscar</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3">Nombre</th>
                        <th class="px-6 py-3">Identificación</th>
                        <th class="px-6 py-3">Teléfono</th>
                        <th class="px-6 py-3">Estado</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($customers as $customer)
                    <tr class="{{ $customer->is_active ? '' : 'bg-gray-50 opacity-70' }}">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $customer->name }}
                            @if($customer->is_frequent)
                                <span
                                    x-data="{ loading: false }"
                                    class="ml-1"
                                >
                                    <button
                                        type="button"
                                        @click="
                                            loading = true;
                                            fetch('{{ route('salesperson.clientes.frecuente', $customer) }}', {
                                                method: 'PATCH',
                                                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}
                                            })
                                            .then(r => r.json())
                                            .then(() => { loading = false; window.location.reload(); })
                                            .catch(() => { loading = false; })
                                        "
                                        :disabled="loading"
                                        title="Quitar frecuente"
                                        class="inline-flex items-center px-1.5 py-0.5 bg-amber-100 text-amber-600 text-[9px] font-black uppercase rounded cursor-pointer hover:bg-amber-200 disabled:opacity-50"
                                    >
                                        ★ Frecuente
                                    </button>
                                </span>
                            @else
                                <span
                                    x-data="{ loading: false }"
                                    class="ml-1"
                                >
                                    <button
                                        type="button"
                                        @click="
                                            loading = true;
                                            fetch('{{ route('salesperson.clientes.frecuente', $customer) }}', {
                                                method: 'PATCH',
                                                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}
                                            })
                                            .then(r => r.json())
                                            .then(() => { loading = false; window.location.reload(); })
                                            .catch(() => { loading = false; })
                                        "
                                        :disabled="loading"
                                        title="Marcar como frecuente"
                                        class="inline-flex items-center px-1.5 py-0.5 bg-gray-100 text-gray-400 text-[9px] font-black uppercase rounded cursor-pointer hover:bg-amber-100 hover:text-amber-500 disabled:opacity-50"
                                    >
                                        ☆
                                    </button>
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $customer->identification }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $customer->phone ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @if($customer->is_active)
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[9px] font-black uppercase rounded">Activo</span>
                            @else
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[9px] font-black uppercase rounded">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            @if($customer->is_active)
                                <a href="{{ route('salesperson.clientes.edit', $customer->id) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold uppercase">Editar</a>
                                <a href="{{ route('salesperson.clientes.show', $customer->id) }}" class="text-gray-400 hover:text-gray-600 text-xs font-bold uppercase">Historial</a>
                            @endif
                            @auth
                                @if(auth()->user()->role->value === 'administrator')
                                    @if(! $customer->is_active)
                                        <form method="POST" action="{{ route('salesperson.clientes.reactivar', $customer->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-bold uppercase">Reactivar</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('salesperson.clientes.destroy', $customer->id) }}" class="inline" onsubmit="return confirm('¿Desactivar o eliminar este cliente?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold uppercase">Eliminar</button>
                                        </form>
                                    @endif
                                @endif
                            @endauth
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-xs text-gray-400 font-bold uppercase">No se encontraron clientes.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $customers->links() }}</div>
    </x-ui.card>
</div>
@endsection
