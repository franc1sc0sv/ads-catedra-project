@extends('layouts.app')

@section('title', 'Editar proveedor')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Editar proveedor</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $supplier->company_name }}</p>
    </div>

    <x-ui.card>
        <form method="POST" action="{{ route('inventory-manager.proveedores.update', $supplier) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <x-ui.input name="company_name" label="Razón social" :value="$supplier->company_name" required />

            <div class="flex flex-col gap-1">
                <label for="tax_id_readonly" class="text-sm font-medium text-gray-700">Identificador fiscal (RFC)</label>
                <input id="tax_id_readonly" type="text" value="{{ $supplier->tax_id }}" readonly
                       class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500" />
                <p class="text-xs text-gray-500 mt-1">
                    El RFC no se puede modificar. Si cambió, da de baja este proveedor y crea uno nuevo.
                </p>
            </div>

            <x-ui.input name="phone" label="Teléfono" :value="$supplier->phone" />
            <x-ui.input name="email" label="Correo" type="email" :value="$supplier->email" />
            <x-ui.input name="address" label="Dirección" :value="$supplier->address" />

            <div class="flex items-center gap-3 pt-4">
                <x-ui.button type="submit">Actualizar</x-ui.button>
                <a href="{{ route('inventory-manager.proveedores.index') }}">
                    <x-ui.button type="button" variant="ghost">Cancelar</x-ui.button>
                </a>
            </div>
        </form>
    </x-ui.card>
@endsection
