<?php

declare(strict_types=1);

namespace App\Http\Requests\Proveedores;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StorePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => [
                'required',
                'integer',
                Rule::exists('suppliers', 'id')->where(fn ($q) => $q->where('is_active', true)),
            ],
            'expected_at' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medication_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('medications', 'id')->where(fn ($q) => $q->where('is_active', true)),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function attributes(): array
    {
        return [
            'supplier_id' => 'proveedor',
            'expected_at' => 'fecha esperada de entrega',
            'notes' => 'observaciones',
            'items' => 'líneas',
            'items.*.medication_id' => 'medicamento',
            'items.*.quantity' => 'cantidad',
            'items.*.unit_price' => 'precio unitario',
        ];
    }
}
