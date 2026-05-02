@extends('layouts.app')

@section('title', 'Nuevo pedido')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Nuevo pedido</h1>
        <p class="text-gray-500 text-sm mt-1">Arma una orden de compra a un proveedor activo.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4">
            <x-ui.alert variant="danger" title="Revisa los datos">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        </div>
    @endif

    @php
        $medsJson = $medications
            ->map(fn ($m) => ['id' => $m->id, 'name' => $m->name, 'price' => (float) $m->price])
            ->values()
            ->toJson();
    @endphp

    <x-ui.card>
        <form method="POST" action="{{ route('inventory-manager.pedidos.store') }}"
              x-data="pedidoForm({{ $medsJson }})">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.select
                    name="supplier_id"
                    label="Proveedor"
                    placeholder="Selecciona un proveedor"
                    searchable
                    :required="true"
                    :options="collect($suppliers)->map(fn($s) => ['value' => $s->id, 'label' => $s->company_name])->all()"
                />

                <x-ui.input name="expected_at" type="date" label="Fecha esperada de entrega (opcional)" :value="old('expected_at')" :error="$errors->first('expected_at')" />
            </div>

            <div class="mt-4">
                <x-ui.input name="notes" label="Observaciones (opcional)" :value="old('notes')" :error="$errors->first('notes')" />
            </div>

            <hr class="my-6 border-gray-200">

            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold text-gray-900">Líneas</h2>
                <x-ui.button type="button" variant="secondary" size="sm" x-on:click="addLine()">
                    Agregar línea
                </x-ui.button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-3 py-2">Medicamento</th>
                            <th class="px-3 py-2 w-28">Cantidad</th>
                            <th class="px-3 py-2 w-32">Precio unit.</th>
                            <th class="px-3 py-2 w-32 text-right">Subtotal</th>
                            <th class="px-3 py-2 w-12"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(line, idx) in lines" :key="idx">
                            <tr class="border-t border-gray-100">
                                <td class="px-3 py-2">
                                    <div class="relative">
                                        <select :name="`items[${idx}][medication_id]`" required
                                            x-model="line.medication_id"
                                            x-on:change="onMedChange(idx)"
                                            class="w-full appearance-none bg-white border border-gray-300 rounded-lg pl-3 pr-8 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                                            <option value="">Selecciona</option>
                                            <template x-for="med in medications" :key="med.id">
                                                <option :value="med.id" x-text="med.name"></option>
                                            </template>
                                        </select>
                                        <svg class="w-4 h-4 text-gray-400 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" min="1" step="1" required
                                        :name="`items[${idx}][quantity]`"
                                        x-model.number="line.quantity"
                                        class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm" />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" min="0" step="0.01" required
                                        :name="`items[${idx}][unit_price]`"
                                        x-model.number="line.unit_price"
                                        class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm" />
                                </td>
                                <td class="px-3 py-2 text-right text-gray-700"
                                    x-text="formatMoney(subtotal(line))"></td>
                                <td class="px-3 py-2 text-right">
                                    <button type="button" x-on:click="removeLine(idx)"
                                        class="text-red-500 hover:text-red-700 text-sm">×</button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="lines.length === 0">
                            <td colspan="5" class="px-3 py-4 text-center text-gray-500">
                                Agrega al menos una línea.
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-gray-200 bg-gray-50">
                            <td colspan="3" class="px-3 py-3 text-right font-semibold">Total estimado</td>
                            <td class="px-3 py-3 text-right font-semibold" x-text="formatMoney(total())"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="flex items-center gap-3 pt-6">
                <x-ui.button type="submit">Crear pedido</x-ui.button>
                <a href="{{ route('inventory-manager.pedidos.index') }}">
                    <x-ui.button type="button" variant="ghost">Cancelar</x-ui.button>
                </a>
            </div>
        </form>
    </x-ui.card>

    <script>
        function pedidoForm(medications) {
            return {
                medications,
                lines: [{ medication_id: '', quantity: 1, unit_price: 0 }],
                addLine() {
                    this.lines.push({ medication_id: '', quantity: 1, unit_price: 0 });
                },
                removeLine(idx) {
                    this.lines.splice(idx, 1);
                },
                onMedChange(idx) {
                    const med = this.medications.find(m => String(m.id) === String(this.lines[idx].medication_id));
                    if (med && (!this.lines[idx].unit_price || this.lines[idx].unit_price === 0)) {
                        this.lines[idx].unit_price = med.price;
                    }
                },
                subtotal(line) {
                    const q = Number(line.quantity) || 0;
                    const p = Number(line.unit_price) || 0;
                    return q * p;
                },
                total() {
                    return this.lines.reduce((sum, l) => sum + this.subtotal(l), 0);
                },
                formatMoney(n) {
                    return '$' + (Number(n) || 0).toFixed(2);
                },
            };
        }
    </script>
@endsection
