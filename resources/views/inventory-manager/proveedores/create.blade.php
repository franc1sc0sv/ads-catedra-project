@extends('layouts.app')

@section('title', 'Nuevo proveedor')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Nuevo proveedor</h1>
        <p class="text-gray-500 text-sm mt-1">Registra una empresa proveedora.</p>
    </div>

    <x-ui.card>
        <form method="POST" action="{{ route('inventory-manager.proveedores.store') }}" class="space-y-4">
            @csrf

            <x-ui.input name="company_name" label="Razón social" required />
            <x-ui.input name="tax_id" label="Identificador fiscal (RFC)" required />
            <x-ui.input name="phone" label="Teléfono" />
            <x-ui.input name="email" label="Correo" type="email" />
            <x-ui.input name="address" label="Dirección" />

            <div class="flex items-center gap-3 pt-4">
                <x-ui.button type="submit">Guardar</x-ui.button>
                <a href="{{ route('inventory-manager.proveedores.index') }}">
                    <x-ui.button type="button" variant="ghost">Cancelar</x-ui.button>
                </a>
            </div>
        </form>
    </x-ui.card>
@endsection
