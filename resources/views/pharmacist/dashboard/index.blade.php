@extends('layouts.app')

@section('title', 'Panel de Farmacia')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard de Farmacia</h1>
        <p class="text-gray-500 text-sm mt-1">Gestión de validación y dispensación de recetas.</p>
    </div>

    {{-- Tarjetas de Acceso Rápido --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        
        {{-- Tarjeta: Cola de recetas con Vista Previa --}}
        <x-ui.card class="relative overflow-hidden border-l-4 border-indigo-500 flex flex-col justify-between">
            <div class="p-1">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest">Pendientes</p>
                        <h3 class="text-lg font-bold text-gray-800">Cola de Recetas</h3>
                    </div>
                    <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>

                {{-- Vista Previa de Recetas Pendientes --}}
                <div class="space-y-2 mb-6">
                    @forelse($prescriptions as $number => $items)
                        @if($loop->iteration <= 3) {{-- Solo mostramos las primeras 3 --}}
                            <div class="flex items-center justify-between bg-gray-50 p-2 rounded-md border border-gray-100">
                                <div class="flex flex-col">
                                    {{-- $number es el 'prescription_number' --}}
                                    <span class="text-[11px] font-bold text-gray-700">#{{ $number }}</span>
                                    {{-- Sacamos el nombre del paciente del primer item del grupo --}}
                                    <span class="text-[10px] text-gray-500 truncate w-32">
                                        {{ $items->first()->patient_name ?? 'Sin nombre' }}
                                    </span>
                                </div>
                                <span class="text-[10px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-medium">
                                    {{ $items->count() }} ítem(s)
                                </span>
                            </div>
                        @endif
                    @empty
                        <div class="py-4 text-center">
                            <p class="text-xs text-gray-400 italic">No hay recetas pendientes de validar.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-auto pt-4 border-t border-gray-50">
                <a href="{{ route('pharmacist.queue') }}" class="inline-flex items-center text-indigo-600 text-sm font-bold hover:translate-x-1 transition-transform">
                    Ir a la cola de recetas
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
        </x-ui.card>

        {{-- Tarjeta: Historial --}}
        <x-ui.card class="relative overflow-hidden border-l-4 border-green-500 flex flex-col justify-between">
            <div class="p-1">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-[10px] font-bold text-green-500 uppercase tracking-widest">Finalizadas</p>
                        <h3 class="text-lg font-bold text-gray-800">Historial</h3>
                    </div>
                    <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mb-5">Consulta las recetas que ya han sido validadas.</p>
            </div>
            <div class="mt-auto pt-4 border-t border-gray-50">
                <a href="{{ route('pharmacist.history') }}" class="inline-flex items-center text-green-600 text-sm font-bold hover:translate-x-1 transition-transform">
                    Ver historial completo
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
        </x-ui.card>

    </div>

    {{-- Datos de la cuenta --}}
    <x-ui.card title="Perfil del Farmacéutico">
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-sm">
            <div>
                <dt class="text-gray-400 font-medium">Nombre</dt>
                <dd class="font-bold text-gray-900 mt-1">{{ auth()->user()->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-400 font-medium">Email</dt>
                <dd class="font-medium text-gray-900 mt-1">{{ auth()->user()->email }}</dd>
            </div>
            <div>
                <dt class="text-gray-400 font-medium">Rol</dt>
                <dd class="mt-1">
                    <x-ui.badge variant="green">
                        {{ auth()->user()->role->label() }}
                    </x-ui.badge>
                </dd>
            </div>
            <div>
                <dt class="text-gray-400 font-medium">Fecha de Registro</dt>
                <dd class="font-medium text-gray-900 mt-1">{{ auth()->user()->created_at->format('d/m/Y') }}</dd>
            </div>
        </dl>
    </x-ui.card>
@endsection