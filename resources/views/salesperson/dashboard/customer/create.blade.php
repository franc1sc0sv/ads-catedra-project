@extends('layouts.app')

@section('title', 'Nuevo Cliente')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('salesperson.clientes.index') }}" class="p-2 bg-white rounded-lg border border-gray-200 text-gray-400 hover:text-indigo-600 shadow-sm transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Registrar Nuevo Cliente</h1>
    </div>

    <form action="{{ route('salesperson.clientes.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Datos de Identidad --}}
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                <h3 class="text-[10px] font-bold text-indigo-600 uppercase">Información Básica</h3>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Nombre Completo</label>
                    <input type="text" name="name" value="{{ old('name') }}" required minlength="3"
                           class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-red-500 text-[10px] mt-1 italic font-bold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">DUI (########-#)</label>
                    <input type="text" name="identification" value="{{ old('identification') }}" required
                           pattern="\d{8}-\d{1}" maxlength="10" 
                           title="Debe ingresar 8 números, un guion y 1 dígito (ej: 01234567-8)"
                           class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 @error('identification') border-red-500 @enderror"
                           placeholder="00000000-0">
                    @error('identification') <p class="text-red-500 text-[10px] mt-1 italic font-bold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Dirección Exacta</label>
                    <textarea name="address" rows="2" class="w-full rounded-xl border-gray-200 focus:ring-indigo-500">{{ old('address') }}</textarea>
                </div>
            </div>

            {{-- Datos de Contacto y Estado --}}
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                <h3 class="text-[10px] font-bold text-indigo-600 uppercase">Configuración</h3>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Teléfono (####-####)</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" 
                           pattern="[267]{1}\d{3}-?\d{4}" maxlength="9"
                           title="Inicie con 2, 6 o 7 y use guion (ej: 7777-7777)"
                           class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 @error('phone') border-red-500 @enderror">
                    @error('phone') <p class="text-red-500 text-[10px] mt-1 italic font-bold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
                </div>

                <div class="flex flex-col gap-3 pt-4 border-t border-gray-50">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_frequent" value="1" {{ old('is_frequent') ? 'checked' : '' }} class="rounded text-indigo-600">
                        <span class="ml-2 text-[10px] font-bold text-gray-600 uppercase italic">Cliente Frecuente</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded text-green-600">
                        <span class="ml-2 text-[10px] font-bold text-gray-600 uppercase italic">Cuenta Activa</span>
                    </label>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full mt-6 bg-indigo-600 text-white py-4 rounded-2xl font-bold uppercase text-xs tracking-widest hover:bg-indigo-700 shadow-lg transition">
            Guardar Nuevo Cliente
        </button>
    </form>
</div>
@endsection