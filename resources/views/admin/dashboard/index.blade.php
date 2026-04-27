@extends('layouts.app')

@section('title', 'Panel del Administrador')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard del rol Administrator</h1>
        <p class="text-gray-500 text-sm mt-1">Gestión global del sistema FarmaSys.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-ui.card title="Usuarios">
            <p class="text-sm text-gray-600 mb-4">Alta, baja y roles del personal.</p>
            <span class="text-xs text-gray-400 disabled">Próximamente (Fase B)</span>
        </x-ui.card>
        <x-ui.card title="Configuración">
            <p class="text-sm text-gray-600 mb-4">Parámetros del sistema y catálogos base.</p>
            <span class="text-xs text-gray-400 disabled">Próximamente (Fase B)</span>
        </x-ui.card>
        <x-ui.card title="Reportes">
            <p class="text-sm text-gray-600 mb-4">Métricas operativas y de ventas.</p>
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
                    <x-ui.badge variant="yellow">{{ auth()->user()->role->label() }}</x-ui.badge>
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Miembro desde</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ auth()->user()->created_at->format('d/m/Y') }}</dd>
            </div>
        </dl>
    </x-ui.card>
@endsection
