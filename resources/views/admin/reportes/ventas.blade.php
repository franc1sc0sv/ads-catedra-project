@extends('layouts.app')

@section('title', 'Reporte de ventas')

@php
    $defaultFrom = $filters['from'] ?? now()->startOfMonth()->format('Y-m-d');
    $defaultTo = $filters['to'] ?? now()->format('Y-m-d');
@endphp

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reporte de ventas</h1>
                <p class="text-gray-500 text-sm mt-1">KPIs y detalle de ventas en el rango seleccionado.</p>
            </div>
            <a href="{{ route('admin.reportes.ventas.export', request()->query()) }}">
                <x-ui.button variant="secondary">Descargar CSV</x-ui.button>
            </a>
        </div>

        <x-ui.card>
            <form method="GET" action="{{ route('admin.reportes.ventas.index') }}"
                  class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <x-ui.input name="from" type="date" label="Desde" :value="$defaultFrom" />
                <x-ui.input name="to" type="date" label="Hasta" :value="$defaultTo" />

                <div class="flex flex-col gap-1">
                    <label for="payment_method" class="text-sm font-medium text-gray-700">Método de pago</label>
                    <select id="payment_method" name="payment_method"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Todos</option>
                        @foreach ($paymentMethods as $m)
                            <option value="{{ $m->value }}" @selected(($filters['payment_method'] ?? '') === $m->value)>
                                {{ $m->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label for="salesperson_id" class="text-sm font-medium text-gray-700">Vendedor</label>
                    <select id="salesperson_id" name="salesperson_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Todos</option>
                        @foreach ($salespersons as $s)
                            <option value="{{ $s->id }}" @selected((string) ($filters['salesperson_id'] ?? '') === (string) $s->id)>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label for="status" class="text-sm font-medium text-gray-700">Estado</label>
                    <select id="status" name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @foreach ($statuses as $st)
                            <option value="{{ $st->value }}" @selected(($filters['status'] ?? \App\Enums\SaleStatus::COMPLETED->value) === $st->value)>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-5 flex gap-2 justify-end">
                    <a href="{{ route('admin.reportes.ventas.index') }}">
                        <x-ui.button type="button" variant="ghost">Limpiar</x-ui.button>
                    </a>
                    <x-ui.button type="submit" variant="primary">Filtrar</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <x-ui.card>
                <p class="text-xs uppercase tracking-wide text-gray-500">Ventas completadas</p>
                <p class="text-2xl font-semibold text-gray-900 mt-1">{{ number_format($kpis['count_completed']) }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs uppercase tracking-wide text-gray-500">Ingreso total</p>
                <p class="text-2xl font-semibold text-gray-900 mt-1">${{ number_format($kpis['total_revenue'], 2) }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs uppercase tracking-wide text-gray-500">Ticket promedio</p>
                <p class="text-2xl font-semibold text-gray-900 mt-1">${{ number_format($kpis['avg_ticket'], 2) }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs uppercase tracking-wide text-gray-500">Cancelaciones</p>
                <p class="text-2xl font-semibold text-gray-900 mt-1">{{ number_format($kpis['count_cancelled']) }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs uppercase tracking-wide text-gray-500">Monto cancelado</p>
                <p class="text-2xl font-semibold text-gray-900 mt-1">${{ number_format($kpis['total_cancelled'], 2) }}</p>
            </x-ui.card>
        </div>

        <div>
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Ventas</h2>
            @if ($ventas->isEmpty())
                <x-ui.card>
                    <p class="text-sm text-gray-600">No hay ventas para los filtros seleccionados.</p>
                </x-ui.card>
            @else
                <x-ui.table>
                    <x-slot:header>
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Cliente</th>
                            <th class="px-4 py-3">Vendedor</th>
                            <th class="px-4 py-3">Método</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                    </x-slot:header>

                    @foreach ($ventas as $venta)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $venta->id }}</td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $venta->sold_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $venta->customer?->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $venta->salesperson?->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $venta->payment_method?->label() }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge variant="{{ $venta->status === \App\Enums\SaleStatus::CANCELLED ? 'red' : 'green' }}">
                                    {{ $venta->status?->label() }}
                                </x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-right font-medium">${{ number_format((float) $venta->total, 2) }}</td>
                        </tr>
                    @endforeach
                </x-ui.table>

                <div class="mt-3">
                    {{ $ventas->links() }}
                </div>
            @endif
        </div>

        <div>
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Top productos vendidos</h2>
            @if ($topProductos->isEmpty())
                <x-ui.card>
                    <p class="text-sm text-gray-600">Sin datos para los filtros seleccionados.</p>
                </x-ui.card>
            @else
                <x-ui.table>
                    <x-slot:header>
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Medicamento</th>
                            <th class="px-4 py-3 text-right">Unidades</th>
                        </tr>
                    </x-slot:header>

                    @foreach ($topProductos as $i => $p)
                        <tr>
                            <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-medium">{{ $p->name }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format((int) $p->total_units) }}</td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        </div>
    </div>
@endsection
