@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">
    {{-- Header --}}
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('salesperson.clientes.index') }}" class="p-2.5 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-indigo-600 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest italic">Edición</span>
            <h1 class="text-2xl font-black text-gray-900">{{ $customer->name }}</h1>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs font-bold rounded-r-xl">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('salesperson.clientes.update', ['cliente' => $customer->id]) }}" method="POST">
        @csrf
        @method('PATCH')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Datos Principales --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                    <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Identidad Protegida</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-2 ml-1">Nombre Completo</label>
                            <input type="text" name="name" value="{{ old('name', $customer->name) }}" required class="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-2 ml-1">DUI (No modificable)</label>
                            <input type="text" value="{{ $customer->identification }}" disabled class="w-full bg-gray-100 border-gray-200 rounded-2xl px-4 py-3 text-sm font-mono text-gray-400 cursor-not-allowed">
                            {{-- Campo oculto para mantener el valor en el request si fuera necesario --}}
                            <input type="hidden" name="identification" value="{{ $customer->identification }}">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase mb-2 ml-1">Dirección Registrada</label>
                        <textarea name="address" rows="3" class="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">{{ old('address', $customer->address) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Contacto y Acción --}}
            <div class="space-y-6">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                    <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Preferencias y Contacto</h2>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-2 ml-1">Teléfono</label>
                            <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                                   pattern="(\+?503[\s-]?)?[267]\d{3}[\s-]?\d{4}"
                                   maxlength="15"
                                   title="Formato: ####-####, debe iniciar con 2, 6 o 7. Opcional prefijo +503 (ej: +503 7777-7777)"
                                   placeholder="+503 7777-7777"
                                   class="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-2 ml-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full bg-gray-50 border-gray-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>

                        <div class="pt-4 border-t border-gray-50 space-y-4">
                            <label class="flex items-center group cursor-pointer">
                                <input type="checkbox" name="is_frequent" value="1" {{ old('is_frequent', $customer->is_frequent) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-0">
                                <span class="ml-3 text-[10px] font-black text-gray-400 uppercase group-hover:text-indigo-600 transition-colors italic">Cliente Frecuente ★</span>
                            </label>
                            <label class="flex items-center group cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $customer->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-0">
                                <span class="ml-3 text-[10px] font-black text-gray-400 uppercase group-hover:text-green-600 transition-colors italic">Cuenta Activa</span>
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95">
                    Actualizar Cambios
                </button>
            </div>
        </div>
    </form>

    {{-- Mantenimiento (Solo Admin) --}}
    @if(Auth::user()->role === 'admin')
    <div class="mt-12 bg-red-50/50 rounded-3xl border-2 border-dashed border-red-100 p-8 flex flex-col md:flex-row justify-between items-center gap-6">
        <div>
            <h3 class="text-red-600 font-black uppercase text-xs tracking-widest mb-1 italic">Control de Registros</h3>
            <p class="text-gray-500 text-[10px] font-bold uppercase tracking-tight">Borrado definitivo del cliente del sistema central.</p>
        </div>
        <form id="delete-customer-{{ $customer->id }}" action="{{ route('salesperson.clientes.destroy', ['cliente' => $customer->id]) }}" method="POST">
            @csrf
            @method('DELETE')
            <x-ui.confirm
                target-form="delete-customer-{{ $customer->id }}"
                title="Eliminar cliente"
                message="¿Confirmas la eliminación permanente del cliente {{ $customer->name }}? Esta acción no se puede deshacer."
                confirm-label="Eliminar"
                variant="danger"
            >
                <button type="button" class="bg-white text-red-600 border border-red-200 px-8 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all">
                    Eliminar Registro
                </button>
            </x-ui.confirm>
        </form>
    </div>
    @endif
</div>
@endsection