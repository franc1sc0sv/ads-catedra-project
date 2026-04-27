@extends('layouts.app')

@section('title', 'Editar usuario')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Editar usuario</h1>
            <p class="text-gray-500 text-sm mt-1">
                Rol actual: <x-ui.badge variant="blue">{{ $usuario->role->label() }}</x-ui.badge>
            </p>
        </div>

        <x-ui.card>
            <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}" class="flex flex-col gap-5" novalidate>
                @csrf
                @method('PUT')

                <x-ui.input
                    name="name"
                    label="Nombre completo"
                    type="text"
                    :value="$usuario->name"
                    autocomplete="name"
                    :required="true"
                />

                <x-ui.input
                    name="email"
                    label="Correo electrónico"
                    type="email"
                    :value="$usuario->email"
                    autocomplete="email"
                    :required="true"
                />

                <div class="flex flex-col gap-1">
                    <label for="role" class="text-sm font-medium text-gray-700">Rol</label>
                    <select id="role" name="role"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}"
                                @selected(old('role', $usuario->role->value) === $role->value)>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('admin.usuarios.index') }}">
                        <x-ui.button type="button" variant="ghost">Cancelar</x-ui.button>
                    </a>
                    <x-ui.button type="submit" variant="primary">Guardar cambios</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
@endsection
