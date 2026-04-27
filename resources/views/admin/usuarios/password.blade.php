@extends('layouts.app')

@section('title', 'Restablecer contraseña')

@section('content')
    <div class="max-w-xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Restablecer contraseña</h1>
            <p class="text-gray-500 text-sm mt-1">
                Usuario: <span class="font-medium text-gray-800">{{ $usuario->name }}</span>
                ({{ $usuario->email }})
            </p>
        </div>

        <x-ui.card>
            <form method="POST"
                  action="{{ route('admin.usuarios.actualizarPassword', $usuario) }}"
                  class="flex flex-col gap-5"
                  novalidate>
                @csrf
                @method('PUT')

                <x-ui.input
                    name="password"
                    label="Nueva contraseña"
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
                    <x-ui.button type="submit" variant="primary">Restablecer</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
@endsection
