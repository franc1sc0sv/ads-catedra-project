@extends('layouts.app')

@section('title', 'Nuevo usuario')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Nuevo usuario</h1>
            <p class="text-gray-500 text-sm mt-1">Crea una cuenta para el personal del sistema.</p>
        </div>

        <x-ui.card>
            <form method="POST" action="{{ route('admin.usuarios.store') }}" class="flex flex-col gap-5" novalidate>
                @csrf

                <x-ui.input
                    name="name"
                    label="Nombre completo"
                    type="text"
                    autocomplete="name"
                    :required="true"
                />

                <x-ui.input
                    name="email"
                    label="Correo electrónico"
                    type="email"
                    autocomplete="email"
                    :required="true"
                />

                <x-ui.select
                    name="role"
                    label="Rol"
                    placeholder="Seleccionar rol"
                    :required="true"
                    :options="collect($roles)->map(fn($r) => ['value' => $r->value, 'label' => $r->label()])->all()"
                />

                <x-ui.input
                    name="password"
                    label="Contraseña inicial"
                    type="password"
                    autocomplete="new-password"
                    :required="true"
                />

                <x-ui.input
                    name="password_confirmation"
                    label="Confirmar contraseña"
                    type="password"
                    autocomplete="new-password"
                    :required="true"
                />

                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('admin.usuarios.index') }}">
                        <x-ui.button type="button" variant="ghost">Cancelar</x-ui.button>
                    </a>
                    <x-ui.button type="submit" variant="primary">Crear usuario</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
@endsection
