@extends('layouts.auth')

@section('title', 'Confirmar contraseña')

@section('content')
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Confirma tu contraseña</h2>
    <p class="text-sm text-gray-500 mb-6">Por seguridad, confirma tu contraseña antes de continuar.</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="flex flex-col gap-5" novalidate>
        @csrf

        <x-ui.input
            name="password"
            label="Contraseña"
            type="password"
            autocomplete="current-password"
            :required="true"
        />

        <x-ui.button type="submit" :block="true">Confirmar</x-ui.button>
    </form>
@endsection
