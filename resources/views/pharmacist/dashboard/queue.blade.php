@extends('layouts.app')

@section('title', 'Cola de Recetas')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Cola de Recetas</h1>
        <p class="text-gray-500 text-sm mt-1">Listado de recetas agrupadas por número de receta para validación masiva.</p>
    </div>

    <x-ui.card title="Recetas Pendientes">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="text-xs font-medium text-gray-500 uppercase">
                        <th class="px-4 py-3 text-left">N° Receta</th>
                        <th class="px-4 py-3 text-left">Paciente y Medicamentos</th>
                        <th class="px-4 py-3 text-left">Médico</th>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100 text-sm">
                    {{-- Cambiamos el bucle para manejar la colección agrupada --}}
                    @forelse($prescriptions as $number => $items)
                        @php 
                            // Extraemos el primer registro para obtener datos generales (Paciente, Médico, Fecha)
                            $firstItem = $items->first(); 
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Número de receta --}}
                            <td class="px-4 py-4 font-medium text-indigo-600">
                                #{{ $number }}
                            </td>

                            {{-- Paciente y detalle de TODOS los medicamentos del grupo --}}
                            <td class="px-4 py-4">
                                <div class="text-gray-900 font-bold mb-1">{{ $firstItem->patient_name }}</div>
                                <div class="space-y-1">
                                    @foreach($items as $item)
                                        <div class="text-xs text-gray-600 flex items-center">
                                            <span class="mr-1 text-indigo-400">💊</span>
                                            <span class="font-medium">{{ $item->medication->name ?? 'Desconocido' }}</span>
                                            <span class="ml-1 text-gray-400 text-[10px]">({{ $item->dosage }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            {{-- Nombre del médico --}}
                            <td class="px-4 py-4 text-gray-700">
                                {{ $firstItem->doctor_name }}
                            </td>

                            {{-- Fecha de emisión --}}
                            <td class="px-4 py-4 text-gray-500">
                                {{ $firstItem->issued_at->format('d/m/Y H:i') }}
                            </td>

                            {{-- Formulario para validar la receta completa --}}
                            <td class="px-4 py-4 text-right">
                                <form action="{{ route('pharmacist.validate', $number) }}" method="POST" onsubmit="return confirm('¿Confirmas que deseas validar y despachar todos los medicamentos de la receta #{{ $number }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-all">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Validar Receta
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="text-gray-400 italic">No hay recetas pendientes en este momento.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <script>
        // Recarga automática cada 30 segundos para mantener la cola fresca
        setTimeout(() => { 
            window.location.reload(); 
        }, 30000);
    </script>
@endsection