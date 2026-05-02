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

                            {{-- Acciones: Validar / Rechazar --}}
                            <td class="px-4 py-4 text-right" x-data="{ open: false }">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Validar --}}
                                    <form id="pharmacist-validate-{{ $number }}" action="{{ route('pharmacist.validate', $number) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <x-ui.confirm
                                            target-form="pharmacist-validate-{{ $number }}"
                                            title="Validar receta"
                                            message="¿Confirmas que deseas validar y despachar todos los medicamentos de la receta #{{ $number }}?"
                                            confirm-label="Validar"
                                            variant="primary"
                                        >
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-bold rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-all">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Validar
                                            </button>
                                        </x-ui.confirm>
                                    </form>

                                    {{-- Rechazar --}}
                                    <button type="button" @click="open = true"
                                        class="inline-flex items-center px-3 py-2 border border-red-200 text-sm font-bold rounded-md text-red-600 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-all">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Rechazar
                                    </button>

                                    {{-- Modal de rechazo --}}
                                    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
                                        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4" @click.outside="open = false">
                                            <h3 class="text-lg font-bold text-gray-900 mb-2">Rechazar receta #{{ $number }}</h3>
                                            <p class="text-sm text-gray-500 mb-4">La venta quedará cancelada y no se descontará inventario.</p>
                                            <form action="{{ route('pharmacist.reject', $number) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1" for="reason-{{ $number }}">
                                                    Motivo del rechazo
                                                </label>
                                                <textarea
                                                    id="reason-{{ $number }}"
                                                    name="reason"
                                                    rows="3"
                                                    required
                                                    minlength="3"
                                                    maxlength="255"
                                                    class="w-full rounded-lg border-gray-300 text-sm mb-4"
                                                    placeholder="Ingrese el motivo del rechazo..."
                                                ></textarea>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="open = false"
                                                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">
                                                        Cancelar
                                                    </button>
                                                    <button type="submit"
                                                        class="px-4 py-2 text-sm font-bold rounded-lg text-white bg-red-600 hover:bg-red-700 transition-colors">
                                                        Confirmar rechazo
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
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