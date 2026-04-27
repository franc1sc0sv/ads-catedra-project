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
    <x-ui.input name="price" label="Precio" type="number" step="0.01" :value="$medicamento?->price" :required="true" />
    <x-ui.input name="min_stock" label="Stock mínimo" type="number" :value="$medicamento?->min_stock ?? 0" :required="true" />
    @if ($mode === 'create')
        <x-ui.input name="stock_inicial" label="Stock inicial" type="number" value="0" />
    @endif
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    <x-ui.input
        name="expires_at"
        label="Fecha de vencimiento"
        type="date"
        :value="$medicamento?->expires_at?->format('Y-m-d')"
        :required="true"
    />
    <div class="flex flex-col gap-1">
        <label for="category" class="text-sm font-medium text-gray-700">Categoría</label>
        <select name="category" id="category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            @foreach ($categorias as $cat)
                <option value="{{ $cat->value }}" @selected(old('category', $medicamento?->category?->value) === $cat->value)>{{ $cat->label() }}</option>
            @endforeach
        </select>
        @error('category')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    <div class="flex flex-col gap-1">
        <label for="supplier_id" class="text-sm font-medium text-gray-700">Proveedor</label>
        <select name="supplier_id" id="supplier_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            <option value="">— Selecciona —</option>
            @foreach ($suppliers as $s)
                <option value="{{ $s->id }}" @selected((int) old('supplier_id', $medicamento?->supplier_id) === (int) $s->id)>{{ $s->company_name }}</option>
            @endforeach
        </select>
        @error('supplier_id')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>
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
