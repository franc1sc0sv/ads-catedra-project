@extends('layouts.app')

@section('title', 'Ajuste de Stock')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Ajuste de Stock</h1>
        <p class="text-gray-500 text-sm mt-1">Corrige stock fuera de los flujos de venta y pedido. Cada ajuste queda registrado de forma inmutable.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4">
            <x-ui.alert variant="danger">
                @foreach ($errors->all() as $err)
                    <p>{{ $err }}</p>
                @endforeach
            </x-ui.alert>
        </div>
    @endif

    <x-ui.card>
        <form method="POST" action="{{ route('inventory-manager.ajustes.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <label for="medication_id" class="text-sm font-medium text-gray-700">Medicamento</label>
                    <select name="medication_id" id="medication_id" required
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">— Selecciona —</option>
                        @foreach ($medicamentos as $m)
                            <option value="{{ $m->id }}"
                                @selected((int) old('medication_id', $preselected) === (int) $m->id)>
                                {{ $m->name }} (stock: {{ $m->stock }})
                            </option>
                        @endforeach
                    </select>
                    @error('medication_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-1">
                    <label for="type" class="text-sm font-medium text-gray-700">Tipo de ajuste</label>
                    <select name="type" id="type" required
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo->value }}"
                                @selected(old('type', $defaultType) === $tipo->value)>
                                {{ $tipo->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <x-ui.input
                    name="quantity"
                    label="Cantidad (negativa = baja, positiva = entrada)"
                    type="number"
                    :value="old('quantity')"
                    :required="true"
                />
            </div>

            <div class="mt-4">
                <label for="reason" class="text-sm font-medium text-gray-700">Motivo (mínimo 5 caracteres)</label>
                <textarea
                    name="reason"
                    id="reason"
                    rows="3"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                >{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('inventory-manager.catalogo.index') }}">
                    <x-ui.button type="button" variant="ghost">Cancelar</x-ui.button>
                </a>
                <x-ui.button type="submit">Confirmar ajuste</x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
