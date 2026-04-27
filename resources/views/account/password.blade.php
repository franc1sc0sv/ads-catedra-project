@extends('layouts.app')

@section('title', 'Cambiar contraseña')

@section('content')
    <div class="max-w-xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Cambiar contraseña</h1>
            <p class="text-gray-500 text-sm mt-1">Actualiza la contraseña de tu cuenta.</p>
        </div>

        @if (session('status'))
            <div class="mb-4">
                <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <x-ui.card>
            <form method="POST" action="{{ route('account.password.update') }}" class="flex flex-col gap-5" novalidate>
                @csrf
                @method('PUT')

                <x-ui.input
                    name="current_password"
                    label="Contraseña actual"
                    type="password"
                    autocomplete="current-password"
                    :required="true"
                />

                <x-ui.input
                    name="password"
                    label="Nueva contraseña"
                    type="password"
                    autocomplete="new-password"
                    :required="true"
                />

                <x-ui.input
                    name="password_confirmation"
                    label="Confirmar nueva contraseña"
                    type="password"
                    autocomplete="new-password"
                    :required="true"
                />

                <div class="flex justify-end pt-2">
                    <x-ui.button type="submit" variant="primary">Actualizar contraseña</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
@endsection
