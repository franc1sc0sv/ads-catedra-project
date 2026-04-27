@extends('layouts.auth')

@section('title', 'Iniciar sesión')

@section('content')
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Bienvenido de vuelta</h2>
    <p class="text-sm text-gray-500 mb-6">Inicia sesión para continuar.</p>

    @if ($errors->has('email'))
        <div class="mb-5">
            <x-ui.alert variant="danger">
                {{ $errors->first('email') }}
            </x-ui.alert>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5" novalidate>
        @csrf

        <x-ui.input
            name="email"
            label="Correo electrónico"
            type="email"
            autocomplete="email"
            :required="true"
            autofocus
        />

        <x-ui.input
            name="password"
            label="Contraseña"
            type="password"
            autocomplete="current-password"
            :required="true"
        />

        <x-ui.button type="submit" :block="true">Ingresar</x-ui.button>
    </form>
@endsection
