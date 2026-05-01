@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
    <div class="flex flex-col gap-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reportes</h1>
            <p class="text-gray-500 text-sm mt-1">Reportes administrativos y bitácora de auditoría.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('admin.reportes.ventas.index') }}"
               class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:border-primary/40 hover:shadow-md transition">
                <h3 class="text-lg font-semibold text-gray-900">Reporte de ventas</h3>
                <p class="text-sm text-gray-600 mt-2">KPIs de ingreso, ticket promedio, top productos y exportación CSV.</p>
            </a>

            <a href="{{ route('reportes.inventario.index') }}"
               class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:border-primary/40 hover:shadow-md transition">
                <h3 class="text-lg font-semibold text-gray-900">Reporte de inventario</h3>
                <p class="text-sm text-gray-600 mt-2">Valor de inventario, alertas de bajo stock y vencimientos.</p>
            </a>

            <a href="{{ route('admin.reportes.movimientos.index') }}"
               class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:border-primary/40 hover:shadow-md transition">
                <h3 class="text-lg font-semibold text-gray-900">Reporte de movimientos</h3>
                <p class="text-sm text-gray-600 mt-2">Historial detallado de movimientos de inventario con filtros.</p>
            </a>

            <a href="{{ route('admin.reportes.bitacora.index') }}"
               class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:border-primary/40 hover:shadow-md transition">
                <h3 class="text-lg font-semibold text-gray-900">Bitácora de auditoría</h3>
                <p class="text-sm text-gray-600 mt-2">Eventos críticos del sistema (login, cambios de rol, ajustes).</p>
            </a>
        </div>
    </div>
@endsection
