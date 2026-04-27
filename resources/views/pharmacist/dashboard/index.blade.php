@extends('layouts.app')

@section('title', 'Panel de Farmacia')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard del rol Pharmacist</h1>
        <p class="text-gray-500 text-sm mt-1">Validación y dispensación de recetas.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <x-ui.card title="Cola de recetas">
            <p class="text-sm text-gray-600 mb-4">Recetas pendientes de validar y dispensar.</p>
            <span class="text-xs text-gray-400 disabled">Próximamente (Fase B)</span>
        </x-ui.card>
        <x-ui.card title="Historial">
            <p class="text-sm text-gray-600 mb-4">Recetas dispensadas previamente.</p>
            <span class="text-xs text-gray-400 disabled">Próximamente (Fase B)</span>
        </x-ui.card>
    </div>

    <x-ui.card title="Tu cuenta">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-400">Nombre</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ auth()->user()->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Correo</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ auth()->user()->email }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Rol</dt>
                <dd class="mt-0.5">
                    <x-ui.badge variant="green">{{ auth()->user()->role->label() }}</x-ui.badge>
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Miembro desde</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ auth()->user()->created_at->format('d/m/Y') }}</dd>
            </div>
        </dl>
    </x-ui.card>
@endsection
