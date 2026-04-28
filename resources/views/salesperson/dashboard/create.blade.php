@extends('layouts.app')

@section('title', 'Nueva Venta')

@section('content')
<div class="max-w-6xl mx-auto py-6 px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Punto de Venta (POS)</h1>
        <a href="{{ route('salesperson.dashboard') }}" class="text-indigo-600 hover:underline">← Volver al panel</a>
    </div>

    <form action="{{ route('salesperson.ventas.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Sección de Carrito --}}
            <div class="lg:col-span-2 space-y-6">
                <x-ui.card title="Selección de Productos">
                    <div class="flex gap-4 mb-4">
                        <select id="product-picker" class="flex-1 rounded-lg border-gray-300">
                            <option value="">Buscar medicamento...</option>
                            @foreach($medications as $med)
                            <option value="{{ $med->id }}" data-price="{{ $med->price }}" data-stock="{{ $med->stock }}">
                                {{ $med->name }} (Stock: {{ $med->stock }})
                            </option>
                            @endforeach
                        </select>
                        <button type="button" onclick="addItem()" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold">
                            + Añadir
                        </button>
                    </div>

                    <table class="min-w-full" id="cart-table">
                        <thead>
                            <tr class="text-xs font-bold text-gray-400 uppercase border-b">
                                <th class="py-2 text-left">Producto</th>
                                <th class="py-2 text-center">Cant.</th>
                                <th class="py-2 text-right">Subtotal</th>
                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            {{-- Contenido dinámico --}}
                        </tbody>
                    </table>
                </x-ui.card>
            </div>

            {{-- Resumen y Pago --}}
            <div class="space-y-6">
                <x-ui.card title="Datos de Facturación">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase">Cliente</label>
                            <select name="customer_id" class="w-full rounded-lg border-gray-300">
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase">Método de Pago</label>
                            <div class="mt-1 flex items-center p-2 bg-green-50 border border-green-200 rounded-lg">
                                <span class="text-green-700 font-bold">Efectivo</span>
                            </div>
                            <input type="hidden" name="payment_method" value="cash">
                        </div>
                    </div>

                    <div class="mt-8 border-t pt-4">
                        <div class="flex justify-between items-center mb-6">
                            <span class="text-gray-500 font-bold uppercase tracking-wider text-xs">TOTAL A PAGAR</span>
                            <span class="text-4xl font-extrabold text-gray-950" id="total-display">$0.00</span>
                        </div>

                        <input type="hidden" name="total" id="total-input" value="0">
                        <input type="hidden" name="subtotal" id="subtotal-input" value="0">
                        <input type="hidden" name="tax" value="0">
                        <input type="hidden" name="sold_at" value="{{ now() }}">
                        <input type="hidden" name="status" value="completed">

                        <button type="submit" style="background-color: #16a34a !important; color: white !important;" class="w-full py-4 rounded-xl font-bold text-lg hover:opacity-90 transition shadow-md flex items-center justify-center">
                            Confirmar Venta
                        </button>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </form>
</div>

<script>
    let index = 0;

    function addItem() {
        const picker = document.getElementById('product-picker');
        const opt = picker.options[picker.selectedIndex];
        if (!opt.value) return;

        if (parseInt(opt.dataset.stock) <= 0) {
            alert('Producto sin stock disponible');
            return;
        }

        const tbody = document.querySelector('#cart-table tbody');

        if (document.querySelector(`input[value="${opt.value}"][name*="product_id"]`)) {
            alert('El producto ya se encuentra en el listado');
            picker.value = '';
            return;
        }

        const row = document.createElement('tr');
        row.innerHTML = `
        <td class="py-4 text-sm font-medium text-gray-900">
            ${opt.text.split('(')[0]}
            <input type="hidden" name="items[${index}][product_id]" value="${opt.value}">
            <input type="hidden" name="items[${index}][unit_price]" value="${opt.dataset.price}">
        </td>
        <td class="text-center">
            <input type="number" name="items[${index}][quantity]" value="1" min="1" max="${opt.dataset.stock}"
                class="w-20 border rounded-lg text-center p-2 focus:ring-green-500"
                oninput="validateAndCalc(this)">
        </td>
        <td class="text-right font-bold text-gray-900 sub-val">$${parseFloat(opt.dataset.price).toFixed(2)}</td>
        <td class="text-right">
            <button type="button" onclick="this.closest('tr').remove();calc()" class="text-red-400 hover:text-red-600 transition">
                Eliminar
            </button>
        </td>
        `;
        tbody.appendChild(row);
        index++;
        calc();
        picker.value = '';
    }

    function validateAndCalc(input) {
        const max = parseInt(input.max);
        if (parseInt(input.value) > max) {
            input.value = max;
        }
        if (parseInt(input.value) < 1 || input.value === '') {
            input.value = 1;
        }
        calc();
    }

    function calc() {
        let t = 0;
        document.querySelectorAll('#cart-table tbody tr').forEach(r => {
            const q = r.querySelector('input[name*="quantity"]').value || 1;
            const p = r.querySelector('input[name*="unit_price"]').value;
            const s = q * p;
            r.querySelector('.sub-val').innerText = `$${s.toFixed(2)}`;
            t += s;
        });
        document.getElementById('total-display').innerText = `$${t.toFixed(2)}`;
        document.getElementById('total-input').value = t.toFixed(2);
        document.getElementById('subtotal-input').value = t.toFixed(2);
    }
</script>
@endsection