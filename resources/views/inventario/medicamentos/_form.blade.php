@props([
    'medicamento' => null,
    'suppliers',
    'categorias',
    'mode' => 'create',
])

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <x-ui.input name="name" label="Nombre" :value="$medicamento?->name" :required="true" />
    <x-ui.input name="barcode" label="Código de barras" :value="$medicamento?->barcode" :required="true" />
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
    <x-ui.input name="price" label="Precio" type="number" step="0.01" min="0.01" max="999999.99" :value="$medicamento?->price" :required="true" />
    <x-ui.input name="min_stock" label="Stock mínimo" type="number" min="0" :value="$medicamento?->min_stock ?? 0" :required="true" />
    @if ($mode === 'create')
        <x-ui.input name="stock_inicial" label="Stock inicial" type="number" min="0" value="0" />
    @endif
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    <x-ui.input
        name="expires_at"
        label="Fecha de vencimiento"
        type="date"
        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
        :value="$medicamento?->expires_at?->format('Y-m-d')"
        :required="true"
    />
    <x-ui.select
        name="category"
        label="Categoría"
        :value="$medicamento?->category?->value"
        :required="true"
        :options="collect($categorias)->map(fn($c) => ['value' => $c->value, 'label' => $c->label()])->all()"
    />
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    <x-ui.select
        name="supplier_id"
        label="Proveedor"
        placeholder="— Selecciona —"
        searchable
        :value="$medicamento?->supplier_id"
        :required="true"
        :options="collect($suppliers)->map(fn($s) => ['value' => $s->id, 'label' => $s->company_name])->all()"
    />
    @if ($mode === 'edit')
        <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-gray-700">Estado</label>
            <label class="flex items-center gap-2 mt-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $medicamento?->is_active)) />
                Activo (visible en catálogo y ventas)
            </label>
        </div>
    @endif
</div>

<div class="mt-4">
    <label for="description" class="text-sm font-medium text-gray-700">Descripción</label>
    <textarea
        name="description"
        id="description"
        rows="3"
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
    >{{ old('description', $medicamento?->description) }}</textarea>
    @error('description')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>
