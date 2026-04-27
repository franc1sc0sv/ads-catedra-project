@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Usuarios</h1>
                <p class="text-gray-500 text-sm mt-1">Gestión del personal del sistema.</p>
            </div>
            <a href="{{ route('admin.usuarios.create') }}">
                <x-ui.button variant="primary">Nuevo usuario</x-ui.button>
            </a>
        </div>

        @if (session('status'))
            <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
        @endif

        <x-ui.card>
            <form method="GET" action="{{ route('admin.usuarios.index') }}"
                  class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-2">
                    <x-ui.input
                        name="search"
                        label="Buscar"
                        type="text"
                        :value="$filters['search'] ?? ''"
                        placeholder="Nombre o correo"
                    />
                </div>
                <div class="flex flex-col gap-1">
                    <label for="role" class="text-sm font-medium text-gray-700">Rol</label>
                    <select id="role" name="role"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="todos">Todos</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}"
                                @selected(($filters['role'] ?? null) === $role->value)>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <label for="estado" class="text-sm font-medium text-gray-700">Estado</label>
                    <select id="estado" name="estado"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        @php $estado = $filters['estado'] ?? 'todos'; @endphp
                        <option value="todos" @selected($estado === 'todos')>Todos</option>
                        <option value="activos" @selected($estado === 'activos')>Activos</option>
                        <option value="inactivos" @selected($estado === 'inactivos')>Inactivos</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex gap-2 justify-end">
                    <a href="{{ route('admin.usuarios.index') }}">
                        <x-ui.button type="button" variant="ghost">Limpiar</x-ui.button>
                    </a>
                    <x-ui.button type="submit" variant="primary">Filtrar</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        @if ($usuarios->isEmpty())
            <x-ui.card>
                @php $hasFilters = ! empty($filters['search']) || ! empty($filters['role']) || (($filters['estado'] ?? 'todos') !== 'todos'); @endphp
                @if ($hasFilters)
                    <p class="text-sm text-gray-600">
                        No hay usuarios que coincidan con los filtros.
                        <a href="{{ route('admin.usuarios.index') }}" class="text-primary hover:underline">Limpiar filtros</a>.
                    </p>
                @else
                    <p class="text-sm text-gray-600">Aún no hay usuarios registrados.</p>
                @endif
            </x-ui.card>
        @else
            <x-ui.table>
                <x-slot:header>
                    <tr>
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3">Correo</th>
                        <th class="px-4 py-3">Rol</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Último acceso</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </x-slot:header>

                @foreach ($usuarios as $usuario)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $usuario->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $usuario->email }}</td>
                        <td class="px-4 py-3">
                            <x-ui.badge variant="blue">{{ $usuario->role->label() }}</x-ui.badge>
                        </td>
                        <td class="px-4 py-3">
                            @if ($usuario->is_active)
                                <x-ui.badge variant="green">Activo</x-ui.badge>
                            @else
                                <x-ui.badge variant="red">Inactivo</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $usuario->last_login_at?->format('d/m/Y H:i') ?? 'Nunca' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                <a href="{{ route('admin.usuarios.edit', $usuario) }}">
                                    <x-ui.button type="button" variant="ghost" size="sm">Editar</x-ui.button>
                                </a>
                                <a href="{{ route('admin.usuarios.cambiarPassword', $usuario) }}">
                                    <x-ui.button type="button" variant="ghost" size="sm">Cambiar password</x-ui.button>
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.usuarios.toggleActiva', $usuario) }}">
                                    @csrf
                                    @method('PATCH')
                                    @if ($usuario->is_active)
                                        <x-ui.button type="submit" variant="danger" size="sm">Desactivar</x-ui.button>
                                    @else
                                        <x-ui.button type="submit" variant="secondary" size="sm">Activar</x-ui.button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>

            <div>
                {{ $usuarios->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
