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
                <x-ui.select
                    name="medication_id"
                    label="Medicamento"
                    placeholder="— Selecciona —"
                    searchable
                    :required="true"
                    :value="$preselected"
                    :options="collect($medicamentos)->map(fn($m) => ['value' => $m->id, 'label' => $m->name . ' (stock: ' . $m->stock . ')'])->all()"
                />

                <x-ui.select
                    name="type"
                    label="Tipo de ajuste"
                    :required="true"
                    :value="$defaultType"
                    :options="collect($tipos)->map(fn($t) => ['value' => $t->value, 'label' => $t->label()])->all()"
                />
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
