@php
    $window = (int) ($filters['expiry_window_days'] ?? 30);
@endphp

<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reporte de inventario</h1>
            <p class="text-gray-500 text-sm mt-1">
                Estado actual del catálogo: stock, valor y vencimientos.
            </p>
        </div>
        <a href="{{ route('reportes.inventario.export', request()->query()) }}">
            <x-ui.button variant="secondary">Descargar CSV</x-ui.button>
        </a>
    </div>

    <x-ui.card>
        <form method="GET" action="{{ route('reportes.inventario.index') }}"
              class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <x-ui.select
                name="category"
                label="Categoría"
                placeholder="Todas"
                :value="$filters['category'] ?? ''"
                :options="collect($categories)->map(fn($c) => ['value' => $c->value, 'label' => $c->label()])->all()"
            />

            <x-ui.select
                name="supplier_id"
                label="Proveedor"
                placeholder="Todos"
                searchable
                :value="$filters['supplier_id'] ?? ''"
                :options="collect($suppliers)->map(fn($s) => ['value' => $s->id, 'label' => $s->company_name])->all()"
            />

            <x-ui.select
                name="stock_state"
                label="Estado de stock"
                placeholder="Todos"
                :value="$filters['stock_state'] ?? ''"
                :options="[
                    ['value' => 'normal', 'label' => 'Normal'],
                    ['value' => 'low', 'label' => 'Bajo mínimo'],
                    ['value' => 'zero', 'label' => 'En cero'],
                    ['value' => 'expired', 'label' => 'Vencidos'],
                ]"
            />

            <x-ui.select
                name="expiry_window_days"
                label="Ventana vencimiento"
                :value="(string) $window"
                :options="[
                    ['value' => '30', 'label' => '30 días'],
                    ['value' => '60', 'label' => '60 días'],
                    ['value' => '90', 'label' => '90 días'],
                ]"
            />

            <div class="md:col-span-4 flex gap-2 justify-end">
                <a href="{{ route('reportes.inventario.index') }}">
                    <x-ui.button type="button" variant="ghost">Limpiar</x-ui.button>
                </a>
                <x-ui.button type="submit" variant="primary">Filtrar</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <x-ui.card>
            <p class="text-xs uppercase tracking-wide text-gray-500">Activos</p>
            <p class="text-2xl font-semibold text-gray-900 mt-1">{{ number_format($kpis['active_count']) }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-xs uppercase tracking-wide text-gray-500">Valor inventario</p>
            <p class="text-2xl font-semibold text-gray-900 mt-1">${{ number_format($kpis['inventory_value'], 2) }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-xs uppercase tracking-wide text-gray-500">Bajo mínimo</p>
            <p class="text-2xl font-semibold text-gray-900 mt-1">{{ number_format($kpis['low_stock_count']) }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-xs uppercase tracking-wide text-gray-500">Próximos a vencer ({{ $window }} d)</p>
            <p class="text-2xl font-semibold text-gray-900 mt-1">{{ number_format($kpis['expiring_soon_count']) }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-xs uppercase tracking-wide text-gray-500">Vencidos</p>
            <p class="text-2xl font-semibold text-gray-900 mt-1">{{ number_format($kpis['expired_count']) }}</p>
        </x-ui.card>
    </div>

    @if ($rows->isEmpty())
        <x-ui.card>
            <p class="text-sm text-gray-600">No hay medicamentos para los filtros seleccionados.</p>
        </x-ui.card>
    @else
        <x-ui.table>
            <x-slot:header>
                <tr>
                    <th class="px-4 py-3">Medicamento</th>
                    <th class="px-4 py-3">Categoría</th>
                    <th class="px-4 py-3">Proveedor</th>
                    <th class="px-4 py-3 text-right">Precio</th>
                    <th class="px-4 py-3 text-right">Stock</th>
                    <th class="px-4 py-3 text-right">Mínimo</th>
                    <th class="px-4 py-3">Vence</th>
                    <th class="px-4 py-3">Estado</th>
                </tr>
            </x-slot:header>

            @foreach ($rows as $row)
                @php
                    $isLow = $row->stock <= $row->min_stock;
                    $isZero = $row->stock <= 0;
                    $isExpired = $row->expires_at && $row->expires_at->lt(now()->startOfDay());
                @endphp
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $row->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $row->category?->label() }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $row->supplier?->company_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">${{ number_format((float) $row->price, 2) }}</td>
                    <td class="px-4 py-3 text-right">{{ number_format((int) $row->stock) }}</td>
                    <td class="px-4 py-3 text-right">{{ number_format((int) $row->min_stock) }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $row->expires_at?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if ($isExpired)
                            <x-ui.badge variant="red">Vencido</x-ui.badge>
                        @elseif ($isZero)
                            <x-ui.badge variant="red">Sin stock</x-ui.badge>
                        @elseif ($isLow)
                            <x-ui.badge variant="yellow">Bajo</x-ui.badge>
                        @else
                            <x-ui.badge variant="green">Normal</x-ui.badge>
                        @endif
                    </td>
                </tr>
            @endforeach
        </x-ui.table>

        <div>
            {{ $rows->links() }}
        </div>
    @endif
</div>
