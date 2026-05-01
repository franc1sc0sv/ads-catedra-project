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
            <div class="flex flex-col gap-1">
                <label for="category" class="text-sm font-medium text-gray-700">Categoría</label>
                <select id="category" name="category"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Todas</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->value }}" @selected(($filters['category'] ?? '') === $c->value)>
                            {{ $c->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label for="supplier_id" class="text-sm font-medium text-gray-700">Proveedor</label>
                <select id="supplier_id" name="supplier_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Todos</option>
                    @foreach ($suppliers as $s)
                        <option value="{{ $s->id }}" @selected((string) ($filters['supplier_id'] ?? '') === (string) $s->id)>
                            {{ $s->company_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label for="stock_state" class="text-sm font-medium text-gray-700">Estado de stock</label>
                <select id="stock_state" name="stock_state"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Todos</option>
                    <option value="normal" @selected(($filters['stock_state'] ?? '') === 'normal')>Normal</option>
                    <option value="low" @selected(($filters['stock_state'] ?? '') === 'low')>Bajo mínimo</option>
                    <option value="zero" @selected(($filters['stock_state'] ?? '') === 'zero')>En cero</option>
                    <option value="expired" @selected(($filters['stock_state'] ?? '') === 'expired')>Vencidos</option>
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label for="expiry_window_days" class="text-sm font-medium text-gray-700">Ventana vencimiento</label>
                <select id="expiry_window_days" name="expiry_window_days"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="30" @selected($window === 30)>30 días</option>
                    <option value="60" @selected($window === 60)>60 días</option>
                    <option value="90" @selected($window === 90)>90 días</option>
                </select>
            </div>

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
