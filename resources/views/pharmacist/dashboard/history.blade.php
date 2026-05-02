@extends('layouts.app')

@section('title', 'Historial de Recetas')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Historial de Recetas</h1>
        <p class="text-gray-500 text-sm mt-1">Registro de todas las recetas validadas y despachadas.</p>
    </div>

    <x-ui.card title="Recetas Despachadas">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="text-xs font-medium text-gray-500 uppercase">
                        <th class="px-4 py-3 text-left">N° Receta</th>
                        <th class="px-4 py-3 text-left">Paciente y Medicamentos</th>
                        <th class="px-4 py-3 text-left">Médico</th>
                        <th class="px-4 py-3 text-left">Validado por</th>
                        <th class="px-4 py-3 text-left">Fecha Validación</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100 text-sm">
                    {{-- Cambiamos el bucle para manejar los grupos --}}
                    @forelse($history as $number => $items)
                        @php 
                            $first = $items->first(); 
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Número de receta (La clave del grupo) --}}
                            <td class="px-4 py-4 font-medium text-gray-900">
                                #{{ $number }}
                            </td>

                            {{-- Detalle del Paciente y lista de medicamentos --}}
                            <td class="px-4 py-4">
                                <div class="text-gray-900 font-medium">{{ $first->patient_name }}</div>
                                <div class="text-xs text-gray-500 mt-1 space-y-0.5">
                                    @foreach($items as $medicine)
                                        <div class="flex items-center">
                                            <span class="mr-1 text-gray-400">check</span>
                                            {{ $medicine->medication->name ?? 'N/A' }}
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            {{-- Médico --}}
                            <td class="px-4 py-4 text-gray-600 italic">
                                {{ $first->doctor_name }}
                            </td>

                            {{-- Farmacéutico que validó --}}
                            <td class="px-4 py-4">
                                <x-ui.badge variant="gray">
                                    {{ $first->pharmacist->name ?? 'Sistema' }}
                                </x-ui.badge>
                            </td>

                            {{-- Fecha --}}
                            <td class="px-4 py-4 text-gray-500">
                                {{ $first->validated_at ? $first->validated_at->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400 italic">
                                No hay registros en el historial.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection