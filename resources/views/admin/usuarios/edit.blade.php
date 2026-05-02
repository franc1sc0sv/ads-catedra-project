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

                <x-ui.select
                    name="role"
                    label="Rol"
                    :value="$usuario->role->value"
                    :required="true"
                    :options="collect($roles)->map(fn($r) => ['value' => $r->value, 'label' => $r->label()])->all()"
                />

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
