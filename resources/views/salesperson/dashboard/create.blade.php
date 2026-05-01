@extends('layouts.app')

@section('content')
@php($allowAnonymous = (bool) setting('permite_venta_sin_cliente', true))
<div class="max-w-6xl mx-auto py-6 px-4">
    <form action="{{ route('salesperson.ventas.store') }}" method="POST" id="pos-form">
        @csrf
        <input type="hidden" name="payment_method" value="cash">

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Carrito -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h2 class="text-lg font-bold mb-4">Productos</h2>
                    <div class="flex gap-4 mb-6">
                        <select id="product-picker" class="flex-1 rounded-lg border-gray-300">
                            <option value="">Seleccionar medicamento...</option>
                            @foreach($medications as $med)
                                <option value="{{ $med->id }}"
                                    data-price="{{ $med->price }}"
                                    data-stock="{{ $med->stock }}"
                                    data-controlled="{{ $med->category->requiresPrescription() ? 1 : 0 }}">
                                    {{ $med->name }} (${{ $med->price }})
                                </option>
                            @endforeach
                        </select>
                        <button type="button" onclick="addItem()" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold">Añadir</button>
                    </div>

                    <table class="w-full" id="cart-table">
                        <thead class="border-b text-xs text-gray-400 uppercase">
                            <tr>
                                <th class="text-left py-3">Medicamento</th>
                                <th class="text-center py-3">Cant.</th>
                                <th class="text-right py-3">Subtotal</th>
                                <th class="py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y"></tbody>
                    </table>
                </div>
            </div>

            <!-- Checkout -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h2 class="text-lg font-bold mb-4">Resumen de Venta</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase" for="customer_id">Cliente</label>
                            <select id="customer_id" name="customer_id" class="w-full rounded-lg border-gray-300" {{ $allowAnonymous ? '' : 'required' }}>
                                @if ($allowAnonymous)
                                    <option value="">— Cliente anónimo —</option>
                                @endif
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase" for="sold_at">Fecha de venta</label>
                            <input type="datetime-local"
                                   id="sold_at"
                                   name="sold_at"
                                   value="{{ old('sold_at', now()->format('Y-m-d\TH:i')) }}"
                                   max="{{ now()->format('Y-m-d\TH:i') }}"
                                   required
                                   class="w-full rounded-lg border-gray-300 text-sm">
                        </div>

                        <!-- Sección de Receta (Solo visible si hay controlados) -->
                        <div id="medical-fields" class="hidden p-4 bg-amber-50 border border-amber-200 rounded-lg space-y-3">
                            <p class="text-[10px] font-bold text-amber-700 uppercase tracking-tight">Datos Obligatorios de Receta</p>
                            <input type="text" name="doctor_name" id="doctor_name" value="{{ old('doctor_name') }}" placeholder="Nombre del Médico" class="w-full text-sm rounded-md border-amber-300">
                            <input type="text" name="doctor_license" id="doctor_license" value="{{ old('doctor_license') }}" placeholder="JVP (Ej: 12345)" class="w-full text-sm rounded-md border-amber-300">
                        </div>

                        <div class="pt-4 border-t">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-gray-500 font-medium text-sm">Subtotal</span>
                                <span id="subtotal-display" class="font-mono text-gray-700">$0.00</span>
                            </div>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-gray-500 font-medium text-sm">IVA estimado</span>
                                <span id="tax-display" class="font-mono text-gray-700">$0.00</span>
                            </div>
                            <div class="flex justify-between items-center mb-4 pt-2 border-t">
                                <span class="text-gray-500 font-medium">Total</span>
                                <span id="total-display" class="text-3xl font-black text-gray-900">$0.00</span>
                            </div>
                            <p class="text-[10px] text-gray-400 mb-3">El total final se calcula en el servidor con la tasa de IVA configurada.</p>
                            <button type="submit" id="btn-submit" class="w-full py-4 bg-green-600 text-white rounded-xl font-bold text-lg hover:bg-green-700 transition-colors">
                                Confirmar Pago
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const TAX_RATE = {{ (float) setting('tasa_iva', 0.13) }};
    let cart = [];

    function addItem() {
        const picker = document.getElementById('product-picker');
        const opt = picker.options[picker.selectedIndex];
        if (!opt.value) return;

        if (cart.find(i => i.id === opt.value)) return alert('Ya está en el carrito');

        cart.push({
            id: opt.value,
            name: opt.text.split('(')[0].trim(),
            price: parseFloat(opt.dataset.price),
            stock: parseInt(opt.dataset.stock),
            controlled: opt.dataset.controlled === "1",
            qty: 1
        });
        render();
    }

    function updateQty(id, qty) {
        const item = cart.find(i => i.id === id);
        if (item) item.qty = Math.min(item.stock, Math.max(1, parseInt(qty)));
        render();
    }

    function removeItem(id) {
        cart = cart.filter(i => i.id !== id);
        render();
    }

    function render() {
        const body = document.querySelector('#cart-table tbody');
        body.innerHTML = '';
        let subtotal = 0;
        let needsPrescription = false;

        cart.forEach((item, index) => {
            const sub = item.qty * item.price;
            subtotal += sub;
            if (item.controlled) needsPrescription = true;

            body.innerHTML += `
                <tr class="text-sm">
                    <td class="py-4">
                        <span class="font-bold text-gray-800">${item.name}</span>
                        ${item.controlled ? '<span class="ml-2 px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] rounded font-black uppercase">Receta</span>' : ''}
                        <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                        <input type="hidden" name="items[${index}][quantity]" value="${item.qty}">
                    </td>
                    <td class="text-center">
                        <input type="number" onchange="updateQty('${item.id}', this.value)" value="${item.qty}" class="w-16 border-gray-200 rounded text-center">
                    </td>
                    <td class="text-right font-mono font-bold">$${sub.toFixed(2)}</td>
                    <td class="text-right"><button type="button" onclick="removeItem('${item.id}')" class="text-red-400 hover:text-red-600 font-bold ml-4">×</button></td>
                </tr>
            `;
        });

        const tax = subtotal * TAX_RATE;
        const total = subtotal + tax;
        document.getElementById('subtotal-display').innerText = `$${subtotal.toFixed(2)}`;
        document.getElementById('tax-display').innerText = `$${tax.toFixed(2)}`;
        document.getElementById('total-display').innerText = `$${total.toFixed(2)}`;

        // Validación dinámica de campos médicos
        const medicalDiv = document.getElementById('medical-fields');
        const btn = document.getElementById('btn-submit');
        const docName = document.getElementById('doctor_name');
        const docLic = document.getElementById('doctor_license');

        if (needsPrescription) {
            medicalDiv.classList.remove('hidden');
            docName.required = docLic.required = true;
            btn.innerText = 'Enviar a Farmacéutico';
            btn.className = btn.className.replace('bg-green-600', 'bg-amber-600').replace('hover:bg-green-700', 'hover:bg-amber-700');
        } else {
            medicalDiv.classList.add('hidden');
            docName.required = docLic.required = false;
            btn.innerText = 'Confirmar Pago';
            btn.className = btn.className.replace('bg-amber-600', 'bg-green-600').replace('hover:bg-amber-700', 'hover:bg-green-700');
        }
    }
</script>
@endsection
